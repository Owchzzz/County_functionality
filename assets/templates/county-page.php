<html>
	<head>
		<?php wp_head();?>
		<style>
			body:before {
				display:none;
				z-index:-5;
			}
			body {
				background-color:#d4d4d4;
			}
		</style>
	</head>
	<div class="overlay"></div>
	<div class="techriver">
	<nav class="navbar navbar-default navbar-static-top">
  		<div class="container">
	    		<div class="navbar-header">
				<a class="navbar-brand" href="<?php echo home_url('/');?>">
					<img alt="Militiatoday" src="http://port-80.74cjgse9go561orvo9o6g3kysii19k9g9vlw38kn75jyvi.box.codeanywhere.com/wordpress/wp-content/uploads/2015/07/logo4.png"/>
				</a>
			</div>
			
			<form class="navbar-form navbar-right" role="search">
				<div class="search-group">
					<input type="text" name="s" placeholder="search" class="searchtext"/>
					<input type="submit" value="search" class="searchbtn"/>
				</div>
			</form>
 		</div>
	</nav>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-md-8"> <!--main body-->
					<div class="content">
						<div class="content-header">
							<h2>
								County Chapters
							</h2>
						</div>
						<div class="content-body">
							<?php echo do_shortcode('[load_main_county]');?>
						</div>
						
					</div>
				</div>
				
				<div class="col-xs-12 col-md-3 col-md-offset-1">
					<div class="content rounded">
						<div class="content-header rounded-top">
							<h4>
								Recent Posts
							</h4>
						</div>
						<div class="content-body">
							<p>
							Curabitur ullamcorper commodo vulputate. Integer eget auctor ipsum. Donec scelerisque tincidunt eros. Etiam ex justo, blandit et dui non, facilisis vestibulum dui. Curabitur sit amet vestibulum orci. Sed ut aliquam turpis. Quisque finibus malesuada condimentum.	
							</p>
						</div>
					</div>
					
					<div class="content rounded margin-top">
						<div class="content-header rounded-top">
							<h4>
								Classifieds
							</h4>
						</div>
						<div class="content-body">
							<p>
							Maecenas efficitur tempus nibh, eget ornare elit consequat non. Vivamus consequat nibh nec elit laoreet, ac faucibus nunc hendrerit. Fusce id pellentesque tortor, vitae vulputate ante. Duis feugiat ullamcorper felis, nec tincidunt est tempus ac. Aliquam quis pellentesque augue. Fusce sit amet odio id tortor lacinia molestie eget at augue. Sed dignissim tincidunt mi sed scelerisque. Suspendisse non volutpat leo.	
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php wp_footer();?>
</html>