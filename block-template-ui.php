<?php
/**
 * Plugin Name:       Block Template UI
 * Description:       Proof of Concept User Interface for WordPress Block Templates.
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Matt Watson
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       block-template-ui
 *
 * @package           block-template-ui
 */

/**
 * Register Custom Post Type.
 *
 * Register the CPT for the Block Template UI, we have called it 'Page Templates' but it could be anything.
 *
 * @return void
 */
function block_template_ui_block_template_ui_register_custom_post_type() {
	$labels = [
		'name'                  => _x( 'Page Templates', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Page Template', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Page Templates', 'text_domain' ),
		'name_admin_bar'        => __( 'Page Template', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	];

	/**
	 * Note that these settings would need to be optimised.
	 * For example, we wouldn't want this to be publicly visible.
	 */
	$args = [
		'label'                 => __( 'Page Template', 'text_domain' ),
		'description'           => __( 'Page Templates', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest'          => true,
		'menu_icon'             => 'dashicons-editor-table',
	];
	register_post_type( 'page_template', $args );
}
add_action( 'init', 'block_template_ui_block_template_ui_register_custom_post_type' );

/**
 * Register Meta Box.
 *
 * A very simple meta box to allow us to apply the template to a specific page.
 *
 * This should be registered as a repeatable logic field as part of the sidebar,
 * but for a PoC this is sufficient.
 *
 * @return void
 */
function block_template_ui_block_template_ui_register_meta_box() {
	add_meta_box(
		'page-template',
		__( 'Page Template', 'text_editor' ),
		function( $post ) {
			$post_types = get_post_types( [ 'public' => true ], 'objects' );
			$selected_post_type = get_post_meta( $post->ID, 'page_template_post', true );
			$selected_post_id = get_post_meta( $post->ID, 'page_template_post_id', true );
			/**
			 * In an ideal scenario you would select a Post Type, and a new AND/OR choice
			 * would appear so you could select another Post Type, OR a post ID.
			 *
			 * You would also be able to select multiple post IDs with a combination of AND/OR choices.
			 *
			 * That, or we would have two multiselect fields where you could search for a post ID
			 * AND/OR search for a post type.
			 */
			?>
			<p>
				<label for="page_template_post">Post Type</label>
				<select id="page_template_post" name="page_template_post">
					<option value="">Please Select...</option>
					<?php
					foreach ( $post_types as $post_type ) {

						if ( $post_type->name === 'page_template' ) {
							continue;
						}

						?>
						<option value="<?php echo esc_attr( $post_type->name ); ?>" <?php echo selected( $selected_post_type, $post_type->name ); ?>><?php echo esc_html( $post_type->labels->singular_name ); ?></option>
						<?php
					}
					?>
				</select>
			</p>

			<p>
				<label for="page_template_post_id">Post ID</label>
				<select id="page_template_post_id" name="page_template_post_id">
					<option value="">Please Select...</option>
					<?php
					if ( ! empty( $selected_post_type ) ) {
						$post_query = new WP_Query([
							'post_type' => $selected_post_type,
							'posts_per_page' => -1,
							'post_status' => 'any',
						]);
						foreach( $post_query->posts as $select_post ) {
							?>
							<option value="<?php echo esc_attr( $select_post->ID ); ?>" <?php echo selected( $selected_post_id, $select_post->ID ); ?>><?php echo esc_html( $select_post->post_title ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</p>
			<?php
		},
		'page_template'
	);
}
add_action( 'add_meta_boxes', 'block_template_ui_block_template_ui_register_meta_box' );

/**
 * Save Post Meta.
 *
 * @param  int    $post_id Post ID.
 * @param  object $post    Post Object.
 *
 * @return void
 */
function block_template_ui_block_template_ui_save_post_meta( $post_id, $post ) {

	/**
	 * IMPORTANT! PoC Only: We are missing security validation here.
	 */

	if ( ! empty( $_POST['page_template_post'] ) ) {
		update_post_meta( $post_id, 'page_template_post', $_POST['page_template_post'] );
	} else {
		delete_post_meta( $post_id, 'page_template_post' );
	}

	if ( ! empty( $_POST['page_template_post_id'] ) ) {
		update_post_meta( $post_id, 'page_template_post_id', $_POST['page_template_post_id'] );
	} else {
		delete_post_meta( $post_id, 'page_template_post_id' );
	}

}
add_action( 'save_post', 'block_template_ui_block_template_ui_save_post_meta', 10, 2 );

/**
 * Dynamic Post Templates.
 *
 * In truth we would likely run this template creator on save, and then save the data
 * elsewhere, and/or implement a caching layer.
 *
 * @return void
 */
function block_template_ui_block_template_ui_dynamic_post_templates() {
	$page_template_query = new WP_Query(
		[
			'post_type' => 'page_template',
			'posts_per_page' => -1, // PoC Only: This should not be set to -1.
		]
	);

	// Loop through each post template.
	foreach ( $page_template_query->posts as $page_template ) {
		if ( ! has_blocks( $page_template->post_content ) ) {
			continue;
		}

		$selected_post_type = get_post_meta( $page_template->ID, 'page_template_post', true );
		$selected_post_id = get_post_meta( $page_template->ID, 'page_template_post_id', true );

		if ( ! $selected_post_type ) {
			return;
		}

		// Get the blocks from the post template and build the template.
		$blocks = parse_blocks( $page_template->post_content );
		$template = block_template_ui_block_template_ui_build_block_template( $blocks );

		/**
		 * If the post template is set to run on a page ID,
		 * this will prevent the code running when not on that post ID.
		 *
		 * Also this is missing security validation.
		 */
		if ( ! empty( $selected_post_id ) && $_GET['post'] !== $selected_post_id ) {
			return;
		}

		$post_type_object                = get_post_type_object( $selected_post_type );
		$post_type_object->template      = $template;
		$post_type_object->template_lock = 'all';
	}
}
add_action( 'init', 'block_template_ui_block_template_ui_dynamic_post_templates', 10, 2 );

/**
 * Build Block Template.
 *
 * Loop through the blocks and inner blocks to build up our array.
 *
 * Note that there may be a better built in WP core function to do this.
 * This was cobbled together as a PoC.
 *
 * @param  array $blocks Blocks.
 *
 * @return void
 */
function block_template_ui_block_template_ui_build_block_template( $blocks ) {
	$template = [];
	foreach ( $blocks as $block ) {

		// If the block name is empty, bail.
		if ( empty( $block['blockName'] ) ) {
			continue;
		}

		$template_attributes = [];


		/**
		 * Note that certain blocks need special handling, like we should not have the
		 * outer <p> on paragraph blocks.
		 *
		 * PoC only, the real deal would need these considerations (there might be a WP core function
		 * that handles this already)
		 */
		if ( ! empty( $block['innerHTML'] ) ) {
			$inner_html = str_replace(['<p>', '</p>'], '', $block['innerHTML'] );
			$template_attributes['content'] = trim( $inner_html );
		}

		// Handle Image URLs.
		if ( $block['blockName'] === 'core/image' ) {
			$template_attributes['url'] = wp_get_attachment_url( $block['attrs']['id'] );
		}

		if ( ! empty( $block['attrs'] ) ) {
			$template_attributes[] = (array) $block['attrs'];
		}

		$template_row = [
			$block[ 'blockName' ],
			$template_attributes,
		];

		if ( $block['innerBlocks'] ) {
			$template_row[] = block_template_ui_block_template_ui_build_block_template( $block['innerBlocks'] );
		}

		$template[] = $template_row;
	}

	return $template;
};