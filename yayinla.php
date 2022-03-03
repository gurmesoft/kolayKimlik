<?php
/*
 *  eklenti dizininde ilgili bilgileri okuyup gurmewoo da yayınlama yapan bir sınıftır
 *
 *  Todo:
 *      + Eklentiyi siteye yükle
 *      - Versiyonu güncelle
 *      - Eğer versiyon major değişiklik içeriyorsa jiraya ilgili projesine issue aç
 *      - Son Güncellenme tarihi updat et
 * */
class GurmeWooYayinla{
	private $eklentiBilgileri;
	private $eklentiDosyasi;
	private $ziplenecekler;
	private $eklentiId;
	private $eklentiVersiyon;
	private $apiUsername="apiuser@gurmewoo.com";
	private $apiPassword='b*ewEtGcj$a&LZbXQLchV^lS';
	private $jiraUsername="fuatpoyrazz@gmail.com";
	private $jiraApiKey="dl80lPkmkO8HHpdQ0Vz40310";
	private $yeniEklentiZip;
	private $apiUrl="https://gurmewoo.com/";
	private $jiraUrl='https://gurmesoft.atlassian.net/';
	private $discordHookUrl="https://discord.com/api/webhooks/809022097758224416/mw7La6js8IOzOYXeO-2J6rfQbM1SJrbCemSUET-CJu6PFqULHD-zvlv7jag8yKdMZRTE";
	private $jiraAssignId="5f37dbf96db35e003931ca35"; //Fuat POYRAZ
	public function __construct($eklentiDosyasi,$ziplenecekler){
		$this->eklentiDosyasi=$eklentiDosyasi;
		$this->ziplenecekler=$ziplenecekler;
		$this->gerekliBilgileriOku();
	}

	private function gerekliBilgileriOku(){
		$dosyaIcerigi = file_get_contents($this->eklentiDosyasi);
		$degerler = token_get_all( $dosyaIcerigi );

		foreach($degerler as $deger){
			if($deger[0] == T_COMMENT){
				$baslikBilgileri = preg_replace("/_.*/s","",$deger[1]);
				break; //exiting as we only need the first block of comment
			}
		}
		$satirlar=explode("*",$baslikBilgileri);

		foreach($satirlar as $satir){
			$sSatir=explode(": ",$satir);
			if(isset($sSatir[1]))
				$this->eklentiBilgileri[trim($sSatir[0])]=trim($sSatir[1]);
		}
		$this->eklentiId=strtolower($this->eklentiBilgileri["ID"]);
		$this->eklentiVersiyon = $this->eklentiBilgileri["Version"];
		return $this;
	}

	public function zipDosyasiniOlustur(){
		$this->zip = new ZipArchive;
		$this->yeniEklentiZip=sprintf("%s-v%s-%s.zip",$this->eklentiId,$this->eklentiVersiyon,uniqid());
		if ($this->zip->open($this->yeniEklentiZip, ZipArchive::CREATE) === TRUE)
		{
			foreach ($this->ziplenecekler as $dosya){
				if(is_dir($dosya)){
					$this->dirToZip($dosya,0);
				}else{
					$this->zip->addFile($dosya, sprintf("%s/%s",$this->eklentiId,$dosya));
				}
			}
			$this->zip->close();
		}
		return $this;
	}

	private function dirToZip($folder,$exclusiveLength){
		$handle = opendir($folder);
		while(FALSE !== $f = readdir($handle)){

			if($f != '.' && $f != '..' && $f != basename(__FILE__)){
				$filePath = "$folder/$f";
				$localPath = substr($filePath, $exclusiveLength);

				if(is_file($filePath)){
					$this->zip->addFile($filePath, sprintf("%s/%s",$this->eklentiId,$localPath));
				}elseif(is_dir($filePath)){
					if(!strpos($localPath,".git")){
						$this->zip->addEmptyDir(sprintf("%s/%s",$this->eklentiId,$localPath));
						$this->dirToZip($filePath, $exclusiveLength);
					}
				}
			}
		}
		closedir($handle);
	}

	public function dosyayiYukle(){
		$dosya=file_get_contents($this->yeniEklentiZip);
		return $this->cUrl($this->apiUrl."wp-json/wp/v2/media/",$dosya,array(
			'Content-Type: application/zip',
			'Content-Disposition: form-data; filename="'.$this->yeniEklentiZip.'"',
			'Authorization: Basic ' . base64_encode( $this->apiUsername . ':' . $this->apiPassword ),
		));

	}

	private function cUrl($url,$post,$headers=array()){
		$ch = curl_init($url);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec( $ch );
		curl_close( $ch );
		return json_decode( $result );
	}

