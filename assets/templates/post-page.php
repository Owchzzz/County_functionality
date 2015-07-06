<?php
if(have_posts()) the_post();
global $post;
$globalpost = $post;
class tc_post {
	public $ID;
	public $post;
	
	protected $table;
	protected $county;
	protected $custom_post_id;
	
	public function __construct($postID,$post){
		global $wpdb;
		$this->ID = $postID;
		$this->post = $post;
		$this->table = $wpdb->prefix.'tc_county';
		
		$this->county_setup();
		
	}
	
	public function county_setup(){
		global $wpdb;
		$this->custom_post_id = get_post_type( $this->post->ID );
		$this->county = $wpdb->get_row("SELECT * FROM {$this->table} WHERE custom_post_id='{$this->custom_post_id}'");
	}
	
								 
	public function get_county_title() {
		return $this->county->name;
	}
	
	public function get_county_id() {
		return $this->county->id;
	}

	public function get_link() {
		return home_url('/index.php/'.$this->county->custom_page_id);	
	}
	
	public function get_title() {
		return $this->county->name;	
	}
	
}
$county = new tc_post($post->ID,$post);
$data = new tc_post($post->ID,$globalpost);
?>

<html>
	<head>
		<?php wp_head();?>
		<style>
			body:before {
				display:none;	
			}
			body {
				/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#e33d1d+0,c4361a+100 */
background: #e33d1d; /* Old browsers */
background: -moz-linear-gradient(top,  #e33d1d 0%, #c4361a 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#e33d1d), color-stop(100%,#c4361a)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #e33d1d 0%,#c4361a 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #e33d1d 0%,#c4361a 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #e33d1d 0%,#c4361a 100%); /* IE10+ */
background: linear-gradient(to bottom,  #e33d1d 0%,#c4361a 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e33d1d', endColorstr='#c4361a',GradientType=0 ); /* IE6-9 */
									
			}
		</style>
	</head>
	<body>
		<div class="techriver2">
			<?php require_once('header-county.php');?>
			<div class="container post">
				<div class="row">
					<div class="col-xs-12">
						
						<h1 class="post-title"> <a href="<?php echo $county->get_link();?>"><i class="icon white margin-right ion-arrow-left-a"></i></a> <?php the_title();?></h1>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-8 outerpadding">
						
						<div class="box-content">
							
							<div class="content">
								<?php the_content();?>
								
								<!--Author-->
								<div class="post-meta">
								<small class="author">Published on: <?php the_time('F jS, Y') ?> <!-- by <?php the_author() ?> --> <?php edit_post_link('Edit this entry.','',''); ?></small>
								</div>
								<!--Navigation-->
								<div class="navigation">
									<div class="next-posts"><?php next_posts_link(); ?></div>
									<div class="prev-posts"><?php previous_posts_link(); ?></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-md-4 outerpadding">
						<div class="box-content">
							
						</div>
					</div>
				</div>
			</div>
			
		</div>
		
		<!--Footer-->
		<?php wp_footer();?>
		<script>
			jQuery(function($){
			$('.crsl-items').carousel({
    visible: 1,
    itemMinWidth: 180,
    itemEqualHeight: 370,
    itemMargin: 9,
  });
  
  $("a[href=#]").on('click', function(e) {
    e.preventDefault();
  });
			}); 
		</script>
	</body>
</html>