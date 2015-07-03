<?php

class tc_county_frontend {
	protected $table;
	public function __construct($table) {
		$this->table = $table;
		add_action('wp_enqueue_scripts',array($this,'load_frontend_scripts'),0);
		add_shortcode('load_main_county',array($this,'load_main_county'));
		add_shortcode('load_county',array($this,'load_county'));
		
		add_action('wp_ajax_set_session',array($this,'ajax_set_session'));
		add_action('wp_ajax_nopriv_set_session',array($this,'ajax_set_session'));
		
		add_action('wp_ajax_get_session',array($this,'ajax_get_session'));
		add_action('wp_ajax_nopriv_get_session',array($this,'ajax_get_session'));
		
		add_action('wp_print_scripts',array($this,'localize_script'));
		
		add_action('template_redirect',array($this,'do_themeing'));
	}
	
	
	
	
	private function is_county($id) {
		if($post = get_post($id)) {
			return $post->post_type == 'tc_county' ? true:false;
		}
	}
	
	private function is_county_post($id) {
		if($post = get_post($id)) {
			//var_dump($post);
		}
	}
	
	public function localize_script() {
		if($id=get_the_ID()) {
			if($this->is_county($id)) {
				//This is valid county. load in county specific jquery.
				wp_register_script('tc_county_main_frontend_js',TC_COUNTY_PATH.'assets/js/mainfrontend.js',array('jquery'));
				wp_enqueue_script('tc_county_main_frontend_js');
			}
			else if ($this->is_county_post($id)) {
				echo 'is a valid county post';
			}
		}
	}
	
	public function ajax_set_session(){
		$serialized = array();
		foreach($_POST as $key=>$val) {
			$serialized[mysql_real_escape_string($key)] = mysql_real_escape_string($val);
		}
		$_SESSION[$serialized['session']] = $serialized['value'];
		
	}
	
	public function ajax_get_session() {
		if(isset($_SESSION[$_POST['session']])) {
			echo $_SESSION[$_POST['session']];
		}
	}
	
