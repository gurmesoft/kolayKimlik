<?php
/**
 * Plugin Name: kolaykimlik
 * Plugin URI: https://www.gurmewoo.com/
 * Description: T.C kimlik onayı ve Vergi Numarası onayı ile kullanıcı kayıtları, ödeme işlemleri ve çeşitli form eklentileri (Contact Form, NinjaForms, WPForms) kullanarak özelleştirilmiş formlar oluşturmaları için geliştirilmiştir.
 * Version: 1.3.1
 * Author: GurmeWoo.com
 * Author URI: https://www.gurmewoo.com
 * Plugin URI: https://gurmewoo.com/product/kolaykimlik-wordpress-woocommerce-kimlik-ve-vergi-numarasi-kontrol-eklentisi
 * Text Domain: kolay-kimlik
 * WC requires at least: 4.9
 * WC tested up to: 5.6
 */

require_once 'includes/class-wk-tcinputsettings.php';
require_once 'includes/class-kk-contactform-seven.php';
require_once 'includes/class-wkninjaform.php';
require_once 'includes/class-wkwoocheckout.php';
require __DIR__ . '/vendor/autoload.php';

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_tc_kimlik_vergi_no_dogrulama_kolay_kimlik_dorulama() {
	if ( ! class_exists( 'Appsero\Client' ) ) {
		require_once __DIR__ . '/appsero/src/Client.php';
	}

	$client = new Appsero\Client( '413cb71d-9871-47e4-9c85-b1ca59681eb9', 'T.C Kimlik & Vergi No Dogrulama - Kolay Kimlik Doğrulama', __FILE__ );

	// Active insights
	$client->insights()->init();

	// Active automatic updater
	$client->updater();
}

appsero_init_tracker_tc_kimlik_vergi_no_dogrulama_kolay_kimlik_dorulama();


$settings_page = new Wk_TcInputSettings();
$woo_checkout  = new WkWooCheckOut();

if ( class_exists( 'WPCF7' ) ) {
	new KK_ContactForm_Seven();
}
