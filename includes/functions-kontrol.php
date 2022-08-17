<?php

function strto($to, $str)
{
	if ('lower' === $to) {
		return mb_strtolower(str_replace(array('I', 'Ğ', 'Ü', 'Ş', 'İ', 'Ö', 'Ç'), array('ı', 'ğ', 'ü', 'ş', 'i', 'ö', 'ç'), $str), 'utf-8');
	} elseif ('upper' === $to) {
		return mb_strtoupper(str_replace(array('ı', 'ğ', 'ü', 'ş', 'i', 'ö', 'ç'), array('I', 'Ğ', 'Ü', 'Ş', 'İ', 'Ö', 'Ç'), $str), 'utf-8');
	} else {
		trigger_error('Lütfen geçerli bir strto() parametresi giriniz.', E_USER_ERROR);
	}
}

function kk_nvi_sorgulama($data)
{
	/*
	$xml = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				<soap:Body>
					<TCKimlikNoDogrula  xmlns="http://tckimlik.nvi.gov.tr/WS">
						<TCKimlikNo>' . $data['tcno'] . '</TCKimlikNo>
						<Ad>' . strto('upper', $data['isim']) . '</Ad>
						<Soyad>' . strto('upper', $data['soyisim']) . '</Soyad>
						<DogumYili>' . $data['dogumyili'] . '</DogumYili>
					</TCKimlikNoDogrula >
				</soap:Body>
			</soap:Envelope>';
	*/
	$client = new SoapClient( 'https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL' );
	
	$result = $client->TCKimlikNoDogrula(
		array(
			'TCKimlikNo' => $data['tcno'] ,
			'Ad'         => strto('upper', $data['isim']),
			'Soyad'      => strto('upper', $data['soyisim']),
			'DogumYili'  => $data['dogumyili'],
		)
	);

	return $result->TCKimlikNoDogrulaResult;
}

function kk_standart_sorgulama($tc_kimlik)
{
	$engelli_nolar = array('11111111110', '22222222220', '33333333330', '44444444440', '55555555550', '66666666660', '7777777770', '88888888880', '99999999990');

	foreach ($engelli_nolar as $no) {
		if ($tc_kimlik === $no) {

			return false;
		}
	}

	if (0 === $tc_kimlik[0] || !ctype_digit($tc_kimlik) || 11 !== strlen($tc_kimlik)) {

		return false;
	} else {
		$say = 0;
		for ($i = 0; $i <= 9; $i++) {
			$say = $say + $tc_kimlik[$i];
		}

		if (substr($say, -1) == substr($tc_kimlik, -1)) {
			return true;
		} else {
			var_dump($tc_kimlik);
			die;
			return false;
		}

		return true;
	}
}

function kk_vergi_kontrol($tax_number)
{
	if (10 !== strlen($tax_number)) {
		return false;
	}

	$total = 0;

	for ($i = 0; $i < 9; $i++) {
		$tmp1 = ($tax_number[$i] + (9 - $i)) % 10;
		$tmp2 = ($tmp1 * (2 ** (9 - $i))) % 9;

		if (0 !== $tmp1 && 0 === $tmp2) {
			$tmp2 = 9;
		}

		$total += $tmp2;
	}

	if (0 === $total % 10) {
		$check_num = 0;
	} else {
		$check_num = 10 - ($total % 10);
	}

	if ((int) $tax_number[9] !== $check_num) {
		return false;
	}

	return true;
}
