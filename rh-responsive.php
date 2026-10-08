<?php

/**
 * Plugin Name:       RH Responsive
 * Plugin URI:        https://github.com/herbeckrobin/rh-responsive
 * Update URI:        https://github.com/herbeckrobin/rh-responsive
 * Description:       Schließt WP-Responsive-Lücken: konfigurierbarer Navigations-Breakpoint und per-Block-Sichtbarkeit (Mobile/Tablet/Desktop ausblenden). Teil der rh-blueprint Kollektion.
 * Version:           0.4.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Robin Herbeck
 * Author URI:        https://robinherbeck.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rh-responsive
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('RHRESP_VERSION', '0.4.0');
define('RHRESP_PLUGIN_FILE', __FILE__);
define('RHRESP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RHRESP_PLUGIN_URL', plugin_dir_url(__FILE__));

$rhresp_autoload = RHRESP_PLUGIN_DIR . 'vendor/autoload.php';

if (! is_readable($rhresp_autoload)) {
    add_action('admin_notices', static function (): void {
        // Ohne eigenes vendor/ läuft der Core nur, wenn ein anderes Modul ihn mitbringt.
        // Dann gilt dessen Branding, sonst bleibt es beim Standardnamen.
        $name = (function_exists('rhbp_brand') ? rhbp_brand('name', 'RH') : 'RH') . ' Responsive';
        echo '<div class="notice notice-error"><p><strong>' . esc_html($name) . ':</strong> Composer-Dependencies fehlen. Bitte <code>composer install</code> im Plugin-Verzeichnis ausführen.</p></div>';
    });
    return;
}

require_once $rhresp_autoload;

RhResponsive\Plugin::boot();
