<?php
require_once 'functions-kontrol.php';

class KK_ContactForm_Seven {
	public function __construct() {
		add_action( 'wpcf7_init', array( $this, 'tag_ekle_tc_kimlik' ) );
		add_filter( 'wpcf7_validate_tc_kimlik*', array( $this, 'bilgi_kontrol_c_f7' ), 10, 2 );
		add_filter( 'wpcf7_messages', array( $this, 'hata_mesajlari' ), 10, 1 );
		add_action( 'wpcf7_admin_init', array( $this, 'sermaye_ekle' ), 15, 0 );
	}

	public function tag_ekle_tc_kimlik() {
		wpcf7_add_form_tag(
			array( 'tc_kimlik', 'tc_kimlik*' ),
			array( $this, 'tag_olustur' ),
			array(
				'name-attr' => true,
			)
		);
	}

	public function bilgi_kontrol_c_f7( $result, $tag ) {
		foreach ( $tag['options'] as $item ) {
			if ( 'nvi' === $item ) {
				$hasnvi = true;
			}
		}
		
		if ( $hasnvi ) {
			$data              = array();
			$name              = $tag->name;
			$tc                = isset( $_POST[ $name ] ) ? sanitize_text_field( $_POST[ $name ] ) : '';
			$data['tcno']      = $tc;
			$name_of_t_c       = isset( $_POST['name_of_t_c'] ) ? sanitize_text_field( $_POST['name_of_t_c'] ) : '';
			$data['isim']      = $name_of_t_c;
			$surname_of_t_c    = isset( $_POST['surname_of_t_c'] ) ? sanitize_text_field( $_POST['surname_of_t_c'] ) : '';
			$data['soyisim']   = $surname_of_t_c;
			$year_of_t_c       = isset( $_POST['year_of_t_c'] ) ? sanitize_text_field( $_POST['year_of_t_c'] ) : '';
			$data['dogumyili'] = $year_of_t_c;

			if ( 'tc_kimlik' === $tag->basetype ) {
				if ( $tag->is_required() && '' === $tc ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
				}
				if ( ! kk_standart_sorgulama( $tc ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_tc' ) );
				}
				if ( empty( $name_of_t_c ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_name' ) );
				}
				if ( empty( $surname_of_t_c ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_surname' ) );
				}
				if ( empty( $year_of_t_c ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_year' ) );
				}
				if ( ! is_numeric( $tc ) || ! is_numeric( $year_of_t_c ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_value' ) );
				}
			
				if ( false === kk_nvi_sorgulama( $data ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_data' ) );
				}
			}
		} else {
			$name = $tag->name;
			$tc = isset( $_POST[ $name ] ) ? sanitize_text_field( $_POST[ $name ] ) : '';

			if ( 'tc_kimlik' === $tag->basetype ) {
				if ( $tag->is_required() && '' === $tc ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
				}
				if ( ! is_numeric( $tc ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_value' ) );
				}
				if ( ! kk_standart_sorgulama( $tc ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_tc' ) );
				}
			}
		}

		return $result;
	}

	public function hata_mesajlari( $messages ) {
		$messages = array_merge(
			$messages,
			array(
				'invalid_tc'      => array(
					'description' =>
					__( 'Girilen değer, Tc Kimlik No formatında değildir.', 'kolay-kimlik' ),
					'default'     =>
					__( 'Girilen değer, Tc Kimlik No formatında değildir.', 'kolay-kimlik' ),
				),
				'invalid_name'    => array(
					'description' =>
					__( 'Ad Bilgisi Eksik Bırakılamaz.', 'kolay-kimlik' ),
					'default'     =>
					__( 'Ad Bilgisi Eksik Bırakılamaz.', 'kolay-kimlik' ),
				),
				'invalid_surname' => array(
					'description' =>
					__( 'Soyad Bilgisi Eksik Bırakılamaz.', 'kolay-kimlik' ),
					'default'     =>
					__( 'Soyad Bilgisi Eksik Bırakılamaz.', 'kolay-kimlik' ),
				),
				'invalid_year'    => array(
					'description' =>
					__( 'Yıl Bilgisi Eksik Bırakılamaz.', 'kolay-kimlik' ),
					'default'     =>
					__( 'Yıl Bilgisi Eksik Bırakılamaz.', 'kolay-kimlik' ),
				),
				'invalid_data'    => array(
					'description' =>
					__( 'Girilen Bilgiler Uyumsuz.', 'kolay-kimlik' ),
					'default'     =>
					__( 'Girilen Bilgiler Uyumsuz.', 'kolay-kimlik' ),
				),
				'invalid_value'   => array(
					'description' =>
					__( 'Yıl ve TC Kimlik Bilgileri Sadece Rakam İçerebilir.', 'kolay-kimlik' ),
					'default'     =>
					__( 'Yıl ve TC Kimlik Bilgileri Sadece Rakam İçerebilir.', 'kolay-kimlik' ),
				),
			)
		);
		return $messages;
	}
	public function tag_olustur( $tag ) {
		if ( empty( $tag->name ) ) {
			return '';
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );
		$class            = wpcf7_form_controls_class( $tag->type, 'wpcf7-tc' );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();

		$atts['size']      = 40;
		$atts['maxlength'] = 11;
		$atts['minlength'] = 11;

		$atts['class']       = $tag->get_class_option( $class );
		$atts['id']          = $tag->get_id_option();
		$atts['tabindex']    = $tag->get_option( 'tabindex', 'signed_int', true );
		$atts['placeholder'] = '';

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$value = (string) reset( $tag->values );

		$value = $tag->get_default_option( $value );

		$value = wpcf7_get_hangover( $tag->name, $value );

		$atts['value'] = $value;

		$atts['type'] = 'text';

		$atts['name'] = $tag->name;

		$atts = wpcf7_format_atts( $atts );

		foreach ( $tag['options'] as $item ) {
			if ( 'nvi' === $item ) {
				$hasnvi = true;
			}
		}

		$tag_valide = $tag->is_required();

		if ( $hasnvi && $tag_valide ) {
			$html = sprintf(
				'<label>Ad (required)</label><br>
                    <span class="wpcf7-form-control-wrap"><input type="text" name="name_of_t_c" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Soyad (required)</label><br>
                    <span class="wpcf7-form-control-wrap "><input type="text" name="surname_of_t_c" value="" size="40" class="wpcf7-form-control wpcf7-surname_of_t_c wpcf7-surname_of_t_c wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Doğum Yılı (required)</label><br>
                    <span class="wpcf7-form-control-wrap "><input type="text"  maxlength="4" name="year_of_t_c" value="" size="40"  class="wpcf7-form-control wpcf7-year_of_t_c wpcf7-surname_of_t_c wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Tc Kimlik Numarası(required)</label><br>
                    <span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span><br>',
				sanitize_html_class( $tag->name ),
				$atts,
				$validation_error
			);
		} elseif ( $hasnvi && ! $tag_valide ) {
			$html = sprintf(
				'<label>Ad</label><br>
                    <span class="wpcf7-form-control-wrap"><input type="text" name="name_of_t_c" value="" size="40" class="wpcf7-form-control  wpcf7-text wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Soyad</label><br>
                    <span class="wpcf7-form-control-wrap "><input type="text" name="surname_of_t_c" value="" size="40" class="wpcf7-form-control wpcf7-surname_of_t_c wpcf7-surname_of_t_c wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Doğum Yılı</label><br>
                    <span class="wpcf7-form-control-wrap "><input type="text" maxlength="4" name="year_of_t_c" value="" size="40"  class="wpcf7-form-control wpcf7-year_of_t_c wpcf7-surname_of_t_c wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Tc Kimlik Numarası</label><br>
                    <span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span><br>',
				sanitize_html_class( $tag->name ),
				$atts,
				$validation_error
			);
		} elseif ( ! $tag['options'] && $tag_valide ) {
			$html = sprintf(
				'<label>Tc Kimlik Numarası(required)</label><br>
                    <span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
				sanitize_html_class( $tag->name ),
				$atts,
				$validation_error
			);
		} else {
			$html = sprintf(
				'<label>Tc Kimlik Numarası</label><br>
                    <span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
				sanitize_html_class( $tag->name ),
				$atts,
				$validation_error
			);
		}
		return $html;
	}

	public function sermaye_ekle() {
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add(
			'tc_kimliknvi',
			__( 'tc_kimlik nvi', 'kolay-kimlik' ),
			array( $this, 'nvi_sema_olustur' )
		);
		$tag_generator->add(
			'tc_kimlik',
			__( 'tc_kimlik', 'kolay-kimlik' ),
			array( $this, 'sema_olustur' )
		);
	}

	public function nvi_sema_olustur( $args = '' ) {
		$args = wp_parse_args( $args, array() );
		$type = $args['id'];

		if ( ! in_array( $type, array( 'email', 'url', 'tel' ) ) ) {
			$type = 'tc_kimlik';
		}

		if ( 'tc_kimlik' === $type ) {
			$description = __( 'Bu etiket iletişim formunuza Ad,Soyad,Doğum Yılı ve Tc Kimlik girişi yapılabilecek alanlar ekler. (Uyumluluğu kontrol eder, uyumsuz bilgi girişini engeller. ', 'kolay-kimlik' );
		}

		$desc_link = wpcf7_link( __( 'https://contactform7.com/text-fields/', 'contact-form-7' ), __( 'Text fields', 'contact-form-7' ) ); ?>
		<div class="control-box">
			<fieldset>
				<legend><?php echo esc_html( $description ); ?></legend>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
									<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
								</fieldset>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" readonly name="name" class="tg-name oneline" id="tc-no" value="tc-no nvi" /></td>
						</tr>

						<?php if ( in_array( $type, array( 'text', 'email', 'url' ) ) ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Akismet', 'contact-form-7' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Akismet', 'contact-form-7' ) ); ?></legend>

										<?php if ( 'text' === $type ) : ?>
											<label>
												<input type="checkbox" name="akismet:author" class="option" />
												<?php echo esc_html( __( "This field requires author's name", 'contact-form-7' ) ); ?>
											</label>
										<?php elseif ( 'email' === $type ) : ?>
											<label>
												<input type="checkbox" name="akismet:author_email" class="option" />
												<?php echo esc_html( __( "This field requires author's email address", 'contact-form-7' ) ); ?>
											</label>
										<?php elseif ( 'url' === $type ) : ?>
											<label>
												<input type="checkbox" name="akismet:author_url" class="option" />
												<?php echo esc_html( __( "This field requires author's URL", 'contact-form-7' ) ); ?>
											</label>
										<?php endif; ?>

									</fieldset>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="<?php echo esc_attr( $type ); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
			</div>

			<br class="clear" />
			<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>">
																   <?php
																	sprintf(
																		'To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.',
																		'contact-form-7',
																		'<strong><span class="mail-tag"></span></strong>'
																	);
																	?>
			<input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
			<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html_e( 'To use the value input through this field in a mail field, you need to insert the corresponding mail-tag into the field on the Mail tab.', 'contact-form-7' ) ), esc_html_e( '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
		</div>
		<?php
	}

	public function sema_olustur( $args = '' ) {
		$args = wp_parse_args( $args, array() );
		$type = $args['id'];

		if ( ! in_array( $type, array( 'email', 'url', 'tel' ) ) ) {
			$type = 'tc_kimlik';
		}

		if ( 'tc_kimlik' === $type ) {
			$description = __( 'Bu etiket iletişim formunuza  Tc Kimlik girişi yapılabilecek alan ekler. (Girişin TC Kimlik No formatına uyumluluğunu kontrol eder, tutarsız rakam girişini engeller.) ', 'contact-form-7' );
		}

		$desc_link = wpcf7_link( __( 'https://contactform7.com/text-fields/', 'contact-form-7' ), __( 'Text fields', 'contact-form-7' ) );
		?>
		<div class="control-box">
			<fieldset>
				<legend><?php echo sprintf( esc_html( $description ), esc_html( $desc_link ) ); ?></legend>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
									<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
								</fieldset>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" readonly name="name" class="tg-name oneline" id="tc-no" value="tc-no" /></td>
						</tr>

						<?php if ( in_array( $type, array( 'text', 'email', 'url' ) ) ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Akismet', 'contact-form-7' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Akismet', 'contact-form-7' ) ); ?></legend>

										<?php if ( 'text' === $type ) : ?>
											<label>
												<input type="checkbox" name="akismet:author" class="option" />
												<?php echo esc_html( __( "This field requires author's name", 'contact-form-7' ) ); ?>
											</label>
										<?php elseif ( 'email' === $type ) : ?>
											<label>
												<input type="checkbox" name="akismet:author_email" class="option" />
												<?php echo esc_html( __( "This field requires author's email address", 'contact-form-7' ) ); ?>
											</label>
										<?php elseif ( 'url' === $type ) : ?>
											<label>
												<input type="checkbox" name="akismet:author_url" class="option" />
												<?php echo esc_html( __( "This field requires author's URL", 'contact-form-7' ) ); ?>
											</label>
										<?php endif; ?>

									</fieldset>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="<?php echo esc_attr( $type ); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
			</div>

			<br class="clear" />

			<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_attr( 'To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.' ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
		</div>
		<?php
	}
}

?>
