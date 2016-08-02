<?php
/**
 * Class for render and saving custom sidebars.
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

if ( ! class_exists( 'Cherry_Custom_Sidebar' ) ) {

	/**
	 * Class for render and saving custom sidebars.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Custom_Sidebar {

		/**
		 * Holds the instances of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Sets up the needed actions for adding and saving the meta boxes.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Add the `Layout` meta box on the 'add_meta_boxes' hook.
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );

			// Saves the post format on the post editing page.
			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

			// Registrate dynamic sidebar
			add_action( 'register_sidebar', array( $this, 'register_dynamic_sidebar' ) );
		}

		/**
		 * Adds the meta box if the post type supports 'cherry-post-style' and the current user has
		 * permission to edit post meta.
		 *
		 * @since  1.0.0
		 * @param  string $post_type The post type of the current post being edited.
		 * @param  object $post      The current post object.
		 * @return void
		 */
		public function add_meta_boxes( $post_type, $post ) {

			cherry_sidebars()->init_modules();

			$allowed_post_types = apply_filters(
				'cherry_sidebar_post_type',
				array(
					'page',
					'post',
					'portfolio',
					'testimonial',
					'service',
					'team',
					'product',
				)
			);

			if ( in_array( $post_type, $allowed_post_types )
					&& ( current_user_can( 'edit_post_meta', $post->ID )
					|| current_user_can( 'add_post_meta', $post->ID )
					|| current_user_can( 'delete_post_meta', $post->ID ) )
				) {

				/**
				 * Filter the array of 'add_meta_box' parametrs.
				 *
				 * @since 1.0.0
				 */
				$metabox = apply_filters( 'cherry_custom_sidebar', array(
					'id'            => 'cherry-sidebars',
					'title'         => __( 'Post Sidebars', 'cherry' ),
					'page'          => $post_type,
					'context'       => 'side',
					'priority'      => 'default',
					'callback_args' => false,
				) );

				/**
				 * Add meta box to the administrative interface.
				 *
				 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
				 */
				add_meta_box(
					$metabox['id'],
					$metabox['title'],
					array( $this, 'callback_metabox' ),
					$metabox['page'],
					$metabox['context'],
					$metabox['priority'],
					$metabox['callback_args']
				);
			}
		}

		/**
		 * Displays a meta box of radio selectors on the post editing screen, which allows theme users to select
		 * the layout they wish to use for the specific post.
		 *
		 * @since  1.0.0
		 * @param  object $post    The post object currently being edited.
		 * @param  array  $metabox Specific information about the meta box being loaded.
		 * @return void
		 */
		public function callback_metabox( $post, $metabox ) {
			cherry_sidebars()->init_modules();
			wp_nonce_field( basename( __FILE__ ), 'cherry-sidebar-nonce' );

			global $wp_registered_sidebars;

			$select_sidebar = $this->get_post_sidebar( $post->ID );
			$select_options = array( '' => __( 'Sidebar not selected', 'cherry-sidebars' ) );

			foreach ( $wp_registered_sidebars as $sidebar => $sidebar_value ) {
				$select_options[ $sidebar_value['id'] ] = $sidebar_value['name'];
			}

			foreach ( $wp_registered_sidebars as $sidebar => $sidebar_value ) {
				if ( array_key_exists( 'dynamic-sidebar',$sidebar_value ) ) {
					continue;
				}

				if ( array_key_exists( 'is_global',$sidebar_value ) && false === $sidebar_value['is_global'] ) {
					continue;
				}

				$output = '<p><strong>' . $sidebar_value['name'] . '</strong></p>';

				$value = ( is_array( $select_sidebar ) && array_key_exists( $sidebar_value['id'], $select_sidebar ) ) ? $select_sidebar[ $sidebar_value['id'] ] : '' ;

				$ui_select = new UI_Select(
					array(
						'id' => $sidebar_value['id'],
						'name' => 'theme_sidebar[' . $sidebar_value['id'] . ']',
						'value' => $value,
						'options' => $select_options,

					)
				);

				$output .= $ui_select->render();

				echo $output;
			};

			?>
				<p class="howto">
					<?php printf(
						__( 'You can choose page sidebars or create a new sidebar on %swidgets page%s .', 'cherry-sidebars' ),
						'<a href="widgets.php" target="_blank" title="' . __( 'Widgets Page', 'cherry-sidebars' ) . '">',
						'</a>'
					); ?>
				</p>
			<?php
		}

		/**
		 * Register dynamic sidebar.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function register_dynamic_sidebar() {
			global $wp_registered_sidebars;

			$instance = new Cherry_Sidebar_Utils();
			$cusotm_sidebar_array = $instance->get_custom_sidebar_array();

			unset( $cusotm_sidebar_array['cherry-sidebars-counter'] );
			$wp_registered_sidebars = array_merge( $wp_registered_sidebars, $cusotm_sidebar_array );
		}

		/**
		 * Saves the post style metadata if on the post editing screen in the admin.
		 *
		 * @since  1.0.0
		 * @param  int    $post_id The ID of the current post being saved.
		 * @param  object $post    The post object currently being saved.
		 * @return void|int
		 */
		public function save_post( $post_id, $post = '' ) {

			if ( ! is_object( $post ) ) {
				$post = get_post();
			}

			// Verify the nonce for the post formats meta box.
			if ( ! isset( $_POST['cherry-sidebar-nonce'] )
				|| ! wp_verify_nonce( $_POST['cherry-sidebar-nonce'], basename( __FILE__ ) )
				) {
				return $post_id;
			}

			// Get the meta key.
			$meta_key = 'post_sidebar';

			// Get the all submitted `page-sidebar-manager` data.
			$sidebar_id = $_POST['theme_sidebar'];

			update_post_meta( $post_id, $meta_key, $sidebar_id );
		}

		/**
		 * Function get post or page sidebar.
		 *
		 * @since  1.0.0
		 * @param  int $post_id         The ID of the current post being saved.
		 * @return string $post_sidebar Sidebar id value.
		 */
		public function get_post_sidebar( $post_id ) {

			// Get the $post_sidebar.
			$post_sidebar = get_post_meta( $post_id, 'post_sidebar', true );

			return $post_sidebar;
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

	Cherry_Custom_Sidebar::get_instance();
}
