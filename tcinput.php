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


register_activation_hook(__FILE__, 'tcSettingsActiveHook');
register_deactivation_hook( __FILE__, 'tcSettingsDeactiveHook' );

function tcSettingsActiveHook()
{
    $tcSettings['contactForm7'] = 'off';
    $tcSettings['ninjaForm'] = 'off';
    $tcSettings['wpForms']='off';

    add_option('tcSettings', $tcSettings);
}
function tcSettingsDeactiveHook() {

    unregister_post_type( 'tcSettings' );
    delete_option('tcSettings');
    flush_rewrite_rules();
}



$SettingsPage = new tcinputSettings();
$tcSettings = get_option('tcSettings');

if($tcSettings['contactForm7'] == 'on'){
    $CTF7 = new contactFormSeven(); 
}
if($tcSettings['ninjaForm'] == 'on'){
    $NinjaForm = new ninjaForm(); 
}
?>