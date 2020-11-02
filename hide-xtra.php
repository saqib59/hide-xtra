<?php
/*
Plugin Name: Hide Xtra Menu Items
Plugin URI:
Description: 
Version: 1.0
Author: 
Author URI: 
License: 
Requires at least: 5.0
Tested up to: 5.4
*/

if ( ! class_exists( 'HideXtraMenu' ) ) {
	class HideXtraMenu {
		private static $user_id;

		public function __construct() {
			add_action( 'init', array($this,'handle_post'));
			add_action( 'admin_menu', array($this,'add_menu'));
			add_action( 'network_admin_menu', array($this,'add_menu'));
			add_action( 'admin_enqueue_scripts', array($this,'include_js_and_css'));
			add_action( 'add_menu_classes', array($this,'change_menu_order'));
			add_action( 'wp_ajax_xtra_click_on_menu', array($this,'click_on_menu'));
			add_action( 'login_enqueue_scripts', array($this,'change_wp_logo_xtra'));
			add_action( 'admin_head', array($this,'change_wp_logo_xtra'));
		}

		public function add_menu() {
			add_menu_page( 'Hide Xtra Menu Items',  __( 'Hide Xtra Menu' ), 'read', 'xtra_settings', array($this,'render_main_page'), 'dashicons-welcome-view-site' );

			$settings = self::get_option();
		}
		/**
		 * Save settings
		 */
		public function handle_post() {
			$page = filter_input( INPUT_GET, 'page' );
			if ( 'xtra_settings' !== $page ) {
				return;
			}
			$save_settings = filter_input( INPUT_POST, 'save_settings' );
			if ( !is_null( $save_settings ) ) {
				self::save_settings();
			}
		}

		private static function get_loggedin_user_id() {
			if ( is_null( self::$user_id ) && current_user_can( 'manage_options' )) {
				self::$user_id = get_current_user_id();
			}

			return self::$user_id;
		}

		public function include_js_and_css() {
			$settings = self::get_option();
			wp_enqueue_script( 'xtra_admin_script', plugin_dir_url(__FILE__) . 'assets/js/admin-menu.js' );
			wp_enqueue_style( 'xtra_admin_styles', plugin_dir_url(__FILE__) . 'assets/css/styles.css' );
			wp_localize_script( 'xtra_admin_script', 'xtraSettings', $settings );
		}

		/**
		 * Render main page for settings
		 */
		public function render_main_page() {
			$settings = self::get_option();
			$settings_logo = self::get_option('logo');
			require_once dirname( __FILE__ ) . '/inc/settings.php';
		}

		/**
		 * Save settings on form submit
		 */
		private static function save_settings() {
			
			if ( ! check_admin_referer( 'save-settings' ) ) {
				return;
			}
			
			else{
				/*echo "<pre>";
				var_dump($_POST);
				exit();*/
				$always_show_menu = $_POST['always_show'];
				$always_show_menu = explode(",",$always_show_menu);
				$settings_arr['always_show'] = $always_show_menu;
				update_option('wp_xtra_logo', $_POST['wp_xtra_logo']);
				update_user_meta( self::get_loggedin_user_id(), 'xtra_settings_always_show', $settings_arr );	
			}
			
		}
		/**
		 * Add classes to hidden menus
		 */
		public function change_menu_order( $menu ) {
			$hidden = self::get_option();
			$current_page = add_query_arg( [] );
			foreach ( $menu as $key => $item ) {

				$slug = $item[2];
					/*echo "<pre>";
					var_dump($slug);
					echo "</pre>";*/

				if ( false !== strpos( $current_page, $slug ) ) {
					$this->add_click( $slug );
					continue;
				}
				
				if ( !in_array( $slug, $hidden['always_show'], true ) ) {

					if ($slug == 'xtra_settings') {
						continue;
					}
					//Add hidden classes
					$item[4] .= ' hidden xtra_hidden';
					$menu[ $key ] = $item;
					$hidden_item = true;
				}
			}

			return $menu;
		}


		private static function get_option($logo='') {
			if (!empty($logo)) {
				$saved = get_option( 'wp_xtra_logo' );	
			}
			else{
				$saved = get_user_meta( self::get_loggedin_user_id(), 'xtra_settings_always_show', true );
			}
			return $saved;
		}

		/**
		 * AJAX handler
		 */
		public function click_on_menu() {
			$slug = filter_input( INPUT_POST, 'slug' );

			if ( ! $slug ) {
				return;
			}

			$this->add_click( $slug );

			wp_send_json_success();
		}

		private function add_click( $slug ) {
			$slug = str_replace( 'admin.php?page=', '', $slug );

			$saved = self::get_auto_hidden_menu_option();

			$saved[ $slug ]['last_time'] = time();
			if ( empty( $saved[ $slug ]['first_time'] ) ) {
				$saved[ $slug ]['first_time'] = time();
			}
		}

		/**
		 * Get AUTO hidden admin menu option
		 *
		 * @return array
		 */
		private static function get_auto_hidden_menu_option() {
			$option = get_user_meta( self::get_loggedin_user_id(), 'xtra_auto_hidden_menu_items', true );
			if ( ! $option ) {
				$option = [];
			}

			return $option;
		}
		/**
		 * Change wp logo from admin head and login page
		 *
		 */
		public function change_wp_logo_xtra() {
			$settings = self::get_option('logo');
			
			?>
			<style type="text/css"> 
				body.login div#login h1 a {
				background-image: url(<?php echo $settings; ?>) !important;  
				padding-bottom: 30px; 
				} 
			</style>
				<?php 

	}
}
	new HideXtraMenu();
}
