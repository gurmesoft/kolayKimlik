<?php

class checkTc
{ 
    public function getUserData(){
        
        $data = array(
        "isim"      => "",
        "soyisim"   => "",
        "dogumyili" => "",
        "tcno"      => ""
        );
        $this->checkTc($data);
    }   

    public function checkTc($data){

		$data = '<?xml version="1.0" encoding="utf-8"?>
		<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
		<TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
		<TCKimlikNo>'.$data["tcno"].'</TCKimlikNo>
		<Ad>'.$data["isim"].'</Ad>
		<Soyad>'.$data["soyisim"].'</Soyad>
		<DogumYili>'.$data["dogumyili"].'</DogumYili>
		</TCKimlikNoDogrula>
		</soap:Body>
		</soap:Envelope>';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true );
		curl_setopt($ch, CURLOPT_POST,true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_HEADER,false);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array(
		'POST /Service/KPSPublic.asmx HTTP/1.1',
		'Host: tckimlik.nvi.gov.tr',
		'Content-Type: text/xml; charset=utf-8',
		'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/TCKimlikNoDogrula"',
		'Content-Length: '.strlen($data)
		));
		$response = curl_exec($ch);
		curl_close($ch);
        // Sonuç True Yada False Gelecek.
	    return strip_tags($response);
	}
}

?>