<?php

class Wk_tcinputSettings
{
    public function __construct()
    {

        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_init', array($this, 'getOptionPage'));
        add_filter('plugin_action_links', array($this, "settingsLink"), 10, 5);
        $this->tcSettings = get_option('tcSettings');
    }
    public function addAdminMenu()
    {
        add_menu_page('wooKimlik', 'wooKimlik', 'manage_options', 'wookimlik-settings');
        add_submenu_page('wookimlik-settings', 'wooKimlik', 'wooKimlik', 'manage_options', 'wookimlik-settings', array($this, 'adminSettingsPage'));
    }

    public function settingsLink($links, $plugin_file)
    {
        if ($plugin_file == "wookimlik/wookimlik.php") {
            $links[] = "<a href='" . admin_url("admin.php?page=wookimlik-settings") . "'>Ayarlar</a>";
        }
        return $links;
    }

    public function getOptionPage()
    {
        add_settings_section(
            'defaultSection',
            __('<h1>wooKimlik</h1>', 'wookimlik'),
            array($this, 'defaultSectionCallBack'),
            'wookimlik-settings'
        );
        add_settings_field(
            'header',
            __('Etki Alanları<hr>', 'wookimlik'),
            array($this, 'headerCb'),
            'wookimlik-settings',
            'defaultSection'
        );
        add_settings_field(
            'woocommerce_enable',
            __('Aktif Et', 'wookimlik'),
            array($this, 'woocommerceEnable'),
            'wookimlik-settings',
            'defaultSection'
        );
        add_settings_field(
            'woocommerce',
            __('WooCommerce Ödeme Sayfası', 'wookimlik'),
            array($this, 'woocommerceCallBack'),
            'wookimlik-settings',
            'defaultSection'
        );
        add_settings_field(
            'contactForm7',
            __('Contact Form 7', 'wookimlik'),
            array($this, 'contactForm7CallBack'),
            'wookimlik-settings',
            'defaultSection'
        );
        add_settings_field(
            'ninjaForm',
            __('Ninja Form', 'wookimlik'),
            array($this, 'ninjaFormCallBack'),
            'wookimlik-settings',
            'defaultSection'
        );
        add_settings_field(
            'wpForms',
            __('WPForms', 'wookimlik'),
            array($this, 'wpFormCallBack'),
            'wookimlik-settings',
            'defaultSection'
        );
        register_setting('tcSettings', 'tcSettings', array($this, 'emptyCallBack'));
    }
    public function defaultSectionCallBack()
    {
        echo '<h3><p>' . __("Bu eklenti ile sitenizde kullandığınız iletişim formu eklentilerine, ödeme sayfalarına TC kimlik numarası alanı ekleyebilir.Girilen bilgilerin doğruluğunu kontrol edebilirsiniz.", "tcinput") . '<h3></p>';
        echo '<p class="description">' . __("", "tcinput") . '</p><br>';
    }
    public function headerCB()
    {
        echo '<p>' . __("Nasıl Kullanırım ?", "tcinput") . '</p><hr>';
    }
    public function woocommerceEnable()
    {
?>
        <input type='checkbox' id='woocommerce' name='tcSettings[enabled]' <?php echo ($this->tcSettings['enabled'] == 'on' ? 'checked' : '') ?> />
    <?php
    }
    public function woocommerceCallBack()
    {
    ?>
        <p>
            <select name="tcSettings[woocommerce]" id="woocommerce">
                <option name="none" value="none" <?php echo $this->tcSettings['woocommerce'] == 'none'  ? 'selected' : ''; ?>><?php _e('Kapalı', 'tcinput'); ?></option>
                <option name="standart" value="standart" <?php echo $this->tcSettings['woocommerce'] == 'standart'  ? 'selected' : ''; ?>><?php _e('Format Kontrol', 'tcinput'); ?></option>
                <option name="nvi" value="nvi" <?php echo $this->tcSettings['woocommerce'] == 'nvi'  ? 'selected' : ''; ?>><?php _e('NVI Kontrol', 'tcinput'); ?></option>
            </select>
        </p>
        <p class="description">
            <input type='checkbox' id='woocommerce' name='tcSettings[woocommerceRequired]' <?php echo ($this->tcSettings['woocommerceRequired'] == 'on' ? 'checked' : '') ?> />
            <label for='tcSettings[woocommerceRequired]'> Zorunlu Alan Olarak Ekle </label>
        </p>
        <p class="description">Bu özellik ile WooCommerce ödeme sayfanıza TC kimlik giriş alanı ekleyebilirsiniz."Format Kontrol" girilen numaranın sadece TC kimlik numara algoritması ile kontrolünü sağlar.<br>
            "NVI Kontrol" seçildiğinde TC kimlik ve Doğum Yılı bölümü eklenecektir. Ad,Soyad,Doğum Yılı ve TC Kimlik alanlarına girilen bilgiler nufus müdürlüğü sistemine kayıtlı bilgler ile doğrulanır.<br>
            Her iki kontrolü de "Zorunlu Alan Olarak Ekle" seçeneği ile isteğe bağlı bırakabilir yada zorunlu kılabilirsiniz. </p>
        <p>
        <p> <img src="<?php echo plugin_dir_url(__DIR__) ?>assets/woocommerce.png"></p>
        <input type='checkbox' id='woocommerceVergi' name='tcSettings[woocommerceVergi]' <?php echo ($this->tcSettings['woocommerceVergi'] == 'on' ? 'checked' : '') ?> />
        <label for='tcSettings[woocommerceRequired]'> Vergi Dairesi/No Alanı Ekle </label></p>
        <input type='checkbox' id='woocommerceRequiredVergiAlani' name='tcSettings[woocommerceRequiredVergi]' <?php echo ($this->tcSettings['woocommerceRequiredVergi'] == 'on' ? 'checked' : '') ?> />
        <label for='tcSettings[woocommerceRequired]'>Zorunlu Alan Olarak Ekle </label></p>
        <p> <img style="width: 400px; height: 60px;" src="<?php echo plugin_dir_url(__DIR__) ?>assets/woocommerceVergi.png"></p>
        <hr>
        <input type="submit" class="button button-primary" value="Ayarı Kaydet" />
    <?php
    }
    public function contactForm7CallBack()
    {
    ?>
        <p class="description">Bu özellik ile contact form eklentinize tckimlik,tckimlik nvi etiketleri eklenecektir.Bu etiketler ile formlarınıza tc kimlik sorgulama alanları ekleyebilirsiniz.<br>
            Nvi opsiyonlu etiket seçildiğinde 4 adet giriş bölümü açılacaktır. Ad,Soyad,Doğum Yılı ve TC Kimlik bu alanlara girilen bilgiler nufus müdürlüğü sistemine kayıtlı bilgler ile doğrulanır.<br>
            Her iki etiketide zorunlu kılma seçeneği formu düzenlediğiniz bölümde size sunulmuştur.</p>
        <p> <img src="<?php echo plugin_dir_url(__DIR__) ?>assets/contactform7.png"></p>
        <hr>
    <?php
    }
    public function ninjaFormCallBack()
    {
        echo '<p class="description">' . __('Bu seçenek çok yakında aktif edilecektir.', 'tcinput') . '</p>';
    }
    public function wpFormCallBack()
    {
        echo '<p class="description">' . __('Bu seçenek çok yakında aktif edilecektir.', 'tcinput') . '</p>';
    }
    public function adminSettingsPage()
    {
    ?>
        <form method="POST" action="options.php">
            <?php
            settings_fields('tcSettings');
            do_settings_sections('wookimlik-settings');
            ?>
        </form>
<?php
    }
}
?>