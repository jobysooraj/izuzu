<?php
namespace Botble\Marketplace\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Forms\Concerns\HasLocationFields;
use Botble\Ecommerce\Models\Customer;
use Botble\Marketplace\Facades\MarketplaceHelper;
use Botble\Marketplace\Forms\Concerns\HasSubmitButton;
use Botble\Marketplace\Http\Requests\StoreRequest;
use Botble\Marketplace\Models\Store;

class StoreForm extends FormAbstract
{
    use HasLocationFields;
    use HasSubmitButton;

    public function setup(): void
    {
        Assets::addScriptsDirectly('vendor/core/plugins/marketplace/js/store.js');
        Assets::addScriptsDirectly('https://maps.googleapis.com/maps/api/js?key=' . env('GOOGLE_MAPS_API_KEY') . '&libraries=places');

        Assets::addStylesDirectly('vendor/core/core/base/libraries/dropzone/dropzone.css');
        Assets::addScriptsDirectly('vendor/core/core/base/libraries/dropzone/dropzone.js');
        Assets::addScriptsDirectly('vendor/core/plugins/marketplace/js/test.js');
        Assets::addStylesDirectly('themes/farmart/css/custom.css');

        $this
            ->model(Store::class)
            ->setValidatorClass(StoreRequest::class)
            ->columns(6)
            ->formClass('become-vendor-form')
            ->template('core/base::forms.form-no-wrap')
            ->hasFiles()
            ->add('name', TextField::class, NameFieldOption::make()->required()->colspan(6))
            ->add(
                'slug',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('plugins/marketplace::stores.partials.shop-url-field', ['store' => $this->getModel()])->render())
                    ->colspan(3)
            )
            ->add('email', EmailField::class, EmailFieldOption::make()->required()->colspan(3))
            ->add('phone', TextField::class, [
                'label'    => trans('plugins/marketplace::store.forms.phone'),
                'required' => true,
                'attr'     => [
                    'placeholder'  => trans('plugins/marketplace::store.forms.phone_placeholder'),
                    'data-counter' => 15,
                ],
                'colspan'  => 6,
            ])
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->colspan(6))
            ->add('content', EditorField::class, ContentFieldOption::make()->colspan(6))
            ->addLocationFields()
            ->add(
                'company',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/marketplace::store.forms.company'))
                    ->placeholder(trans('plugins/marketplace::store.forms.company_placeholder'))
                    ->maxLength(255)
                    ->colspan(3)
            )
            ->add(
                'tax_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/marketplace::store.forms.tax_id'))
                    ->colspan(3)
                    ->maxLength(255)
            )
            ->add(
                'certificate_of_incorporation',
                'html',
                HtmlFieldOption::make()
                    ->label(__('Certificate of Incorporation'))
                    ->required()
                    ->colspan(6) // Add this
                    ->wrapperAttributes(['class' => 'mb-3 position-relative', 'data-field-name' => 'certificate_file'])
                    ->content('<div id="certificate-dropzone" class="dropzone" data-placeholder="' . __('Drop Certificate of Incorporation here or click to upload') . '"></div>'),
            )
            ->add(
                'government_id',
                'html',
                HtmlFieldOption::make()
                    ->label(__('Government ID'))
                    ->required()
                    ->colspan(6) // Add this
                    ->wrapperAttributes(['class' => 'mb-3 position-relative', 'data-field-name' => 'government_id_file'])
                    ->attributes(['data-placeholder' => ''])
                    ->content('<div id="government-id-dropzone" class="dropzone" data-placeholder="' . __('Drop Government ID here or click to upload') . '"></div>'),
            )
            ->add(
                'logo',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Logo'))
                    ->colspan(3)
            )
            ->add(
                'logo_square',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Square Logo'))
                    ->helperText(__('This logo will be used in some special cases. Such as checkout page.'))
                    ->colspan(3)
            )
            ->add(
                'cover_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Cover Image'))
                    ->colspan(3)
            )
            ->add('lat', TextField::class, [
                'label'      => trans('Latitude'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder' => trans('Latitude'),
                ],
                'colspan'    => 3, // Half of the row
            ])
            ->add('lng', TextField::class, [
                'label'      => trans('Longitude'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder' => trans('Longitude'),
                ],
                'colspan'    => 3, // Half of the row
            ])
            ->add('map_picker', HtmlField::class, [
                'label'   => __('Pick Store Location'),
                'colspan' => 6,
                'html'    => '<div id="store-map" style="width: 100%; height: 400px;"></div>',
            ])

            ->add('status', SelectField::class, [
                'label'      => trans('core/base::tables.status'),
                'required'   => true,
                'choices'    => BaseStatusEnum::labels(),
                'help_block' => [
                    TextField::class => trans('plugins/marketplace::marketplace.helpers.store_status', [
                        'customer' => CustomerStatusEnum::LOCKED()->label(),
                        'status'   => BaseStatusEnum::PUBLISHED()->label(),
                    ]),
                ],
                'colspan'    => 3, // changed from 4 to 3
            ])

            ->add('customer_id', SelectField::class, [
                'label'    => trans('plugins/marketplace::store.forms.store_owner'),
                'required' => true,
                'choices'  => [0 => trans('plugins/marketplace::store.forms.select_store_owner')] + Customer::query()
                    ->where('is_vendor', true)
                    ->pluck('name', 'id')
                    ->all(),
                'colspan'  => 3, // remains 3
            ])
            ->when(! MarketplaceHelper::hideStoreSocialLinks(), function (): void {
                $this
                    ->add('extended_info_content', HtmlField::class, [
                        'html' => view('plugins/marketplace::partials.extra-content', ['model' => $this->getModel()]),
                    ]);
            });
    }
}
