<?php
/**
Plugin Name: County Functionality
Plugin URI: 
Description: A plugin by techriver that allows the management of multiple counties. Create bios, pages, and blogs specific to that county.
Author: TechRiver
Version: 0.0.1
Author URI: http://ma.tt/
*/

require_once('loader.php');
define('TC_COUNTY_PATH',plugin_dir_url(__FILE__));
if( !class_exists('TC_County_functionality')) {
	class TC_County_functionality {
		
		protected $version;
		public $table;
		protected $loader;
		public function __construct() {
			global $wpdb;
			
			//Settings
			$this->table = $wpdb->prefix.'tc_county';
			$this->version = "0.0.18";
			$this->loader = new Tc_County_loader($this->version,$this->table);
	
			//Initial run Time
			$this->init();
		}
		
		public function install() {
			$this->loader->install();
		}
		
		public function init() {
			$this->loader->update();
			add_action('admin_menu',array($this,'register_menus'));
			add_action('init',array($this,'initialize_page'));
			add_action('init',array($this,'initialize_session'));
			
			add_action('admin_init',array($this,'register_roles'));
		}
		
		public function initialize_session() {
			if( !session_id() ) {
				session_start();
			}
		}
		
		public function register_roles() {
			$this->loader->roles();
		}
		
		
		public function initialize_page() {
			$args = array('name'=>'tc_county_func_main','post_type'=>'page','post_status'=>'trash'); // Delete from trash.
			$posts = get_posts($args);
			foreach($posts as $post) {
				wp_delete_post($post->ID,true);
			}
			
			
			$page = get_posts( array( 'name' => 'tc_county_func_main', 'post_type' => 'page'));
			if(empty($page) || $page === null) {
				$page_build = array(
						'post_name' => 'tc_county_func_main',
						'post_title' => 'Counties',
						'post_status' => 'publish',
						'post_content' => '[load_main_county]',
						'post_excerpt' => 'Main County Page.',
						'post_type' => 'page'
					);
				$post_id = wp_insert_post($page_build);
			}
		}
		
		public function register_menus() {
			add_menu_page('County Functionality','County System','manage_options','county_func_admin', array($this,'load_county_admin'), 'dashicons-location-alt');
			add_submenu_page('county_func_admin','Add new County','Add new County','manage_options','county_func_admin_add_county',array($this,'load_county_admin_add_county'));
			add_submenu_page(null,'Edit county','Edit County','manage_options','county_func_admin_edit_county',array($this,'load_county_admin_edit_county'));
			add_submenu_page('county_func_admin','Settings', 'Settings','manage_options','county_func_admin_settings',array($this,'load_county_admin_settings'));
			if(WP_DEBUG) { // Checking if in development mode.
				add_submenu_page('county_func_admin','All Counties','All Counties','manage_options','county_func_admin_show_counties',array($this,'show_counties')); 
			}
		}
		
		/* county administration */
		public function load_county_admin() {
			$this->load_county_admin_process();
			require_once('admin/main.php');
		}
		
		private function load_county_admin_process() {
			global $wpdb;
			if(isset($_GET['action'])) {
				if($_GET['action'] == 'delete') {
					//if($_SESSION['verifiednonce'] != $_GET['_wpnonce']){
						if(wp_verify_nonce($_GET['_wpnonce'],'sp_delete_customer')) {
							$_SESSION['verifiednonce'] = $_GET['_wpnonce'];
							
							$data = $wpdb->get_results("SELECT * FROM {$this->table} WHERE id={$_GET['id']}",ARRAY_A);
							wp_delete_post($data[0]['post_id'],true); // Delete the page.
							$res = $wpdb->delete($this->table,array('id'=>$_GET['id']));
							$_SESSION['status'] = true;
							if($res) {
								$_SESSION['delete_status'] = true;
							}
							else {
								$_SESSION['delete_status'] = false;
							}
						}
						//else {
							
						//}
					//}
						
				}
			}
		}
		
		
		//Add functionality
		public function load_county_admin_add_county() {
			$this->load_county_admin_add_county_process();
			require_once('admin/add.php');
		}
		
		private function load_county_admin_add_county_process() {
			global $wpdb;
			if(isset($_GET['action'])) {
				if($_GET['action'] == 'save_new') {
					$data = array();
					foreach($_POST as $key=>$val) {
						$data[mysql_real_escape_string($key)] = mysql_real_escape_string($val);
					}
					
					if(!empty($data)) {
						$data['established'] = current_time('mysql',1);
						$act = $wpdb->insert($this->table,$data);
						$_SESSION['status'] = true;
						if($act) {
							$this->redirect(admin_url('admin.php?page=county_func_admin&status=success_insert_new_county'));
							
						}
						else {
							$this->redirect(admin_url('admin.php?page=county_func_admin&status=fail_insert_new_county'));
						}
						exit();
					}
					else {
						$this->redirect(admin_url('admin.php?page=county_func_admin&status=fail_insert_no_data'));
					}
				}
			}
		}
		
		
		//Edit Fuctionality
		public function load_county_admin_edit_county() {
			global $wpdb;
			if(isset($_GET['action'])) {
				if($_GET['action'] == 'save_new') {
					$_SESSION['status'] = true;
					$data = array();
					foreach($_POST as $key=>$val) {
						if($key !== 'id')
						$data[mysql_real_escape_string($key)] = mysql_real_escape_string($val);
					}
					if($wpdb->update($this->table,$data,array('id'=>$_POST['id']))) {
						$this->redirect(admin_url('admin.php?page=county_func_admin&status=success_modify'));
					}
					else {
						$this->redirect(admin_url('admin.php?page=county_func_admin&status=fail_modify'));
					}
				}
				else if($_GET['action'] == 'modify'){
					if(isset($_GET['id'])) {
						$results = $wpdb->get_results("SELECT * FROM {$this->table} WHERE id={$_GET['id']}",ARRAY_A);
						if($results) {
							require_once('admin/edit.php');
						}
						else {
							$this->redirect(admin_url('admin.php?page=county_func_admin&status=unable_to_edit_nopriv'));
						}

					}
					else {
						$this->redirect(admin_url('admin.php?page=county_func_admin&status=unable_to_edit_nopriv'));
					}
				}
				else {
					$this->redirect(admin_url('admin.php?page=county_func_admin&status=unable_to_edit_nopriv'));
				}
			}
			else {
				$this->redirect(admin_url('admin.php?page=county_func_admin&status=unable_to_edit_nopriv'));
			}
				
		}
		
		//Load settings
		public function load_county_admin_settings() {
			require_once('admin/settings.php');
		}
		
		
		
		//Show counties Functionality
		public function show_counties() {
			$this->redirect(admin_url('edit.php?post_type=tc_county'));
		}
		
		
		private function redirect($url) {
			echo '<meta <META http-equiv="refresh" content="0;URL='.$url.'">';
			return true;
		}
	}
	
	$tccounty = new TC_County_functionality();
	register_activation_hook(__FILE__,array(&$tccounty,'install'));
	
	
	
}
