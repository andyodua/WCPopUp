<?php

/**
 * Fired during plugin activation
 *
 * @link       WCPopUp
 * @since      1.0.0
 *
 * @package    Wcpopup
 * @subpackage Wcpopup/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wcpopup
 * @subpackage Wcpopup/includes
 * @author     WCPopUp <WCPopUp>
 */
class Wcpopup_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::db_install();
		self::set_option();
	}
	public function set_option(){
		self::chk_option("wcpopup_db_version", WCPOPUP_DB_VERSION);
		self::chk_option("wcpopup_chk_name", WCPOPUP_CHK_NAME);
		self::chk_option("wcpopup_chk_phone", WCPOPUP_CHK_PHONE);
		self::chk_option("wcpopup_chk_email", WCPOPUP_CHK_EMAIL);
		self::chk_option("wcpopup_chk_country", WCPOPUP_CHK_COUNTRY);
		self::chk_option("wcpopup_popup_enable", WCPOPUP_POPUP_ENABLE);
		self::chk_option("wcpopup_popup_counter", WCPOPUP_POPUP_COUNTER);
		self::chk_option("wcpopup_popup_timer", WCPOPUP_POPUP_TIMER);
		self::chk_option("wcpopup_progressbar_enable", WCPOPUP_PROGRESSBAR_ENABLE);
		self::chk_option("wcpopup_savetodb_enable", WCPOPUP_SAVETODB_ENABLE);
		self::chk_option("wcpopup_country_poshlina", WCPOPUP_COUNTRY_POSHLINA);
		self::chk_option("wcpopup_country_weight", WCPOPUP_COUNTRY_WEIGHT);
		self::chk_option("wcpopup_country_poshlina_default", WCPOPUP_COUNTRY_POSHLINA_DEFAULT);
		self::chk_option("wcpopup_country_weight_default", WCPOPUP_COUNTRY_WEIGHT_DEFAULT);		
	}
	public function chk_option($option,$value){
		if (!get_option($option)){
			add_option($option, $value);
			return true;
		}
	}
	public function db_install () {
		global $wpdb;
		$table_name = $wpdb->prefix . "wcpopup_clients";
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		  
			$sql = "
				CREATE TABLE `".$table_name."` (
				  `uid` varchar(255) NOT NULL,
				  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `name` tinytext NOT NULL,
				  `phone` varchar(255) DEFAULT NULL,
				  `email` varchar(255) DEFAULT NULL,				  
				  `country_detect` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				  `country_select` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				  `user_agent` varchar(255) DEFAULT NULL,
				  `ip_addr` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`uid`),
				  UNIQUE KEY `id` (`uid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			$rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text ) );
			add_option("wcpopup_db_version", WCPOPUP_DB_VERSION);

		}else{			
			$installed_ver = get_option( "wcpopup_db_version" );
			if( $installed_ver != WCPOPUP_DB_VERSION ) {

				$sql = "
					CREATE TABLE `".$table_name."` (
					  `uid` varchar(255) NOT NULL,
					  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `name` tinytext NOT NULL,
					  `phone` varchar(255) DEFAULT NULL,
					  `email` varchar(255) DEFAULT NULL,				  					  
					  `country_detect` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					  `country_select` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					  `user_agent` varchar(255) DEFAULT NULL,
					  `ip_addr` varchar(255) DEFAULT NULL,
					  PRIMARY KEY (`uid`),
					  UNIQUE KEY `id` (`uid`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				update_option( "wcpopup_db_version", WCPOPUP_DB_VERSION);
			}		
		}
		
	}
}
