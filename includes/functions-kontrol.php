<?php

function nvi_sorgulama( $data ) {
	$xml      = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soap:Body>
			<tc_kimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
			<tc_kimlikNo>' . $data['tcno'] . '</tc_ükimlikNo>
			<Ad>' . $data['isim'] . '</Ad>
			<Soyad>' . $data['soyisim'] . '</Soyad>
			<DogumYili>' . $data['dogumyili'] . '</DogumYili>
			</tc_kimlikNoDogrula>
			</soap:Body>
			</soap:Envelope>';
	$args     = array(
		'method'      => 'POST',
		'timeout'     => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'headers'     => array(
			'Content-Type' => 'text/xml',
		),
		'body'        => $xml,
		'sslverify'   => false,
	);
	$response = wp_remote_post( 'https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx', $args );

	return wp_strip_all_tags( wp_remote_retrieve_body( $response ) );
}

function standart_sorgulama( $tc_kimlik ) {
	$engelli_nolar = array( '11111111110', '22222222220', '33333333330', '44444444440', '55555555550', '66666666660', '7777777770', '88888888880', '99999999990' );
	foreach ( $engelli_nolar as $no ) {
		if ( $tc_kimlik === $no ) {
			return false;
		}
	}

	if ( 0 === $tc_kimlik[0] || ! ctype_digit( $tc_kimlik ) || 11 !== strlen( $tc_kimlik ) ) {
		return false;
	} else {
		$ilkt = null;
		$sont = null;
		$tumt = null;
		for ( $a = 0; $a < 9; $a += 2 ) {
			$ilkt += $tc_kimlik[ $a ];
		}
		for ( $a = 1; $a < 9; $a += 2 ) {
			$sont += $tc_kimlik[ $a ];
		}
		for ( $a = 0; $a < 10; $a++ ) {
			$tumt += $tc_kimlik[ $a ];
		}

		if ( ( $ilkt * 7 - $sont ) % 10 !== $tc_kimlik[9] || $tumt % 10 !== $tc_kimlik[10] ) {
			return false;
		}

		return true;
	}
}

function vergi_kontrol( $tax_number ) {
	if ( 10 !== strlen( $tax_number ) ) {
		return false;
	}

	$total = 0;

	for ( $i = 0; $i < 9; $i++ ) {
		$tmp1 = ( $tax_number[ $i ] + ( 9 - $i ) ) % 10;
		$tmp2 = ( $tmp1 * ( 2 ** ( 9 - $i ) ) ) % 9;

		if ( 0 !== $tmp1 && 0 === $tmp2 ) {
			$tmp2 = 9;
		}

		$total += $tmp2;
	}

	if ( 0 === $total % 10 ) {
		$check_num = 0;
	} else {
		$check_num = 10 - ( $total % 10 );
	}

	if ( (int) $tax_number[9] !== $check_num ) {
		return false;
	}

	return true;
}
