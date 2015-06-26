<style>
div.group {
	display:block;
	margin-top:25px;
	padding:5px;
}
	
.group > input {
	display:block;
	padding:5px;
	font-size:14px;
	width:50%;
}

	
.group > input[type="submit"] {
	border:0px;
	width:20%;
	background-color:#3b99c0;
	color:white;
}
</style>

<?php

$us_state_abbrevs_names = array(
	'AL'=>'ALABAMA',
	'AK'=>'ALASKA',
	'AS'=>'AMERICAN SAMOA',
	'AZ'=>'ARIZONA',
	'AR'=>'ARKANSAS',
	'CA'=>'CALIFORNIA',
	'CO'=>'COLORADO',
	'CT'=>'CONNECTICUT',
	'DE'=>'DELAWARE',
	'DC'=>'DISTRICT OF COLUMBIA',
	'FM'=>'FEDERATED STATES OF MICRONESIA',
	'FL'=>'FLORIDA',
	'GA'=>'GEORGIA',
	'GU'=>'GUAM GU',
	'HI'=>'HAWAII',
	'ID'=>'IDAHO',
	'IL'=>'ILLINOIS',
	'IN'=>'INDIANA',
	'IA'=>'IOWA',
	'KS'=>'KANSAS',
	'KY'=>'KENTUCKY',
	'LA'=>'LOUISIANA',
	'ME'=>'MAINE',
	'MH'=>'MARSHALL ISLANDS',
	'MD'=>'MARYLAND',
	'MA'=>'MASSACHUSETTS',
	'MI'=>'MICHIGAN',
	'MN'=>'MINNESOTA',
	'MS'=>'MISSISSIPPI',
	'MO'=>'MISSOURI',
	'MT'=>'MONTANA',
	'NE'=>'NEBRASKA',
	'NV'=>'NEVADA',
	'NH'=>'NEW HAMPSHIRE',
	'NJ'=>'NEW JERSEY',
	'NM'=>'NEW MEXICO',
	'NY'=>'NEW YORK',
	'NC'=>'NORTH CAROLINA',
	'ND'=>'NORTH DAKOTA',
	'MP'=>'NORTHERN MARIANA ISLANDS',
	'OH'=>'OHIO',
	'OK'=>'OKLAHOMA',
	'OR'=>'OREGON',
	'PW'=>'PALAU',
	'PA'=>'PENNSYLVANIA',
	'PR'=>'PUERTO RICO',
	'RI'=>'RHODE ISLAND',
	'SC'=>'SOUTH CAROLINA',
	'SD'=>'SOUTH DAKOTA',
	'TN'=>'TENNESSEE',
	'TX'=>'TEXAS',
	'UT'=>'UTAH',
	'VT'=>'VERMONT',
	'VI'=>'VIRGIN ISLANDS',
	'VA'=>'VIRGINIA',
	'WA'=>'WASHINGTON',
	'WV'=>'WEST VIRGINIA',
	'WI'=>'WISCONSIN',
	'WY'=>'WYOMING',
	'AE'=>'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST',
	'AA'=>'ARMED FORCES AMERICA (EXCEPT CANADA)',
	'AP'=>'ARMED FORCES PACIFIC'
);
$county = $results[0];
$users = get_users();
?>

<div class="wrap">
	<h2>
		Add a new County
	</h2>
	<form action="<?php echo admin_url('admin.php?page='.$_REQUEST['page'].'&action=save_new');?>" method="POST">
		<input type="hidden" name="id" value="<?php echo $county['id'];?>"/>
		<div class="group">
			<label>County Name: </label>
			<input type="text" name="name" class="widefat" value="<?php echo $county['name'];?>"/>	
		</div>
		<div class="group">
			<label>County Administrator</label>
			<select name="county_admin">
				<?php
					
					foreach($users as $user) {
						$data = $user->data;
						if($county['county_admin'] == $data->ID){
							echo '<option value="'.$data->ID.'">'.$data->display_name.'</option>';
						} 
					}
					foreach($users as $user) {
						$data = $user->data;
						if($county['county_admin'] !== $data->ID) echo '<option value="'.$data->ID.'">'.$data->display_name.'</option>';
					}
				?>
			</select>
		</div>
		<div class="group">
			<label>State: </label>
			<select name="state">
				<?php
					foreach($us_state_abbervs_names as $key=>$val) {
						if($key == $county['state']) {
							echo '<option value="'.$key.'">'.$val.'</option>';
						}
					}
					foreach($us_state_abbrevs_names as $key=>$val) {
						if($key !== $county['state'])
						echo '<option value="'.$key.'">'.$val.'</option>';
					} 
				?>
			</select>
		</div>
		<div class="group">
			<label>Description:</label>
			<?php wp_editor($county['description'],'description');?>
		</div>
		<div class="group">
			<input type="submit"/>
		</div>
		
	</form>
</div>