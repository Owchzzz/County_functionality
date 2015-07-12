
<style>
ul.county-admin-menu {
	margin-top:0px;
}
ul.county-admin-menu > li {
	display:inline;
	padding:3px;
	border-right:1px solid #fafafa;
}
	
ul.county-admin-menu > li:(last-of-type) {
	border-right:none;
}
	
div.optionspannel {
	background-color:#fafafa;
	padding:15px;
	border:1px solid #cdcdcd;
}
	
.optionspannel label{
	display:block;	
	margin-top:8px;
}
	
.optionspannel input[type="submit"] {
	display:block;
	float:right;
	clear:both;
	margin-top:15px;
	font-size:11px;
	background-color:#545454;
	padding:5px;
	color:white;
	border:0px;
	cursor:pointer;
}
</style>

<?php if(isset($action)) {
	if($action == true) {
		echo '<div class="updated notice is-dismissible"><p>Succesfully deleted post.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
	}
	else {
		echo '<div class="error notice is-dismissible"><p>Unable to execute command.</p></div>';
	}	
}
?>

<div class="wrap">
	<h4>
		<i class="dashicons-before dashicons-location"></i><?php echo $county['name'];?>
	</h4>
	<div class="postbox is-dismissible" style="padding:15px;">
		<h3>
			Welcome to county administration!
		</h3>
		<p>
			Here you will be able to manage all the aspects of your county. If you find any problems / bugs please email <a href="#">owchzzz@gmail.com</a>
		</p>
	</div>
	<div style="display:block;height:10px;clear:both;">
		<!-- Empty Block-->
	</div>
	
	<div class="postbox" style="padding:15px;width:90%;margin-bottom:15px;">
		<b style="display:block;float:left;">Admin menu: </b>
		<ul style="display:inline;list-style-type:none;float:left;margin-left:15px;" class="county-admin-menu">
			<li><a href="<?php echo admin_url('post.php?post='.$county['post_id'].'&action=edit');?>">Edit Main County Page</a></li>
			<li><a href="admin.php?page=<?php echo $menuslug;?>&cat=news">News Section</a></li>
			<li><a href="admin.php?page=<?php echo $menuslug;?>&cat=business">Business Section</a></li>
			<li><a href="admin.php?page=<?php echo $menuslug;?>&cat=obituaries">Obituaries</a></li>
			<li><a href="admin.php?page=<?php echo $menuslug;?>&cat=classifieds">Classifieds</a></li>
		</ul>
		<div style="clear:both">
			
		</div>
	</div>
	<div class="postbox blog-posts" style="padding:15px;width:60%;float:left;">
		<?php $post_name = "Blog";
		
			if(isset($_GET['cat'])) {
				$cat = $_GET['cat'];
				if($cat == 'news' ){
					$post_name = "News";
				}
				else if($cat == 'business') {
					$post_name = "Business";
				}
				else if($cat == 'obituaries') {
					$post_name = "Obituaries";
				}
				else if($cat == 'classifieds') {
					$post_name = "Classifieds";
				}
			}
		?>
		<h2 style="margin-bottom:15px;">
			<?php echo $post_name;?> Posts <a href="<?php echo admin_url('post-new.php?post_type='.$county['custom_post_id']);?><?php if(isset($_GET['cat'])) echo '&cat='.$_GET['cat'];?>" class="add-new-h2">Add new</a>
		</h2>
		<hr/>
		
		<!--TABLE-->
		<form method="POST">

	<?php // Show table
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Techriver_maplists_list extends WP_List_Table{
	protected $tablename; //Name of table you are going to use refer to contructor function
	protected $per_page; //Items per page. Set in the constructor function
	protected $county;
	protected $columns; // Columns for the table set in the constructor function
	
	
	  public function __construct($county) {
 
        parent::__construct( [
            'singular' => __( 'List', 'sp' ), //singular name of the listed records
            'plural'   => __( 'Lists', 'sp' ), //plural name of the listed records
            'ajax'     => false //should this table support ajax?
 
        ] );
		global $wpdb;
		  $this->county = $county;
		  
		//Settings
		$this->tablename = $wpdb->prefix . 'tc_county'; //Change this to the table name of your data
		$this->per_page = 10; //Change this to the number of items per page.
		
		  
		 $this->columns = array( // Columns for the table please use the correct identifier for the key. use the exact same name as what is stored on database.
			 'cb'      => '<input type="checkbox" />', // Leave this in for bulk functionality
			 'ID' => 'ID',
			 'post_title' => 'Title',
		 );
		 if(isset($_GET['cat'])){
			if($_GET['cat'] == 'classifieds') {
				  $this->columns['classifieds_approved'] = 'Approved Classified';
			  } 
		 } 
			  
		
 
    }
	
	public function get_data($per_page = 1, $page_number = 1) {
		global $wpdb;
 
		$args = array(
		'posts_per_page' => $per_page,
		'offset' => ($page_number - 1) * $per_page);
		
		if(! empty($_REQUEST['orderby']) ) {
			$args['orderby'] = $_REQUEST['orderby'];
			$args['order'] = $_REQUEST['order'];
		}
		
		if(! empty($_REQUEST['s'])) {
			$args['s'] = $_REQUEST['s'];
		}
		
		$args['post_type'] = $this->county['custom_post_id'];
		if(isset($_GET['cat'])) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category_county',
					'field' => 'slug',
					'terms' => array($_GET['cat'])
				)
			);
		}
		else {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category_county',
					'field' => 'slug',
					'terms' => array('news','business','events','obituaries'),
					'operator' => 'NOT IN')
			);
		}
		
		$results = (array) get_posts($args);
		
		$fresults = array();
		foreach($results as $result) {
			$rawarr = $result;
			$finarr = array();
			foreach($rawarr as $key=>$val) {
				$finarr[$key] = $val;
			}
			$fresults[] = $finarr;
		}
		//echo 'County id: '.$this->county['custom_post_id'];
		//var_dump($results);

		 return $fresults;
	}
	
	public static function delete_data( $id, $tablename ) {
		  global $wpdb;
		  $wpdb->delete(
		    "{$tablename}",
		    [ 'id' => $id ],
		    [ '%d' ]
		  );
	}
	
	public static function record_count($county) {
  		$args = array('post_type'=>$county['custom_post_id']);
		if(isset($_GET['cat'])) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category_county',
					'field' => 'slug',
					'terms' => array($_GET['cat'])
				)
			);
		}
		else {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category_county',
					'field' => 'slug',
					'terms' => array('news','business','events','obituaries'),
					'operator' => 'NOT IN')
			);
		}
		$results = get_posts($args);
 		
  		return count($results);
	}
	
	
	public function no_items() {
  		_e( 'No data avaliable.', 'sp' );
	}
	
	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'special':
			case 'city':
				return $item[ $column_name ];
			default:
				return $item[ $column_name ]; //Show the default val
		}
	}
	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);
	}
	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {
		$delete_nonce = wp_create_nonce( 'sp_delete_customer' );
		$title = '<strong>' . $item['name'] . '</strong>';
		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		];
		return $title . $this->row_actions( $actions );
	}
	
	function column_id( $item ) {
		$delete_nonce = wp_create_nonce( 'sp_delete_customer');
		$modify_nonce = wp_create_nonce( 'sp_modify_customer' );
		$title = '<strong>' . $item['ID'] . '</strong>';
		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce ),
			'modify' => '<a href="'.admin_url('post.php?post='.$item['ID'].'&action=edit').'">Modify</a>'
		];
		return $title . $this->row_actions( $actions );
	}
	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = $this->columns;
		return $columns;
	}
	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'ID' => array ( 'ID', true)
		);
		return $sortable_columns;
	}
	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];
		return $actions;
	}
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$columns = $this->columns;
		$hidden = array(); //hidden columns
		$sortable = $this->get_sortable_columns();
		
		$this->_column_headers = array($columns,$hidden,$sortable);
		/** Process bulk action */
		$this->process_bulk_action();
		$per_page     = $this->per_page;
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count($this->county);
		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );
		$this->items = $this->get_data( $per_page, $current_page );
		$this->process_bulk_action();
	}
	public function process_bulk_action() {
		
		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				$this->delete_data($id,$this->tablename);
			}
			//wp_redirect( admin_url('admin.php?page=tcmaplists_admin')  );
			echo '<meta http-equiv="refresh" content="0; url='.admin_url('admin.php?page=tcmaplists_admin').'">';
			exit;
		}
	}
}
$map_lists_list = new Techriver_maplists_list($county);

