<?php
/**
 * Custom sidebar DOM render functions.
 *
 * @package   Cherry Sidebar Manager
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'cherry_register_sidebar' ) ) {

	/**
	 * New sidebar register.
	 *
	 * @param  array $args Sidebar settings.
	 * @return string Sidebar ID added to $wp_registered_sidebars global.
	 */
	function cherry_register_sidebar( $args ) {

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
		 * Filter as the default sidebar arguments.
		 *
		 * @since 4.0.0
		 * @param array $defaults
		 */
		$defaults = apply_filters( 'cherry_sidebar_defaults', $defaults );

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filters the sidebar arguments.
		 *
		 * @since 4.0.0
		 * @param array $args
		 */
		$args = apply_filters( 'cherry_sidebar_args', $args );

		/**
		 * Fires before execute WordPress `register_sidebar` function.
		 *
		 * @since 4.0.0
		 * @param array $args
		 */
		do_action( 'cherry_register_sidebar', $args );

		/**
		 * Register the sidebar.
		 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
		 */
		return register_sidebar( $args );
	}
}

add_action( 'wp_ajax_add_new_custom_sidebar', 'add_custom_sidebar' );

/**
 * Custom sidebar item render.
 *
 * @return void
 */
function add_custom_sidebar() {
	check_ajax_referer( 'new_custom_sidebar', 'security' );

	$nonce = isset( $_GET['security'] ) ? $_GET['security'] : $security ;

	if ( ! wp_verify_nonce( $nonce, 'new_custom_sidebar' ) ) {
		exit;
	}

	global $wp_registered_sidebars;

	$instance = new Cherry_Sidebar_Utils();
	$cusotm_sidebar_array = $instance->get_custom_sidebar_array();

	$form_data = isset( $_GET['formdata'] ) ? $_GET['formdata'] : $formdata ;

	if ( ! array_key_exists( 'cherry-sidebars-counter', $cusotm_sidebar_array ) ) {
		$cusotm_sidebar_array['cherry-sidebars-counter'] = 0;
	} else {
		$cusotm_sidebar_array['cherry-sidebars-counter'] += 1;
	}

	$id = $cusotm_sidebar_array['cherry-sidebars-counter'];
	$args = array(
		'name' => $form_data[0]['value'],
		'id' => 'cherry-sidebars-' . $id,
		'description' => $form_data[1]['value'],
		'dynamic-sidebar' => true,
	);
	$registrate_custom_sidebar = cherry_register_sidebar( $args );
	$cusotm_sidebar_array[ 'cherry-sidebars-' . $id ] = $wp_registered_sidebars[ $registrate_custom_sidebar ];
?>
	<div class="widgets-holder-wrap closed cherry-widgets-holder-wrap">
		<div class='cherry-delete-sidebar-manager'>
			<div class="cherry-spinner-wordpress spinner-wordpress-type-1"><span class="cherry-inner-circle"></span></div>
			<span class="dashicons dashicons-trash"></span>
		</div>
		<div id="<?php echo esc_attr( 'cherry-sidebar-manager-' . $id ) ?>" class="widgets-sortables ui-sortable cherry-sidebar-manager">
			<div class="sidebar-name">
				<div class="sidebar-name-arrow"><br></div>
				<h3><?php echo esc_html( $form_data[0]['value'] ) ?><span class="spinner"></span></h3>
			</div>
			<div class="sidebar-description">
				<p class="description"><?php echo esc_html( $form_data[1]['value'] ) ?></p>
			</div>
		</div>
	</div>
<?php
	$instance->set_custom_sidebar_array( $cusotm_sidebar_array );
	wp_die();
}
add_action( 'wp_ajax_remove_custom_sidebar', 'remove_custom_sidebar' );

/**
 * Custom sidebar removing function.
 *
 * @since 1.0.0
 * @return void
 */
function remove_custom_sidebar() {
	check_ajax_referer( 'remove_custom_sidebar', 'security' );

	$nonce = isset( $_GET['security'] ) ? $_GET['security'] : $security ;

	if ( ! wp_verify_nonce( $nonce, 'remove_custom_sidebar' ) ) {
		exit;
	}

	$id = isset( $_GET['id'] ) ? $_GET['id'] : $id ;

	$instance = new Cherry_Sidebar_Utils();
	$cusotm_sidebar_array = $instance->get_custom_sidebar_array();
	unset( $cusotm_sidebar_array[ $id ] );

	$instance->set_custom_sidebar_array( $cusotm_sidebar_array );
}
?>
