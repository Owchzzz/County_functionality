<header class="county-header">
				<div class="container">
				<div class="row">
					<div class="col-md-offset-3 col-xs-6 county-header">
						<a href="<?php echo $county->get_link();?>">
						<img src="http://port-80.74cjgse9go561orvo9o6g3kysii19k9g9vlw38kn75jyvi.box.codeanywhere.com/wordpress/wp-content/uploads/2015/07/countylogo.png" class="county-logo"/>
						<h2 class="county-text">
							<?php echo $county->get_title();?>
						</h2>
						</a>
					</div>
					<div class="col-xs-offset-3  col-md-offset-0 col-xs-3">
						<nav class="county-menu">
							<ul>
								<li><a href="<?php echo home_url('/');?>">Militiatoday Home</a></li>
								<li><A href="<?php echo home_url('/index.php/tc_county_func_main/');?>">Counties</a></li>
								<li><a href="<?php echo home_url('/');?>"><?php echo $county->get_title();?> Channels</a></li>
								<li><a href="<?php echo home_url('/');?>">Localnews</a></li>
								<li><a href="<?php echo home_url('/');?>">The PCP</a></li>
								<li><a href="<?php echo home_url('/');?>">The ASR</a></li>
							</ul>
						</nav>
					</div>
					</div><!-- ./container-->		 
									 
				</div>
				<div style="clear:both;"></div>
			</header>