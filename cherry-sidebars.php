<?php
/**
 * Plugin Name: Cherry Sidebars
 * Plugin URI:  https://zemez.io/wordpress/
 * Description: Plugin for creating and managing sidebars in WordPress.
 * Version:     1.1.2.4
 * Author:      Zemez
 * Text Domain: cherry-sidebars
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 *
 * @package Cherry_Sidebars
 * @author Template Monster
 * @license GPL-3.0+
 * @copyright 2002-2017, Template Monster
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If class 'Cherry_Sidebars' not exists.
if ( ! class_exists( 'Cherry_Sidebars' ) ) {

	/**
	 * Sets up and initializes the Cherry Sidebars plugin.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Sidebars {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * A reference to an instance of cherry framework core class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private $core = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Set the constants needed by the plugin.
			add_action( 'plugins_loaded', array( $this, 'constants' ), 0 );

			// Internationalize the text strings used.
			add_action( 'plugins_loaded', array( $this, 'lang' ), 1 );

			// Load the installer core.
			add_action( 'after_setup_theme', require( trailingslashit( dirname( __FILE__ ) ) . 'cherry-framework/setup.php' ), 0 );

			// Load the core functions/classes required by the rest of the plugin.
			add_action( 'after_setup_theme', array( $this, 'get_core' ), 1 );

			// Laad the modules.
			add_action( 'after_setup_theme', array( 'Cherry_Core', 'load_all_modules' ), 2 );

			// Load the functions files.
			add_action( 'after_setup_theme', array( $this, 'includes' ),3 );

			// Load the admin files.
			add_action( 'after_setup_theme', array( $this, 'admin' ), 4 );

			// Init modules
			add_action( 'after_setup_theme', array( $this, 'init_modules' ), 10 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Defines constants for the plugin.
		 *
		 * @since 1.0.0
		 */
		function constants() {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$plugin_data = get_plugin_data( plugin_dir_path( __FILE__ ) . basename( __FILE__ ) );

			/**
			 * Set constant name for the post type name.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SIDEBARS_SLUG', basename( dirname( __FILE__ ) ) );

			/**
			 * Set the version number of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SIDEBARS_VERSION', $plugin_data['Version'] );

			/**
			 * Set constant path to the plugin directory.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SIDEBARS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/**
			 * Set constant path to the plugin URI.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SIDEBARS_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}

		/**
		 * Loads files from the '/includes' folder.
		 *
		 * @since 1.0.0
		 */
		function includes() {
			require_once( CHERRY_SIDEBARS_DIR . 'includes/class-cherry-include-sidebars.php' );
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		function lang() {
			load_plugin_textdomain( 'cherry-sidebars', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Loads admin files.
		 *
		 * @since 1.0.0
		 */
		function admin() {
				require_once( CHERRY_SIDEBARS_DIR . 'admin/includes/class-cherry-sidebar-utils.php' );
			if ( is_admin() ) {
				require_once( CHERRY_SIDEBARS_DIR . 'admin/includes/class-cherry-sidebars-admin.php' );
				require_once( CHERRY_SIDEBARS_DIR . 'admin/includes/class-cherry-custom-sidebar.php' );
				require_once( CHERRY_SIDEBARS_DIR . 'admin/includes/class-cherry-sidebar-migrate.php' );
			}
		}

		/**
		 * Loads the core functions. These files are needed before loading anything else in the
		 * theme because they have required functions for use.
		 *
		 * @since 1.1.0
		 */
		public function get_core() {
			/**
			 * Fires before loads the core theme functions.
			 *
			 * @since  1.1.0
			 */
			do_action( 'cherry_sidebsrs_core_before' );

			global $chery_core_version;

			if ( null !== $this->core ) {
				return $this->core;
			}

			if ( 0 < sizeof( $chery_core_version ) ) {
				$core_paths = array_values( $chery_core_version );
				require_once( $core_paths[0] );
			} else {
				die( 'Class Cherry_Core not found' );
			}

			$this->core = new Cherry_Core( array(
				'base_dir' => CHERRY_SIDEBARS_DIR . 'cherry-framework',
				'base_url' => CHERRY_SIDEBARS_URI . 'cherry-framework',
				'modules'  => array(
					'cherry-js-core' => array(
						'autoload' => false,
					),
					'cherry-ui-elements' => array(
						'autoload' => false,
					),
					'cherry-interface-builder' => array(
						'autoload' => false,
					),
				),
			));
		}

		/**
		 * Init modules.
		 *
		 * @since 1.0.0
		 */
		function init_modules() {
			if ( is_admin() ) {
				cherry_sidebars()->get_core()->init_module( 'cherry-js-core' );
				cherry_sidebars()->get_core()->init_module( 'cherry-ui-elements' );
			}
		}

		/**
		 * On plugin activation.
		 *
		 * @since 1.0.0
		 */
		function activation() {
			flush_rewrite_rules();
		}

		/**
		 * On plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		function deactivation() {
			flush_rewrite_rules();
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
}

if ( ! function_exists( 'cherry_sidebars' ) ) {

	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function cherry_sidebars() {
		return Cherry_Sidebars::get_instance();
	}
}

cherry_sidebars();
