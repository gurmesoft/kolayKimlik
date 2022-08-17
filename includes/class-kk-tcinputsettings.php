<?php
class KK_TcInputSettings {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'get_option_page' ) );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 5 );
		$this->tc_settings = get_option( 'tc_settings' );
	}
	public function add_admin_menu() {
		add_menu_page( 'kolayKimlik', 'kolayKimlik', 'manage_options', 'kolaykimlik-settings' );
		add_submenu_page( 'kolaykimlik-settings', 'kolayKimlik', 'kolayKimlik', 'manage_options', 'kolaykimlik-settings', array( $this, 'admin_settings_page' ) );
	}

	public function settings_link( $links, $plugin_file ) {
		if ( 'kolaykimlik/kolaykimlik.php' === $plugin_file ) {
			$links[] = "<a href='" . admin_url( 'admin.php?page=kolaykimlik-settings' ) . "'>Ayarlar</a>";
		}
		return $links;
	}

	public function get_option_page() {
		add_settings_section(
			'defaultSection',
			__( 'kolayKimlik', 'kolay-kimlik' ),
			array( $this, 'default_section_call_back' ),
			'kolaykimlik-settings'
		);
		add_settings_field(
			'header',
			__( 'Etki Alanları<hr>', 'kolay-kimlik' ),
			array( $this, 'header_cb' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		add_settings_field(
			'woocommerce_enable',
			__( 'Aktif Et', 'kolay-kimlik' ),
			array( $this, 'woocommerce_enable' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		add_settings_field(
			'woocommerce',
			__( 'WooCommerce Ödeme Sayfası', 'kolay-kimlik' ),
			array( $this, 'woocommerce_call_back' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		add_settings_field(
			'contactForm7',
			__( 'Contact Form 7', 'kolay-kimlik' ),
			array( $this, 'contact_form_call_back' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		add_settings_field(
			'ninjaForm',
			__( 'Ninja Form', 'kolay-kimlik' ),
			array( $this, 'ninja_form_call_back' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		add_settings_field(
			'wpForms',
			__( 'WPForms', 'kolay-kimlik' ),
			array( $this, 'wp_form_call_back' ),
			'kolaykimlik-settings',
			'defaultSection'
		);
		register_setting( 'tc_settings', 'tc_settings', array( $this, 'emptyCallBack' ) );
	}
	public function default_section_call_back() {
		echo '<h3><p>' . esc_html( __( 'Bu eklenti ile sitenizde kullandığınız iletişim formu eklentilerine, ödeme sayfalarına TC kimlik numarası alanı ekleyebilir.Girilen bilgilerin doğruluğunu kontrol edebilirsiniz.', 'kolay-kimlik' ) ) . '<h3></p>';
		echo '<p class="description">' . esc_html( __( 'kolay-kimlik' ) ) . '</p><br>';
	}
	public function header_cb() {
		echo '<p>' . esc_html( __( 'Nasıl Kullanırım ?', 'kolay-kimlik' ) ) . '</p><hr>';
	}
	public function woocommerce_enable() {
		?>
		<input type='checkbox' id='woocommerce' name='tc_settings[enabled]' <?php echo 'on' === @$this->tc_settings['enabled'] ? 'checked' : ''; ?> />
		<?php
	}
	public function woocommerce_call_back() {
		?>
		<p>
			<select name="tc_settings[woocommerce]" id="woocommerce">
				<option value="none" <?php echo 'none' === @$this->tc_settings['woocommerce'] ? 'selected' : ''; ?>><?php esc_attr_e( 'Kapalı', 'kolay-kimlik' ); ?></option>
				<option value="standart" <?php echo 'standart' === @$this->tc_settings['woocommerce'] ? 'selected' : ''; ?>><?php esc_attr_e( 'Format Kontrol', 'kolay-kimlik' ); ?></option>
				<option value="nvi" <?php echo 'nvi' === @$this->tc_settings['woocommerce'] ? 'selected' : ''; ?>><?php esc_attr_e( 'NVI Kontrol', 'kolay-kimlik' ); ?></option>
			</select>
		</p>
		<p class="description">
			<input type='checkbox' id='woocommerce' name='tc_settings[woocommerceRequired]' <?php echo 'on' === @$this->tc_settings['woocommerceRequired'] ? 'checked' : ''; ?> />
			<label for='tc_settings[woocommerceRequired]'> Zorunlu Alan Olarak Ekle </label>
		</p>
		<p class="description">

		<?php
			esc_attr_e( 'Bu özellik ile WooCommerce ödeme sayfanıza TC kimlik giriş alanı ekleyebilirsiniz."Format Kontrol" girilen numaranın sadece TC kimlik numara algoritması ile kontrolünü sağlar.', 'kolay-kimlik' );
		?>
		<br/>
		<?php
			esc_attr_e( '"NVI Kontrol" seçildiğinde TC kimlik ve Doğum Yılı bölümü eklenecektir. Ad,Soyad,Doğum Yılı ve TC Kimlik alanlarına girilen bilgiler nufus müdürlüğü sistemine kayıtlı bilgler ile doğrulanır.', 'kolay-kimlik' );
		?>
		<br/>
		<?php
			esc_attr_e( 'Her iki kontrolü de "Zorunlu Alan Olarak Ekle" seçeneği ile isteğe bağlı bırakabilir yada zorunlu kılabilirsiniz.', 'kolay-kimlik' );
		?>
	</p>
		<p>
		<p> <img src="<?php echo esc_url( plugin_dir_url( __DIR__ ) . 'assets/woocommerce.png' ); ?>" alt="WooCommerce"></p>
		<input type='checkbox' id='woocommerceVergi' name='tc_settings[woocommerceVergi]' <?php echo 'on' === @$this->tc_settings['woocommerceVergi'] ? 'checked' : ''; ?> />
		<label for='tc_settings[woocommerceRequired]'> Vergi Dairesi/No Alanı Ekle </label></p>
		<input type='checkbox' id='woocommerceRequiredVergiAlani' name='tc_settings[woocommerceRequiredVergi]' <?php echo 'on' === @$this->tc_settings['woocommerceRequiredVergi'] ? 'checked' : ''; ?> />
		<label for='tc_settings[woocommerceRequired]'>Zorunlu Alan Olarak Ekle </label></p>
		<p> <img style="width: 400px; height: 60px;" src="<?php echo esc_url( plugin_dir_url( __DIR__ ) . 'assets/woocommerceVergi.png' ); ?>" alt="WooCommerce"></p>
		<hr>
		<input type="submit" class="button button-primary" value="Ayarı Kaydet" />
		<?php
	}
	public function contact_form_call_back() {
		?>
		<p class="description">
		<?php
			esc_attr_e( 'Bu özellik ile contact form eklentinize tc_kimlik,tc_kimlik nvi etiketleri eklenecektir.Bu etiketler ile formlarınıza tc kimlik sorgulama alanları ekleyebilirsiniz.', 'kolay-kimlik' );
		?>
		<br/>
		<?php
			esc_attr_e( 'Nvi opsiyonlu etiket seçildiğinde 4 adet giriş bölümü açılacaktır. Ad,Soyad,Doğum Yılı ve TC Kimlik bu alanlara girilen bilgiler nufus müdürlüğü sistemine kayıtlı bilgler ile doğrulanır.', 'kolay-kimlik' );
		?>
		<br/>
		<?php
			esc_attr_e( 'Her iki etiketide zorunlu kılma seçeneği formu düzenlediğiniz bölümde size sunulmuştur.', 'kolay-kimlik' );
		?>
		</p>
		<p> <img src="<?php echo esc_url( plugin_dir_url( __DIR__ ) . 'assets/contactform7.png' ); ?>" alt="WooCommerce"></p>
		<hr>
		<?php
	}
	public function ninja_form_call_back() {
		echo '<p class="description">' . esc_html( __( 'Bu seçenek çok yakında aktif edilecektir.', 'kolay-kimlik' ) ) . '</p>';
	}
	public function wp_form_call_back() {
		echo '<p class="description">' . esc_html( __( 'Bu seçenek çok yakında aktif edilecektir.', 'kolay-kimlik' ) ) . '</p>';
	}
	public function admin_settings_page() {
		?>
		<form method="POST" action="options.php">
			<?php
			settings_fields( 'tc_settings' );
			do_settings_sections( 'kolaykimlik-settings' );
			?>
		</form>
		<?php
	}
}
?>
