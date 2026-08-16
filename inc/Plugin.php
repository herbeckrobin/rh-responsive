<?php

declare(strict_types=1);

namespace RhResponsive;

use RhBlueprint\Core\Core;
use RhBlueprint\Core\UpdateChecker;
use RhBlueprint\Core\Settings\SettingsPage;
use RhResponsive\Admin\ResponsiveGroup;

/**
 * Bootstrap von rh-responsive.
 *
 * Hängt am Core-Hook `rh-blueprint/core/booted` (init). Registriert die Settings
 * im Tab "Responsive" und bootet die Logik. Braucht nur den Core.
 */
final class Plugin
{
    public static function boot(): void
    {
        add_action('plugins_loaded', static function (): void {
            (new UpdateChecker('rh-responsive', RHRESP_PLUGIN_FILE))->boot();
        }, 0);

        add_action('rh-blueprint/core/booted', [self::class, 'onCoreBooted']);
    }

    public static function onCoreBooted(Core $core): void
    {
        $core->settings()->registerTab('responsive', __('Responsive', 'rh-responsive'), 50);
        $core->settings()->registerGroup(new ResponsiveGroup());

        (new Responsive())->boot();

        add_filter('rh-blueprint/dashboard/quick_links', static function (array $links): array {
            $links[] = [
                'label' => __('Responsive', 'rh-responsive'),
                'url' => admin_url('admin.php?page=' . SettingsPage::MENU_SLUG . '&tab=responsive'),
                'icon' => 'smartphone',
            ];
            return $links;
        });
    }
}
