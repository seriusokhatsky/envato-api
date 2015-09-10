<?php

namespace SSAdmin;

use WP_List_Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class LicensesTable extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Licenses' ), //singular name of the listed records
			'plural'   => __( 'License' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );

	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'purchase_code'    => __( 'Purchase Code' ),
			'user_id' => __( 'User'),
			'envato_id' => __( 'Envato item ID'),
			'support' => __( 'Support untill'),
		];

		return $columns;
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'purchase_code':
			case 'user_id':
			case 'envato_id':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}


	public function column_purchase_code($item){

		// create a nonce
		$delete_nonce = wp_create_nonce( 'ss_delete_purchase_code' );
		
		// Get Envato Data
		$envato_data = json_decode($item['api_response'], true);

		
		// Return
		$out = sprintf('<strong>%s</strong><p>Item: %s<br />Envato Buyer: %s<br>Type: %s</p>',
			$item[ 'purchase_code' ],
			$envato_data['item']['name'],
			$envato_data['buyer'],
			$envato_data['license']
		);

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&purchase_id=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		];

		return $out . $this->row_actions( $actions );
		
		
	}

	public function column_user_id( $item ) {
		// Get user data
		$thisUser = get_userdata($item['user_id']);
		
		// Return
		return sprintf('<strong>ID: %s</strong><p>Username: %s</p>',
			$item['user_id'],
			$thisUser->user_login
		);
	}

	public function column_support( $item ) {
		if( ss_is_date_expired( $item['supported_until'] )) {
			return '<strong style="color:red;">Expired in </strong>' . $item['supported_until'];
		} else {
			return '<strong style="color:green;">Suppor until </strong>' . $item['supported_until'];
		}
		return $item['supported_until'];
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'envato_id' => array( 'envato_id', true ),
			'support' => array( 'supported_until', true ),
		);

		return $sortable_columns;
	}

	public function prepare_items( $search = false, $user_id = false) {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'licenses_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );


		$this->items = self::get_licenses( $per_page, $current_page, $search, $user_id );

	}

	public static function get_licenses( $per_page = 5, $page_number = 1, $search = false, $user_id = false ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}ss_purchase_codes";

		if( $search ) {
			$sql .= sprintf(
						" WHERE `envato_id` LIKE '%%%s%%' OR `user_id` LIKE '%%%s%%' OR `purchase_code` LIKE '%%%s%%'", 
						$search,	
						$search,	
						$search
					);
		}

		if( $user_id ) {
			$sql .= sprintf(
						" WHERE `user_id`=%d", 
						$user_id
					);
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

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	public static function delete_license( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}ss_purchase_codes",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ss_purchase_codes";

		return $wpdb->get_var( $sql );
	}

	public function has_items() {
		return (self::record_count() > 0);
	}

	public function no_items() {
	 	_e( 'No licenses avaliable.', 'ss' );
	}
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	public function process_bulk_action() {

	  //Detect when a bulk action is being triggered...
	  if ( 'delete' === $this->current_action() ) {

	    // In our file that handles the request, verify the nonce.
	    $nonce = esc_attr( $_REQUEST['_wpnonce'] );

	    if ( ! wp_verify_nonce( $nonce, 'ss_delete_purchase_code' ) ) {
	      die( 'Go get a life script kiddies' );
	    }
	    else {
	      self::delete_license( absint( $_GET['purchase_id'] ) );

			echo '<div id="message" class="updated"><p>Purchase Codes Deleted.</p></div>';
	    }

	  }

	  // If the delete bulk action is triggered
	  if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
	       || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
	  ) {

	    $delete_ids = esc_sql( $_POST['bulk-delete'] );

	    // loop over the array of record IDs and delete them
	    foreach ( $delete_ids as $id ) {
	      self::delete_license( $id );

	    }

		echo '<div id="message" class="updated"><p>Purchase Codes Deleted.</p></div>';
	  }
	}
}