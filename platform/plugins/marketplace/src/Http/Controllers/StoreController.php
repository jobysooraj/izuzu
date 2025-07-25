<?php
namespace Botble\Marketplace\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Supports\Breadcrumb;
use Botble\Ecommerce\Http\Controllers\BaseController;
use Botble\Marketplace\Facades\MarketplaceHelper;
use Botble\Marketplace\Forms\PayoutInformationForm;
use Botble\Marketplace\Forms\StoreForm;
use Botble\Marketplace\Forms\TaxInformationForm;
use Botble\Marketplace\Http\Requests\PayoutInformationSettingRequest;
use Botble\Marketplace\Http\Requests\StoreRequest;
use Botble\Marketplace\Http\Requests\TaxInformationSettingRequest;
use Botble\Marketplace\Models\Store;
use Botble\Marketplace\Tables\StoreTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/marketplace::store.name'), route('marketplace.store.index'));
    }

    public function index(StoreTable $table)
    {
        $this->pageTitle(trans('plugins/marketplace::store.name'));

        return $table->renderTable();
    }

    public function create()
    {

        $this->pageTitle(trans('plugins/marketplace::store.create'));

        return view('plugins/marketplace::stores.form', [
            'store' => new Store(),
            'form'  => StoreForm::create()
                ->setUrl(route('marketplace.store.create'))
                ->renderForm(),
        ]);
    }

    public function store(StoreRequest $request)
    {

        $form = StoreForm::create()
            ->setRequest($request);

        $form->save();

        $store   = $form->getModel();
        $storage = Storage::disk('local');

        if (! $storage->exists("marketplace/{$store->slug}")) {
            $storage->makeDirectory("marketplace/{$store->slug}");
        }

        if ($certificateFile = $request->file('certificate_file')) {
            $certificateFilePath = $storage->putFileAs(
                "marketplace/{$store->slug}",
                $certificateFile,
                'certificate.' . $certificateFile->getClientOriginalExtension()
            );
            $store->certificate_file = $certificateFilePath;
        }

        if ($governmentIdFile = $request->file('government_id_file')) {
            $governmentIdFilePath = $storage->putFileAs(
                "marketplace/{$store->slug}",
                $governmentIdFile,
                'government_id.' . $governmentIdFile->getClientOriginalExtension()
            );
            $store->government_id_file = $governmentIdFilePath;
        }

        $store->save();
        if ($request->has('social_links')) {
            if ($socialLinks = $request->input('social_links', [])) {
                $socials     = array_keys(MarketplaceHelper::getAllowedSocialLinks());
                $socialLinks = collect($socialLinks)->only($socials)->filter();
                MetaBox::saveMetaBoxData($store, 'social_links', $socialLinks);
            }
        }

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('marketplace.store.index'))
            ->setNextUrl(route('marketplace.store.edit', $store->id))
            ->withCreatedSuccessMessage();
    }

    public function edit(Store $store, Request $request)
    {
        $form = StoreForm::createFromModel($store)
            ->setUrl(route('marketplace.store.update', $store->getKey()))
            ->renderForm();

        $taxInformationForm    = null;
        $payoutInformationForm = null;

        if ($store->customer->is_vendor) {
            $taxInformationForm = TaxInformationForm::createFromModel($store->customer)
                ->setUrl(route('marketplace.store.update-tax-info', $store))
                ->renderForm();

            $payoutInformationForm = PayoutInformationForm::createFromModel($store->customer)
                ->setUrl(route('marketplace.store.update-payout-info', $store))
                ->renderForm();
        }

        event(new BeforeEditContentEvent($request, $store));

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $store->name]));

        return view(
            'plugins/marketplace::stores.form',
            compact('store', 'form', 'taxInformationForm', 'payoutInformationForm')
        );
    }

    public function update(Store $store, StoreRequest $request)
    {

        StoreForm::createFromModel($store)
            ->setRequest($request)
            ->save();
        $storage    = Storage::disk('local');
        $folderPath = "marketplace/{$store->slug}";

        if (! $storage->exists($folderPath)) {
            $storage->makeDirectory($folderPath);
        }

        if ($certificateFile = $request->file('certificate_file')) {
            if ($certificateFile->isValid()) {
                $filename                = 'certificate.' . $certificateFile->getClientOriginalExtension();
                $path                    = $storage->putFileAs($folderPath, $certificateFile, $filename);
                $store->certificate_file = $path;
            }
        }

        if ($governmentIdFile = $request->file('government_id_file')) {
            if ($governmentIdFile->isValid()) {
                $filename                  = 'government_id.' . $governmentIdFile->getClientOriginalExtension();
                $path                      = $storage->putFileAs($folderPath, $governmentIdFile, $filename);
                $store->government_id_file = $path;
            }
        }

        $store->save();
        if ($request->has('social_links')) {
            if ($socialLinks = $request->input('social_links', [])) {
                $socials     = array_keys(MarketplaceHelper::getAllowedSocialLinks());
                $socialLinks = collect($socialLinks)->only($socials)->filter();
                MetaBox::saveMetaBoxData($store, 'social_links', $socialLinks);
            }
        }

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('marketplace.store.index'))
            ->withUpdatedSuccessMessage();
    }

    public function updateTaxInformation(Store $store, TaxInformationSettingRequest $request)
    {
        $customer = $store->customer;

        if ($customer && $customer->id) {
            $customer->vendorInfo->update($request->validated());
        }

        event(new UpdatedContentEvent(STORE_MODULE_SCREEN_NAME, $request, $store));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('marketplace.store.index'))
            ->withUpdatedSuccessMessage();
    }

    public function updatePayoutInformation(Store $store, PayoutInformationSettingRequest $request)
    {
        $customer = $store->customer;

        if ($customer && $customer->id) {
            $vendorInfo                        = $customer->vendorInfo;
            $vendorInfo->payout_payment_method = $request->input('payout_payment_method');
            $vendorInfo->bank_info             = $request->input('bank_info', []);
            $vendorInfo->save();
        }

        event(new UpdatedContentEvent(STORE_MODULE_SCREEN_NAME, $request, $store));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('marketplace.store.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Store $store)
    {
        return DeleteResourceAction::make($store);
    }
}
