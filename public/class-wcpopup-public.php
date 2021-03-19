<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       WCPopUp
 * @since      1.0.0
 *
 * @package    Wcpopup
 * @subpackage Wcpopup/public
 */

/**
 * @package    Wcpopup
 * @subpackage Wcpopup/public
 * @author     WCPopUp <WCPopUp>
 */
class Wcpopup_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version; 
	}
	public function forceWcSession()	{
		if (is_user_logged_in() || is_admin()){
			return;
		}
		if (isset(WC()->session) && !WC()->session->has_session()){
			WC()->session->set_customer_session_cookie( true );
		}
	}
	public function forceSession(){
		if( !session_id() ){
			session_start();
		}
	}
	public function forceSessionEnd(){
		session_destroy ();
	}
	private $fieldNameRequred = array();
	public function WcPopUpInit(){
		$this->forceSession();
		$this->createTemplateForm();
		$this->fieldNameRequred = array(
			'country'=>(int)get_option('wcpopup_chk_country'),
			'username'=>(int)get_option('wcpopup_chk_name'),
			'userphone'=>(int)get_option('wcpopup_chk_phone'),
			'usermail'=>(int)get_option('wcpopup_chk_email'),
		);
	}
	
	public function createTemplateForm(){
		$this->register_post_type();
		if ( get_posts( array( 'post_type' => 'wcpopup-form' ) ) ) {
			return;
		}
		$post_id = wp_insert_post( array(
			'post_type' => 'wcpopup-form',
			'post_status' => 'publish',
			'post_title' => 'wcpopup-form-welcome',
			'post_content' => trim( '
			<div style="display: none;" id="wcpopup_modal" >
			  <h2>Давайте познакомимся</h2>
			  <p>
			  <form action="#" method="post" class="form-horizontal">
			  
			   <div class="mb-3">
				[wcpoup-field type="label" forname="country" label="Страна доставки:" ]
				[wcpoup-field type="select" country="UA;RU;KZ;"]

			  </div>			  
			   <div class="mb-3">
				[wcpoup-field type="label" forname="username" label="Ваше имя"]
				[wcpoup-field name="username" type="input" placeholder="Ваше имя"]
			  </div>
			   <div class="mb-3">
			    [wcpoup-field type="label" forname="userphone" label="Номер телефона" ]
				[wcpoup-field name="userphone" type="input" placeholder="Номер телефона"]
			  </div>
			  <div class="mb-3">
			    [wcpoup-field type="label" forname="usermail" label="Email"]
				[wcpoup-field name="usermail" type="input" placeholder="Email"]

			  </div>
			   <div class="mb-3">
				<div class="wcpopup-submit-button">
					[wcpoup-field type="button" name="send" title="Продолжить"]
					[wcpoup-field type="button" name="later" title="Закрыть"]
				</div>				
			  </div>
			  </p>
			</div>				
			' ),
		) );
		
	}
	public static function register_post_type() {
		register_post_type( 'wcpopup-form', array(
			'labels' => array(
				'name' => __( 'WcPopUp Forms', 'wcpopup-form-welcome' ),
				'singular_name' => __( 'WcPopUp Form', 'wcpopup-form-welcome' ),
			),
			'rewrite' => false,
			'query_var' => false,
			'public' => true,
			'show_ui' => true,
			'publicly_queryable' => false, 
			'exclude_from_search' => false, 
			'show_in_nav_menus' => true, 
/*			'capability_type' => 'page',
			'capabilities' => array(
				'edit_post' => 'wcpopup-form',
				'read_post' => 'wcpopup-form',
				'delete_post' => 'wcpopup-form',
				'edit_posts' => 'wcpopup-form',
				'edit_others_posts' => 'wcpopup-form',
				'publish_posts' => 'wcpopup-form',
				'read_private_posts' => 'wcpopup-form',
			),*/
		) );
	}	
	
	
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wcpopup-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name."fancy", plugin_dir_url( __FILE__ ) . 'css/jquery.fancybox.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() { 
		//wp_enqueue_script( $this->plugin_name."js", plugin_dir_url( __FILE__ ) . 'js/jquery.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name."fancy", plugin_dir_url( __FILE__ ) . 'js/jquery.fancybox.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wcpopup-public.js', array( 'jquery' ), $this->version, false );
	}
	/**
	 * Get user geolocation
	 *
	 * @since    1.0.0
	 */	
	public function getUserCountry(){
		 //$WC_Geolocation= new WC_Geolocation(); 
		 $IpAddr = WC_Geolocation::get_ip_address();
		 $GeoLocation = WC_Geolocation::geolocate_ip( $IpAddr );
		 return $GeoLocation['country']; 
	}
	public function ajaxVar(){
		?>
		<span 
			id="wcpopup_variable"
			data-timer="<?php echo (int)get_option('wcpopup_popup_timer')*1000; ?>"
			data-counter ="<?php echo (int)$_SESSION['WCPOPUP_COUNTER']; ?>"
			data-count ="<?php echo (int)get_option('wcpopup_popup_counter'); ?>"
			data-start="<?php echo (int)$_SESSION['WCPOPUP_START']; ?>"
			>
		</span>
		<?php
	}
	/**
	 * Popup form create
	 *
	 * @since    1.0.0
	 */
	public function labelCreate( $atts ){
		$name = sanitize_text_field($atts['forname']);
		$requred = ($this->fieldNameRequred[$name] == 1) ? '<span class="form-label-requred">*</span>' : "";
		$html = '<label for="'.$name.'" class="form-label">'.sanitize_text_field($atts['label']).$requred.'</label>';
		return $html;
	}
	public function selectCountryFieldGen( $atts ){
		global $woocommerce;
		$UserCountry = $this->getUserCountry(); 		
		$countries_obj   = new WC_Countries();
		$countries   = $countries_obj->__get('countries');
		
		$country = explode(";",$atts['country']); //UA;RU;KZ;
		
		foreach($countries AS $key=>$name){
			$selected = "";
			if ($UserCountry == $key) {
				$selected = "selected";
			}
			if (in_array($key, $country)){
				$select .= "<option ".$selected." value='".$key."' >".$name." ".$_SESSION['SELCOUNRTY']."</option>";
			}
		}		
		$html .= '<div class="wcpopup-select-box" >
				  <select id="country" name="country" required>
					'.$select.'
				  </select>
				</div>';
		return $html;
	} 
	
	public function inputFieldGen($atts){
		$name = sanitize_text_field($atts['name']);
		$html = '<div class="wcpopup-input-box">
				  <input type="text" class="form-control" name="'.$name.'" id="'.$name.'" placeholder="'.sanitize_text_field($atts['placeholder']).'" required>
				</div>';
		return $html;
	}
	public function buttonGen($atts){
		$html = '<button type="submit" id="'.sanitize_text_field($atts['name']).'">'.sanitize_text_field($atts['title']).'</button>';
		return $html;
	}
	public function doShortcode($atts){
		switch($atts['type']){
			case 'select': $html = $this->selectCountryFieldGen($atts); break;
			case 'input': $html = $this->inputFieldGen($atts); break;
			case 'button': $html = $this->buttonGen($atts); break;
			case 'label': $html = $this->labelCreate($atts); break;
		}
		return $html;
	}
	public function popupCountryForm() {
		global $woocommerce;
		$this->ajaxVar(); 
		if ( get_option('wcpopup_popup_enable') ){ 
			//WCPOPUP = false and wcpopup_popup_enable = true
			$content_post = get_posts( array( 
				'post_type' => 'wcpopup-form',
				'name' => 'wcpopup-form-welcome',
				'post_status' => 'publish',
				'posts_per_page' => 1
			));
			$content_post = $content_post[0];
			$content = $content_post->post_content;
			echo do_shortcode($content);
		}
	}
	/**
	 * Ajax function set country in session
	 *
	 * @since    1.0.0
	 */	
	public function sendDataAjax() {
		global $wpdb;
		$id = array();  
			
		$SelCountry = substr(sanitize_text_field($_POST['country']),0,2);
		if (!empty($SelCountry) ){
			WC()->session->set( 'SELCOUNRTY', $SelCountry );
		}else{
			if (get_option('wcpopup_chk_country')) {
				$id[] = 'country';
			}
		}

		$UserName = substr(sanitize_text_field($_POST['username']),0,25);
		if (!empty($UserName) ){
			WC()->session->set( 'USERNAME', $UserName );
		}else{
			if (get_option('wcpopup_chk_name')) {
				$id[] = 'username';
			}
		}

		$UserPhone = substr(wc_sanitize_phone_number($_POST['userphone']),0,20);
		if (!empty($UserPhone) && strlen($UserPhone) > 10){
			WC()->session->set( 'USERPHONE', $UserPhone );
		}else{
			if (get_option('wcpopup_chk_phone')) {
				$id[] = 'userphone';
			}
		}

		$UserMail = sanitize_email($_POST['usermail']);
		if (!empty($UserMail) ){
			WC()->session->set( 'USERMAIL', $UserMail );
		}else{
			if (get_option('wcpopup_chk_email')) {
				$id[] = 'usermail';
			}
		}

		if (count($id) >= 1){
			$this->ajaxreturn(2,$id);
			unset($id);
			return false;
		}
		
		if (count($id) == 0){
			$this->WcPopUpClose();
			$_SESSION['WCPOPUP_COUNTER'] = get_option('wcpopup_popup_counter');
			if (get_option('wcpopup_savetodb_enable')) {
				$this->storeToDB(array(
					'SELCOUNRTY' => $SelCountry,
					'USERNAME' => $UserName,
					'USERPHONE' => $UserPhone,
					'USERMAIL' => $UserMail,
				));
			}
			$this->ajaxreturn(1,$id);
			return true;
		}
	}
	public function ajaxreturn($status,$id = array()){			
		$return['status'] = $status;
		$return['id'] = $id;
		echo json_encode($return);
		wp_die(); 
	}
	
	public function storeToDB($array){
		global $wpdb;
		$table_name = $wpdb->prefix . "wcpopup_clients";
		$data = array( 
			'uid' => md5(WC_Geolocation::get_ip_address().wc_get_user_agent().date('Y-m-d')),
			'date' => current_time('mysql'), 
			'name' => $array['USERNAME'],
			'country_detect' => $this->getUserCountry(),
			'country_select' => $array['SELCOUNRTY'],
			'user_agent' => wc_get_user_agent(),
			'ip_addr' => WC_Geolocation::get_ip_address(),
			'phone' =>  $array['USERPHONE'],
			'email' =>  $array['USERMAIL'],
		);
		if (!$wpdb->insert( $table_name, $data, $format )){
			$wpdb->update( $table_name, $data, array('uid' => md5( WC_Geolocation::get_ip_address().wc_get_user_agent() )) );
		}
	}
	
	
	public function change_default_checkout_country() {
		return WC()->session->get('SELCOUNRTY'); 
	}
	
	public function set_checkout_fields($fields) {
	  $fields['billing'] = $this->set_billing_fields($fields['billing']);
	  return $fields;
	}

	public function set_billing_fields($fields) {
	  $fields['billing_first_name']['default'] = WC()->session->get('USERNAME');
	  $fields['billing_phone']['default'] = WC()->session->get('USERPHONE');
	  $fields['billing_email']['default'] = WC()->session->get('USERMAIL');
	  return $fields;
	}
	public function ajaxWcPopUpClose(){
		$this->WcPopUpClose();
		$this->ajaxreturn(1);
	}
	public function WcAddToCart (){
		$_SESSION['WCPOPUP_COUNTER']++; //counter click
		if ($_SESSION['WCPOPUP_COUNTER'] > get_option('wcpopup_popup_counter')){
			$this->WcPopUpClose();
		}
		return true;
	}
	public function WcPopUpClose (){
		$_SESSION['WCPOPUP_START']  = 1;
		return true;
	}	
	public function getCountryPoshlina($country){
		$ph = $this->parseSetting('wcpopup_country_poshlina');
		if (!empty($country)){
			$return = $ph[$country];
			if (empty($return)) return (int)get_option('wcpopup_country_poshlina_default');
			return $return;
		}
		return (int)get_option('wcpopup_country_poshlina_default'); //default value
	}
	public function getCountryWeight($country){
		$wg = $this->parseSetting('wcpopup_country_weight');
		if (!empty($country)){
			$return = $wg[$country];
			if (empty($return)) return (int)get_option('wcpopup_country_weight_default');
			return $return;
		}
		return (int)get_option('wcpopup_country_weight_default'); //default value
	}	
	public function parseSetting($optionName){
		$srcarray = explode(";",get_option($optionName));
		foreach($srcarray AS $key=>$row){ 
			list($country,$value) = explode(":",$row);
			$array[$country] = $value;
		}
		return $array;
	}
	
	public function cartData($id){
		
		$cart = WC()->cart->get_cart();
		if ( !empty( $cart ) ){
			foreach ( $cart as $cart_item_key => $cart_item ) {
				$_product   		= apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$shipping_class_term = get_term( $cart_item['data']->get_shipping_class_id(), 'product_shipping_class' );
				?>
				<!-- WCPopUp -->
				<span class="<?php echo $id;?>" data-shiping="<?php echo $cart_item['data']->get_shipping_class_id(); ?>" data-shipname="<?php echo $shipping_class_term->name; ?>">
						<span class="product_quantity" data-quantity="<?php echo $cart_item['quantity']; ?>"></span>
						<span class="jprice" data-price="<?php echo  $_product->get_price(); ?>"></span>
						<span class="jweight" data-weight="<?php echo  $cart_item['data']->get_weight(); ?>"></span>
				</span>
				<?php
			}
		}		
	}
	public function getSelectUserCountry(){
		$SelCountry = WC()->session->get('SELCOUNRTY');
		if (empty($SelCountry)){
			$UserCountry = $this->getUserCountry(); 
			return $UserCountry;
		}
		return $SelCountry;
		
	}
	public function progressBarAjax($idbar,$idisplay){
		$cart = WC()->cart->get_cart();
		if ( !empty( $cart ) ){
			if (!empty(WC()->session->get('SELCOUNRTY'))){
			?>
			<!-- WCPopUp -->
			<span id="selectedcountry" data-country="<?php echo  $this->getSelectUserCountry(); ?>"></span>
			<?php
			}
			?>
			<script>
			jQuery(document).ready(function() {
				jQuery('.<?php echo $idisplay; ?> .product_total').empty();
				var masiv_weight = [];
				var masiv_price = [];
				var masiv_shipname = [];
				 jQuery(".<?php echo $idbar;?>").each(function(ii,jj){
					var shipid = parseInt(jQuery(this).data('shiping'));
					masiv_weight[shipid] =0;
					masiv_price[shipid] = 0;	
					masiv_shipname[shipid] = jQuery(this).data('shipname');	
				});
				jQuery(".<?php echo $idbar;?>").each(function(ii,jj){	
					var array = jQuery(this).logic(); 
					var shipid = parseInt(jQuery(this).data('shiping'));
					masiv_weight[shipid] += array.weight;
					masiv_price[shipid] += array.price;
				});
				jQuery.each(masiv_shipname,function(shipid,shipname){
					if (masiv_weight[shipid] > 0){
						jQuery('.<?php echo $idisplay; ?> .product_total').progresbar(
							shipid,
							shipname,
							parseFloat(masiv_price[shipid]),
							parseFloat(masiv_weight[shipid]),
							'<?php echo $this->getCountryWeight($this->getSelectUserCountry()); ?>',
							'<?php echo $this->getCountryPoshlina($this->getSelectUserCountry()); ?>',
						);
					}
				});
			})
				

			</script>			
			<?php
		}
	}	
	public function displayProgressBlock($id){
		?>
			<div class="<?php echo $id; ?>">
				<div class="product_total">
				</div>
			</div>
			<br>			
		<?php
	}
	
	public function displayProgressBlockXoo(){
		if (!get_option('wcpopup_progressbar_enable_xoo')) return true;
		$this->cartData('wcpopup_progressbar_xoo');
		$this->progressBarAjax('wcpopup_progressbar_xoo','wcpopup_progressbar_xoo_display');
		$this->displayProgressBlock('wcpopup_progressbar_xoo_display');
	}
	
	public function displayProgressBlockWC(){
		if (!get_option('wcpopup_progressbar_enable_wc')) return true;
		$this->cartData('wcpopup_progressbar_wc');
		$this->progressBarAjax('wcpopup_progressbar_wc','wcpopup_progressbar_wc_display');
		$this->displayProgressBlock('wcpopup_progressbar_wc_display');
	}
	
	
	public function wcCartError(){
		$wc_notices = wc_get_notices( 'error' );
		foreach ( $wc_notices as $wc_notice ) {
			echo  "<h2 style='color:red;'>".htmlspecialchars_decode($wc_notice['notice'])."</h2>";
		}		
		?>
		<script>
		jQuery(document).ready(function() {
			jQuery('.wc-backward').on('click',function(){
				jQuery('.xoo-wsc-modal').addClass('xoo-wsc-cart-active');
				return false;
			})
		})
		</script>		
		<?php
	}	
	
}
