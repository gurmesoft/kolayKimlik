<?php
require_once ('functions-kontrol.php');
class ninjaForm
{   
    public function __construct(){

        add_filter( 'ninja_forms_submit_data', array($this,'bilgiKontrolNinjaForm'), 10, 2 );

    }

    public function bilgiKontrolNinjaForm( $form_data ) {

        foreach( $form_data[ 'fields' ] as $field ) { 
   
            if( 'tc_kimlik' == $field[ 'key' ] ) {
                $tc = $field[ 'value' ];
                if(! standartSorgulama($tc)) {
    
                    $errors = [
                        'fields' => [
                          'tc_kimlik' => __( 'Hatalı Tc No Formatı', 'tcinput' ),
                        ]
                      ];
                    
                    $response = [
                        'errors' => $errors,
                    ];
                    
                    echo wp_json_encode( $response );
                }              

            }          
             
        } 

        return $form_data;
    }     
}    