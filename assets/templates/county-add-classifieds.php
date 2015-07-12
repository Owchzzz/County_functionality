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
		</style>
	</head>
	<body>
		<div class="techriver2">
			<?php require_once('header-county.php');?>
			<div class="container"> <!-- Main Container-->
				
			</div><!-- End of container-->
			
		</div>
		
		<!--Footer-->
		<?php wp_footer();?>
		
	</body>
</html>