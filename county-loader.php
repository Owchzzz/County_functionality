<?php
// Object to initialize all county and their functionality.
if(!class_exists('tc_county_loader_spec')) {
	class tc_county_loader_spec {
		protected $table;
		public function __construct($table) {
			$this->table = $table;
		}	
		
		public function load_dependencies() {
			
			if(! post_type_exists('tc_county')) { // Register the county post type.
				$args = array(
						'labels' => array(
							'name' => __('Counties'),
							'singular_name' => __('County')
						),
						'name' => 'County',
						'has_archive' => true,
						'public' => true, 
						'show_in_menu' => false
					);
					register_post_type('tc_county', $args);
			}
		}
		
		public function check_unassigned_counties() {
			global $wpdb;
			$sql = "SELECT * FROM {$this->table} WHERE custom_post_id LIKE '' OR custom_post_id IS NULL";
			$counties = $wpdb->get_results($sql,ARRAY_A);
			if(!empty($counties)){
				
				foreach($counties as $county) {
					$_p = array();
					$id = $county['id'];
					$county_name = $county['name'];
					$new_county_name = 'tc_county_'.strtolower(str_replace(' ','_',$county_name)).'_'.$id;
					if(strlen($new_county_name) < 20) 
					{
						// Valid Name
					}
					else 
					{
						$new_county_name = 'tc_county_id_'.$id;
					}
					
					
					$_p['custom_post_id'] = $new_county_name;
					$wpdb->update($this->table,$_p,array('id'=>$id));
				}
				
				return true;
			}
			
			$sql = "SELECT * FROM {$this->table} WHERE custom_page_id LIKE '' OR custom_page_id LIKE NULL OR custom_page_id IS NULL";
			$counties = $wpdb->get_results($sql,ARRAY_A);
			if(!empty($counties)) {
				foreach ($counties as $county) {
					$_p = array();
					$id = $county['id'];
					$county_name = $county['name'];
					$new_county_name = 'tc_page_'.strtolower(str_replace(' ','_',$county_name)).'_'.$id;
					if(strlen($new_county_name) < 20) 
					{
						// Valid Name
					}
					else 
					{
						$new_county_name = 'tc_page_id'.$id;
					}
					
					
					$_p['custom_page_id'] = $new_county_name;
					$wpdb->update($this->table,$_p,array('id'=>$id));
				}
			}
			
			return false;
		}
		
		public function run_page_init() {
			global $wpdb;
			$sql = "SELECT * FROM {$this->table}";
			$counties = $wpdb->get_results($sql,ARRAY_A);
			$this->build_pages($counties);
			$this->build_posts($counties);
			flush_rewrite_rules();
		}
		
		private function build_pages($counties) { // AKA Build counties
			//Search in trash and delete
				$args = array('post_type'=>'tc_county','post_status'=>'trash');
			$posts = get_posts($args);
			foreach($posts as $post) {
				wp_delete_post($post->ID,true);
			}
		
			foreach($counties as $county) {
				
				
				$args = array('name'=>$county['custom_page_id'],'post_type'=>'tc_county');
				$page = get_posts($args);

				if(empty($page)) { // Build a page for this county
					$this->build_page($county);
				}
				else {
					//Check if page is in trash and rebuild page.

					$id = $page[0]->post_status;
					if( $id !== 'publish') {
						echo 'In trash must restore';
					}
					
				}
				
			}
		}
		
		private function build_posts($counties) {
			foreach($counties as $county) {
				if( ! post_type_exists($county['custom_post_id']) ) {
					
					$labels = array(
						'name' => 'County Posts',
						'singular_name' => 'County Post',
						'add_new' => 'Post to '.$county['name'],
						'add_new_item' => 'Post to '.$county['name'],
					);
					$args = array(
						'labels' => $labels,
						'name' => $county['name'],
						'has_archive' => true,
						'public' => true,
						'show_in_menu' => false
					);
					register_post_type($county['custom_post_id'], $args);
				}
			}
			
			return true;
    	
		}
		
		private function build_page($county) {
			global $wpdb;
			$page_build = array(
						'post_name' => $county['custom_page_id'],
						'post_title' => $county['name'],
						'post_status' => 'publish',
						'post_content' => '[load_county id="'.$county['id'].'"]',
						'post_excerpt' => 'County: '.$county['name'],
						'post_type' => 'tc_county'
					);
					$post_id = wp_insert_post( $page_build );
			$data = array('post_id' => $post_id);
			$status=$wpdb->update($this->table,$data,array('id'=>$county['id']));
			return $status;
		}
	}
}