<?php
namespace Botble\Ecommerce\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\DatePickerField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Http\Requests\CustomerCreateRequest;
use Botble\Ecommerce\Models\Customer;

class CustomerForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addScriptsDirectly('vendor/core/plugins/ecommerce/js/address.js')
            ->addStylesDirectly('vendor/core/plugins/ecommerce/css/review.css');

        $this
            ->model(Customer::class)
            ->setValidatorClass(CustomerCreateRequest::class)
            ->template('plugins/ecommerce::customers.form')
            ->columns()
            ->add('name', TextField::class, NameFieldOption::make()->maxLength(120)->colspan(1))
            ->add('email', TextField::class, EmailFieldOption::make()->required()->colspan(1))
            ->add(
                'is_vendor',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/ecommerce::customer.is_vendor'))
                    ->attributes([
                        'data-bb-toggle' => 'collapse',
                        'data-bb-target' => '.vendor-fields',
                    ])
                    ->defaultValue(0)
                    ->colspan(2)
            )
            ->add(
                'phone',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/ecommerce::customer.phone'))
                    ->placeholder(trans('plugins/ecommerce::customer.phone_placeholder'))
                    ->maxLength(15)
                    ->colspan(1)
            )
            ->add(
                'dob',
                DatePickerField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/ecommerce::customer.dob'))
                    ->defaultValue(null)
                    ->colspan(1)
            )
            ->add(
                'is_change_password',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/ecommerce::customer.change_password'))
                    ->attributes([
                        'data-bb-toggle' => 'collapse',
                        'data-bb-target' => '#password-collapse',
                    ])
                    ->defaultValue(0)
                    ->colspan(2)
            )
            ->add(
                'password',
                'password',
                TextFieldOption::make()
                    ->label(trans('plugins/ecommerce::customer.password'))
                    ->required()
                    ->maxLength(60)
                    ->collapsible('is_change_password', 1, ! $this->getModel()->exists || $this->getModel()->is_change_password)
                    ->colspan(1)
            )
            ->add(
                'password_confirmation',
                'password',
                TextFieldOption::make()
                    ->label(trans('plugins/ecommerce::customer.password_confirmation'))
                    ->required()
                    ->maxLength(60)
                    ->collapsible('is_change_password', 1, ! $this->getModel()->exists || $this->getModel()->is_change_password)
                    ->colspan(1)
            )
            ->add(
                'private_notes',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/ecommerce::customer.private_notes'))
                    ->helperText(trans('plugins/ecommerce::customer.private_notes_helper'))
                    ->rows(2)
                    ->colspan(2)
            )
            // ->add(
            //     'shop_name',
            //     TextField::class,
            //     TextFieldOption::make()
            //         ->label(__('Shop Name'))
            //         ->placeholder(__('Store Name'))
            //         ->required()
            //         ->defaultValue(optional($this->getModel()->store)->name)

            //         ->collapsible('is_vendor', 1, ! $this->getModel()->exists || $this->getModel()->is_vendor),

            // )
            // ->add(
            //     'slug',
            //     HtmlField::class,
            //     HtmlFieldOption::make()
            //         ->content(view('plugins/marketplace::stores.partials.shop-url-field', ['store' => $this->getModel()])->render())
            //         ->colspan(3)
            //         ->defaultValue(optional($this->getModel()->store)->slug)

            //         ->collapsible('is_vendor', 1, ! $this->getModel()->exists || $this->getModel()->is_vendor),
            // )
            // // ->add(
            // //     'shop_url',
            // //     TextField::class,
            // //     TextFieldOption::make()
            // //         ->label(__('Shop URL'))
            // //         ->placeholder(__('Store URL'))
            // //         ->required()
            // //         ->defaultValue(optional($this->getModel()->store)->slug)

            // //         ->attributes([
            // //             'data-url' => route('public.ajax.check-store-url'),
            // //         ])
            // //         ->prepend(
            // //             sprintf(
            // //                 '<span class="position-absolute top-0 end-0 shop-url-status"></span><div class="input-group"><span class="input-group-text">%s</span>',
            // //                 route('public.store', ['slug' => '/'])
            // //             )
            // //         )
            // //         ->append('</div>')
            // //         ->collapsible('is_vendor', 1, ! $this->getModel()->exists || $this->getModel()->is_vendor),
            // // )
            // ->add(
            //     'shop_number',
            //     TextField::class,
            //     TextFieldOption::make()
            //         ->label(__('Shop Number'))
            //         ->placeholder(__('Store Number'))
            //         ->required()
            //         ->defaultValue(optional($this->getModel()->store)->phone)
            //         ->collapsible('is_vendor', 1, ! $this->getModel()->exists || $this->getModel()->is_vendor)
            // )

            ->add('status', SelectField::class, StatusFieldOption::make()->choices(CustomerStatusEnum::labels()))
            ->add(
                'avatar',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(trans('plugins/ecommerce::customer.avatar'))
            )
            ->setBreakFieldPoint('status')
            ->when($this->getModel()->getKey(), function (): void {
                /**
             * @var Customer $model
             */
                $model = $this->getModel();

                $wishlist = $model->wishlist->loadMissing('product');

                $metaBoxes = [
                    'addresses' => [
                        'title'          => trans('plugins/ecommerce::addresses.addresses'),
                        'content'        => view('plugins/ecommerce::customers.addresses.addresses', [
                            'addresses' => $model->addresses()->get(),
                        ])->render(),
                        'header_actions' => view('plugins/ecommerce::customers.addresses.address-actions')->render(),
                        'wrap'           => true,
                        'has_table'      => true,
                    ],
                    'wishlist'  => [
                        'title'     => trans('plugins/ecommerce::ecommerce.wishlist'),
                        'content'   => view('plugins/ecommerce::customers.wishlist', compact('wishlist'))->render(),
                        'wrap'      => true,
                        'has_table' => true,
                    ],
                ];

                if (is_plugin_active('payment')) {
                    $metaBoxes['payments'] = [
                        'title'     => trans('plugins/ecommerce::payment.name'),
                        'content'   => view('plugins/ecommerce::customers.payments.payments', [
                            'payments' => $model->payments()->get(),
                        ])->render(),
                        'wrap'      => true,
                        'has_table' => true,
                    ];
                }

                $this->addMetaBoxes($metaBoxes);
            });
    }
}
