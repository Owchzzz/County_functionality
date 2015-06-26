<?php
require_once('county-loader.php');
require_once('county-frontend.php');
if(!class_exists('TC_County_loader')) {
	class TC_County_loader {
		protected $version;
		protected $table;
		
		protected $county_loader;
		protected $county_frontend;
		public function __construct($version,$table) {
			$this->version = $version;
			$this->table = $table;
			$this->county_loader = new tc_county_loader_spec($table);
			$this->county_frontened = new tc_county_frontend($table);
			add_action('init',array($this,'startSession'), 1);
			add_action('wp_logout',array($this,'endSession'));
			add_action('wp_login',array($this,'endSession'));
		}
		
		public function startSession() {
			if(!session_id()) {
				session_start();
			}
		}
		
		function endSession() {
			session_destroy();
		}
		
		public function install() {
		
			$this->create_table();
			add_option('tc_county_func_ver',$this->version);
		}
		
		
		public function update() {
			if($this->version != get_option('tc_county_func_ver')) {
				$this->create_table();
				update_option('tc_county_func_ver',$this->version);
			}
			add_action('init',array($this,'run_check'));	
			
		}
		
		
		public function run_check() {
			$this->county_loader->load_dependencies();
			$this->county_loader->check_unassigned_counties();
			$this->county_loader->run_page_init();
			
		}
		
		
		public function create_table() {
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE {$this->table} (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				name text NOT NULL,
				established datetime,
				state text NOT NULL,
				custom_post_id text NOT NULL,
				custom_page_id text,
				post_id text,
				description text,
				county_admin text,
				facebook_url text,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
		
		
		
	}
}