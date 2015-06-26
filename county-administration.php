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

	}
	
	public function load_admin_menu() {
		$cid = $this->countyID;
		global $wpdb;
		
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
				'edit.php?post_type='.$county['custom_post_id']
				);
		}
	}
	
	public function load_admin() {
		$county = $this->get_county();
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