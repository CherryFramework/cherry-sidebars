<?php
/**
 * Cherry Sidebar Utils.
 *
 * @package   Cherry Sidebar Manager
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2016 Cherry Team
 **/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Sidebar_Utils' ) ) {

	/**
	 * Cherry Sidebar Utils.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Sidebar_Utils {

		/**
		 * Current theme name.
		 *
		 * @var string
		 */
		public $current_theme;

		/**
		 * Current sidebars theme settings.
		 *
		 * @var array
		 */
		public $get_theme_option;

		/**
		 * Sets up our actions/filters or another settings.
		 *
		 * @since 1.0.0
		 */
		function __construct() {
			$this->current_theme = wp_get_theme();
			$this->get_theme_option = get_option( $this->current_theme . '_sidbars', array() );
		}

		/**
		 * Get current sidebars theme settings.
		 *
		 * @since 1.0.0
		 * @return array Current sidebar settings.
		 */
		public function get_custom_sidebar_array() {
			if ( ! is_array( $this->get_theme_option ) || ! array_key_exists( 'custom_sidebar', $this->get_theme_option ) ) {
				$custom_sidebar_array = array();
			} else {
				$custom_sidebar_array = $this->get_theme_option['custom_sidebar'];
			}

			return $custom_sidebar_array;
		}

		/**
		 * Updated custom sidebars array and save to database.
		 *
		 * @since 1.0.0
		 * @param array $new_custom_sidebar_array New theme sidebar settings.
		 * @return  void
		 */
		public function set_custom_sidebar_array( $new_custom_sidebar_array ) {

			$this->get_theme_option['custom_sidebar'] = $new_custom_sidebar_array;

			update_option( $this->current_theme . '_sidbars', $this->get_theme_option );
		}
	}
}
