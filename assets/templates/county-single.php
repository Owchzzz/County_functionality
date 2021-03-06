<?php

global $post;

class tc_post {
	public $ID;
	public $post;
	
	public function __constructor($postID,$post){
		$this->ID = $postID;
		$this->post = $post;
	}
	
	public function get_title() {
		return get_the_title($this->ID);
	}
	
	public function get_link() {
		return $this->post['post_type'];	
	}
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
			
			.changecategoryselect{
	font-size:12px;
	float:right;
	
}
			.awpcp-category-dropdown-container input {
				display:block;
				font-size:10px;
				background-color:#c4361a;
				color:white;
				font-family:bebas;
				padding:5px;
			}
			
			.icon {
				cursor:pointer;	
			}
		</style>
	</head>
	<body>
		<div class="MODALS">
			<!-- Modal -->
<div class="modal fade" id="adsNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <?php echo do_shortcode('[AWPCPPLACEAD]');?>
      </div>
     
    </div>
  </div>
</div><!--End of modal-->
		</div>
		
		<div class="techriver2">
			<?php require_once('header-county.php');?>
			
			<div class="container" id="tc-body">
				<div class="row">
					<div class="col-xs-12 outerpadding">
						<div class="box-content">
							<?php echo do_shortcode('[load_county_desc id="'.$post->ID.'"]');?>
						</div>
					</div>
				</div>
				<div class="row">
				
					<div class="col-xs-3 outerpadding">
						<div class="white-cover">
							<h2 class="header-brand">Local News</h2>
							<div class="box-content">
							
								<?php echo do_shortcode('[load_news_snippet post_id="'.$post->ID.'" type="news"]');?>
						
								<div style="clear:both;"></div>
							</div>
						</div>
						<div class="clear"></div>
						<div class=" margin-top"></div>
							<h2 class="header-brand">Business News</h2>
							<div class="box-content">
							
								<?php echo do_shortcode('[load_news_snippet post_id="'.$post->ID.'" type="business"]');?>
						
								<div style="clear:both;"></div>
							</div>
						
						<div class="clear"></div>
						<div class=" margin-top"></div>
							<h2 class="header-brand">Events News</h2>
							<div class="box-content">
							
								<?php echo do_shortcode('[load_news_snippet post_id="'.$post->ID.'" type="events"]');?>
						
								<div style="clear:both;"></div>
							</div>
						
					</div>

					<div class="col-xs-9 outerpadding">
						<div class="white-cover">
							<h2 class="header-brand">Blog Posts</h2>
							<div class="box-content" style="">
								<div id="tc-blog-posts-snips">
								
								   <?php echo do_shortcode('[load_county_blog_snips id="'.$post->ID.'"]');?>
								 
								</div>
								<a href="?view=blog" class="pull-right">View all Blog Posts</a>
								<div class="clear"></div>
							</div>
						</div>

					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 outerpadding">
						<?php
					?>
						<h2 class="header-brand">Classifieds</h2>
							<div class="box-content" style="">
								<?php
global $wpdb; $tablename = $wpdb->prefix.'tc_county';$data=$wpdb->get_row("SELECT * FROM {$tablename} WHERE post_id={$post->ID}",ARRAY_A);?>
								
								<?php 

if(isset($_GET['action']) && $_GET['action'] == 'browse-cat' || isset($_GET['category_id'])) {
	echo do_shortcode('[AWPCPBROWSECATS county_id="'.$data['id'].'"]');   
}
   else {
	echo do_shortcode('[AWPCPBROWSEADS county_id="'.$data['id'].'"]');
   }
?>
								<div style="clear:both;"></div>
							</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 outerpadding">
						<div class="box-content">
							<div class="social-media" style="float:left;"><i class="icon ion-social-facebook"></i><i class="icon ion-social-twitter"></i><i class="icon ion-social-youtube"></i></div>
							<div class="pull-right"><?php echo $county->get_title();?></div>
							<div class="clear"></div>
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
    visible: 3,
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