$map_lists_list->prepare_items();
$map_lists_list->search_box('Search Posts', 'search-box');
$map_lists_list->display();


?> <!--END OF MAP List Table PHP-->
				
	</form>
		
		<!--END OF TABLE-->
	</div>
	
	
	<div class="postbox blog-widgets" style="padding:15px;width:32%;float:right;"><!--Sidebar-->
		<h4>
			County options
		</h4>
		
			<div class="optionspannel">
				<form method="post">
					<input type="hidden" name="action" value="themes"/>
					<label><input type="checkbox" name="custom_theme" /> Enable custom template</label>
					<input type="submit" value="save"/>
					<div style="clear:both;">
						
					</div>
				</form>	
				
			</div>
		<h4>
			Navigation
		</h4>
		
			<div class="optionspannel">
				<form method="post">
					<input type="hidden" name="action" value="navigation" />
					<label><input type="checkbox" name="nav_menu_news" /> News Section</label>
					<label><input type="checkbox" name="nav_menu_business" /> Business Section</label>
					<label><input type="checkbox" name="nav_menu_obituaries" /> Obituaries Section</label>
					<label><input type="checkbox" name="nav_menu_events" /> Local Events Section</label>
					<label><input type="checkbox" name="nav_menu_classifieds" /> Classifieds Section</label>
					<input type="submit" value="save"/>
					<div style="clear:both;">
						
					</div>
				</form>	
			</div>
	</div>
</div>