<?php

class WooKimlikOtomatikGuncelleme{
    private $plugin;
    private $slug;
    private $protocol;
    private $instance;
    private $apiUrl="https://gurmewoo.com/index.php";
    private $apiVersion="1.1";
    public function __construct($slug,$plugin){
        $this->protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $this->instance=str_replace($this->protocol, "", get_bloginfo('wpurl'));
        $this->eklentiBilgisi=get_file_data(WP_PLUGIN_DIR ."/".$plugin, [
            'Version' => 'Version',
            'Name' => 'Plugin Name'
        ], 'plugin');
        $this->plugin=$plugin;
        $this->slug=$slug;
        $this->productId=strtoupper($this->eklentiBilgisi["Name"]);
        $this->aktifLisans=get_option($this->productId."Lisans");
        //var_dump($this->aktifLisans);
    }


    public function check_for_plugin_update($checked_data)
    {
        if(!isset($this->aktifLisans["LisansKodu"])){
            return $checked_data;
        }
        if ( !is_object( $checked_data ) ||  ! isset ( $checked_data->response ) )
            return $checked_data;

        $request_string = $this->prepare_request('plugin_update');
        if($request_string === FALSE)
            return $checked_data;

        global $wp_version;

        // Start checking for an update
        $request_uri = $this->apiUrl . '?' . http_build_query( $request_string , '', '&');

        //check if cached
        $data  =   get_site_transient( $this->productId.'_plugin_update_'. md5( $request_uri ) );
        if  ( $data    === FALSE )
        {
            $data = wp_remote_get( $request_uri, array(
                'timeout'     => 20,
                'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
            ) );

            if(is_wp_error( $data ) || $data['response']['code'] != 200)
                return $checked_data;

            set_site_transient( $this->productId.'_plugin_update_' . md5( $request_uri ), $data, 60 * 60 * 4 );

        }

        $response_block = json_decode($data['body']);

        if(!is_array($response_block) || count($response_block) < 1)
            return $checked_data;

        //retrieve the last message within the $response_block
        $response_block = $response_block[count($response_block) - 1];
        $response = isset($response_block->message) ? $response_block->message : '';

        if (is_object($response) && !empty($response)) // Feed the update data into WP updater
        {
            $response  =   $this->postprocess_response( $response );

            $checked_data->response[$this->plugin] = $response;
        }

        return $checked_data;
    }

    public function plugins_api_call($def, $action, $args)
    {
        if (!is_object($args) || !isset($args->slug) || $args->slug != $this->slug)
            return $def;

        $request_string = $this->prepare_request($action, $args);
        if($request_string === FALSE)
            return new WP_Error('plugins_api_failed', __('Eklenti tanımlaması sırasında problem oluştu.' , 'gurmewoo') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Tekrar Deneyin', 'gurmewoo' ) .'&lt;/a>');;

        global $wp_version;

        $request_uri = $this->apiUrl . '?' . http_build_query( $request_string , '', '&');
        $data = wp_remote_get( $request_uri, array(
            'timeout'     => 20,
            'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
        ) );

        if(is_wp_error( $data ) || $data['response']['code'] != 200)
            return new WP_Error('plugins_api_failed', __('API çağrısı sırasında HTTP isteğinde problem oldu' , 'gurmewoo') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Tekrar Deneyin', 'gurmewoo' ) .'&lt;/a>', $data->get_error_message());

        $response_block = json_decode($data['body']);
        $response_block = $response_block[count($response_block) - 1];
        $response = $response_block->message;

        if (is_object($response) && !empty($response))
        {
            $response  =   $this->postprocess_response( $response );

            return $response;
        }
    }


    public function prepare_request($action, $args = array())
    {
        global $wp_version;

        return array(
            'woo_sl_action'         => $action,
            'version'               => $this->eklentiBilgisi["Version"],
            'product_unique_id'     => $this->productId,
            'licence_key'           => $this->aktifLisans["LisansKodu"],
            'domain'                => $this->instance,
            'wp-version'            => $wp_version,

            'api_version'           => $this->apiVersion
        );
    }

    private function postprocess_response( $response )
    {
        //include slug and plugin data
        $response->slug    =   $this->slug;
        $response->plugin  =   $this->plugin;

        //if sections are being set
        if ( isset ( $response->sections ) )
            $response->sections = (array)$response->sections;

        //if banners are being set
        if ( isset ( $response->banners ) )
            $response->banners = (array)$response->banners;

        //if icons being set, convert to array
        if ( isset ( $response->icons ) )
            $response->icons    =   (array)$response->icons;

        return $response;

    }
}

function wooKimlikPlusGuncelleme()
{
    $otoGuncelleme = new WooKimlikOtomatikGuncelleme('wookimlik', "wookimlik/wookimlik.php");
    add_filter('pre_set_site_transient_update_plugins', array(
        $otoGuncelleme,
        'check_for_plugin_update'
    ));
    add_filter('plugins_api', array(
        $otoGuncelleme,
        'plugins_api_call'
    ) , 10, 3);
}

add_action('after_setup_theme', 'wooKimlikPlusGuncelleme');