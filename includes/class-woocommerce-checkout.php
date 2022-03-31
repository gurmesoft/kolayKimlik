<?php
require_once( 'functions-kontrol.php' );
class WkWooCheckOut {
	protected $tc_settigns;

	public function __construct() {
		$this->tc_settings = get_option( 'tcSettings' );
		if ( isset( $this->tcSettings['enabled'] ) ) {
			add_filter( 'woocommerce_checkout_fields', array( $this, 'yeniAlanlarEkle' ) );
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'hataEkle' ), 10, 2 );
			add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'siparisFaturaBilgileri' ) );
		}
	}

	public function siparisFaturaBilgileri( $siparis ) {
		$musteri = $siparis->get_customer_id();
		echo '<div class="address"><p><strong>TC Kimlik No:</strong> ' . esc_html( get_user_meta( $musteri, 'billing_tckimlik', true ) ) . '</p>';
		echo '<p><strong>Vergi Dairesi:</strong> ' . esc_html( get_user_meta( $musteri, 'billing_vergiDairesi', true ) ) . '</p>';
		echo '<p><strong>Vergi No:</strong> ' . esc_html( get_user_meta( $musteri, 'billing_vergiNo', true ) ) . '</p></div>';
	}
	public function yeniAlanlarEkle( $alanlar ) {
		if ( $this->tcSettings['woocommerce'] != 'none' ) {
			$alanlar['billing']['billing_tckimlik'] = array(
				'type' => 'text',
				'required' => ( $this->tcSettings['woocommerceRequired'] == 'on' ? true : false ),
				'class' => ( $this->tcSettings['woocommerce'] == 'standart' ? array( 'my-field-class form-row-wide' ) : array( 'my-field-class form-row-first' ) ),
				'label' => __( 'TC Kimlik No', 'kolay-kimlik' ),
				'placeholder' => __( '' ),
				'priority' => 21,
			);
			if ( $this->tcSettings['woocommerce'] == 'nvi' ) {
				$alanlar['billing']['billing_dogumYili'] = array(
					'type' => 'text',
					'maxlength' => 4,
					'required' => ( $this->tcSettings['woocommerceRequired'] == 'on' ? true : false ),
					'class' => array( 'my-field-class form-row-last' ),
					'label' => __( 'Doğum Yılı', 'kolay-kimlik' ),
					'placeholder' => '',
					'priority' => 22,
				);
			}
		}

		if ( $this->tcSettings['woocommerceVergi'] == 'on' ) {
			$alanlar['billing']['billing_vergiDairesi'] = array(
				'type' => 'text',
				'required' => ( $this->tcSettings['woocommerceRequiredVergi'] == 'on' ? true : false ),
				'class' => array( 'my-field-class form-row-first' ),
				'label' => __( 'Vergi Dairesi', 'kolay-kimlik' ),
				'placeholder' => '',
				'priority' => 23,
			);
			$alanlar['billing']['billing_vergiNo'] = array(
				'type' => 'text',
				'required' => ( $this->tcSettings['woocommerceRequiredVergi'] == 'on' ? true : false ),
				'class' => array( 'my-field-class form-row-last' ),
				'label' => __( 'Vergi No', 'kolay-kimlik' ),
				'placeholder' => '',
				'priority' => 24,
			);
		}
		return $alanlar;
	}
	public function hataEkle( $errors ) {
		if ( ! wp_verify_nonce( $nonce, 'my-nonce' ) ) {
			die( 'Security check' );
		}
		if ( $this->tcSettings['woocommerce'] == 'standart' && $this->tcSettings['woocommerceRequired'] == 'on' ) {
			$data = array(
				'tcno' => sanitize_text_field( $_POST['billing_tckimlik'] ),
			);

			if ( empty( $data['tcno'] ) ) {
				$customError = __( '<strong>Fatura TC Kimlik Numarasi</strong> gerekli bir alandır.', 'kolay-kimlik' );
				$errors->add( 'validation', $customError );
			}
			if ( ! is_numeric( $data['tcno'] ) ) {
				$customError = __( '<strong>Fatura TC Kimlik Numarasi</strong> sadece rakam içerebilir.', 'kolay-kimlik' );
				$errors->add( 'validation', $customError );
			}
			if ( ! standartSorgulama( $data['tcno'] ) && ! empty( $data['tcno'] ) ) {
				$customError = __( '<strong>Fatura TC Kimlik Numarasi</strong> uyumsuz formattadir.', 'kolay-kimlik' );
				$errors->add( 'validation', $customError );
			}
		} elseif ( $this->tcSettings['woocommerce'] == 'nvi' && $this->tcSettings['woocommerceRequired'] == 'on' ) {
			$data = array(
				'tcno' => sanitize_text_field( $_POST['billing_tckimlik'] ),
				'isim' => sanitize_text_field( $_POST['billing_first_name'] ),
				'soyisim' => sanitize_text_field( $_POST['billing_last_name'] ),
				'dogumyili' => sanitize_text_field( $_POST['billing_dogumYili'] ),
			);
			if ( empty( $data['tcno'] ) ) {
				$customError = __( '<strong>Fatura TC Kimlik Numarasi</strong> gerekli bir alandır.', 'kolay-kimlik' );
				$errors->add( 'validation', $customError );
			}
			if ( empty( $data['dogumyili'] ) ) {
				$customError = __( '<strong>Fatura Fatura Doğum Yılı</strong> gerekli bir alandır.', 'kolay-kimlik' );
				$errors->add( 'validation', $customError );
			}
			if ( ! is_numeric( $data['tcno'] ) || ! is_numeric( $data['dogumyili'] ) ) {
				$customError = __( '<strong>Fatura  TC Kimlik Numarasi ve Doğum Yılı</strong> sadece rakam içerebilir.', 'kolay-kimlik' );
				$errors->add( 'validation', $customError );
			}
			if ( nviSorgulama( $data ) == 'false' ) {
				$customError = __( '<strong>Fatura Kimlik Bilgileri Uyumsuz!</strong>', 'kolay-kimlik' );
				$errors->add( 'validation', $customError );
			}
		} elseif ( $this->tcSettings['woocommerce'] == 'standart' && $this->tcSettings['woocommerceRequired'] == null ) {
			//Otomatik doldurma secenegi eklenebilir
		}
		if ( $this->tcSettings['woocommerceVergi'] == 'on' && $this->tcSettings['woocommerceRequiredVergi'] == 'on' ) {
			$data = array(
				'vergidairesi' => sanitize_text_field( $_POST['billing_vergiDairesi'] ),
				'vergino' => sanitize_text_field( $_POST['billing_vergiNo'] ),
			);

			if ( empty( $data['vergidairesi'] ) ) {
				$hataMesaji = __( '<strong>Fatura Vergi Dairesi</strong> gerekli bir alandır.', 'kolay-kimlik' );
				$errors->add( 'validation', $hataMesaji );
			}
			if ( empty( $data['vergino'] ) ) {
				$hataMesaji = __( '<strong>Fatura Vergi No</strong> gerekli bir alandır.', 'kolay-kimlik' );
				$errors->add( 'validation', $hataMesaji );
			}
			if ( ! is_numeric( $data['vergino'] ) ) {
				$hataMesaji = __( '<strong>Fatura Vergi No</strong> sadece rakam içermelidir.', 'kolay-kimlik' );
				$errors->add( 'validation', $hataMesaji );
			}
			if ( strlen( $data['vergino'] ) != 10 ) {
				$hataMesaji = __( '<strong>Fatura Vergi No</strong> 10 haneli olmalıdır.', 'kolay-kimlik' );
				$errors->add( 'validation', $hataMesaji );
			}
			if ( vergiKontrol( $data['vergino'] ) ) {
				$customError = __( '<strong>Vergi Numarası</strong> geçersizdir.', 'kolay-kimlik' );
				$errors->add( 'validation', $customError );
			}
		}
	}
}
