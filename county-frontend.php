<?php

class tc_county_frontend {
	protected $table;
	public function __construct($table) {
		$this->table = $table;
		add_action('wp_enqueue_scripts',array($this,'load_frontend_scripts'),0);
		add_shortcode('load_main_county',array($this,'load_main_county'));
		add_shortcode('load_county',array($this,'load_county'));
		add_shortcode('load_news_snippet',array($this,'load_news_snippet'));
		add_shortcode('load_county_blog_snips',array($this,'load_county_blog_snips'));
		add_shortcode('load_county_desc',array($this,'load_county_desc'));
		
		add_action('wp_ajax_set_session',array($this,'ajax_set_session'));
		add_action('wp_ajax_nopriv_set_session',array($this,'ajax_set_session'));
		
		add_action('wp_ajax_get_state',array($this,'ajax_get_state'));
		add_action('wp_ajax_nopriv_get_state',array($this,'ajax_get_state'));
		
		add_action('wp_ajax_get_county_snip',array($this,'ajax_get_county_snip'));
		add_action('wp_ajax_nopriv_get_county_snip',array($this,'ajax_get_county_snip'));
		
		
		
		
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
	
	public function ajax_get_state() {
		$serialized = array();
		foreach($_POST as $key=>$val) {
			$serialized[mysql_real_escape_string($key)] = mysql_real_escape_string($val);	
		}
		
		if(isset($serialized['name'])) {
			global $wpdb;
			$data = $wpdb->get_results("SELECT * FROM {$this->table} WHERE state='{$serialized['name']}'");
			$jsondata = json_encode($data);
			echo $jsondata;
			exit();
		}
		else {
			wp_send_json_error();
			exit();
		}
		
		return false;
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
	
	public function ajax_get_localnews_snips() {
		global $wpdb;
		if(isset($_POST['id'])) {
			return false;
			
		}
		return true;
	}
	
	public function ajax_get_county_snip() {
		$serialized = array();
		foreach($_POST as $key=>$val) {
			$serialized[mysql_real_escape_string($key)] = mysql_real_escape_string($val);	
		} // Serialize data.
		
		if(isset($serialized['post_id'])) {
			$count = $serialized['count'];
			$post = get_post($serialized['post_id']);
			if(!empty($post)) {
				$post_meta = get_post_meta($post->ID,'tc_description',true);
				$params =array();
				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail_size' );
				$url = $thumb['0'];
					if($count%2 == 0) {
						$params['mainclass'] = 'county-overview';
						$params['content'] = '<div class="col-xs-12 col-sm-4">
											<img class="img-responsive" src="'.$url.'"/>
										</div>
										<div class="col-xs-12 col-sm-8">
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
										<div class="col-xs-12 col-sm-8">
											<h3 class="county-name pull-left">
												<a href="'.get_the_permalink($post->ID).'" onclick="set_session_href(event,this,\'current_county\',\''.$post->ID.'\');" class="header-link">'.get_the_title($post->ID).'</a>
											</h3>
											<p class="county-desc">
												'.$post_meta.'	
											<br/>
												<a href="'.get_the_permalink($post->ID).'" onclick="set_session_href(event,this,\'current_county\',\''.$post->ID.'\');" class="county-link">View more</a>
											</p>
										</div>
										<div class="col-xs-12 col-sm-4">
											<img class="img-responsive" src="'.$url.'"/>
										</div>
										';
					}
					$output='<div class="'.$params['mainclass'].'">
										'.$params['content'].'
										<div class="clear"></div>
									</div>';	
				echo $output;
				exit();
			}
		}
	}
	
	

	
	public function load_frontend_scripts() {
		//Bootstrap
		wp_register_style('twitterbootstrap',TC_COUNTY_PATH.'assets/bootstrap/css/bootstrap.min.css');
		wp_register_script('twitterbootstrapjs',TC_COUNTY_PATH.'assets/bootstrap/js/bootstrap.min.js',array('jquery'));
		
		//Sliders
		wp_register_style('crsl-style',TC_COUNTY_PATH.'assets/css/crsl.css');
		wp_register_script('crsl-js',TC_COUNTY_PATH.'assets/js/crsl.js',array('jquery'));
		
		//Raphael library
		wp_register_script('raphael-js',TC_COUNTY_PATH.'assets/us-map/lib/raphael.js');
		wp_enqueue_script('raphael-js');
		
		//US MAP
		wp_register_script('us-map',TC_COUNTY_PATH.'assets/us-map/us-map.js',array('jquery'));
		wp_register_script('color-jquery',TC_COUNTY_PATH.'assets/us-map/example/color.jquery.js',array('jquery'));
		wp_enqueue_script('color-jquery');
		wp_enqueue_script('us-map');
		
		$wpdata = array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'spinner' => TC_COUNTY_PATH.'assets/images/ajax-loader.gif'
		);
		wp_register_script('county-page',TC_COUNTY_PATH.'assets/js/county-page.js',array('jquery','us-map'));
		wp_localize_script('county-page','wpdata',$wpdata);
		wp_enqueue_script('county-page');
		
		
		
		wp_enqueue_style('twitterbootstrap');
		wp_enqueue_script('twitterbootstrapjs');
		wp_enqueue_style('crsl-style');
		wp_enqueue_script('crsl-js');
		
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
	
	public function load_county_desc($args){
		extract(shortcode_atts(array(
			'id'=>-1
		),$args));	
		global $wpdb;
		if(isset($args['id'])) {
			$data = $wpdb->get_row("SELECT * FROM {$this->table} WHERE post_id={$args['id']}",ARRAY_A);
			if(!empty($data)) {
				$output="";
				$desc=get_post_meta($args['id'],'tc_description',true);
				$thumb = (wp_get_attachment_image_src( get_post_thumbnail_id($args['id']), 'thumbnail_size' )) ? wp_get_attachment_image_src( get_post_thumbnail_id($args['id']), 'thumbnail_size' ) : false;
				if($thumb) {
					$output.= '<img class="img-header" src="'.$thumb[0].'"/>';	
				}
				else {
					$output.= '';
				}
				$output .= '<p class="par-header">'.$desc.'</p>';
				return $output;
			}
		}
		return false;
	}
	
	public function load_news_snippet($args) {
		extract(shortcode_atts(array(
			'post_id'=>-1,
			'type'=>'news',
		),$args));	
		global $wpdb;
		$output="";
		if(isset($args['post_id'])) {
			if(!isset($args['type'])) $args['type'] = 'news';
			if($args['post_id'] !== -1) {
				
				$result = $wpdb->get_row("SELECT * FROM {$this->table} WHERE post_id={$args['post_id']}",ARRAY_A);
				if(!empty($result)) {
					$custom_post_id = $result['custom_post_id'];
					
					$args = array(
						'post_type' => $custom_post_id,
						'posts_per_page'=>5,
						'tax_query' => array(array(
							'taxonomy'=>'category_county',
							'field' => 'slug',
							'terms' => $args['type']
						))
						);
					$posts = get_posts($args);
					foreach($posts as $post_arr) {
						global $post;
						$save_post = $post;
						$post = get_post($post_arr->ID);
						setup_postdata($post);
						
						$content = "";
						
						if(get_post_thumbnail_id($post->ID)) {
							$content= '<img class="thumb" src="'.wp_get_attachment_url(get_post_thumbnail_id($post->ID)).'" alt="'.get_the_title().'"/><p class="content-half">'.get_the_excerpt().' <a href="'.get_the_permalink().'">View more..</a></p>';
						}
						else {
							$content= '<h4>'.get_the_title().'</h4><p class="content-full">'.get_the_excerpt().' <a href="'.get_the_permalink().'">View more..</a></p>';
						}
						$output.= ' <div class="news-snippet">
						'.$content.'
						</div><!--News Snippet-->';
						$post = $save_post;
					}
				}
				else {
					return false;
				}
			}
		}
		else {
			return false;   
		}
		
		return $output;
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
	
	
	public function load_county_blog_snips($args) {
			extract(shortcode_atts(array(
				'id'=>-1,
			),$args));
		if(!isset($args['id'])) {
			return false;	
		}
		else{
			$output="";
			global $wpdb;
			$data = $wpdb->get_row("SELECT * FROM {$this->table} WHERE post_id={$args['id']}",ARRAY_A);
			if(!empty($data)) {
				$custompostid = $data['custom_post_id'];
				if($custompostid) {
					$output.='<div class="crsl-items" data-navigation="navbtns">

								 <div class="crsl-wrap">';
					$args =array(
							'post_type' => $custompostid,
							'posts_per_page' => 15,
							'tax_query' => array(
								array('taxonomy'=>'category_county','field'=>'slug','terms' => array('news','business','events','obituaries'),'operator' =>'NOT IN')
						));
					$posts = get_posts($args);
					if(!empty($posts)) {
						foreach($posts as $post_arr) {
							global $post;
							$save_post = $post;
							$post = get_post($post_arr->ID);
							setup_postdata($post);
							$postthumb='<a href="'.get_the_permalink().'"><img class="thumb" src="'.TC_COUNTY_PATH.'assets/images/countylogo.png" alt="'.get_the_title().'"/></a>';
							if(get_post_thumbnail_id($post->ID)) {
								$postthumb = '<a href="'.get_the_permalink().'"><img class="thumb" src="'.wp_get_attachment_url(get_post_thumbnail_id($post->ID)).'" alt="'.get_the_title().'"/></a>'; 
							}
							$output.=' <div class="col-xs-4 crsl-item">
								<div class="thumbnail">
								 '.$postthumb.'
								  <span class="postdate">'.get_the_date().'</span>
								</div>

								<h3><a href="'.get_the_permalink().'">'.get_the_title().'</a></h3>
									<p>'.get_the_excerpt().'</p>
								<p class="readmore"><a href="'.get_the_permalink().'">Read More &raquo;</a></p>
							   </div>
							   ';
							
							//End post
							$post = $save_post;
						}
						$output.='</div><!-- @end .crsl-wrap -->
							    </div><!-- @end .crsl-items -->
								<nav class="slidernav">
								 <div id="navbtns" class="clearfix">
								   <a href="#" class="previous">prev</a>
								   <a href="#" class="next">next</a>
								 </div>
							    </nav>';
						return $output;
					}
				}
			}
			else {
				echo 'No data found.';	
			}
			
		}
	}
	
	public function load_main_county() { //Deprecated
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
						$params['content'] = '<div class="col-xs-12 col-sm-4">
											<img class="img-responsive" src="'.$url.'"/>
										</div>
										<div class="col-xs-12 col-sm-8">
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
										<div class="col-xs-12 col-sm-8">
											<h3 class="county-name pull-left">
												<a href="'.get_the_permalink($post->ID).'" onclick="set_session_href(event,this,\'current_county\',\''.$post->ID.'\');" class="header-link">'.get_the_title($post->ID).'</a>
											</h3>
											<p class="county-desc">
												'.$post_meta.'	
											<br/>
												<a href="'.get_the_permalink($post->ID).'" onclick="set_session_href(event,this,\'current_county\',\''.$post->ID.'\');" class="county-link">View more</a>
											</p>
										</div>
										<div class="col-xs-12 col-sm-4">
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
			$this->singlecounty_page_redirect();
		}
		else {
			if($data = $this->is_valid_posttype($posttype)) {
				$this->postcounty_page_redirect();
			}
		}
		
	}
	
	
	private function is_valid_posttype($postslug) {
		global $wpdb;
		$data = $wpdb->get_row("SELECT * FROM {$this->table} WHERE custom_post_id='{$postslug}'");
		if(!empty($data)) {
			return $data;	
		}
		
		return false;
	}
	
	private function county_page_redirect() {
			$templatefilename = 'assets/templates/county-page.php';
		$this->do_theme_redirect($templatefilename);
	}
	
	private function singlecounty_page_redirect() {
		if(!isset($_GET['view']))  $templatefilename = 'assets/templates/county-single.php';
		else {
			$templatefilename = 'assets/templates/county-posts.php';
		}
		
		$this->do_theme_redirect($templatefilename);
	}
	
	private function postcounty_page_redirect() {
		$templatefilename = 'assets/templates/post-page.php';
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