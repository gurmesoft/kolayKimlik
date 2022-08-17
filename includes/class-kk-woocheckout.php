<?php
require_once 'functions-kontrol.php';

class KK_WooCheckOut {
	protected $tc_settigns;

	public function __construct() {
		$this->tc_settings = get_option( 'tc_settings' );
		if ( isset( $this->tc_settings['enabled'] ) ) {
			add_filter( 'woocommerce_checkout_fields', array( $this, 'yeni_alan_ekle' ) );
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'hata_ekle' ), 10, 2 );
			add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'siparis_fatura_bilgileri' ) );
		}
	}

	public function siparis_fatura_bilgileri( $siparis ) {
		$musteri = $siparis->get_customer_id();
		echo '<div class="address"><p><strong>TC Kimlik No:</strong> ' . esc_html( get_user_meta( $musteri, 'billing_tc_kimlik', true ) ) . '</p>';
		echo '<p><strong>Vergi Dairesi:</strong> ' . esc_html( get_user_meta( $musteri, 'billing_vergiDairesi', true ) ) . '</p>';
		echo '<p><strong>Vergi No:</strong> ' . esc_html( get_user_meta( $musteri, 'billing_vergiNo', true ) ) . '</p></div>';
	}
	public function yeni_alan_ekle( $alanlar ) {
		if ( 'none' !== $this->tc_settings['woocommerce'] ) {
			$alanlar['billing']['billing_tc_kimlik'] = array(
				'type'        => 'text',
				'required'    => ( 'on' === $this->tc_settings['woocommerceRequired'] ? true : false ),
				'class'       => ( 'standart' === $this->tc_settings['woocommerce'] ? array( 'my-field-class form-row-wide' ) : array( 'my-field-class form-row-first' ) ),
				'label'       => __( 'TC Kimlik No', 'kolay-kimlik' ),
				'placeholder' => '',
				'priority'    => 21,
			);
			if ( 'nvi' === $this->tc_settings['woocommerce'] ) {
				$alanlar['billing']['billing_dogumYili'] = array(
					'type'        => 'text',
					'required'    => ( 'on' === $this->tc_settings['woocommerceRequired'] ? true : false ),
					'maxlength'   => 4,
					'class'       => array( 'my-field-class form-row-last' ),
					'label'       => __( 'Doğum Yılı', 'kolay-kimlik' ),
					'placeholder' => '',
					'priority'    => 22,
				);
			}
		}

		if ( 'on' === @$this->tc_settings['woocommerceVergi'] ) {
			$alanlar['billing']['billing_vergiDairesi'] = array(
				'type'        => 'text',
				'required'    => ( 'on' === $this->tc_settings['woocommerceRequiredVergi'] ? true : false ),
				'class'       => array( 'my-field-class form-row-first' ),
				'label'       => __( 'Vergi Dairesi', 'kolay-kimlik' ),
				'placeholder' => '',
				'priority'    => 23,
			);

			$alanlar['billing']['billing_vergiNo'] = array(
				'type'        => 'text',
				'required'    => ( 'on' === $this->tc_settings['woocommerceRequiredVergi'] ? true : false ),
				'class'       => array( 'my-field-class form-row-last' ),
				'label'       => __( 'Vergi No', 'kolay-kimlik' ),
				'placeholder' => '',
				'priority'    => 24,
			);
		}
		return $alanlar;
	}
	public function hata_ekle($fields, $errors ) {

		if ( 'standart' === $this->tc_settings['woocommerce'] && 'on' === $this->tc_settings['woocommerceRequired'] ) {
			$data = array(
				'tcno' => sanitize_text_field( $_POST['billing_tc_kimlik'] ),
			);
			if ( empty( $data['tcno'] ) ) {
				$custom_error = __( '<strong>Fatura TC Kimlik Numarasi</strong> gerekli bir alandır.', 'kolay-kimlik' );
				$errors->add( 'validation', $custom_error );
			}
			if ( ! is_numeric( $data['tcno'] ) ) {
				$custom_error = __( '<strong>Fatura TC Kimlik Numarasi</strong> sadece rakam içerebilir.', 'kolay-kimlik' );
				$errors->add( 'validation', $custom_error );
			}
			if ( ! kk_standart_sorgulama( $data['tcno'] ) && ! empty( $data['tcno'] ) ) {
				$custom_error = __( '<strong>Fatura TC Kimlik Numarasi</strong> uyumsuz formattadir.', 'kolay-kimlik' );
				$errors->add( 'validation', $custom_error );
			}
		} elseif ( 'nvi' === $this->tc_settings['woocommerce'] && 'on' === $this->tc_settings['woocommerceRequired'] ) {
			$data = array(
				'tcno'      => sanitize_text_field( $_POST['billing_tc_kimlik'] ),
				'isim'      => sanitize_text_field( $_POST['billing_first_name'] ),
				'soyisim'   => sanitize_text_field( $_POST['billing_last_name'] ),
				'dogumyili' => sanitize_text_field( $_POST['billing_dogumYili'] ),
			);
			if ( empty( $data['tcno'] ) ) {
				$custom_error = __( '<strong>Fatura TC Kimlik Numarasi</strong> gerekli bir alandır.', 'kolay-kimlik' );
				$errors->add( 'validation', $custom_error );
			}
			if ( empty( $data['dogumyili'] ) ) {
				$custom_error = __( '<strong>Fatura Fatura Doğum Yılı</strong> gerekli bir alandır.', 'kolay-kimlik' );
				$errors->add( 'validation', $custom_error );
			}
			if ( ! is_numeric( $data['tcno'] ) || ! is_numeric( $data['dogumyili'] ) ) {
				$custom_error = __( '<strong>Fatura  TC Kimlik Numarasi ve Doğum Yılı</strong> sadece rakam içerebilir.', 'kolay-kimlik' );
				$errors->add( 'validation', $custom_error );
			}
			if ( kk_nvi_sorgulama( $data ) === false ) {
				$custom_error = __( '<strong>Fatura Kimlik Bilgileri Uyumsuz! </strong>', 'kolay-kimlik' );
				$errors->add( 'validation', $custom_error );
			}
		}

		if ( 'on' === $this->tc_settings['woocommerceVergi'] && 'on' === $this->tc_settings['woocommerceRequiredVergi'] ) {
			$data = array(
				'vergidairesi' => sanitize_text_field( $_POST['billing_vergiDairesi'] ),
				'vergino'      => sanitize_text_field( $_POST['billing_vergiNo'] ),
			);

			if ( empty( $data['vergidairesi'] ) ) {
				$hata_mesaji = __( '<strong>Fatura Vergi Dairesi</strong> gerekli bir alandır.', 'kolay-kimlik' );
				$errors->add( 'validation', $hata_mesaji );
			}
			if ( empty( $data['vergino'] ) ) {
				$hata_mesaji = __( '<strong>Fatura Vergi No</strong> gerekli bir alandır.', 'kolay-kimlik' );
				$errors->add( 'validation', $hata_mesaji );
			}
			if ( ! is_numeric( $data['vergino'] ) ) {
				$hata_mesaji = __( '<strong>Fatura Vergi No</strong> sadece rakam içermelidir.', 'kolay-kimlik' );
				$errors->add( 'validation', $hata_mesaji );
			}
			if ( strlen( $data['vergino'] ) !== 10 ) {
				$hata_mesaji = __( '<strong>Fatura Vergi No</strong> 10 haneli olmalıdır.', 'kolay-kimlik' );
				$errors->add( 'validation', $hata_mesaji );
			}
			if ( ! kk_vergi_kontrol( $data['vergino'] ) ) {
				$custom_error = __( '<strong>Vergi Numarası</strong> geçersizdir.', 'kolay-kimlik' );
				$errors->add( 'validation', $custom_error );
			}
		}
	}
}
