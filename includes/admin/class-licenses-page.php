<?php

namespace SSAdmin;

use SSAdmin\LicensesTable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class LicensesPage {

	static $instance;

	public $licenses_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );

		// Add Columns to User List
		add_filter('manage_users_columns', array( $this, 'column_user_id' ));
		add_action('manage_users_custom_column',  array( $this, 'column_purchases' ), 10, 3);
	}
	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function plugin_menu() {

		$hook = add_menu_page(
			'Licenses list',
			'Purchase Codes',
			'manage_options',
			'envato-codes',
			[ $this, 'plugin_settings_page' ]
		);

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}

	/**
	* Screen options
	*/
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Purchase codes',
			'default' => 30,
			'option'  => 'licenses_per_page'
		];

		add_screen_option( $option, $args );

		$this->licenses_obj = new LicensesTable();
	}

	public function plugin_settings_page() {
		if( isset($_POST['s']) ){
			$this->licenses_obj->prepare_items($_POST['s']);
		} else if( isset($_GET['uid']) ) {
			$this->licenses_obj->prepare_items(false, stripslashes($_GET['uid']));
		} else {
			$this->licenses_obj->prepare_items();
		}
		?>
		<div class="wrap">
			<h2>Purchase codes</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
					        <form method="post">
					            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
								<?php $this->licenses_obj->search_box('Search Codes', 'ss-envato-search'); ?>
					        </form>
							<form method="post">
								<?php $this->licenses_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	// Add Columns
	function column_user_id($columns) {
	    $columns['user_id'] = 'User ID';
	    $columns['purchase_codes'] = 'Purchase Codes';
	    return $columns;
	}

	// Column Callbacks
	function column_purchases($value, $column_name, $user_id){
		// Get User Data
		global $wpdb;
		$table = $wpdb->prefix . "ss_purchase_codes";
	    $user = get_userdata( $user_id );
		
		// User ID
		if( 'user_id' == $column_name ){
			
			return $user_id;
		
		// Purchase Codes
		} else if( 'purchase_codes' == $column_name ){
			
			// Return Codes
			$count = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM ".$table." WHERE user_id = '%s'", $user_id));
			if($count == 0){
				return $count;
			} else {
				return $count.' <small><a href="admin.php?page=envato-codes&uid='.$user_id.'">(View)</a></small>';
			}
			
		} // end if else	
		
	}
}