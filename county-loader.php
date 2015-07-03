<?php
// Object to initialize all county and their functionality.
if(!class_exists('tc_county_loader_spec')) {
	class tc_county_loader_spec {
		protected $table;
		protected $counties;
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
						'show_in_menu' => false,
						'capability_type' => 'edit_county'
						//'capability_type'     => array('edit_county'),
					);
				if(!current_user_can('manage_options')) {	
					$args['capabilities'] = array('create_posts' => false);
				}
				
					register_post_type('tc_county', $args);
					global $wp_post_types;
					$wp_post_types['tc_county']->capability_type ='edit_county';
	
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
			$this->counties = $counties;
			$this->build_pages($counties);
			$this->build_posts($counties);
			
			add_action('add_meta_boxes',array($this,'build_meta'));
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
						'show_in_menu' => false,
						'taxonomies' => array('category_county'),
						'capability_type' => 'edit_county',
						'capabilities' => array('edit_posts'=>'edit_county','edit_post'=>'edit_county')
						//'capability_type' => array('edit_county'),
					);
					register_post_type($county['custom_post_id'], $args); // registers Main County Posts
					
					$labels = array(
						'name'              => _x( $county['name'].' Categories', 'taxonomy general name' ),
						'singular_name'     => _x( $county['name'].' Categories', 'taxonomy singular name' ),
						'search_items'      => __( 'Search '.$county['name'].' Categories' ),
						'all_items'         => __( 'All '.$county['name'].' Categories' ),
						'parent_item'       => __( 'Parent Category' ),
						'parent_item_colon' => __( 'Parent Category:' ),
						'edit_item'         => __( 'Edit Category' ),
						'update_item'       => __( 'Update Category' ),
						'add_new_item'      => __( 'Add New Category' ),
						'new_item_name'     => __( 'New Category Name' ),
						'menu_name'         => __( 'Cateogires' ),
					);
					
					$args = array(
						'hierarchical' => true,
						'labels' => $labels,
						'show_ui' => true,
						'show_admin_column' => true,
						'query_var' => 'category_county',
					);
					
					
					
						
					register_taxonomy('category_county',array($county['custom_post_id']), $args);
					wp_insert_term('News','category_county',array('description'=>'News Section','slug'=>'news'));
					wp_insert_term('Events','category_county',array('description'=>'Local Events','slug'=>'events'));
					wp_insert_term('Obituaries','category_county',array('description'=>'The Obituaries','slug'=>'obituaries'));
					wp_insert_term('Businesses','category_county',array('description'=>'Bussiness news','slug'=>'business'));
					
				}

			}
			
			return true;
    	
		}
		
		
		public function build_meta() {
			if(isset($_GET['cat'])) {
				if($_GET['cat'] == 'classifieds') {
					foreach($this->counties as $county) {
						add_meta_box('clasifieds-capability','Classifieds',array($this,'loadCountyMeta'),$county['custom_post_id'],'normal','high');
					}
				}
			}
			
		}
															  
		public function loadCountyMeta() {
			if(isset($_GET['cat'])) {
				if($_GET['cat'] == 'classifieds') {
					echo 'Please take note that this you are posting to classifieds. These are paid advertisements and should be handled appropriately.';
				}
				else {
					
				}
			}
		}
		
		private function build_nav($county) {
			if(!wp_get_nav_menu_object('menu_county_'.$county['id']) ) {
				$menu_id = wp_create_nav_menu('menu_county_'.$county['id']);
				$this->add_menu_item($menu_id,'Home','/');
				$this->add_menu_item($menu_id,$county['name']. ' Channels','/tc_county/'.$county['custom_page_id'].'?page=news');
				
			}
		}
		
		
		private function add_menu_item($menu_id,$title,$url) {
			wp_update_nav_menu_item($menu_id,0,array(
				'menu-item-title' => __($title),
				'menu-item-classes' =>'menu-item menu-item-type-post_type menu-item-object-page menu-item-346',
				'menu-item-url' => home_url($url),
				'menu-item-status' =>'publish'
			));
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