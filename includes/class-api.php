<?php

namespace SS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class API {

	public $token = '';

	public $base_url = 'https://api.envato.com/%s/market';

	public $api_ver = 'v2';

	public $url = '';

	public function __construct( Options $options ) {
		$this->token = $options->get('token');
		if( empty($this->token) ) {
			SS_Envato_API()->notices->add_warning('Please, set up your Envato API token.', true);
		}
	}

	public function call($method, $data = array(), $api_ver = 'v2') {
		
		$this->api_ver = $api_ver;

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
		$this->url = str_replace('%s', $this->api_ver, $this->base_url);

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
