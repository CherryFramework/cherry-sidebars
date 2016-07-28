<?php
/**
 * Sets up the admin functionality for the plugin.
 *
 * @package Cherry_Sidebars
 * @author Template Monster
 * @license GPL-3.0+
 * @copyright 2002-2016, Template Monster
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * Class for admin functionally.
 *
 * @since 1.0.0
 */
class Cherry_Sidebars_Admin {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {

		// Load admin javascript and stylesheet.
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_assets' ), 1 );

		add_action( 'after_setup_theme', array( $this, 'widgets_ajax_page' ), 10 );
		add_action( 'sidebar_admin_setup', array( $this, 'registrates_custom_sidebar' ), 10 );
		add_action( 'widgets_admin_page', array( $this, 'edit_wp_registered_sidebars' ), 10 );
		add_action( 'sidebar_admin_page', array( $this, 'widgets_page' ), 10 );
	}

	/**
	 * Register and Enqueue admin-specific stylesheet and javascript.
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix Hook suffix.
	 * @return void
	 */
	public function add_admin_assets( $hook_suffix ) {

		if ( 'widgets.php' === $hook_suffix ) {
			wp_register_script( 'cherry_admin_sidebars_js', trailingslashit( CHERRY_SIDEBARS_URI ) . 'admin/assets/js/min/cherry-admin-sidebars.min.js', array( 'jquery' ), CHERRY_SIDEBARS_VERSION, true );
			wp_register_style( 'cherry_admin_sidebars_css', trailingslashit( CHERRY_SIDEBARS_URI ) . 'admin/assets/css/cherry-admin-sidebars.css', array(), CHERRY_SIDEBARS_VERSION, 'all' );

			wp_register_style( 'interface-builder', trailingslashit( CHERRY_SIDEBARS_URI ) . 'admin/assets/css/interface-builder.css', array(), CHERRY_SIDEBARS_VERSION, 'all' );

			$cherry_framework_object = array( 'ajax_nonce_new_sidebar' => wp_create_nonce( 'new_custom_sidebar' ) , 'ajax_nonce_remove_sidebar' => wp_create_nonce( 'remove_custom_sidebar' ) );
			wp_localize_script( 'cherry_admin_sidebars_js', 'cherryFramework', $cherry_framework_object );

			wp_enqueue_script( 'cherry_admin_sidebars_js' );
			wp_enqueue_style( 'cherry_admin_sidebars_css' );
			wp_enqueue_style( 'interface-builder' );

		} elseif ( false !== strpos( $hook_suffix, 'post' ) ) {
			wp_register_style( 'cherry-sidebars-post-page', trailingslashit( CHERRY_SIDEBARS_URI ) . 'admin/assets/css/cherry-sidebars-post-page.css', array(), CHERRY_SIDEBARS_VERSION, 'all' );
			wp_enqueue_style( 'cherry-sidebars-post-page' );
		}
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 */
	public function widgets_page() {
		cherry_sidebars()->init_modules();
		require_once( trailingslashit( CHERRY_SIDEBARS_DIR ) . 'admin/views/cherry-widgets-page.php' );
	}

	/**
	 * Registration new custom sidebars.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function registrates_custom_sidebar() {
		global $wp_registered_sidebars;

		$instance = new Cherry_Sidebar_Utils();
		$sidebars_array = $instance->get_custom_sidebar_array();
		unset( $sidebars_array['cherry-sidebars-counter'] );

		$wp_registered_sidebars = array_merge( $wp_registered_sidebars, $sidebars_array );
	}

	/**
	 * Editing registered sidebars.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function edit_wp_registered_sidebars() {
		global $wp_registered_sidebars;

		$instance = new Cherry_Sidebar_Utils();
		$sidebars_array = $instance->get_custom_sidebar_array();
		unset( $sidebars_array['cherry-sidebars-counter'] );
		$sidebars_array_lengh = count( $sidebars_array );

		foreach ( $sidebars_array as $sidebar => $custom_sidebar ) {
			unset( $wp_registered_sidebars[ $sidebar ] );
		}
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 */
	public function widgets_ajax_page() {
		require_once( trailingslashit( CHERRY_SIDEBARS_DIR ) . 'admin/views/cherry-new-sidebar.php' );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

Cherry_Sidebars_Admin::get_instance();
