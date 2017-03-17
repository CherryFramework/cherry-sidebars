<?php
/**
 * Copy created custom sidebars from parent theme to child on theme switch.
 *
 * @package Cherry_Sidebars
 * @author Template Monster
 * @license GPL-3.0+
 * @copyright 2002-2016, Template Monster
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Custom_Sidebar_Migrate' ) ) {

	/**
	 * Copy created custom sidebars from parent theme to child on theme switch.
	 *
	 * @since 1.1.0
	 */
	class Cherry_Custom_Sidebar_Migrate {

		/**
		 * Holds the instances of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Utils class instance
		 *
		 * @var object
		 */
		private $utils = null;

		/**
		 * Transient key to store widgets in.
		 *
		 * @var string
		 */
		public $transient_custom = 'cherry_migrate_sidebars';

		/**
		 * Transient key to store default widgets in
		 *
		 * @var string
		 */
		public $transient_default = 'cherry_default_sidebars';

		/**
		 * Sets up the needed actions
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			add_action( 'switch_theme', array( $this, 'maybe_migrate' ), 10, 3 );
			add_action( 'after_switch_theme', array( $this, 'maybe_finalize_migration' ), 0 );
		}

		/**
		 * Check if we need to copy existing sidebars and do it if yes.
		 *
		 * @param  string   $new_name  Name of the new theme.
		 * @param  WP_Theme $new_theme WP_Theme instance of the new theme.
		 * @param  WP_Theme $old_theme WP_Theme instance of the old theme.
		 * @return void
		 */
		public function maybe_migrate( $new_name, $new_theme, $old_theme ) {

			if ( ! $this->is_migrate_required( $new_theme, $old_theme ) ) {
				return;
			}

			if ( $this->child_has_sidebars( $new_theme ) ) {
				return;
			}

			$existing = get_option( $this->utils->option_key( $old_theme ) );

			if ( ! empty( $existing ) ) {
				$this->migrate_sidebars( $new_theme, $old_theme, $existing );
			}

		}

		/**
		 * Finish sidebars migration projects if required
		 *
		 * @return null
		 */
		public function maybe_finalize_migration() {

			$to_migrate = get_transient( $this->transient_custom );

			if ( ! $to_migrate ) {
				return;
			}

			$active_sidebars = get_transient( $this->transient_default );
			$active_sidebars = array_merge( $active_sidebars, $to_migrate );

			remove_action( 'after_switch_theme', '_wp_sidebars_changed' );

			update_option( 'sidebars_widgets', $active_sidebars );
			delete_transient( $this->transient_custom );
			delete_transient( $this->transient_default );
		}

		/**
		 * Process sidebars migration.
		 *
		 * @param  WP_Theme $new_theme WP_Theme instance of the new theme.
		 * @param  WP_Theme $old_theme WP_Theme instance of the old theme.
		 * @return array    $existing  Existing sidebars array.
		 */
		public function migrate_sidebars( $new_theme, $old_theme, $existing ) {

			$keys = array_keys( $existing['custom_sidebar'] );
			array_shift( $keys );

			$active_sidebars = get_option( 'sidebars_widgets' );
			$to_migrate      = array();
			if ( ! empty( $active_sidebars ) ) {
				foreach ( $keys as $sidebar ) {
					if ( isset( $active_sidebars[ $sidebar ] ) ) {
						$to_migrate[ $sidebar ] = $active_sidebars[ $sidebar ];
					}
				}
			}

			$current = get_option( 'sidebars_widgets' );

			set_transient( $this->transient_custom, $to_migrate, HOUR_IN_SECONDS );
			set_transient( $this->transient_default, $current, HOUR_IN_SECONDS );
			update_option( $this->utils->option_key( $new_theme ), $existing );
		}

		/**
		 * Check if child theme already has custom sidebars
		 *
		 * @param  WP_Theme $new_theme WP_Theme instance of the new theme.
		 * @return boolean
		 */
		public function child_has_sidebars( $new_theme ) {

			$this->utils = new Cherry_Sidebar_Utils;
			$sidebars    = get_option( $this->utils->option_key( $new_theme ) );

			if ( ! empty( $sidebars ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Check if migration is required.
		 *
		 * @param  WP_Theme $new_theme WP_Theme instance of the new theme.
		 * @param  WP_Theme $old_theme WP_Theme instance of the old theme.
		 * @return boolean
		 */
		public function is_migrate_required( $new_theme, $old_theme ) {

			$theme_supports = get_theme_support( 'cherry_migrate_sidebars' );

			if ( ! $theme_supports ) {
				return false;
			}

			$new_parent = $new_theme->parent();

			if ( ! $new_parent || ! is_callable( array( $new_parent, 'get_stylesheet' ) ) ) {
				return false;
			}

			return ( $new_parent->get_stylesheet() === $old_theme->get_stylesheet() );
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

	Cherry_Custom_Sidebar_Migrate::get_instance();
}
