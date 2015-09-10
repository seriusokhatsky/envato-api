<?php

namespace SS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class API {

	public $token = '';

	public $base_url = 'https://api.envato.com/v2/market';

	public $url = '';

	public function __construct( Options $options ) {
		$this->token = $options->get('token');
	}

	public function call($method, $data = array()) {

		$response = wp_remote_get( $this->get_url($method, $data), array(
		    'headers'     => $this->get_headers(),
		) );

		return $response;

	}

	public function get_headers() {
		return array(
			'Authorization' => 'Bearer ' . $this->token
		);
	}

	public function get_url( $method, $args = array() ) {
		$this->url = $this->base_url;

		$this->url .= $method;

		if( ! empty( $args ) ) {
			foreach ($args as $key => $value) {
				$this->add_url_param($key, $value);
			}
		}


		return $this->url;
	}

	public function add_url_param( $key, $value ) {
		$this->url = add_query_arg( $key, $value, $this->url );

	}
}
