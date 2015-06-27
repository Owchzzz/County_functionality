<?php

class tc_county_frontend {
	protected $table;
	public function __construct($table) {
		$this->table = $table;
		add_action('wp_enqueue_scripts',array($this,'load_frontend_scripts'));
		add_shortcode('load_main_county',array($this,'load_main_county'));
		add_shortcode('load_county',array($this,'load_county'));
	}
	
	public function load_frontend_scripts() {
		wp_register_style('tc_county_frontend_style',TC_COUNTY_PATH.'assets/css/frontend.css');
		
		
		wp_enqueue_style('tc_county_frontend_style');
	}
	
	public function load_county($args) {
		
		extract(shortcode_atts(array(
						  'id'=>-1,
						  'posts_per_page'=>10),$args));
		if(!isset($args['posts_per_page']) ) $args['posts_per_page'] = 10;
		
		global $wpdb;
		
		$output="";
		if($args['id'] == -1 ){ 
			$output = "Invalid county.";
		}
		else {
			$county_data = $wpdb->get_row("SELECT * FROM {$this->table} WHERE id={$args['id']}",ARRAY_A);
			$custom_post_id = $county_data['custom_post_id'];
			$post_args = array(
			'post_type'=>$custom_post_id,
			'posts_per_page'=>-1,
			);
			$query = null;
			$query = new WP_Query($post_args);
			if($query->have_posts()) {
				$output.="<ul class=\"county-loop\">";
				while($query->have_posts()){
					$query->the_post();
					
					$output.='<li><a style="text-decoration:none;" href="'.get_the_permalink().'" rel="bookmark" title="'.get_the_title().'">'.get_the_title().'</a><br/><br/><p>'.get_the_excerpt().'</p></li>';
				}
				$output.="</ul>";
			}
			wp_reset_postdata();
		}
		return $output;
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