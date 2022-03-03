<?php
require_once ('functions-kontrol.php');
class wooCheckOut{

    public function __construct(){
        $this->tcSettings = get_option('tcSettings');
        if(isset($this->tcSettings['enabled'])){
            add_filter('woocommerce_checkout_fields' , array($this,'yeniAlanlarEkle'));       
            add_action('woocommerce_after_checkout_validation', array($this,'hataEkle'), 10, 2);
            add_action( 'woocommerce_admin_order_data_after_billing_address',array($this,"siparisFaturaBilgileri"));
        }

    }
    function siparisFaturaBilgileri($siparis){
        $musteri = $siparis->get_customer_id();
        echo '<div class="address"><p><strong>TC Kimlik No:</strong> '. get_user_meta($musteri,'billing_tckimlik',true).'</p>';
        echo '<p><strong>Vergi Dairesi:</strong> '. get_user_meta($musteri,'billing_vergiDairesi',true).'</p>';
        echo '<p><strong>Vergi No:</strong> '. get_user_meta($musteri,'billing_vergiNo',true).'</p></div>';
    }
    function yeniAlanlarEkle($alanlar){
        if($this->tcSettings['woocommerce'] != 'none'){
            $alanlar['billing']['billing_tckimlik'] = array(
                'type' => 'text',            
                'required'=> ( $this->tcSettings['woocommerceRequired']=='on' ? true : false ),            
                'class' =>( $this->tcSettings['woocommerce']=='standart' ? array('my-field-class form-row-wide') : array('my-field-class form-row-first')) , 
                'label' => __('TC Kimlik No','tcinput'),
                'placeholder' => __(''),
                'priority' => 21
            );
            if($this->tcSettings['woocommerce']=='nvi'){
                $alanlar['billing']['billing_dogumYili']=array(
                    'type' => 'text',
                    'maxlength' => 4,
                    'required'=> ( $this->tcSettings['woocommerceRequired']=='on' ? true : false ),             
                    'class' => array('my-field-class form-row-last'),         
                    'label' => __('Doğum Yılı','tcinput'),
                    'placeholder' => __(''),
                    'priority' => 22
                );
            }
        }

        if ($this->tcSettings["woocommerceVergi"] == "on") {
            $alanlar['billing']['billing_vergiDairesi'] = array(
                'type' => 'text',
                'required' => ($this->tcSettings['woocommerceRequiredVergi'] == 'on' ? true : false),
                'class' => array('my-field-class form-row-first'),
                'label' => __('Vergi Dairesi', 'tcinput'),
                'placeholder' => __(''),
                'priority' => 23
            );
            $alanlar['billing']['billing_vergiNo'] = array(
                'type' => 'text',
                'required' => ($this->tcSettings['woocommerceRequiredVergi'] == 'on' ? true : false),
                'class' => array('my-field-class form-row-last'),
                'label' => __('Vergi No', 'tcinput'),
                'placeholder' => __(''),
                'priority' => 24
            );
        }
        return $alanlar;
    }
    function hataEkle( $fields, $errors ){        
        if($this->tcSettings['woocommerce']=='standart' && $this->tcSettings['woocommerceRequired']=='on' ){
            if (empty($_POST['billing_tckimlik']) ){
                $customError = '<strong>Fatura TC Kimlik Numarasi</strong> gerekli bir alandır.';					
                $errors->add( 'validation', $customError );
            } 
            if (! is_numeric($_POST['billing_tckimlik']) ){
                $customError = '<strong>Fatura TC Kimlik Numarasi</strong> sadece rakam içerebilir.';					
                $errors->add( 'validation', $customError );
            } 
            if (! standartSorgulama($_POST['billing_tckimlik']) && ! empty($_POST['billing_tckimlik'])){
                $customError = '<strong>Fatura TC Kimlik Numarasi</strong> uyumsuz formattadir.';					
                $errors->add( 'validation', $customError );
            }
        }else if($this->tcSettings['woocommerce']=='nvi' && $this->tcSettings['woocommerceRequired']=='on' ){
            $data=array(            
            'tcno'=>$_POST['billing_tckimlik'],
            'isim'=>$_POST['billing_first_name'],
            'soyisim'=>$_POST['billing_last_name'],
            'dogumyili'=>$_POST['billing_dogumYili'],
            );            
            if (empty($_POST['billing_tckimlik']) ){
                $customError = '<strong>Fatura TC Kimlik Numarasi</strong> gerekli bir alandır.';					
                $errors->add( 'validation', $customError );
            }    
            if (empty($_POST['billing_dogumYili']) ){
                $customError = '<strong>Fatura Fatura Doğum Yılı</strong> gerekli bir alandır.';					
                $errors->add( 'validation', $customError );
            }
            if (! is_numeric($_POST['billing_tckimlik']) OR ! is_numeric($_POST['billing_dogumYili']) ){
                $customError = '<strong>Fatura  TC Kimlik Numarasi ve Doğum Yılı</strong> sadece rakam içerebilir.';					
                $errors->add( 'validation', $customError );
            } 
            if (nviSorgulama($data)=='false'){
                $customError = '<strong>Fatura Kimlik Bilgileri Uyumsuz!</strong>';					
                $errors->add( 'validation', $customError );
            }
        }else if($this->tcSettings['woocommerce']=='standart' && $this->tcSettings['woocommerceRequired']== null ){
            //Otomatik doldurma secenegi eklenebilir
        }
        if ($this->tcSettings["woocommerceVergi"]== "on" and $this->tcSettings["woocommerceRequiredVergi"]== "on"){
            if(empty($_POST['billing_vergiDairesi'])){
                $hataMesaji = '<strong>Fatura Vergi Dairesi</strong> gerekli bir alandır.';
                $errors->add( 'validation', $hataMesaji );
            }
            if(empty($_POST['billing_vergiNo'])) {
                $hataMesaji = '<strong>Fatura Vergi No</strong> gerekli bir alandır.';
                $errors->add('validation', $hataMesaji);
            }
            if(!is_numeric($_POST['billing_vergiNo'])) {
                $hataMesaji = '<strong>Fatura Vergi No</strong> sadece rakam içermelidir.';
                $errors->add('validation', $hataMesaji);
            }
            if(strlen($_POST['billing_vergiNo'])!=10) {
                $hataMesaji = '<strong>Fatura Vergi No</strong> 10 haneli olmalıdır.';
                $errors->add('validation', $hataMesaji);
            }
            if(vergiKontrol($_POST['billing_vergiNo'])){
                $customError = '<strong>Vergi Numarası</strong> geçersizdir.';					
                $errors->add( 'validation', $customError );
            }
        }
    }
}
?>