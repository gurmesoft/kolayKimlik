<?php

class Wk_TcInputSettings {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'addAdminMenu' ) );
		add_action( 'admin_init', array( $this, 'getOptionPage' ) );
		add_filter( 'plugin_action_links', array( $this, 'settingsLink' ), 10, 5 );
		$this->tcSettings = get_option( 'tcSettings' );
	}
	public function addAdminMenu() {
		add_menu_page( 'kolayKimlik', 'kolayKimlik', 'manage_options', 'kolaykimlik-settings' );
		add_submenu_page( 'kolaykimlik-settings', 'kolayKimlik', 'kolayKimlik', 'manage_options', 'kolaykimlik-settings', array( $this, 'adminSettingsPage' ) );
	}

	public function settingsLink( $links, $plugin_file ) {
		if ( $plugin_file == 'kolaykimlik/kolaykimlik.php' ) {
			$links[] = "<a href='" . admin_url( 'admin.php?page=kolaykimlik-settings' ) . "'>Ayarlar</a>";
		}
		return $links;
	}

	public function getOptionPage() {
		add_settings_section(
			'defaultSection',
			__( '<h1>kolayKimlik</h1>', 'kolay-kimlik' ),
			array( $this, 'defaultSectionCallBack' ),
			'kolaykimlik-settings'
		);
		add_settings_field(
			'header',
			__( 'Etki Alanları<hr>', 'kolay-kimlik' ),
			array( $this, 'headerCb' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		add_settings_field(
			'woocommerce_enable',
			__( 'Aktif Et', 'kolay-kimlik' ),
			array( $this, 'woocommerceEnable' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		add_settings_field(
			'woocommerce',
			__( 'WooCommerce Ödeme Sayfası', 'kolay-kimlik' ),
			array( $this, 'woocommerceCallBack' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		add_settings_field(
			'contactForm7',
			__( 'Contact Form 7', 'kolay-kimlik' ),
			array( $this, 'contactForm7CallBack' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		add_settings_field(
			'ninjaForm',
			__( 'Ninja Form', 'kolay-kimlik' ),
			array( $this, 'ninjaFormCallBack' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		add_settings_field(
			'wpForms',
			__( 'WPForms', 'kolay-kimlik' ),
			array( $this, 'wpFormCallBack' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		register_setting( 'tcSettings', 'tcSettings', array( $this, 'emptyCallBack' ) );
	}
	public function defaultSectionCallBack() {
		echo '<h3><p>' . __( 'Bu eklenti ile sitenizde kullandığınız iletişim formu eklentilerine, ödeme sayfalarına TC kimlik numarası alanı ekleyebilir.Girilen bilgilerin doğruluğunu kontrol edebilirsiniz.', 'kolay-kimlik' ) . '<h3></p>';
		echo '<p class="description">' . __( '', 'kolay-kimlik' ) . '</p><br>';
	}
	public function headerCB() {
		echo '<p>' . __( 'Nasıl Kullanırım ?', 'kolay-kimlik' ) . '</p><hr>';
	}
	public function woocommerceEnable() {
		?>
		<input type='checkbox' id='woocommerce' name='tcSettings[enabled]' <?php echo $this->tcSettings['enabled'] == 'on' ? 'checked' : ''; ?> />
		<?php
	}
	public function woocommerceCallBack() {
		?>
		<p>
			<select name="tcSettings[woocommerce]" id="woocommerce">
				<option value="none" <?php echo $this->tcSettings['woocommerce'] == 'none' ? 'selected' : ''; ?>><?php _e( 'Kapalı', 'kolay-kimlik' ); ?></option>
				<option value="standart" <?php echo $this->tcSettings['woocommerce'] == 'standart' ? 'selected' : ''; ?>><?php _e( 'Format Kontrol', 'kolay-kimlik' ); ?></option>
				<option value="nvi" <?php echo $this->tcSettings['woocommerce'] == 'nvi' ? 'selected' : ''; ?>><?php _e( 'NVI Kontrol', 'kolay-kimlik' ); ?></option>
			</select>
		</p>
		<p class="description">
			<input type='checkbox' id='woocommerce' name='tcSettings[woocommerceRequired]' <?php echo $this->tcSettings['woocommerceRequired'] == 'on' ? 'checked' : ''; ?> />
			<label for='tcSettings[woocommerceRequired]'> Zorunlu Alan Olarak Ekle </label>
		</p>
		<p class="description">
		<?php
		_e(
			'Bu özellik ile WooCommerce ödeme sayfanıza TC kimlik giriş alanı ekleyebilirsiniz."Format Kontrol" girilen numaranın sadece TC kimlik numara algoritması ile kontrolünü sağlar.<br>
            "NVI Kontrol" seçildiğinde TC kimlik ve Doğum Yılı bölümü eklenecektir. Ad,Soyad,Doğum Yılı ve TC Kimlik alanlarına girilen bilgiler nufus müdürlüğü sistemine kayıtlı bilgler ile doğrulanır.<br>
            Her iki kontrolü de "Zorunlu Alan Olarak Ekle" seçeneği ile isteğe bağlı bırakabilir yada zorunlu kılabilirsiniz.',
			'kolay-kimlik'
		);
		?>
		 </p>
		<p>
		<p> <img src="<?php echo plugin_dir_url( __DIR__ ); ?>assets/woocommerce.png" alt="WooCommerce"></p>
		<input type='checkbox' id='woocommerceVergi' name='tcSettings[woocommerceVergi]' <?php echo $this->tcSettings['woocommerceVergi'] == 'on' ? 'checked' : ''; ?> />
		<label for='tcSettings[woocommerceRequired]'> Vergi Dairesi/No Alanı Ekle </label></p>
		<input type='checkbox' id='woocommerceRequiredVergiAlani' name='tcSettings[woocommerceRequiredVergi]' <?php echo $this->tcSettings['woocommerceRequiredVergi'] == 'on' ? 'checked' : ''; ?> />
		<label for='tcSettings[woocommerceRequired]'>Zorunlu Alan Olarak Ekle </label></p>
		<p> <img style="width: 400px; height: 60px;" src="<?php echo plugin_dir_url( __DIR__ ); ?>assets/woocommerceVergi.png" alt="WooCommerce"></p>
		<hr>
		<input type="submit" class="button button-primary" value="Ayarı Kaydet" />
		<?php
	}
	public function contactForm7CallBack() {
		?>
		<p class="description">
		<?php
		_e(
			'Bu özellik ile contact form eklentinize tckimlik,tckimlik nvi etiketleri eklenecektir.Bu etiketler ile formlarınıza tc kimlik sorgulama alanları ekleyebilirsiniz.<br>
            Nvi opsiyonlu etiket seçildiğinde 4 adet giriş bölümü açılacaktır. Ad,Soyad,Doğum Yılı ve TC Kimlik bu alanlara girilen bilgiler nufus müdürlüğü sistemine kayıtlı bilgler ile doğrulanır.<br>
            Her iki etiketide zorunlu kılma seçeneği formu düzenlediğiniz bölümde size sunulmuştur.',
			'kolay-kimlik'
		)
		?>
		</p>
		<p> <img src="<?php echo plugin_dir_url( __DIR__ ); ?>assets/contactform7.png" alt="WooCommerce"></p>
		<hr>
		<?php
	}
	public function ninjaFormCallBack() {
		echo '<p class="description">' . __( 'Bu seçenek çok yakında aktif edilecektir.', 'kolay-kimlik' ) . '</p>';
	}
	public function wpFormCallBack() {
		echo '<p class="description">' . __( 'Bu seçenek çok yakında aktif edilecektir.', 'kolay-kimlik' ) . '</p>';
	}
	public function adminSettingsPage() {
		?>
		<form method="POST" action="options.php">
			<?php
			settings_fields( 'tcSettings' );
			do_settings_sections( 'kolaykimlik-settings' );
			?>
		</form>
		<?php
	}
}
?>
