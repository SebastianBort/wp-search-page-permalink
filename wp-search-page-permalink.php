<?php
/*
Plugin Name: SEO permalink dla strony wyszukiwania
Description: Tworzy przepisany permalink dla strony z wyszukiwaniem. Przykładowo, zamiast "domena.pl/?s=fraza" link wyszukiwania wygląda "domena.pl/wyszukiwarka/fraza".
Version: 1.0.0
Author: Sebastian Bort
*/

class WP_Search_Page_Permalink {

        const config_key = 'search_page_permalink';
        const default_value = 'wyszukiwarka';

        private $alias;

        public function __construct() {
                
                add_action('init', [$this, 'load_plugin_settings']);                  
                add_action('load-options-permalink.php', [$this, 'handle_plugin_settings']);      
                add_action('template_redirect', [$this, 'redirect_to_permalink']);
        } 

        private function get_search_alias() {
        
                if(empty($this->alias)) {
                    $this->alias = get_option(self::config_key, self::default_value);
                }    
                return $this->alias;
        }

        public function load_plugin_settings() {
                
                global $wp_rewrite;
            	$wp_rewrite->search_base = $this->get_search_alias();
        }
        
        public function handle_plugin_settings() {
                
                if(!empty($_POST[self::config_key])) {
                       update_option(self::config_key, sanitize_text_field($_POST[self::config_key]), false);   
                } 
                add_settings_field(self::config_key, 'Alias strony wyszukiwania', [$this, 'display_plugin_settings'], 'permalink', 'optional'); 
        } 

        public function display_plugin_settings() {
                	
	            echo sprintf('<input type="text" value="%s" name="%s" class="regular-text">', esc_attr($this->get_search_alias()), self::config_key);
        }

        public function redirect_to_permalink() {
                
               	global $wp_rewrite;
                if(!is_search() || strpos($_SERVER['REQUEST_URI'], "/{$wp_rewrite->search_base}/") !== false) {
                        return false;
                }
                
                $permalink = sprintf('%s/%s/%s', get_home_url(), $wp_rewrite->search_base, urlencode(get_query_var('s')));
                wp_redirect($permalink, 301);
                exit;
        }                
}

new WP_Search_Page_Permalink();

?>