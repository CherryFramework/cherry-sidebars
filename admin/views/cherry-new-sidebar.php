<?php
/**
 * Custom sidebar DOM render functions.
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

if ( ! function_exists( 'cherry_sidebars_register_sidebar' ) ) {

	/**
	 * New sidebar register.
	 *
	 * @since  1.0.0
	 * @param  array $args Sidebar settings.
	 * @return string Sidebar ID added to $wp_registered_sidebars global.
	 */
	function cherry_sidebars_register_sidebar( $args ) {

		// Set up some default sidebar arguments.
		$defaults = array(
			'id'			=> '',
			'name'			=> '',
			'description'	=> '',
			'before_widget'	=> '<aside id="%1$s" class="widget %2$s">',
			'after_widget'	=> '</aside>',
			'before_title'	=> '<h3 class="widget-title">',
			'after_title'	=> '</h3>',
		);

		/**
		 * Filter sidebar arguments defaults.
		 *
		 * @since 1.0.0
		 * @param array $defaults
		 */
		$defaults = apply_filters( 'cherry_sidebars_default_args', $defaults );

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filters the sidebar arguments.
		 *
		 * @since 1.0.0
		 * @param array $args
		 */
		$args = apply_filters( 'cherry_sidebars_sidebar_args', $args );

		/**
		 * Fires before execute WordPress `register_sidebar` function.
		 *
		 * @since 1.0.0
		 * @param array $args
		 */
		do_action( 'cherry_sidebars_register_sidebar', $args );

		/**
		 * Register the sidebar.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
		 */
		return register_sidebar( $args );
	}
}

if ( ! function_exists( 'cherry_sidebars_add_custom_sidebar' ) ) {

	/**
	 * Custom sidebar item render.
	 *
	 * @return void
	 */
	function cherry_sidebars_add_custom_sidebar() {
		check_ajax_referer( 'new_custom_sidebar', 'security' );

		$nonce = isset( $_GET['security'] ) ? $_GET['security'] : $security ;

		if ( ! wp_verify_nonce( $nonce, 'new_custom_sidebar' ) ) {
			exit;
		}

		global $wp_registered_sidebars;

		$instance = new Cherry_Sidebar_Utils();
		$custom_sidebar_array = $instance->get_custom_sidebar_array();

		$form_data = isset( $_GET['formdata'] ) ? $_GET['formdata'] : $formdata ;

		if ( ! array_key_exists( 'cherry-sidebars-counter', $custom_sidebar_array ) ) {
			$custom_sidebar_array['cherry-sidebars-counter'] = 0;
		} else {
			$custom_sidebar_array['cherry-sidebars-counter'] += 1;
		}

		$id = $custom_sidebar_array['cherry-sidebars-counter'];
		$args = array(
			'name' => $form_data[0]['value'],
			'id' => 'cherry-sidebars-' . $id,
			'description' => $form_data[1]['value'],
			'dynamic-sidebar' => true,
		);
		$registrate_custom_sidebar = cherry_sidebars_register_sidebar( $args );
		$custom_sidebar_array[ 'cherry-sidebars-' . $id ] = $wp_registered_sidebars[ $registrate_custom_sidebar ];
	?>
		<div class="widgets-holder-wrap closed cherry-widgets-holder-wrap">
			<div class='cherry-delete-sidebar-manager'>
				<div class="cherry-spinner-wordpress spinner-wordpress-type-1"><span class="cherry-inner-circle"></span></div>
				<span class="dashicons dashicons-trash"></span>
			</div>
			<div id="<?php echo esc_attr( 'cherry-sidebars-' . $id ) ?>" class="widgets-sortables ui-sortable cherry-sidebars-manager">
				<div class="sidebar-name">
					<div class="sidebar-name-arrow"><br></div>
					<h2><?php echo esc_html( $form_data[0]['value'] ) ?><span class="spinner"></span></h2>
				</div>
				<div class="sidebar-description">
					<p class="description"><?php echo esc_html( $form_data[1]['value'] ) ?></p>
				</div>
			</div>
		</div>
	<?php
		$instance->set_custom_sidebar_array( $custom_sidebar_array );
		wp_die();
	}
}

add_action( 'wp_ajax_add_new_custom_sidebar', 'cherry_sidebars_add_custom_sidebar' );

if ( ! function_exists( 'cherry_sidebars_remove_custom_sidebar' ) ) {

	/**
	 * Custom sidebar removing function.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function cherry_sidebars_remove_custom_sidebar() {
		check_ajax_referer( 'remove_custom_sidebar', 'security' );

		$nonce = isset( $_GET['security'] ) ? $_GET['security'] : $security ;

		if ( ! wp_verify_nonce( $nonce, 'remove_custom_sidebar' ) ) {
			exit;
		}

		$id = isset( $_GET['id'] ) ? $_GET['id'] : $id ;

		$instance = new Cherry_Sidebar_Utils();
		$custom_sidebar_array = $instance->get_custom_sidebar_array();
		unset( $custom_sidebar_array[ $id ] );

		$instance->set_custom_sidebar_array( $custom_sidebar_array );
	}
}

add_action( 'wp_ajax_remove_custom_sidebar', 'cherry_sidebars_remove_custom_sidebar' );
