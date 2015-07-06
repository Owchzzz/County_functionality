<?php
if(have_posts()) the_post();
global $post;
$globalpost = $post;
class tc_post {
	public $ID;
	public $post;
	public $category;
	public $args;
	
	protected $table;
	protected $county;
	protected $custom_post_id;
	
	public function __construct($postID,$post){
		global $wpdb;
		$this->ID = $postID;
		$this->post = $post;
		$this->table = $wpdb->prefix.'tc_county';
		
		$this->blog_setup();
		$this->county_setup();
	}
	
	public function county_setup() {
		global $wpdb;
		$this->county = $wpdb->get_row("SELECT * FROM {$this->table} WHERE post_id={$this->ID}",ARRAY_A);
	}
	
	public function blog_setup(){
		global $wpdb;
		$data = $wpdb->get_row("SELECT * FROM {$this->table} WHERE post_id={$this->post->ID}",ARRAY_A);
		$this->custom_post_id = $data['custom_post_id'];
		if(isset($_GET['view'])) {
			$custompostid = $this->custom_post_id;
			$paged = isset($_GET['pagination']) ? $_GET['pagination'] : 1;
			$taxarr = ($_GET['view'] == 'blog') ? array('news','business','obituaries','events') : array($_GET['view']);
			$operator = ($_GET['view'] == 'blog') ? 'NOT IN' : 'IN';
			$args = array(
				'post_type'=>$custompostid,
				'post_status'=>'publish',
				'posts_per_page'=>2,
				'paged'=>$paged,
				'tax_query' => array(
					array('taxonomy' => 'category_county',
						 'field'=>'slug',
						 'terms' => $taxarr,
						 'operator' => $operator))
				);
			
			$this->args = $args;
			
		}
	}
	
	public function get_args() {
		return $this->args;	
	}
	
	public function get_county_id() {
		return $this->county->id;
	}

	public function get_link() {
		return home_url('/index.php/'.$this->county['custom_page_id']);	
	}
	
	public function get_title() {
		return get_the_title();	
	}
	
	public function get_the_blog_type() {
		if(isset($_GET['view'])) {
			
			if($_GET['view'] == 'blog') {
				return 'Blog';	
			}
		}
	}
	
	
	
	/*blog specific functionality*/
	
}
$county = new tc_post($post->ID,$post);

?>

<html>
	<head>
		<?php wp_head();?>
		<style>
			body:before {
				display:none;	
			}
			body {
				background-color:#e33d1d;										
			}
			nav.pagination ul {
				list-style-type:none;	
			}
			nav.pagination ul li {
				display:inline;	
			}
			nav.pagination ul li a.active {
				font-weight:bold;	
			}
		</style>
	</head>
	<body>
		<div class="techriver2">
			<?php require_once('header-county.php');?>
			
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 outerpadding">
						<h2 class="header-brand"><?php echo $county->get_the_blog_type();?> Posts</h2>
						<div class="box-content">
							<div class="blog-posts">
							<?php
							$postslist = new WP_Query($county->get_args());
							setup_postdata($postslist);
							if($postslist->have_posts() ) {
								while($postslist->have_posts()): $postslist->the_post();?>
								<?php 
								$postthumb='<a href="'.get_the_permalink().'"><img class="thumb" src="'.TC_COUNTY_PATH.'assets/images/countylogo.png" alt="'.get_the_title().'"/></a>';
								if(get_post_thumbnail_id($post->ID)) {
									$postthumb = '<a href="'.get_the_permalink().'"><img class="thumb" src="'.wp_get_attachment_url(get_post_thumbnail_id($post->ID)).'" alt="'.get_the_title().'"/></a>'; 
								}?>
								<div class="blog-post col-xs-12">
									<div class="col-xs-4">
										<?php echo $postthumb;?>
									</div>
									<div class="col-xs-8 post-content">
										<h2><?php echo get_the_title();?></h2>
										<p><?php the_excerpt();?>
									</div>
								</div>
								<?php
								endwhile;
								?>
								<div class="clear"></div>
								<nav class="pagination">
								    <ul>
								<?php // Pagination
								$paged = isset($_GET['pagination']) ? $_GET['pagination'] : '1';
								$max_onpage = 10;
								$maxpages = $postslist->max_num_pages;
								$get_arr = array();
								foreach($_GET as $key=>$val) {
									$get_arr[$key] = $val;	
								}
								
								$page = $paged-5; // Start page-5
								$menu_count = 0;
									
								while($menu_count < 10 && $page<=$maxpages) {
									$style = ($paged == $page) ? 'class="active"' : 'class="not-active"';
									if($page > 0) echo '<li><a href="?view='.$get_arr['view'].'&pagination='.$page.'" '.$style.'>'.$page.'</a></li>';
									$menu_count++;
									$page++;
								}
								?>
								    </ul>
								</nav>
								<div class="clear"></div>
								<?php
							}
							//wp_reset_query();
							?>
							
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!--Footer-->
		<?php wp_footer();?>
	</body>
</html>