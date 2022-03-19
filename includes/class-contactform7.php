<?php
require_once('functions-kontrol.php');

class Wk_contactFormSeven
{
    public function __construct()
    {

        add_action('wpcf7_init', array($this, 'tagEkleTcKimlik'));
        add_filter('wpcf7_validate_tckimlik*', array($this, 'bilgiKontrolCF7'), 10, 2);
        add_filter('wpcf7_messages', array($this, 'hataMesajlari'), 10, 1);
        add_action('wpcf7_admin_init', array($this, 'semayaEkle'), 15, 0);
    }

    public function tagEkleTcKimlik()
    {

        wpcf7_add_form_tag(
            array('tckimlik', 'tckimlik*'),
            array($this, 'tagOlustur'),
            array(
                'name-attr' => true,
            )
        );
    }
    public function bilgiKontrolCF7($result, $tag)
    {
        foreach ($tag['options'] as $item) {

            if ($item == 'nvi') {

                $hasnvi = true;
            }
        }
        if ($hasnvi == true) {
            $data = array();
            $name = $tag->name;
            $tc = isset($_POST[$name]) ? trim(wp_unslash(strtr((string) $_POST[$name], "\n", " "))) : '';
            $data['tcno'] = $tc;
            $nameOfTC = isset($_POST['nameoftc']) ? trim(wp_unslash(strtr((string) $_POST['nameoftc'], "\n", " "))) : '';
            $data['isim'] = $nameOfTC;
            $surnameOfTC = isset($_POST['surnameoftc']) ? trim(wp_unslash(strtr((string) $_POST['surnameoftc'], "\n", " "))) : '';
            $data['soyisim'] = $surnameOfTC;
            $yearOfTC = isset($_POST['yearoftc']) ? trim(wp_unslash(strtr((string) $_POST['yearoftc'], "\n", " "))) : '';
            $data['dogumyili'] = $yearOfTC;

            if ('tckimlik' == $tag->basetype) {

                if ($tag->is_required() and '' === $tc) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_required'));
                }
                if (standartSorgulama($tc) == false) {

                    $result->invalidate($tag, wpcf7_get_message('invalid_tc'));
                }
                if (empty($nameOfTC)) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_name'));
                }
                if (empty($surnameOfTC)) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_surname'));
                }
                if (empty($yearOfTC)) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_year'));
                }
                if (!is_numeric($tc) or !is_numeric($yearOfTC)) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_value'));
                }
                if (nviSorgulama($data) == 'false') {
                    $result->invalidate($tag, wpcf7_get_message('invalid_data'));
                }
            }
        } else {

            $name = $tag->name;
            $tc = isset($_POST[$name]) ? trim(wp_unslash(strtr((string) $_POST[$name], "\n", " "))) : '';

            if ('tckimlik' == $tag->basetype) {
                if ($tag->is_required() and '' === $tc) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_required'));
                }
                if (!is_numeric($tc)) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_value'));
                }
                if (standartSorgulama($tc) == false) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_tc'));
                }
            }
        }

        return $result;
    }

    public function hataMesajlari($messages)
    {
        $messages = array_merge($messages, array(
            'invalid_tc' => array(
                'description' =>
                __("Girilen değer, Tc Kimlik No formatında değildir.", 'tcinput'),
                'default' =>
                __("Girilen değer, Tc Kimlik No formatında değildir.", 'tcinput'),
            ),
            'invalid_name' => array(
                'description' =>
                __("Ad Bilgisi Eksik Bırakılamaz.", 'tcinput'),
                'default' =>
                __("Ad Bilgisi Eksik Bırakılamaz.", 'tcinput'),
            ),
            'invalid_surname' => array(
                'description' =>
                __("Soyad Bilgisi Eksik Bırakılamaz.", 'tcinput'),
                'default' =>
                __("Soyad Bilgisi Eksik Bırakılamaz.", 'tcinput'),
            ),
            'invalid_year' => array(
                'description' =>
                __("Yıl Bilgisi Eksik Bırakılamaz.", 'tcinput'),
                'default' =>
                __("Yıl Bilgisi Eksik Bırakılamaz.", 'tcinput'),
            ),
            'invalid_data' => array(
                'description' =>
                __("Girilen Bilgiler Uyumsuz.", 'tcinput'),
                'default' =>
                __("Girilen Bilgiler Uyumsuz.", 'tcinput'),
            ),
            'invalid_value' => array(
                'description' =>
                __("Yıl ve TC Kimlik Bilgileri Sadece Rakam İçerebilir.", 'tcinput'),
                'default' =>
                __("Yıl ve TC Kimlik Bilgileri Sadece Rakam İçerebilir.", 'tcinput'),
            )
        ));
        return $messages;
    }
    public function tagOlustur($tag)
    {

        if (empty($tag->name)) {
            return '';
        }

        $validation_error = wpcf7_get_validation_error($tag->name);
        $class = wpcf7_form_controls_class($tag->type, 'wpcf7-tc');


        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = array();

        $atts['size'] = 40;
        $atts['maxlength'] = 11;
        $atts['minlength'] = 11;

        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->get_id_option();
        $atts['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $atts["placeholder"] = "";

        if ($tag->is_required()) {
            $atts['aria-required'] = 'true';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $value = (string) reset($tag->values);

        $value = $tag->get_default_option($value);

        $value = wpcf7_get_hangover($tag->name, $value);

        $atts['value'] = $value;

        if (wpcf7_support_html5()) {
            $atts['type'] = 'text';
        } else {
            $atts['type'] = 'text';
        }

        $atts['name'] = $tag->name;

        $atts = wpcf7_format_atts($atts);

        foreach ($tag['options'] as $item) {

            if ($item == 'nvi') {

                $hasnvi = true;
            }
        }

        if ($hasnvi == true &&  $tag->is_required() ==  true) {
            $html = sprintf(
                '<label>Ad (required)</label><br>
                    <span class="wpcf7-form-control-wrap"><input type="text" name="nameoftc" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Soyad (required)</label><br>
                    <span class="wpcf7-form-control-wrap "><input type="text" name="surnameoftc" value="" size="40" class="wpcf7-form-control wpcf7-surnameoftc wpcf7-surnameoftc wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Doğum Yılı (required)</label><br>
                    <span class="wpcf7-form-control-wrap "><input type="text"  maxlength="4" name="yearoftc" value="" size="40"  class="wpcf7-form-control wpcf7-yearoftc wpcf7-surnameoftc wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Tc Kimlik Numarası(required)</label><br>
                    <span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span><br>',
                sanitize_html_class($tag->name),
                $atts,
                $validation_error
            );
        } else if ($hasnvi == true &&  $tag->is_required() == false) {
            $html = sprintf(
                '<label>Ad</label><br>
                    <span class="wpcf7-form-control-wrap"><input type="text" name="nameoftc" value="" size="40" class="wpcf7-form-control  wpcf7-text wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Soyad</label><br>
                    <span class="wpcf7-form-control-wrap "><input type="text" name="surnameoftc" value="" size="40" class="wpcf7-form-control wpcf7-surnameoftc wpcf7-surnameoftc wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Doğum Yılı</label><br>
                    <span class="wpcf7-form-control-wrap "><input type="text" maxlength="4" name="yearoftc" value="" size="40"  class="wpcf7-form-control wpcf7-yearoftc wpcf7-surnameoftc wpcf7-validates-as-required" aria-required="true" aria-invalid="false"></span><br>
                    <label> Tc Kimlik Numarası</label><br>
                    <span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span><br>',
                sanitize_html_class($tag->name),
                $atts,
                $validation_error
            );
        } else if ($tag['options'] == null &&  $tag->is_required() == true) {
            $html = sprintf(
                '<label>Tc Kimlik Numarası(required)</label><br>
                    <span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
                sanitize_html_class($tag->name),
                $atts,
                $validation_error
            );
        } else {
            $html = sprintf(
                '<label>Tc Kimlik Numarası</label><br>
                    <span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
                sanitize_html_class($tag->name),
                $atts,
                $validation_error
            );
        }
        return $html;
    }

    public function semayaEkle()
    {
        $tag_generator = WPCF7_TagGenerator::get_instance();
        $tag_generator->add(
            'tckimliknvi',
            __('tckimlik nvi', 'tcinput'),
            array($this, 'nviSemaOlustur')
        );
        $tag_generator->add(
            'tckimlik',
            __('tckimlik', 'tcinput'),
            array($this, 'SemaOlustur')
        );
    }

    public function nviSemaOlustur($contact_form, $args = '')
    {
        $args = wp_parse_args($args, array());
        $type = $args['id'];

        if (!in_array($type, array('email', 'url', 'tel'))) {
            $type = 'tckimlik';
        }

        if ('tckimlik' == $type) {
            $description = __("Bu etiket iletişim formunuza Ad,Soyad,Doğum Yılı ve Tc Kimlik girişi yapılabilecek alanlar ekler. (Uyumluluğu kontrol eder, uyumsuz bilgi girişini engeller.) ", 'contact-form-7');
        }

        $desc_link = wpcf7_link(__('https://contactform7.com/text-fields/', 'contact-form-7'), __('Text fields', 'contact-form-7'));

?>
        <div class="control-box">
            <fieldset>
                <legend><?php echo sprintf(esc_html($description), $desc_link); ?></legend>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo esc_html(__('Field type', 'contact-form-7')); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html(__('Field type', 'contact-form-7')); ?></legend>
                                    <label><input type="checkbox" name="required" /> <?php echo esc_html(__('Required field', 'contact-form-7')); ?></label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'contact-form-7')); ?></label></th>
                            <td><input type="text" readonly name="name" class="tg-name oneline" id="tc-no" value="tc-no nvi" /></td>
                        </tr>

                        <?php if (in_array($type, array('text', 'email', 'url'))) : ?>
                            <tr>
                                <th scope="row"><?php echo esc_html(__('Akismet', 'contact-form-7')); ?></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text"><?php echo esc_html(__('Akismet', 'contact-form-7')); ?></legend>

                                        <?php if ('text' == $type) : ?>
                                            <label>
                                                <input type="checkbox" name="akismet:author" class="option" />
                                                <?php echo esc_html(__("This field requires author's name", 'contact-form-7')); ?>
                                            </label>
                                        <?php elseif ('email' == $type) : ?>
                                            <label>
                                                <input type="checkbox" name="akismet:author_email" class="option" />
                                                <?php echo esc_html(__("This field requires author's email address", 'contact-form-7')); ?>
                                            </label>
                                        <?php elseif ('url' == $type) : ?>
                                            <label>
                                                <input type="checkbox" name="akismet:author_url" class="option" />
                                                <?php echo esc_html(__("This field requires author's URL", 'contact-form-7')); ?>
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
            <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'contact-form-7')); ?>" />
            </div>

            <br class="clear" />

            <p class="description mail-tag"><label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>"><?php echo sprintf(esc_html(__("To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7')), '<strong><span class="mail-tag"></span></strong>'); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" /></label></p>
        </div>
    <?php
    }

    public function SemaOlustur($contact_form, $args = '')
    {
        $args = wp_parse_args($args, array());
        $type = $args['id'];

        if (!in_array($type, array('email', 'url', 'tel'))) {
            $type = 'tckimlik';
        }

        if ('tckimlik' == $type) {
            $description = __("Bu etiket iletişim formunuza  Tc Kimlik girişi yapılabilecek alan ekler. (Girişin TC Kimlik No formatına uyumluluğunu kontrol eder, tutarsız rakam girişini engeller.) ", 'contact-form-7');
        }

        $desc_link = wpcf7_link(__('https://contactform7.com/text-fields/', 'contact-form-7'), __('Text fields', 'contact-form-7'));

    ?>
        <div class="control-box">
            <fieldset>
                <legend><?php echo sprintf(esc_html($description), $desc_link); ?></legend>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo esc_html(__('Field type', 'contact-form-7')); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html(__('Field type', 'contact-form-7')); ?></legend>
                                    <label><input type="checkbox" name="required" /> <?php echo esc_html(__('Required field', 'contact-form-7')); ?></label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'contact-form-7')); ?></label></th>
                            <td><input type="text" readonly name="name" class="tg-name oneline" id="tc-no" value="tc-no" /></td>
                        </tr>

                        <?php if (in_array($type, array('text', 'email', 'url'))) : ?>
                            <tr>
                                <th scope="row"><?php echo esc_html(__('Akismet', 'contact-form-7')); ?></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text"><?php echo esc_html(__('Akismet', 'contact-form-7')); ?></legend>

                                        <?php if ('text' == $type) : ?>
                                            <label>
                                                <input type="checkbox" name="akismet:author" class="option" />
                                                <?php echo esc_html(__("This field requires author's name", 'contact-form-7')); ?>
                                            </label>
                                        <?php elseif ('email' == $type) : ?>
                                            <label>
                                                <input type="checkbox" name="akismet:author_email" class="option" />
                                                <?php echo esc_html(__("This field requires author's email address", 'contact-form-7')); ?>
                                            </label>
                                        <?php elseif ('url' == $type) : ?>
                                            <label>
                                                <input type="checkbox" name="akismet:author_url" class="option" />
                                                <?php echo esc_html(__("This field requires author's URL", 'contact-form-7')); ?>
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
            <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'contact-form-7')); ?>" />
            </div>

            <br class="clear" />

            <p class="description mail-tag"><label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>"><?php echo sprintf(esc_html(__("To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7')), '<strong><span class="mail-tag"></span></strong>'); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" /></label></p>
        </div>
<?php
    }
}

?>