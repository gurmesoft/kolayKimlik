<?php

class tcinputSettings
{   
    public function __construct(){

        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_init', array($this, 'getOptionPage'));
        add_filter('plugin_action_links',array($this,"settingsLink"),10,5);
        $this->tcSettings = get_option('tcSettings');
    } 
    public function addAdminMenu(){
        add_menu_page('TC INPUT','Tc Input Settings','manage_options','tc-input-settings', array($this,'adminSettingsPage'));
    }

    public function settingsLink($links,$plugin_file){
        if($plugin_file=="tcinput/tcinput.php"){
            $links[]="<a href='".admin_url("admin.php?page=tc-input-settings")."'>Settings</a>";
        }
        return $links;
    }
    
    public function getOptionPage(){        
        add_settings_section(
            'defaultSection',
            __('<h1>TC INPUT</h1>', 'wookargo'),
            array($this, 'defaultSectionCallBack'),
            'tc-input-settings'
        );
        add_settings_field(
            'contactForm7',
            __('Contact Form 7 Bağlantısı', 'wookargo'),
            array($this, 'contactForm7CallBack'),
            'tc-input-settings',
            'defaultSection'
        );
        add_settings_field(
            'ninjaForm',
            __('Ninja Form Bağlantısı', 'wookargo'),
            array($this, 'ninjaFormCallBack'),
            'tc-input-settings',
            'defaultSection'
        );
        add_settings_field(
            'wpForms',
            __('WPForms Bağlantısı', 'wookargo'),
            array($this, 'wpFormCallBack'),
            'tc-input-settings',
            'defaultSection'
        );
        register_setting('tcSettings', 'tcSettings', array($this, 'emptyCallBack'));
    }
    public function defaultSectionCallBack(){
        echo '<h3><p>'.__("Bu eklenti ile sitenizde kullandığınız iletişim formu eklentilerinde girilen tc kimlik bilgilerinin doğruluğunu kontrol edebilirsiniz.","tcinput").'<h3></p>';
        echo '<p class="description">'.__("Uyarı: Sadece yüklü olan form eklentileri için aktif ediniz.","tcinput").'</p>';
    }
    public function contactForm7CallBack (){
        ?>
        <select name="tcSettings[contactForm7]" id="contactForm7" type="text" >
        <option name="on" value="on" <?php echo $this->tcSettings['contactForm7'] == 'on'  ? 'selected' : ''; ?>><?php _e('Aktif', 'tcinput');?></option>
        <option name="off" value="off" <?php echo $this->tcSettings['contactForm7'] == 'off'  ? 'selected' : ''; ?>><?php _e('Pasif', 'tcinput');?></option>
        </select>
        <p class="description">Bu özellik aktif edildiğinde contact form eklentinize tckimlik,tckimlik nvi etiketleri eklenecektir.Bu etiketler ile formlarınıza tc kimlik sorgulama alanları ekleyebilirsiniz.<br>
        Nvi opsiyonlu etiket seçildiğinde 4 adet giriş bölümü açılacaktır. Ad,Soyad,Doğum Yılı bu alanlara girilen bilgiler nufus müdürlüğü sistemine kayıtlı bilgler ile doğrulanır.<br>
        Her iki etiketide zorunlu kılma seçeneği formu düzenlediğiniz bölümde size sunulmuştur.</p>
        <p> <img src="<?php echo plugin_dir_url(__DIR__)?>assets/contactform7.png"></p>
        <?php
    }
    public function ninjaFormCallBack (){
        ?>
        <select name="tcSettings[ninjaForm]" id="ninjaForm" type="text"  disabled="true">
        <option name="on" value="on" <?php echo $this->tcSettings['ninjaForm'] == 'on'  ? 'selected' : ''; ?>><?php _e('Aktif', 'tcinput');?></option>
        <option name="off" value="off" <?php echo $this->tcSettings['ninjaForm'] == 'off'  ? 'selected' : ''; ?>><?php _e('Pasif', 'tcinput');?></option>
        </select>
        <?php
        echo '<p class="description">' . __('Bu seçenek çok yakında aktif edilecektir.', 'tcinput') . '</p>';
    }  
    public function wpFormCallBack (){
        ?>
        <select name="tcSettings[wpForms]" id="ninjaForm" type="text"  disabled="true">
        <option name="on" value="on" <?php echo $this->tcSettings['wpForms'] == 'on'  ? 'selected' : ''; ?>><?php _e('Aktif', 'tcinput');?></option>
        <option name="off" value="off" <?php echo $this->tcSettings['wpForms'] == 'off'  ? 'selected' : ''; ?>><?php _e('Pasif', 'tcinput');?></option>
        </select>
        <?php
        echo '<p class="description">' . __('Bu seçenek çok yakında aktif edilecektir.', 'tcinput') . '</p>';
    }   
    public function adminSettingsPage(){
        ?>
        <form method="POST" action="options.php">
        <?php
        settings_fields('tcSettings');
        do_settings_sections('tc-input-settings');
        submit_button(__('Ayarlari Kaydet', 'tcinput'));        
        ?>
		</form>
        <?php
    }
}   
?>