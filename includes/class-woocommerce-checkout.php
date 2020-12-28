<?php
require_once ('functions-kontrol.php');
class wooCheckOut{

    public function __construct(){
        $this->tcSettings = get_option('tcSettings');
        if($this->tcSettings['woocommerce']=='none'){
            return;
        }
        add_filter('woocommerce_checkout_fields' , array($this,'yeniAlanlarEkle'));       
        add_action('woocommerce_after_checkout_validation', array($this,'hataEkle'), 10, 2);       
    }
    function yeniAlanlarEkle($alanlar) {
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
    }
}
?>