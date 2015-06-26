<style>
#message {
	display:block;
	clear:both;
}
#message p {
	padding-left:0px;
	text-align:left;
}
</style>
<?php 
function show_msg($success=true,$message="") {
	
	$class = $success ? 'updated' : 'error';
	echo '<div id="message" class="'.$class.' notice is-dismissible">
	<p>'.$message.'</p>
	<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>';
	$_SESSION['status'] = false;
}
if(isset($_GET['status']) && isset($_SESSION['status']) && $_SESSION['status'] == true) {
	if($_GET['status'] == 'success_insert_new_county') {
		show_msg(true,'Successfully inserted new county.');
	}
	else if($_GET['status'] == 'bulk_delete_complete') {
		show_msg(true,'Successfully completed delete function.');
	}
	else if($_GET['status'] == 'success_modify' && isset($_SESSION['status']) && $_SESSION['status'] == true) {
		show_msg(true,'Successfully modified county.');
	}

}


if(isset($_GET['action']) && isset($_SESSION['status']) && $_SESSION['status'] == true) {
	if($_GET['action'] == 'delete' && isset($_SESSION['delete_status'])) {
		if( $_SESSION['delete_status'] == true) {
			show_msg(true,'Sucessfully deleted county.');
		}
		else {
			show_msg(true,'Unable to delete county.');
		}
	}
}

?>


<div class="wrap">
	
	<div class="wrap">
	<h2 style="float:left; clear:both;">
		County Administration
	</h2>
	<a href="<?php echo admin_url('admin.php?page=county_func_admin_add_county');?>" style="padding:5px 10px;background-color:#fafafa;float:left;text-decoration:none;margin-top:10px;">Add new</a>
	<div style="clear:both;">
		
	</div>
	<hr/>
	<form method="POST">

	<?php // Show table
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Tc_county_list extends WP_List_Table{
	protected $tablename; //Name of table you are going to use refer to contructor function
	protected $per_page; //Items per page. Set in the constructor function
	
	protected $columns; // Columns for the table set in the constructor function
	
	
	  public function __construct() {
 
        parent::__construct( [
            'singular' => __( 'List', 'sp' ), //singular name of the listed records
            'plural'   => __( 'Lists', 'sp' ), //plural name of the listed records
            'ajax'     => false //should this table support ajax?
 
        ] );
		global $wpdb;
		  
		  
		//Settings
		$this->tablename = $wpdb->prefix . 'tc_county'; //Change this to the table name of your data
		$this->per_page = 15; //Change this to the number of items per page.
		
		  
		 $this->columns = array( // Columns for the table please use the correct identifier for the key. use the exact same name as what is stored on database.
			  'cb'      => '<input type="checkbox" />', // Leave this in for bulk functionality
			 'id' => 'ID',
			 'name' => 'Name',
			 'state' => 'State',
			 'custom_post_id' => 'Custom Post ID',
			 'county_admin' => 'Administrator',
			 
		 );
		
 
    }
	
	public function get_data($per_page = 10, $page_number = 1) {
		global $wpdb;
		
		 $sql = "SELECT * FROM {$this->tablename}";
		 if(isset($_POST['s']) && $_POST['s'] !== '') {
			 $search = mysql_real_escape_string($_POST['s']);
			 $sql = "SELECT * FROM {$this->tablename} WHERE name LIKE '%{$search}%'";
		 }
 
		 if ( ! empty( $_REQUEST['orderby'] ) ) {
		   $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		   $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		 }
		 $sql .= " LIMIT $per_page";
		 $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		 $result = $wpdb->get_results( $sql, 'ARRAY_A' );
		 return $result;
	}
	
	public static function delete_data( $id, $tablename ) {
		  global $wpdb;
		  $wpdb->delete(
		    "{$tablename}",
		    [ 'id' => $id ],
		    [ '%d' ]
		  );
	}
	
	public static function record_count($tablename) {
  		global $wpdb;
 
  		$sql = "SELECT COUNT(*) FROM ".$tablename;
 
  		return $wpdb->get_var( $sql );
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
			case 'county_admin':
				$user = get_user_by('id',$)
				return '<a href="'.admin_url('profile.php?')"></a>'
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
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}
	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	
	function column_id( $item ) {
		$delete_nonce = wp_create_nonce( 'sp_delete_customer');
		$modify_nonce = wp_create_nonce( 'sp_modify_customer' );
		$title = '<strong>' . $item['id'] . '</strong>';
		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce ),
			'modify' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Modify</a>',esc_attr( 'county_func_admin_edit_county' ), 'modify', absint( $item['id'] ), $modify_nonce )
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
			'id' => array ( 'id', true)
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
		$total_items  = self::record_count($this->tablename);
		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );
		$this->items = $this->get_data( $per_page, $current_page );
		
	}
	public function process_bulk_action() {
		
		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {
							$delete_ids = esc_sql( $_POST['bulk-delete'] );
			if(!empty($delete_ids)) {
				$_SESSION['status'] = true;
				// loop over the array of record IDs and delete them
				foreach ( $delete_ids as $id ) {
					$this->delete_data($id,$this->tablename);
				}
				//wp_redirect( admin_url('admin.php?page=tcmaplists_admin')  );
			}
				
			echo '<meta http-equiv="refresh" content="0; url='.admin_url('admin.php?page=county_func_admin&status=bulk_delete_complete').'">';
			exit;
		}
	}
}
$map_lists_list = new Tc_county_list();
if(isset($_POST['s'])) $map_lists_list->prepare_items($_POST['s']);
else $map_lists_list->prepare_items();
$map_lists_list->search_box('Search', 'search-table');
$map_lists_list->display();

?> <!--END OF MAP List Table PHP-->
				
	</form>
</div>


</div>
