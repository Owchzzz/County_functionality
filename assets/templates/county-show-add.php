<?php

global $post;


class awpcp_post {
	public $ID;
	public $county;
	public $post;
	
	public function __construct($addID) {
		global $wpdb,$post;
		$this->ID = $addID;	
		$tablename = $wpdb->prefix.'awpcp_ads';
		$data = $wpdb->get_row("SELECT * FROM {$tablename} WHERE ad_id={$this->ID}",ARRAY_A);
		$this->post = $post;
		
		$tablename = $wpdb->prefix.'tc_county';
		$county_id = $data['county_id'];
		$data = $wpdb->get_row("SELECT * FROM {$tablename} WHERE id={$county_id}",ARRAY_A);
		
		$this->county = $data;
	}
	
	
	public function get_title() {
		return $this->county['name'];
	}
	
	public function get_link() {
		return $this->county['custom_page_id'];	
	}
	
}

$county = new awpcp_post(mysql_real_escape_string($_GET['id']));
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

		
		<div class="techriver2">
			<?php require_once('header-county.php');?>
			<div class="container">
				<div class="row">
					<div class="col-xs-9 outerpadding">
						<div class="box-content">
							<?php while(have_posts()): the_post(); ?>
							<?php echo the_content();?>
							<?php endwhile;?>
						</div>
					</div>
					<div class="col-xs-3 outerpadding">
						<div class="box-content">
							<h2>Test content</h2>
							<p>Test Content</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!--Footer-->
		<?php wp_footer();?>
	</body>
</html>