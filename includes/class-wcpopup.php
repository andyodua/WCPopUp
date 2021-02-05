<?php

/**
 *
 * @link       WCPopUp
 * @since      1.0.0
 *
 * @package    Wcpopup
 * @subpackage Wcpopup/includes
 */

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Wcpopup
 * @subpackage Wcpopup/includes
 * @author     WCPopUp <WCPopUp>
 */
class Wcpopup {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wcpopup_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WCPOPUP_VERSION' ) ) {
			$this->version = WCPOPUP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wcpopup';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wcpopup_Loader. Orchestrates the hooks of the plugin.
	 * - Wcpopup_i18n. Defines internationalization functionality.
	 * - Wcpopup_Admin. Defines all hooks for the admin area.
	 * - Wcpopup_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcpopup-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcpopup-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wcpopup-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wcpopup-public.php';

		$this->loader = new Wcpopup_Loader();

	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wcpopup_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wcpopup_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wcpopup_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wcpopup_Public( $this->get_plugin_name(), $this->get_version() );
		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_action( 'init', $plugin_public, 'WcPopUpInit'); //wc popup init
		$this->loader->add_action( 'wp_logout', $plugin_public,'forceSessionEnd'); //clear session
		$this->loader->add_action( 'wp_login', $plugin_public,'forceSessionEnd');	//clear session	
		$this->loader->add_action( 'woocommerce_init', $plugin_public, 'forceWcSession'); //force session init
		$this->loader->add_action( 'wp_footer', $plugin_public, 'popupCountryForm' ); //popup form
		
		$this->loader->add_action( 'wp_ajax_nopriv_wcpopupsenddata',$plugin_public, 'sendDataAjax' ); //ajax send data from form
		$this->loader->add_action( 'wp_ajax_nopriv_wcppopupclose',$plugin_public, 'ajaxWcPopUpClose' ); //ajax send close data
		$this->loader->add_filter( 'default_checkout_billing_country', $plugin_public, 'change_default_checkout_country' ); //change field in checkout form
		
		$this->loader->add_action( 'xoo_wsc_after_products', $plugin_public,'displayProgressBlockXoo'); //add progress bar after product list in plugin side-cart-woocommerce
		$this->loader->add_action( 'woocommerce_review_order_after_cart_contents',$plugin_public, 'displayProgressBlockWC' );	//checkout page ad progress bar

		//$this->loader->add_action( 'woocommerce_review_order_before_cart_contents',$plugin_public, 'progress' );		
		$this->loader->add_action( 'woocommerce_review_order_after_cart_contents',$plugin_public, 'WcPopUpClose' ); //checkout page set close popup
		$this->loader->add_action( 'woocommerce_add_to_cart',$plugin_public, 'WcAddToCart' );	//checkout page	
		
		$this->loader->add_action( 'woocommerce_cart_has_errors' ,$plugin_public, 'wcCartError' );	//checkout pageerror message
		$this->loader->add_filter( 'woocommerce_checkout_fields', $plugin_public,  'set_checkout_fields'); //insert data form form to checkout form
		//$this->loader->add_filter( 'woocommerce_billing_fields', $plugin_public,  'set_checkout_fields');
		
		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wcpopup_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
