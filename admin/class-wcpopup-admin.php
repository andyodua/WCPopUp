<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       WCPopUp
 * @since      1.0.0
 *
 * @package    Wcpopup
 * @subpackage Wcpopup/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wcpopup
 * @subpackage Wcpopup/admin
 * @author     WCPopUp <WCPopUp>
 */
class Wcpopup_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) { 
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);   
		add_action('admin_init', array( $this, 'registerAndBuildFields' )); 
		add_filter('plugin_action_links_'.WCPOPUP_PLUGIN_BASENAME,array( $this, 'WcPopUpLink' ));//Plugin Settings Page
	
	}
	/**
	 * Link setting on plugin page
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */	
	public function WcPopUpLink($links ){
		$action_links = array(
			'settings' 	=> '<a href="' . admin_url( 'admin.php?page=wcpopup-settings' ) . '">Settings</a>',
			'support' 	=> '<a href="https://andy.od.ua/wp/index.php?route=support" target="__blank">Support</a>',
			'upgrade' 	=> '<a href="https://andy.od.ua/wp/index.php?route=article/view&name=wcpopup" target="__blank">Upgrade</a>',
		);

		return array_merge( $action_links, $links );
	}	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wcpopup-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wcpopup-admin.js', array( 'jquery' ), $this->version, false );

	}
	public function addPluginAdminMenu() {
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page(  $this->plugin_name, 'WCPopUp', 'administrator', $this->plugin_name, array( $this, 'displayPluginAdminDashboard' ), 'dashicons-chart-area', 26 );
		
		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page( $this->plugin_name, 'Page Settings', 'Settings', 'administrator', $this->plugin_name.'-settings', array( $this, 'displayPluginAdminSettings' ));
	}
	public function displayPluginAdminDashboard() {
		require_once 'partials/'.$this->plugin_name.'-admin-display.php';
	}
	public function displayPluginAdminSettings() {
		// set this var to be used in the settings-display view
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
		if(isset($_GET['error_message'])){
				add_action('admin_notices', array($this,'settingsPageSettingsMessages'));
				do_action( 'admin_notices', $_GET['error_message'] );
		}
		require_once 'partials/'.$this->plugin_name.'-admin-settings-display.php';
	}
	public function settingsPageSettingsMessages($error_message){
		switch ($error_message) {
				case '1':
						$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );                 $err_code = esc_attr( 'settings_page_example_setting' );                 $setting_field = 'settings_page_example_setting';                 
						break;
		}
		$type = 'error';
		add_settings_error(
					$setting_field,
					$err_code,
					$message,
					$type
			);
	}
	public function addSettingsField($array = array()){
		$args = array (
			'type'      => 'input',
			'subtype'   => $array['subtype'],
			'id'    => $array['id'],
			'name'      => $array['name'],
			'required' => 'true',
			'get_options_list' => '',
			'value_type'=>'normal',
			'wp_data' => 'option'
		);
		add_settings_field(
			$array['id'],
			$array['string'],
			array( $this, 'settings_page_render_settings_field' ),
			'wcpopup_general_settings',
			$array['section'],
			$args
		);		
		register_setting(
			'wcpopup_general_settings',
			$array['id'],
		);		
	}
	public function registerAndBuildFields() { 
		add_settings_section(
			// ID used to identify this section and with which to register options
			'wcpopup_general_section_popup', 
			// Title to be displayed on the administration page
			'',  
			// Callback used to render the description of the section
				array( $this, 'settings_page_display_general_popup' ),    
			// Page on which to add this section of options
			'wcpopup_general_settings'                   
		);	
		add_settings_section(
			// ID used to identify this section and with which to register options
			'wcpopup_general_section_progressbar', 
			// Title to be displayed on the administration page
			'',  
			// Callback used to render the description of the section
				array( $this, 'settings_page_display_general_progressbar' ),    
			// Page on which to add this section of options
			'wcpopup_general_settings'                   
		);			
		$this->addSettingsField(array(
			'subtype'   => 'checkbox',
			'id'    => 'wcpopup_popup_enable',
			'name'      => 'wcpopup_popup_enable',
			'string'      => 'Показывать вспылавающее окно',
			'section'	=> 'wcpopup_general_section_popup', 
		));	
		$this->addSettingsField(array(
			'subtype'   => 'text',
			'id'    => 'wcpopup_popup_counter',
			'name'      => 'wcpopup_popup_counter',
			'string'      => 'Показывать окно раз (событие купить)',
			'section'	=> 'wcpopup_general_section_popup', 
		));				
		$this->addSettingsField(array(
			'subtype'   => 'text',
			'id'    => 'wcpopup_popup_timer',
			'name'      => 'wcpopup_popup_timer',
			'string'      => 'Задержка в секундах',
			'section'	=> 'wcpopup_general_section_popup', 
		));			
		$this->addSettingsField(array(
			'subtype'   => 'checkbox',
			'id'    => 'wcpopup_progressbar_enable_wc',
			'name'      => 'wcpopup_progressbar_enable_wc',
			'string'      => 'Показывать прогрессбар в корзине (checkout)',
			'section'	=> 'wcpopup_general_section_progressbar', 
		));	
		$this->addSettingsField(array(
			'subtype'   => 'checkbox',
			'id'    => 'wcpopup_progressbar_enable_xoo',
			'name'      => 'wcpopup_progressbar_enable_xoo',
			'string'      => 'Показывать прогрессбар в корзине (popup)',
			'section'	=> 'wcpopup_general_section_progressbar', 
		));			
		$this->addSettingsField(array(
			'subtype'   => 'checkbox',
			'id'    => 'wcpopup_chk_name',
			'name'      => 'wcpopup_chk_name',
			'string'      => 'Требовать имя',
			'section'	=> 'wcpopup_general_section_popup', 
		));
		$this->addSettingsField(array(
			'subtype'   => 'checkbox',
			'id'    => 'wcpopup_chk_phone',
			'name'      => 'wcpopup_chk_phone',
			'string'      => 'Требовать телефон',
			'section'	=> 'wcpopup_general_section_popup', 
		));
		$this->addSettingsField(array(
			'subtype'   => 'checkbox',
			'id'    => 'wcpopup_chk_email',
			'name'      => 'wcpopup_chk_email',
			'string'      => 'Требовать емаил',
			'section'	=> 'wcpopup_general_section_popup', 
		));
		$this->addSettingsField(array(
			'subtype'   => 'checkbox',
			'id'    => 'wcpopup_chk_country',
			'name'      => 'wcpopup_chk_country',
			'string'      => 'Требовать страну',
			'section'	=> 'wcpopup_general_section_popup', 
		));		

		$this->addSettingsField(array(
			'subtype'   => 'text',
			'id'    => 'wcpopup_country_poshlina',
			'name'      => 'wcpopup_country_poshlina',
			'string'      => 'Пошлина',
			'section'	=> 'wcpopup_general_section_progressbar', 
		));
		$this->addSettingsField(array(
			'subtype'   => 'text',
			'id'    => 'wcpopup_country_weight',
			'name'      => 'wcpopup_country_weight',
			'string'      => 'Вес',
			'section'	=> 'wcpopup_general_section_progressbar', 
		));			
		$this->addSettingsField(array(
			'subtype'   => 'text',
			'id'    => 'wcpopup_country_poshlina_default',
			'name'      => 'wcpopup_country_poshlina_default',
			'string'      => 'Пошлина по умолчанию',
			'section'	=> 'wcpopup_general_section_progressbar', 
		));
		$this->addSettingsField(array(
			'subtype'   => 'text',
			'id'    => 'wcpopup_country_weight_default',
			'name'      => 'wcpopup_country_weight_default',
			'string'      => 'Вес по умолчанию',
			'section'	=> 'wcpopup_general_section_progressbar', 
		));		
	}
	public function settings_page_display_general_popup() {
		echo '<p><h2>всплывающее окно</h2></p>';
	} 
	public function settings_page_display_general_progressbar() {
		echo '<p><h2>прогресс бар</h2></p>';
	} 	
	public function settings_page_render_settings_field($args) {
			/* EXAMPLE INPUT
								'type'      => 'input',
								'subtype'   => '',
								'id'    => $this->plugin_name.'_example_setting',
								'name'      => $this->plugin_name.'_example_setting',
								'required' => 'required="required"',
								'get_option_list' => "",
									'value_type' = serialized OR normal,
			'wp_data'=>(option or post_meta),
			'post_id' =>
			*/     
		if($args['wp_data'] == 'option'){
			$wp_data_value = get_option($args['name']);
		} elseif($args['wp_data'] == 'post_meta'){
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
		}

		switch ($args['type']) {

			case 'input':
					$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
					if($args['subtype'] != 'checkbox'){
							$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
							$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
							$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
							$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
							$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
							if(isset($args['disabled'])){
									echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
							} else {
									echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
							}

					} else {
							$checked = ($value) ? 'checked' : '';
							echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
					}
					break;
			default:
					# nope
					break;
		}
	}
}
