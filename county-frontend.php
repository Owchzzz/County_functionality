<?php

class tc_county_frontend {
	protected $table;
	public function __construct($table) {
		$this->table = $table;
		add_shortcode('load_main_county',array($this,'load_main_county'));
	}
	
	public function load_main_county() {
		$output = "";
		
		global $wpdb;
		$data = $wpdb->get_results("SELECT * FROM {$this->table}",ARRAY_A);
		foreach($data as $county) {
			$output .= "<li><a href=\"\">{$county['name']}</a>";
		}
		
		return $output;
	}
	
}