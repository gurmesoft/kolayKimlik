<?php
/*
 * Plugin Name: wooKimlik
 * Plugin URI: https://www.gurmewoo.com/
 * Description:
 * Version: 1.2.3
 * Author: GurmeWoo.com
 * Author URI: https://www.gurmewoo.com
 * Plugin URI: https://gurmewoo.com/product/wookimlik-wordpress-woocommerce-kimlik-ve-vergi-numarasi-kontrol-eklentisi
 * Text Domain: WOOKIMLIK
 * WC requires at least: 4.9
 * WC tested up to: 5.6
 * JIRAPROJECT: TI
 * JIRABOARDID: 14
 * ID: WOOKIMLIK
*/

require_once 'includes/settings-wookimlik.php';
require_once 'includes/class-contactform7.php';
require_once 'includes/class-ninjaform.php';
require_once 'includes/class-woocommerce-checkout.php';
require __DIR__ . '/vendor/autoload.php';

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_tc_kimlik_vergi_no_dogrulama_wookimlik()
{

    if (!class_exists('Appsero\Client')) {
        require_once __DIR__ . '/appsero/src/Client.php';
    }

    $client = new Appsero\Client('1a6f9ca6-536a-450b-877a-f0adadb5d297', 'T.C Kimlik & Vergi No Dogrulama - wooKimlik', __FILE__);

    // Active insights
    $client->insights()->init();

    // Active automatic updater
    $client->updater();
}

appsero_init_tracker_tc_kimlik_vergi_no_dogrulama_wookimlik();

register_activation_hook(__FILE__, 'tcSettingsActiveHook');
register_deactivation_hook(__FILE__, 'tcSettingsDeactiveHook');


$SettingsPage = new tcinputSettings();
$wooCheckOut = new wooCheckOut();

if (class_exists("WPCF7")) {
    $CTF7 = new contactFormSeven();
}
// if(class_exists("WooCommerce")){
//         $wooCheckOut = new wooCheckOut();  
// }
// if(class_exists("Ninja_Forms")){
//     $NinjaForm = new ninjaForm();  
// }