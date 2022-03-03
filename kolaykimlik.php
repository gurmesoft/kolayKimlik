<?php
/*
 * Plugin Name: kolayKimlik
 * Plugin URI: https://www.gurmewoo.com/
 * Description:
 * Version: 1.2.5
 * Author: GurmeWoo.com
 * Author URI: https://www.gurmewoo.com
 * Plugin URI: https://gurmewoo.com/product/kolaykimlik-wordpress-woocommerce-kimlik-ve-vergi-numarasi-kontrol-eklentisi
 * Text Domain: KOLAYKIMLIK
 * WC requires at least: 4.9
 * WC tested up to: 5.6
 * JIRAPROJECT: TI
 * JIRABOARDID: 14
 * ID: KOLAYKIMLIK
*/

require_once 'includes/settings-kolaykimlik.php';
require_once 'includes/class-contactform7.php';
require_once 'includes/class-ninjaform.php';
require_once 'includes/class-woocommerce-checkout.php';
require __DIR__ . '/vendor/autoload.php';

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_tc_kimlik_vergi_no_dogrulama_kolay_kimlik_dorulama()
{

    if (!class_exists('Appsero\Client')) {
        require_once __DIR__ . '/appsero/src/Client.php';
    }

    $client = new Appsero\Client('413cb71d-9871-47e4-9c85-b1ca59681eb9', 'T.C Kimlik & Vergi No Dogrulama - Kolay Kimlik Doğrulama', __FILE__);

    // Active insights
    $client->insights()->init();

    // Active automatic updater
    $client->updater();
}

appsero_init_tracker_tc_kimlik_vergi_no_dogrulama_kolay_kimlik_dorulama();


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