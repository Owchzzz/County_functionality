<style>
	.formgroup {
		display:block;
		clear:both;
		margin-top:15px;
	}
</style>
<?php


if(isset($_POST['action']) && $_POST['action'] == 'save') {
	$sanitizeddata = array();
	foreach($_POST as $key=>$val) {
		$sanitizeddata[mysql_real_escape_string($key)] = mysql_real_escape_string($val);
	}
	
	update_option('tc_county_data',$sanitizeddata);
}

$options = get_option('tc_county_data');
?>
<div class="wrap">
	<h4>
		Settings
	</h4>
	<div class="postbox" style="padding:12px;">
		<form action="" method="post">
			<input type="hidden" name="action" value="save"/>
			<div class=".formgroup">
				<b>Main Menu Style ID: </b>
				<input type="text" name="main-menu-id" value="<?php echo $options['main-menu-id'];?>"/> - The style ID of the main menu on your site. this is not the slug/id of the menu inside the actual wordpress database.
			</div>
			
			<input class="primary-btn" style="float:right;clear:both;" type="submit" value="Save"/>
			<div style="clear:both;">
				<!--empty div-->
			</div>
		</form>
	</div>
</div>