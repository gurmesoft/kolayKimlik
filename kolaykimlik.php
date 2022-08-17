<?php
/**
 * Plugin Name: kolaykimlik
 * Plugin URI: https://www.gurmewoo.com/
 * Description: T.C kimlik onayı ve Vergi Numarası onayı ile kullanıcı kayıtları, ödeme işlemleri ve çeşitli form eklentileri (Contact Form, NinjaForms, WPForms) kullanarak özelleştirilmiş formlar oluşturmaları için geliştirilmiştir.
 * Version: 1.4.1
 * Author: GurmeWoo.com
 * Author URI: https://www.gurmewoo.com
 * Plugin URI: https://gurmewoo.com/product/kolaykimlik-wordpress-woocommerce-kimlik-ve-vergi-numarasi-kontrol-eklentisi
 * Text Domain: kolay-kimlik
 * WC requires at least: 4.9
 * WC tested up to: 5.6
 * Requires PHP: 7.4
 */

require_once 'includes/class-kk-tcinputsettings.php';
require_once 'includes/class-kk-contactform-seven.php';
require_once 'includes/class-kk-ninjaform.php';
require_once 'includes/class-kk-woocheckout.php';
require __DIR__ . '/vendor/autoload.php';

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function kk_appsero_init_tracker_tc_kimlik_vergi_no_dogrulama_kolay_kimlik_dorulama() {
	if ( ! class_exists( 'Appsero\Client' ) ) {
		require_once __DIR__ . '/appsero/src/Client.php';
	}
	$client = new Appsero\Client( '5a5d672c-ffef-47fa-a920-eeb38b9627b2', 'T.C Kimlik & Vergi No Dogrulama & Kolay Kimlik Doğrulama', __FILE__ );
	// Active insights
	$client->insights()->init();

	// Active automatic updater
	$client->updater();
}

kk_appsero_init_tracker_tc_kimlik_vergi_no_dogrulama_kolay_kimlik_dorulama();


$settings_page = new KK_TcInputSettings();
$woo_checkout  = new KK_WooCheckOut();

if ( class_exists( 'WPCF7' ) ) {
	new KK_ContactForm_Seven();
}
