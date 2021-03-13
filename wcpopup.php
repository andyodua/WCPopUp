<?php

/**
 * @link              WCPopUp
 * @since             2.0.5
 * @package           Wcpopup
 *
 * @wordpress-plugin
 * Plugin Name:       WCPopUp
 * Plugin URI:        https://andy.od.ua
 * Description:       Woocommerce popup country and progressbar .
 * Version:           2.0.5
 * Author:            Andy.od.ua
 * Author URI:        https://andy.od.ua
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wcpopup
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'WCPOPUP_PLUGIN_FILE' ) ) {
	define( 'WCPOPUP_PLUGIN_FILE', __FILE__ );
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WCPOPUP_VERSION', '2.0.5' );
define( 'WCPOPUP_DB_VERSION', '1.1' );
define( 'WCPOPUP_CHK_NAME', '1' );
define( 'WCPOPUP_CHK_PHONE', '1' );
define( 'WCPOPUP_CHK_EMAIL', '1' );
define( 'WCPOPUP_CHK_COUNTRY', '1' );
define( 'WCPOPUP_POPUP_ENABLE', '1' );
define( 'WCPOPUP_POPUP_COUNTER', '1' );
define( 'WCPOPUP_POPUP_TIMER', '5' );
define( 'WCPOPUP_PROGRESSBAR_ENABLE_WC', '1' );
define( 'WCPOPUP_PROGRESSBAR_ENABLE_XOO', '1' );
define( 'WCPOPUP_SAVETODB_ENABLE', '1' );
define( 'WCPOPUP_COUNTRY_POSHLINA', 'UA:100;RU:200;KZ:1000' );
define( 'WCPOPUP_COUNTRY_WEIGHT', 'UA:10;RU:31;KZ:31' );
define( 'WCPOPUP_COUNTRY_POSHLINA_DEFAULT', '200' );
define( 'WCPOPUP_COUNTRY_WEIGHT_DEFAULT', '10' );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wcpopup-activator.php
 */
function activate_wcpopup() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wcpopup-activator.php';
	Wcpopup_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wcpopup-deactivator.php
 */
function deactivate_wcpopup() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wcpopup-deactivator.php';
	Wcpopup_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wcpopup' );
register_deactivation_hook( __FILE__, 'deactivate_wcpopup' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wcpopup.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
 
function run_wcpopup() {
	if( !class_exists( 'woocommerce' ) ) return;
	$plugin = new Wcpopup();
	$plugin->run();
}
add_action('plugins_loaded', 'run_wcpopup');


