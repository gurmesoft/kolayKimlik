<?php
/**
 * Plugin Name: Tc Input
 * Plugin URI: https://www.gurmewoo.com/
 * Description:
 * Version: 1.0.0
 * Author: GurmeWoo.com
 * Author URI: https://www.gurmewoo.com
 */
require_once 'includes/settings-tcinput.php';
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






