<?php if(isset($action)) {
	if($action == true) {
		echo '<div class="updated notice is-dismissible"><p>Succesfully deleted post.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
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
		<b>Admin menu</b>: <a href="<?php echo admin_url('post.php?post='.$county['post_id'].'&action=edit');?>">Edit Main County Page</a>
	</div>
	<div class="postbox blog-posts" style="padding:15px;width:60%;float:left;">
		<h2 style="margin-bottom:15px;">
			Blogs Posts <a href="<?php echo admin_url('post-new.php?post_type='.$county['custom_post_id']);?>" class="add-new-h2">Add new</a>
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
	
	
	<div class="postbox blog-widgets" style="padding:15px;width:32%;float:right;">
		<h4>
			Sidebar
		</h4>
		<p style="background-color:#fafafa">
			More Settings and functionality here.
		</p>
	</div>
</div>