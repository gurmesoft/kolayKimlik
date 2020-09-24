<?php

class tcinputSettings
{
    public function __construct(){

        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_filter('plugin_action_links',array($this,"settingsLink"),10,5); 

    }

    public function addAdminMenu(){
        add_menu_page('TC INPUT','Tc Input Settings','manage_options','tc-input-settings', array($this,'getOptionPage'));
    }

    public function settingsLink($links,$plugin_file){
        if($plugin_file=="tcinput/tcinput.php"){
            $links[]="<a href='".admin_url("admin.php?page=tc-input-settings")."'>Settings</a>";
        }
        return $links;
    }
    
    public function getOptionpage(){
        echo "Ayarlar Sayfası";
    }
}    
?>