<?php
/**
 * Widget page functions.
 *
 * @package   Cherry_Custom_Sidebar
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 **/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<!-- Modal window to creating new custom sidebar. -->
<?php add_thickbox(); ?>
<div id="new-sidebar-manager-wrap" style="display:none;">
	<div id="create-new-sidebar-manager">
	<h3><?php _e( 'Create new custom sidebar.', 'cherry-sidebars' ); ?></h3>
	<form id="cherry-sidebars-form" class="cherry-ui-core" method="post">
		<div class="cherry-section">
			<?php
				$ui_text = new UI_Text(
					array(
						'id'			=> 'sidebar-manager-name',
						'name'			=> 'sidebar-manager-name',
						'class'			=> 'required',
						'label'			=> __( 'Sidebar name:', 'cherry-sidebars' ),
					)
				);
				echo $ui_text->render();
			?>
		</div>
		<div class="cherry-section">
			<?php
				$ui_text = new UI_Text(
					array(
						'id'			=> 'sidebar-manager-description',
						'name'			=> 'sidebar-manager-description',
						'class'			=> 'required',
						'label'			=> __( 'Sidebar description:', 'cherry-sidebars' ),
					)
				);
				echo $ui_text->render();
			?>
		</div>
		<div class="cherry-section">
			<?php
				echo get_submit_button( __( 'Create Sidebar', 'cherry-sidebars' ), 'button-primary_', 'sidebar-manager-submit', false , 'style="float:right"' );
			?>
		</div>
		<div class="cherry-spinner-wordpress spinner-wordpress-type-1"><span class="cherry-inner-circle"></span></div>
		<div id="cherry-error-message"><?php _e( 'Cannot add new custom sidebar', 'cherry-sidebars' ); ?></div>
	</form>
	</div>
</div>

<!-- Default sidebar title and description block. -->
<div id="cherry-default-sidebars-title" class="cherry-display-none sidebar-manager-name">
	<div class="sidebar-name-arrow"><br></div>
	<h3><?php _e( 'Default Sidebars', 'cherry-sidebars' ); ?></h3>
</div>
<div id="cherry-default-sidebars-description" class="cherry-display-none">
	<p class="description cherry-default-description"><?php _e( 'Default sidebars created in child theme code itself.', 'cherry-sidebars' ); ?></p>
</div>

<!-- Custom sidebar block. -->
<div id="cherry-sidebars-wrap" class="cherry-display-none">
	<div class="sidebar-manager-name"><div class="sidebar-name-arrow"><br></div>
		<h3><?php _e( 'Cherry Custom Sidebars', 'cherry-sidebars' ); ?></h3>
	</div>
	<div id="cherry-sidebars" class="sidebars-holder">
		<p class="description cherry-default-description"><?php _e( 'You can create a custom sidebar yourself and enable if for any page or post. This can be done on page editing stage.', 'cherry-sidebars' ); ?></p>
		<span class="cherry-ui-core"><a class="thickbox button button-default_ btn-create-sidebar" href="#TB_inline?width=600&height=280&inlineId=new-sidebar-manager-wrap"><?php _e( 'Create a new sidebar', 'cherry-sidebars' ); ?></a></span>

		<div id="cherry-sidebars-holder">
			<div class="sidebars-column-1">
			<?php
				global $wp_registered_sidebars;

				$instance = new Cherry_Sidebar_Utils();
				$cusotm_sidebar_array = $instance->get_custom_sidebar_array();
				unset( $cusotm_sidebar_array ['cherry-sidebars-counter'] );

				$sidebar_counter = count( $cusotm_sidebar_array ) - 1;
				$last_sidebar = end( $cusotm_sidebar_array );
				$counter = 0;
				$wp_registered_sidebars = array_merge( $wp_registered_sidebars, $cusotm_sidebar_array );

				if ( empty( $cusotm_sidebar_array ) ) {
					echo '</div><div class="sidebars-column-2">';
				}

				foreach ( $cusotm_sidebar_array as $sidebar => $cusotm_sidebar ) {

					if ( intval( $sidebar_counter / 2 ) + 1 === $counter || 0 === $sidebar_counter ) {
						echo '</div><div class="sidebars-column-2">';
					}

					$wrap_class = 'widgets-holder-wrap';
					if ( ! empty( $cusotm_sidebar['class'] ) ) {
						$wrap_class .= ' sidebar-' . $cusotm_sidebar['class'];
					}

					if ( $counter > 0 ) {
						$wrap_class .= ' closed';
					}

					?>
					<div class="<?php echo esc_attr( $wrap_class ); ?> cherry-widgets-holder-wrap">
						<div class='cherry-delete-sidebar-manager'>
							<div class="cherry-spinner-wordpress spinner-wordpress-type-1"><span class="cherry-inner-circle"></span></div>
							<span class="dashicons dashicons-trash"></span>
						</div>
						<?php wp_list_widget_controls( $sidebar, $cusotm_sidebar['name'] ); // Show the control forms for each of the widgets in this sidebar ?>
					</div>


			<?php
				$counter += 1;
				}
			?>
			</div>
		</div>
	</div>
</div>
<!-- Script changed widgets page dom. -->
<script>
	(function(){
		'use strict';

		var custemSitebarsWrapper = jQuery("#cherry-sidebars-wrap"),
			defoultSitebarsTitle = jQuery("#cherry-default-sidebars-title"),
			defoultSitebarsDescription = jQuery("#cherry-default-sidebars-description"),
			defoultSitebarsWrapper = jQuery("#widgets-right");

		/*Changed widgets page dom*/
		custemSitebarsWrapper.remove().removeClass('cherry-display-none').clone().appendTo(defoultSitebarsWrapper);
		defoultSitebarsDescription.remove().removeClass('cherry-display-none').clone().prependTo(defoultSitebarsWrapper);
		jQuery('>[class ^= "sidebars-column"], #cherry-default-sidebars-description', defoultSitebarsWrapper).wrapAll('<div id="default-sidebars" class="sidebars-holder"></div>');
		defoultSitebarsTitle.remove().removeClass('cherry-display-none').clone().prependTo(defoultSitebarsWrapper);
	}())
</script>
