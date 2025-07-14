<?php
namespace Botble\Eqhit\Providers;

use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\PanelSections\SettingCommonPanelSection;

class EqhitServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->setNamespace('plugins/eqhit');
    }

    public function boot(): void
    {
        $this
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->publishAssets();

        PanelSectionManager::group('settings')->beforeRendering(function () {
            PanelSectionManager::registerItem(
                SettingCommonPanelSection::class,
                fn() => PanelSectionItem::make('eqhit')
                    ->setTitle('Eqhit API Settings')
                    ->withIcon('ti ti-api')
                    ->withDescription('Manage Eqhit API Configuration.')
                    ->withPriority(100)
                    ->withRoute('settings.eqhit')
            );
        });
    }
}
