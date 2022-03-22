<?php

function nviSorgulama($data)
{
    $xml = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soap:Body>
			<TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
			<TCKimlikNo>' . $data["tcno"] . '</TCKimlikNo>
			<Ad>' . $data["isim"] . '</Ad>
			<Soyad>' . $data["soyisim"] . '</Soyad>
			<DogumYili>' . $data["dogumyili"] . '</DogumYili>
			</TCKimlikNoDogrula>
			</soap:Body>
			</soap:Envelope>';
    $args = array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'headers' => array(
            'Content-Type' => 'text/xml'
        ),
        'body' => $xml,
        'sslverify' => false
    );
    $response = wp_remote_post('https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx', $args);
    
    return strip_tags(wp_remote_retrieve_body($response));
}

function standartSorgulama($tckimlik)
{
    $engelliNolar = array('11111111110', '22222222220', '33333333330', '44444444440', '55555555550', '66666666660', '7777777770', '88888888880', '99999999990');
    foreach ($engelliNolar as $no) {
        if ($tckimlik == $no) {
            return false;
        }
    }

    if ($tckimlik[0] == 0 || !ctype_digit($tckimlik) || strlen($tckimlik) != 11) {
        return false;
    } else {
        $ilkt = null;
        $sont = null;
        $tumt = null;
        for ($a = 0; $a < 9; $a += 2) {
            $ilkt += $tckimlik[$a];
        }
        for ($a = 1; $a < 9; $a += 2) {
            $sont += $tckimlik[$a];
        }
        for ($a = 0; $a < 10; $a += 1) {
            $tumt += $tckimlik[$a];
        }

        if (($ilkt * 7 - $sont) % 10 != $tckimlik[9] || $tumt % 10 != $tckimlik[10]) {
            return false;
        }

        return true;
    }
}

function vergiKontrol($taxNumber)
{
    if (strlen($taxNumber) !== 10) {
        return false;
    }

    $total = 0;
    $checkNum = null;
    for ($i = 0; $i < 9; $i++) {
        $tmp1 = ($taxNumber[$i] + (9 - $i)) % 10;
        $tmp2 = ($tmp1 * (2 ** (9 - $i))) % 9;

        if ($tmp1 !== 0 && $tmp2 === 0) {
            $tmp2 = 9;
        }

        $total += $tmp2;
    }

    if ($total % 10 === 0) {
        $checkNum = 0;
    } else {
        $checkNum = 10 - ($total % 10);
    }

    if ((int)$taxNumber[9] !== $checkNum) {
        return false;
    }

    return true;
}
