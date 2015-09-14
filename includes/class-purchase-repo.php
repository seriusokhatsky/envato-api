<?php

namespace SS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PurchaseRepo {

	private $_user_id = 0;
	private $_api;
	private $_item_id;
	private $_purchases;
	private $_table = 'ss_purchase_codes';

	public function __construct(API $api) {
		$this->_api = $api;
	}

	public function load_purchases( $item_ids = 0 ) {
		global $wpdb;
		// Get purchase codes from database
		$this->_purchases = array();
			
		if( empty( $item_ids ) ) {
			$item_ids = $this->get_envato_item_ids();
		}

		$sql = $wpdb->prepare( 
			"
	        SELECT * FROM " . $wpdb->prefix . $this->_table . "
			WHERE envato_id IN ($item_ids)
			AND user_id=%d
			",
	        	$this->get_user_id()
        );

		return $wpdb->get_results( $sql );

	}

	public function get_envato_item_ids() {
		$item_ids = 0;

		if( function_exists( 'bbp_get_forum_id' ) ) {
			$forum_id = bbp_get_forum_id();
			$item_ids = get_post_meta( $forum_id, '_ss_envato_items', true );
		}

		return $item_ids;

	}

	public function validate_code( $code ) {
		// Validate code with envato API return array response from envato OR FALSE

		$response = $this->_api->call('/author/sale', array('code' => $code)); 

		return $this->fetch_purchase_code( $response );

	}
	
	public function is_exists( $code ) {
		global $wpdb;
		// Check if purchase code is already in the database

		$codes = $wpdb->get_results( $wpdb->prepare( 
			"
				SELECT *
				FROM " . $wpdb->prefix . $this->_table . " 
				WHERE purchase_code = %s
			", 
			$code
		), ARRAY_A );

		return $codes;
	}

	public function add_code( $code, $api_response ) {
		global $wpdb;
		// Insert code to the database
		return $wpdb->insert( 
			$wpdb->prefix . $this->_table,
			array( 
				'envato_id' 		=> $api_response['item']['id'], 
				'purchase_code' 	=> $code,
				'status' 			=> 'valid',
				'user_id' 			=> $this->get_user_id(),
				'api_response' 		=> $api_response['raw_response'],
				'support_amount' 	=> $api_response['support_amount'],
				'supported_until' 	=> $api_response['supported_until'],
			) 
		);
	}

	public function is_active_support() {
		global $wpdb;
		// Is active support for item by ID
		
		$result = false; 

		$purchases = $this->load_purchases();

		if( ! empty( $purchases ) ) {
			foreach ($purchases as $code) {
				if( $result ) continue;
				if( ! ss_is_date_expired( $code->supported_until ) ) {
					$result = true;
				}
			}
		}

		return $result;
	}

	public function set_user_id( $user_id ) {
		$this->_user_id = $user_id;
	}

	public function get_user_id() {
		return $this->_user_id;
	}

	public function fetch_purchase_code($response) {
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if( empty($data) || ! empty($data['error']) ) return false;

		$return = array(
			'amount' 			=> $data['amount'],
			'sold_at' 			=> $data['sold_at'],
			'item' 				=> array(
									'id' 						=> $data['item']['id'],
									'name' 						=> $data['item']['name'],
									'number_of_sales' 			=> $data['item']['number_of_sales'],
									'wordpress_theme_metadata' 	=> @$data['item']['wordpress_theme_metadata'],
								),
			'license' 			=> $data['license'],
			'support_amount' 	=> $data['support_amount'],
			'supported_until' 	=> $data['supported_until'],
			'buyer' 			=> $data['buyer'],
			'raw_response' 		=> $body
		);

		return $return;
	}
}