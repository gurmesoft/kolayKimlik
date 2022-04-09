<?php
require_once 'functions-kontrol.php';
class KK_NinjaForm {

	public function __construct() {
		add_filter( 'ninja_forms_submit_data', array( $this, 'bilgi_kontrol_ninja_form' ), 10, 2 );
	}

	public function bilgi_kontrol_ninja_form( $form_data ) {

		foreach ( $form_data['fields'] as $field ) {

			if ( 'tc-no' === $field['key'] ) {
				$tc = $field['value'];
				if ( ! kk_standart_sorgulama( $tc ) ) {

					$errors = array(
						'fields' => array(
							'tc-no' => __( 'Hatalı Tc No Formatı', 'kolay-kimlik' ),
						),
					);

					$response = array(
						'errors' => $errors,
					);

					echo wp_json_encode( $response );
				}
			}
		}

		return $form_data;
	}
}