	public function eklentiDetaylariniGuncelle($yuklenenDosya,$eklentiBilgileri){
		$slug=explode("/",$this->eklentiBilgileri["Plugin URI"]);
		$veri=array(
			"dosyaAdi"=>$this->eklentiId,
			"yeniVersiyon"=>$this->eklentiVersiyon,
			"zipId"=>$yuklenenDosya->id,
			"urunId"=>$eklentiBilgileri->id
		);
		$headers=array('Authorization: Basic ' . base64_encode( $this->apiUsername . ':' . $this->apiPassword ),
		);
		$donus=$this->cUrl($this->apiUrl."wp-json/urunGuncelleme/v1/posts/".end($slug),$veri,$headers);

	}

	private function jiraIssueAc($baslik,$mesaj){
		$json_data=array(
			"fields"=>array(
				"project"=>array(
					"key"=>$this->eklentiBilgileri["JIRAPROJECT"],
				),
				"summary"=>$baslik,
				"description"=>$mesaj,
				"duedate"=>date('Y-m-d',strtotime('+7 day')),
				"priority"=>array(
					"name"=>"Highest"
				),
				"issuetype"=>array(
					"name"=>"Görev"
				),
				"assignee"=>array(
					"accountId"=>$this->jiraAssignId
				)
			)
		);
		$donus=$this->cUrl($this->jiraUrl."rest/api/2/issue/",json_encode($json_data),
			array(
				"Application: application/json",
				"Authorization: Basic ".base64_encode( $this->jiraUsername . ':' . $this->jiraApiKey ),
				"Content-Type: application/json"
			)
		);
		$boarTasi=json_encode(array("issues"=>array($donus->id)));
		$this->cUrl($this->jiraUrl."rest/agile/1.0/board/".$this->eklentiBilgileri["JIRABOARDID"]."/issue",$boarTasi,
			array(
				"Application: application/json",
				"Authorization: Basic ".base64_encode( $this->jiraUsername . ':' . $this->jiraApiKey ),
				"Content-Type: application/json"
			)
		);
	}

	private function discordMesajiGonder($mesaj,$baslik,$extra){
		$timestamp = date("c", strtotime("now"));

		$json_data = json_encode([
			"username" => "GurmeWoo Eklenti Güncelleme Habercisi",
			"content"=>"Yeni bir eklenti güncellemesi var; ".$mesaj,
			"avatar"=>"https://gurmewoo.com/wp-content/uploads/2020/04/cropped-gw3-2048x449.png",
			"embeds" => [
				[
					"title" => $baslik,
					"type" => "rich",
					"description" => $mesaj,
					"url" => str_replace(" ","",$this->eklentiBilgileri["Plugin URI"]),
					"color" => hexdec( "3366ff" ),
					"timestamp"=>$timestamp,
					"image"=>[
						"url"=>$extra->screenshot,
					]
				]
			]

		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		$this->cUrl($this->discordHookUrl,$json_data,
			array(
				"Accept: application/json",
				"Content-Type: application/json"
			)
		);
	}
	public function guncelleme(){
		$yuklenenDosya=$this->zipDosyasiniOlustur()->dosyayiYukle();
		$slug=explode("/",$this->eklentiBilgileri["Plugin URI"]);

		$sunucuEklentiBilgileri=json_decode(file_get_contents($this->apiUrl."wp-json/urunDetay/v1/posts/".end($slug)));
		$aktifVersiyon=substr(str_replace(".","",$sunucuEklentiBilgileri->version),0,2);
		$yeniVersiyon=substr(str_replace('.','',$this->eklentiVersiyon),0,2);
		$baslik=sprintf("%s Eklentisi v%s versiyonundan v%s versiyona güncellendi",$this->eklentiId,$sunucuEklentiBilgileri->version,$this->eklentiVersiyon);

		if(intval($yeniVersiyon) > intval($aktifVersiyon)){
			//major versiyon güncellemesi var
			$mesaj=sprintf("%s eklentisinde major bir versiyon güncellmesi aldı. Lütfen sürüm notları dökümanını kontrol ediniz.",$this->eklentiId);
			$issueMesaji=sprintf("Eklenti %s versiyonundan %s versiyonuna geçti. Demo sayfaları, GurmeWoo ürün sayfası, kurulum dökümanı, sürüm notları değiştirilmeli. Bu sürüm değişikliğiyle ilgili reklam planlaması gözden geçirilmeli (Mailing vs). Eklenti Ürün Sayfası %s",$sunucuEklentiBilgileri->version,$this->eklentiVersiyon,$this->eklentiBilgileri["Plugin URI"]);
			$this->jiraIssueAc("Eklenti Major Versiyon Güncellemesi Yapılması Gerekenler",$issueMesaji);
		}else{
			// minor versiyon guncellenmesi var
			$mesaj=sprintf("%s eklentisinde minör bir değişiklik oldu. Hata giderme ve iyileştirme yapılmış olabilir.",$this->eklentiId);
		}
		$this->eklentiDetaylariniGuncelle($yuklenenDosya,$sunucuEklentiBilgileri);
		$this->discordMesajiGonder($mesaj,$baslik,$sunucuEklentiBilgileri);
	}
}

$yayinla=new GurmeWooYayinla("wookimlik.php",array(
	"assets",
	"includes",
	"wookimlik.php",
));
$yayinla->guncelleme();