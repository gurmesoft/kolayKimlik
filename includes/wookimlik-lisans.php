<?php

class WooKimlikLisansYonetimi
{
    private $protocol;
    private $instance;
    private $apiUrl = "https://gurmewoo.com/index.php";
    public function __construct($plugin)
    {
        $this->protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $this->instance = str_replace($this->protocol, "", get_bloginfo('wpurl'));
        $this->eklentiBilgisi = get_file_data(WP_PLUGIN_DIR . "/" . $plugin, [
            'Version' => 'Version',
            'Name' => 'Plugin Name'
        ], 'plugin');
        $this->productId = strtoupper($this->eklentiBilgisi["Name"]);
        $this->aktifLisans = get_option($this->productId . "Lisans");
        add_action('admin_menu', array($this, "lisansMenusu"));
        add_action('admin_init', array($this, 'lisansAyarlari'));
        add_action('admin_notices', array($this, "uyariMesajlariniGoster"));
    }
    public function uyariMesajlariniGoster()
    {
        if (!isset($this->aktifLisans["LisansAktifMi"])) {
            $message = sprintf("%s eklentisi aktifleştirilmedi.Lütfen <a href='%s'>Lisans</a> sayfasını kullanarak eklentinizi aktifleştirin ve güncellemelerden faydalanmaya başlayın.", $this->eklentiBilgisi["Name"], admin_url("admin.php?page=" . $this->productId . "Lisans"));
            add_settings_error($this->productId . "Uyari", 2, $message, 'error');
        }
        settings_errors($this->productId . "Uyari");
    }
    public function lisansAyarlariniGuncelle()
    {

        $data = $_POST[$this->productId . "Lisans"];
        try {
            if (isset($_POST["lisansIslemi"]) && $_POST["lisansIslemi"] == "deactivate") {
                $durum = $this->lisansAktifPasifEt($_POST[$this->productId . "Lisans"], "deactivate");

                add_settings_error($this->productId . "Uyari", 2, $durum->message, 'updated');

                return array();
            } else {
                $durum = $this->lisansAktifPasifEt($_POST[$this->productId . "Lisans"]["LisansKodu"]);
                if ($durum->status == "success") {

                    $data["LisansAktifMi"] = $durum->licence_status;
                    $data["LisansBaslangic"] = $durum->licence_start;
                    $data["LisansBitis"] = $durum->licence_expire;
                } else {
                    add_settings_error($this->productId . "Uyari", 2, $durum->message, 'error');
                }
            }
        } catch (Exception $e) {
            add_settings_error($this->productId . "Uyari", 1, $e->getMessage(), "error");
        }
        //update_option($this->productId."Lisans",$data);
        return $data;
    }
    public function lisansAyarlari()
    {
        add_settings_section(
            $this->productId . 'Lisans',
            '<h3>Eklenti Lisanslama Bilgileri</h3>',
            array($this, 'lisansAciklamasi'),
            $this->productId . 'Lisans'
        );
        add_settings_field(
            $this->productId . 'LisansKodu',
            'Lisans Kodu: ',
            array($this, 'lisansKodu'),
            $this->productId . 'Lisans',
            $this->productId . 'Lisans'
        );
        if (isset($this->aktifLisans["LisansAktifMi"]) && $this->aktifLisans["LisansAktifMi"] == "active") {
            add_settings_field(
                $this->productId . 'Durumu',
                'Durum: ',
                array($this, 'lisansDurumu'),
                $this->productId . 'Lisans',
                $this->productId . 'Lisans'
            );
            add_settings_field(
                $this->productId . 'BaslangicTarihi',
                'Başlangıç Tarihi: ',
                array($this, 'lisansBaslangicTarihi'),
                $this->productId . 'Lisans',
                $this->productId . 'Lisans'
            );
            add_settings_field(
                $this->productId . 'BitisTarihi',
                'Bitiş Tarihi: ',
                array($this, 'lisansBitisTarihi'),
                $this->productId . 'Lisans',
                $this->productId . 'Lisans'
            );
        }
        add_settings_field(
            $this->productId . 'LisansAktifPasif',
            '',
            array($this, 'lisansAktifPasif'),
            $this->productId . 'Lisans',
            $this->productId . 'Lisans'
        );
        register_setting($this->productId . 'Lisans', $this->productId . 'Lisans', array($this, "lisansAyarlariniGuncelle"));
    }
    public function lisansDurumu()
    {
        echo @$this->aktifLisans["LisansAktifMi"] == "active" ? __("Aktif") : __("Pasif");
    }
    public function lisansBaslangicTarihi()
    {
        echo $this->aktifLisans["LisansBaslangic"];
    }
    public function lisansBitisTarihi()
    {
        echo $this->aktifLisans["LisansBitis"];
    }
    public function lisansMenusu()
    {
        add_submenu_page(
            'wookimlik-settings',
            __('Lisans', "wookimlik"),
            __('Lisans', "wookimlik"),
            'manage_options',
            $this->productId . 'Lisans',
            array($this, 'lisansKontrol'),
        );
    }
    public function lisansKontrol()
    {


        echo '<div class="wrap">
        <h1>GurmeWoo Lisanslama</h1>
        <form method="post" action="options.php">';
        settings_fields($this->productId . 'Lisans'); // settings group name
        do_settings_sections($this->productId . 'Lisans'); // just a page slug
        if (empty($this->aktifLisans["LisansAktifMi"])) {
            submit_button();
        }

        echo '</form></div>';
    }
    public function lisansAciklamasi()
    {

        echo "Lisanslarınızı <a href='https://gurmewoo.com/hesabim/siparisler/' target='_blank'>Hesabım</a> sayfasından yönetebilirsiniz";
    }
    public function lisansKodu()
    {
        $aktiflikDurumu = isset($this->aktifLisans["LisansAktifMi"]) ? 'readonly' : '';
        echo "<input type='text' " . $aktiflikDurumu . " size='48' name='" . $this->productId . "Lisans[LisansKodu]' id='" . $this->productId . "Lisans[LisansKodu]' value='" . @$this->aktifLisans["LisansKodu"] . "'>";
        echo "<p>" . __("Lütfen GurmeWoo'dan aldığınız eklenti için ilgili siparişin detayında kendinize lisans oluşturup. Lisans kodunu üstteki kutucuğa giriniz.") . "</p>";
    }
    public function lisansAktifPasif()
    {

        if (!empty($this->aktifLisans["LisansAktifMi"])) {
            echo '<input type="hidden" name="lisansIslemi" value="deactivate">';
            echo '<input type="submit" class="button" style="width:500px;" value="Lisansı Pasifleştir"/>';
        }
    }

    public function lisansAktifPasifEt($lisans, $islem = "activate")
    {
        $args = array(
            'woo_sl_action' => $islem, //active , deactive
            'licence_key' => $lisans,
            'product_unique_id' => $this->productId,
            'domain' => $this->instance
        );
        $request_uri    = $this->apiUrl . '?' . http_build_query($args);
        $data           = wp_remote_get($request_uri);

        if (is_wp_error($data) || $data['response']['code'] != 200) {
            throw new Exception(__("Lisans Sunucusuna bağlanılamadı"), 0);
            //there was a problem establishing a connection to the API server
        }

        $data_body = json_decode($data['body']);

        if (isset($data_body->status)) {
            if ($data_body->status == 'success' && $data_body->status_code == 's200') {
                //the license is active and the software is active
                //doing further actions like saving the license and allow the plugin to run
                return true;
            } else {
                throw new Exception(__("Lisans aktifleştirilemedi.Lütfen GurmeWoo ekibiyle irtibata geçiniz"));
                //there was a problem activating the license
            }
        } else {
            return $data_body[0];
        }
    }
}

$lisans = new WooKimlikLisansYonetimi("wookimlik/wookimlik.php");