	public function load_frontend_scripts() {
		//Bootstrap
		wp_register_style('twitterbootstrap',TC_COUNTY_PATH.'assets/bootstrap/css/bootstrap.min.css');
		wp_register_script('twitterbootstrapjs',TC_COUNTY_PATH.'assets/bootstrap/js/bootstrap.min.js',array('jquery'));
		
		wp_enqueue_style('twitterbootstrap');
		wp_enqueue_script('twitterbootstrapjs');
		wp_register_style('tc_county_frontend_style',TC_COUNTY_PATH.'assets/css/frontend.css',array('twitterbootstrap'));
		wp_register_script('tc_county_frontend_js',TC_COUNTY_PATH.'assets/js/frontend.js',array('jquery'));
		$options = get_option('tc_county_data');
		$data_arr = array('ajax_url'=> admin_url('admin-ajax.php'),'menu_id'=>$options['main-menu-id']);
		
		$menu_arr = array('Militia Today Home' => home_url('/'),
					  'Channels' => get_the_guid(get_the_ID()).'?page=channels',
					  'Local News' => array('main-link' => get_the_guid(get_the_ID()).'?cat=news',
									    'News' => get_the_guid(get_the_ID()).'?cat=news',
									    'Local Events' => get_the_guid(get_the_ID()).'?cat=events',
									    'Obituaries' => get_the_guid(get_the_ID()).'?cat=obituaries',
									    'Local Business' => get_the_guid(get_the_ID()).'?cat=business',)
					  
					  );
					 
		$data_arr['menu'] = $menu_arr;
		wp_localize_script('tc_county_frontend_js','tc_county',$data_arr);
		
		wp_enqueue_script('tc_county_frontend_js');
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
			
			if(isset($_GET['cat'])) {
				$post_args['tax_query'] = array(
							array(
								'taxonomy' => 'category_county',
								'field' => 'slug',
								'terms' => $_GET['cat']
							)
				);
			}
			$query = null;
			$query = new WP_Query($post_args);
			if($query->have_posts()) {
				$output.="<ul class=\"county-loop\">";
				while($query->have_posts()){
					$query->the_post();
					$output.='<li><a style="text-decoration:none;" href="'.get_the_permalink().'"  rel="bookmark" title="'.get_the_title().'">'.get_the_title().'</a><br/><br/><p>Author: <a href="'.get_author_posts_url(get_the_author_meta('ID')).'">'.get_the_author().'</a> on '.get_the_date().'</p></li>';
					
				}
				$output.="</ul>";
			}
			wp_reset_postdata();
		}
		return $output;
	}
	
	public function load_main_county() {
		global $wpdb;
		$output='';
		$args = array('post_type'=>'tc_county','post_status'=>'publish');
		$posts = new WP_Query($args);
		if(!empty($posts) ){
			$count=0;
			foreach($posts->posts as $post) {
				$post_meta = get_post_meta($post->ID,'tc_description',true);
				$params =array();
				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail_size' );
				$url = $thumb['0'];
					if($count%2 == 0) {
						$params['mainclass'] = 'county-overview';
						$params['content'] = '<div class="col-xs-6">
											<img class="img-responsive" src="'.$url.'"/>
										</div>
										<div class="col-xs-6">
											<h3 class="county-name" >
												<a href="'.get_the_permalink($post->ID).'" onclick="set_session_href(event,this,\'current_county\',\''.$post->ID.'\');" class="header-link">'.get_the_title($post->ID).'</a>
											</h3>
											<p class="county-desc">
												'.$post_meta.'	
											<br/>
												<a href="'.get_the_permalink($post->ID).'" onclick="set_session_href(event,this,\'current_county\',\''.$post->ID.'\');" class="county-link">View more</a>
											</p>
										</div>';
					}
					else {
						$params['mainclass'] = 'county-overview gray';
						$params['content'] = '
										<div class="col-xs-6">
											<h3 class="county-name">
												<a href="'.get_the_permalink($post->ID).'" onclick="set_session_href(event,this,\'current_county\',\''.$post->ID.'\');" class="header-link">'.get_the_title($post->ID).'</a>
											</h3>
											<p class="county-desc">
												'.$post_meta.'	
											<br/>
												<a href="'.get_the_permalink($post->ID).'" onclick="set_session_href(event,this,\'current_county\',\''.$post->ID.'\');" class="county-link">View more</a>
											</p>
										</div>
										<div class="col-xs-6">
											<img class="img-responsive" src="'.$url.'"/>
										</div>
										';
					}
					$output.='<div class="'.$params['mainclass'].'">
										'.$params['content'].'
										<div class="clear"></div>
									</div>';	
					$count++;
			}
		}
		return $output;
		
	}
	
	public function old_load_main_county() {
		$output = '';
		
		global $wpdb;
		$data = $wpdb->get_results("SELECT * FROM {$this->table}",ARRAY_A);
		$count=0;
		
		foreach($data as $county) {
			//$oldoutput .= "<li><a href=\"".site_url('index.php/tc_county/'.$county['custom_page_id'])."\" onclick=\"set_session_href(event,this,'current_county','{$county['id']}');\">{$county['name']}</a>";
			$custompageid = $county['custom_page_id'];
			$count=0;
			if(strlen($custompageid) < 1 ) {
				die('Error. could not retreive data. Try again later');	
			}
			else {
				$post_args = array('post_type' => 'tc_county');
				$post = new WP_Query($post_args);
				var_dump($post);
				echo 'custompageid: '.$custompageid;
				var_dump($post);
				if(empty($post)) {
					die('Error. could not retrieve post. Try again later');
				}
				else {
					$params =array();
					if($count%2 == 0) {
						$params['mainclass'] = 'county-overview';
						$params['content'] = '<div class="col-xs-6">
											<img class="img-responsive" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRIsqVQVpUbAqXzdjKBESA6NMXcVjij1yl11hrATitJb0P2cd9Q"/>
										</div>
										<div class="col-xs-6">
											<h3 class="county-name" >
												<a href="'.site_url('index.php/tc_county/'.$county['custom_page_id']).'" onclick="set_session_href(event,this,\'current_county\',\''.$county['id'].'\');" class="header-link">'.$county['name'].'</a>
											</h3>
											<p class="county-desc">
												'.$county['description'].'	
											<br/>
												<a href="'.site_url('index.php/tc_county/'.$county['custom_page_id']).'" onclick="set_session_href(event,this,\'current_county\',\''.$county['id'].'\');" class="county-link">View more</a>
											</p>
										</div>';
					}
					else {
						$params['mainclass'] = 'county-overview gray';
						$params['content'] = '
										<div class="col-xs-6">
											<h3 class="county-name">
												<a href="'.site_url('index.php/tc_county/'.$county['custom_page_id']).'" onclick="set_session_href(event,this,\'current_county\',\''.$county['id'].'\');" class="header-link">'.$county['name'].'</a>
											</h3>
											<p class="county-desc">
												'.$county['description'].'	
											<br/>
												<a href="'.site_url('index.php/tc_county/'.$county['custom_page_id']).'" onclick="set_session_href(event,this,\'current_county\',\''.$county['id'].'\');" class="county-link">View more</a>
											</p>
										</div>
										<div class="col-xs-6">
											<img class="img-responsive" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRIsqVQVpUbAqXzdjKBESA6NMXcVjij1yl11hrATitJb0P2cd9Q"/>
										</div>
										';
					}
					$output.='<div class="'.$params['mainclass'].'">
										'.$params['content'].'
										<div class="clear"></div>
									</div>';	
					$count++;
				}
				
				
			}
			
		}
		
		return $output;
	}
	
	//Plugin custom themeing functions
	
	public function do_themeing() {
		global $post;
		//Check for main county page
		$pageslug = $post->post_name;
		if($pageslug == 'tc_county_func_main') {
			$this->county_page_redirect();
		}
		
		$posttype = $post->post_type;
		if($posttype == 'tc_county') { // If custom themeing enabled.
			
		}
		
	}
	
	public function county_page_redirect() {
			$templatefilename = 'assets/templates/county-page.php';
		$this->do_theme_redirect($templatefilename);
	}
	
	private function do_theme_redirect($url) {
		global $post, $wp_query;
		if(have_posts()) {
			include($url);
			die();
		} else {
			$wp_query->is_404 = true;
		}
	}
}