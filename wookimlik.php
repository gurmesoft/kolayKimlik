<?php
/*
 * Plugin Name: wooKimlik
 * Plugin URI: https://www.gurmewoo.com/
 * Description:
 * Version: 1.2.0
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


register_activation_hook(__FILE__, 'tcSettingsActiveHook');
register_deactivation_hook( __FILE__, 'tcSettingsDeactiveHook' );


$SettingsPage = new tcinputSettings();
$wooCheckOut = new wooCheckOut();

if(class_exists("WPCF7")){
    $CTF7 = new contactFormSeven();  
}
// if(class_exists("WooCommerce")){
//         $wooCheckOut = new wooCheckOut();  
// }
// if(class_exists("Ninja_Forms")){
//     $NinjaForm = new ninjaForm();  
// }



require_once 'includes/wookimlik-otomatik-guncelleme.php';
require_once 'includes/wookimlik-lisans.php';


