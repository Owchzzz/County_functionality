<?php

class tc_county_administration {
	protected $table;
	protected $countyID;
	
	protected $admin_menu_slug;
	
	public function __construct($table,$countyID) {
		
		$this->table = $table;
		$this->countyID = $countyID;
		$this->admin_menu_slug = 'menu_county_'.$countyID;
		//Perform the checks and initialize
		add_action('admin_menu',array($this,'load_admin_menu'));
		add_action('wp_dashboard_setup',array($this,'load_dashboard'));
		add_action('admin_enqueue_scripts',array($this,'enqueue_scripts'));

	}
	
	public function enqueue_scripts() {
		wp_register_script('tc_county_admin_js',TC_COUNTY_PATH.'assets/js/backend.js',array('jquery'),'1.0.0',true);
		$data_arr =array();
		$data_arr['cat'] = '';
		if(isset($_GET['cat'])) {
			$data_arr['cat'] = $_GET['cat'];
		}
		wp_localize_script('tc_county_admin_js','tc_county_admin',$data_arr);
		wp_enqueue_script('tc_county_admin_js');
	}
	
	public function load_dashboard() {
		global $wpdb;
		$sql = "SELECT * FROM {$this->table} WHERE id={$this->countyID}";
		$data = $wpdb->get_row($sql,ARRAY_A);
		wp_add_dashboard_widget(
			'county_dashboard_'.$this->countyID,
			$data['name'],
			array($this,'load_dashboard_widget')
		);
	}
	
	public function load_dashboard_widget() {
		echo 'Any relevant information about your county will be displayed here.';
	}
	
	
	
	public function load_admin_menu() {
		$cid = $this->countyID;
		global $wpdb;
		
		if(!current_user_can('manage_options')) {
			if(current_user_can('edit_county')) {
				remove_menu_page('edit.php');
			}
		}
		
		$sql = "SELECT * FROM {$this->table} WHERE id={$cid}";
		$data = $wpdb->get_results($sql,ARRAY_A);
		if(!empty($data)) {
			$county = $data[0];
			add_menu_page(
				'County:'.$county['name'],
				$county['name'],
				'edit_county',
				$this->admin_menu_slug,
				array($this,'load_admin'),
				'dashicons-location',
				'5.5');
			
			add_submenu_page(
				$this->admin_menu_slug,
				'Write new post',
				'Write new post',
				'edit_county',
				$this->admin_menu_slug.'_add_blogpost',
				array($this,'load_add_new_post')
				);
		}
	}
	
	public function load_add_new_post() {
		global $wpdb;
		$cid = $this->countyID;
		$sql = "SELECT * FROM {$this->table} WHERE id={$cid}";
		$data = $wpdb->get_results($sql,ARRAY_A);
		if(!empty($data)){
			$county = $data[0];
			echo '<meta <META http-equiv="refresh" content="0;URL='.admin_url('post-new.php?post_type='.$county['custom_post_id']).'">';
		}
		else {
			echo '<meta <META http-equiv="refresh" content="0;URL='.admin_url('admin.php?page='.$this->admin_menu_slug).'">';
		}
		
	}
	
	public function load_admin() {
		$county = $this->get_county();
		if(isset($_GET['action'])) {
			if($_GET['action'] == 'delete') {
				wp_delete_post($_GET['id'],true);
				$action = true;
			}
		}
		
		if(isset($_POST['action'])) {
			global $wpdb;
			if($_POST['action'] == 'themes') {
				$data=array();
				foreach($_POST as $key => $val) {
					if($key !== 'action')
					$data[mysql_real_escape_string($key)] = mysql_real_escape_string($val);
				}
				if($wpdb->update($this->table,$data,array('id'=>$this->countyID))) {
					echo '<div class="update notice is-dismissible">Successfully updated.</div>';
				}
				else {
					echo '<div class="error notice is-dismissible">Error try again later.</div>';
				}
				
			}
			else if($_POST['action'] == 'navigation') {
				$data = array();
				foreach($_POST as $key => $val) {
					if($key !== 'action') 
						$data[mysql_real_escape_string($key)] = mysql_real_escape_string($val);
				}
				if($wpdb->update($this->table,$data,array('id' => $this->countyID))) {
					echo '<div class="update notice is-dismissible">Successfully updated.</div>';
				}
				else {
					echo '<div class="error notice is-dismissible">Error try again later.</div>';
				}
			}
			else {
				echo '<div class="error notice is-dismissible">Unable to complete action</div>';
			}
		}
		$menuslug = $this->admin_menu_slug;
		require_once('backend/my_county.php');
	}
	
	public function add_blogpost() {
		$county = $this->get_county();
		require_once('backend/add_blogpost.php');
	}
	
	
	private function get_county() {
		global $wpdb;
		$cid = $this->countyID;
		$sql = "SELECT * FROM {$this->table} WHERE id={$cid}";
		$data = $wpdb->get_row($sql,ARRAY_A);
		return $data;
	}
}