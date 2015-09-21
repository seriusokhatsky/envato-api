<?php
/**
 * Plugin Name: Envato API
 * Plugin URI: https://twitter.com/sergsokhatskiy
 * Description: API envato plugin to work with purchase codes
 * Version: 0.0.1
 * Author: Sergey Sokhatskiy
 * Author URI: https://twitter.com/sergsokhatskiy
 * Text Domain: envato-api
 */

use SS\Activator;
use SS\Deactivator;
use SS\User;
use SS\PurchaseRepo;
use SS\API;
use SS\Options;
use SS\Notices;
use SS\BBpress;
use SS\FormHandler;
use SSAdmin\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


final class SS_Envato_API {
	protected static $_instance = null;

	public $purchase_repo;
	public $admin;
	public $user;
	public $options;
	public $notices;
	public $bbpress;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'envato-api' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'envato-api' ), '2.1' );
	}

	public function __construct() {
		// Define constants
		$this->define_constants();

		// Include required files
		$this->includes();

		// Hooks
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'template_redirect', array( $this, 'init_redirects' ), 0 );
		if ( is_admin() ) {
			$this->admin_init();
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 1 );

		// Loaded action
		do_action( 'ss_envato_api_loaded' );
	}

	private function define_constants() {
		define( 'SS_PLUGIN_FILE', __FILE__ );
		define( 'SS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
	}

	private function includes() {

		include_once( 'includes/envato-api-functions.php' );
		include_once( 'includes/class-notices.php' );
		include_once( 'includes/class-api.php' );
		include_once( 'includes/class-options.php' );
		include_once( 'includes/class-purchase-repo.php' );
		include_once( 'includes/class-user.php' );
		include_once( 'includes/class-bbpress.php' );
		include_once( 'includes/class-form-handler.php' );
		

		if ( is_admin() ) {
			include_once( 'includes/admin/class-admin.php' );
			include_once( 'includes/admin/class-meta.php' );
			include_once( 'includes/admin/class-licenses-table.php' );
			include_once( 'includes/admin/class-licenses-page.php' );
			include_once( 'includes/admin/class-verification-page.php' );
			include_once( 'includes/admin/class-downloads-page.php' );
		}
		
		$this->notices 			= new Notices();
		$this->options 			= new Options();
		$this->options->redux_init();

		if ( defined( 'DOING_AJAX' ) ) {
			$this->ajax_includes();
		}

		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			$this->frontend_includes();
		}
	}

	public function ajax_includes() {
		//include_once( 'includes/class-wc-ajax.php' );                           // Ajax functions for admin and the front-end
	}

	public function frontend_includes() {
		// Function
		include_once( 'includes/template-tags.php' );

		// Classes
	}


	public function init() {
		// Before init action
		do_action( 'before_ss_envato_api_init' );

		// Load class instances
		// 
		
		$this->api 				= new API( $this->options );
		$this->purchase_repo 	= new PurchaseRepo( $this->api  );
		$this->user 			= new User( $this->purchase_repo );
		$this->bbpress		    = new BBpress( $this->user, $this->purchase_repo, $this->options );
		
		new FormHandler( $this->purchase_repo, $this->notices );

		// Init action
		do_action( 'after_ss_envato_api_init' );
	}



	public function init_redirects() {
		
	}

	public function admin_init() {
		$this->admin = new Admin();
	}

	public function scripts() {
		// Register the script
		wp_register_script( 'envato-api', $this->plugin_url() . '/assets/js/envato-api.js', array(), '1.0.0', true );

		// Localize the script with new data
		$translation_array = array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		);

		wp_localize_script( 'envato-api', 'ss_envato', $translation_array );

		// Enqueued script with localized data.
		wp_enqueue_script( 'envato-api' );
	}


	/** Helper functions ******************************************************/

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

}

function activate_envato_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-envato-api-activator.php';
	Activator::activate();
}

function deactivate_envato_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-envato-api-deactivator.php';
	Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_envato_api' );
register_deactivation_hook( __FILE__, 'deactivate_envato_api' );


function SS_Envato_API() {
	return SS_Envato_API::instance();
}

$GLOBALS['ss_env'] = SS_Envato_API();