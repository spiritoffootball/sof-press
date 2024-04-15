<?php
/**
 * Press Resource Custom Post Type Class.
 *
 * Handles providing an "Press Resource" Custom Post Type.
 *
 * @package Spirit_Of_Football_Press
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Custom Post Type Class.
 *
 * A class that encapsulates a "Press Resource" Custom Post Type.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Press_Resource_CPT {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Press
	 */
	public $plugin;

	/**
	 * Resource loader.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Press_Resource
	 */
	public $resource;

	/**
	 * Custom Post Type name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $post_type_name = 'press_resource';

	/**
	 * Custom Post Type REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $post_type_rest_base = 'press-resources';

	/**
	 * Taxonomy name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_name = 'press-resource-type';

	/**
	 * Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_rest_base = 'press-resource-types';

	/**
	 * Alternative Taxonomy name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_alt_name = 'press-resource-tag';

	/**
	 * Alternative Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_alt_rest_base = 'press-resource-tags';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->resource = $parent;
		$this->plugin   = $parent->plugin;

		// Init when this plugin is loaded.
		add_action( 'sof_press/resource/loaded', [ $this, 'register_hooks' ] );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		// Activation and deactivation.
		add_action( 'sof_press/activate', [ $this, 'activate' ] );
		add_action( 'sof_press/deactivate', [ $this, 'deactivate' ] );

		// Always create post type.
		add_action( 'init', [ $this, 'post_type_create' ] );

		// Make sure our feedback is appropriate.
		add_filter( 'post_updated_messages', [ $this, 'post_type_messages' ] );

		// Make sure our UI text is appropriate.
		add_filter( 'enter_title_here', [ $this, 'post_type_title' ] );

		// Create primary taxonomy.
		add_action( 'init', [ $this, 'taxonomy_create' ] );
		add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_fix_metabox' ], 10, 2 );
		add_action( 'restrict_manage_posts', [ $this, 'taxonomy_filter_post_type' ] );

		// Create alternative taxonomy.
		add_action( 'init', [ $this, 'taxonomy_alt_create' ] );
		add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_alt_fix_metabox' ], 10, 2 );
		add_action( 'restrict_manage_posts', [ $this, 'taxonomy_alt_filter_post_type' ] );

	}

	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		// Pass through.
		$this->post_type_create();
		$this->taxonomy_create();
		$this->taxonomy_alt_create();

		// Go ahead and flush.
		flush_rewrite_rules();

	}

	/**
	 * Actions to perform on plugin deactivation (NOT deletion).
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		// Flush rules to reset.
		flush_rewrite_rules();

	}

	// -------------------------------------------------------------------------

	/**
	 * Create our Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function post_type_create() {

		// Only call this once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Create Post Type args.
		$args = [

			// Labels.
			'labels'              => [
				'name'               => __( 'Press Resources', 'sof-press' ),
				'singular_name'      => __( 'Press Resource', 'sof-press' ),
				'add_new'            => __( 'Add New', 'sof-press' ),
				'add_new_item'       => __( 'Add New Press Resource', 'sof-press' ),
				'edit_item'          => __( 'Edit Press Resource', 'sof-press' ),
				'new_item'           => __( 'New Press Resource', 'sof-press' ),
				'all_items'          => __( 'All Press Resources', 'sof-press' ),
				'view_item'          => __( 'View Press Resource', 'sof-press' ),
				'search_items'       => __( 'Search Press Resources', 'sof-press' ),
				'not_found'          => __( 'No matching Press Resource found', 'sof-press' ),
				'not_found_in_trash' => __( 'No Press Resources found in Trash', 'sof-press' ),
				'menu_name'          => __( 'Press Resources', 'sof-press' ),
			],

			// Defaults.
			'menu_icon'           => 'dashicons-download',
			'description'         => __( 'A press item post type', 'sof-press' ),
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => true,
			'query_var'           => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_position'       => 30,
			'map_meta_cap'        => true,

			// Rewrite.
			'rewrite'             => [
				'slug'       => 'press-resources',
				'with_front' => false,
			],

			// Supports.
			'supports'            => [
				'title',
				'editor',
				'excerpt',
			],

			// REST setup.
			'show_in_rest'        => true,
			'rest_base'           => $this->post_type_rest_base,

		];

		// Set up the post type called "Press".
		register_post_type( $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Override messages for a Custom Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages The existing messages.
	 * @return array $messages The modified messages.
	 */
	public function post_type_messages( $messages ) {

		// Access relevant globals.
		global $post, $post_ID;

		// Define custom messages for our Custom Post Type.
		$messages[ $this->post_type_name ] = [

			// Unused - messages start at index 1.
			0  => '',

			// Item updated.
			1  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Press Resource updated. <a href="%s">View Press Resource</a>', 'sof-press' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Custom fields.
			2  => __( 'Custom field updated.', 'sof-press' ),
			3  => __( 'Custom field deleted.', 'sof-press' ),
			4  => __( 'Press Resource updated.', 'sof-press' ),

			// Item restored to a revision.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			5  => isset( $_GET['revision'] ) ?

				// Revision text.
				sprintf(
					/* translators: %s: The date and time of the revision. */
					__( 'Press Resource restored to revision from %s', 'sof-press' ),
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wp_post_revision_title( (int) $_GET['revision'], false )
				) :

				// No revision.
				false,

			// Item published.
			6  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Press Resource published. <a href="%s">View Press Resource</a>', 'sof-press' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Item saved.
			7  => __( 'Press Resource saved.', 'sof-press' ),

			// Item submitted.
			8  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Press Resource submitted. <a target="_blank" href="%s">Preview Press Resource</a>', 'sof-press' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// Item scheduled.
			9  => sprintf(
				/* translators: 1: The date, 2: The permalink. */
				__( 'Press Resource scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Press Resource</a>', 'sof-press' ),
				/* translators: Publish box date format - see https://php.net/date */
				date_i18n( __( 'M j, Y @ G:i', 'sof-press' ), strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Draft updated.
			10 => sprintf(
				/* translators: %s: The permalink. */
				__( 'Press Resource draft updated. <a target="_blank" href="%s">Preview Press Resource</a>', 'sof-press' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

		];

		// --<
		return $messages;

	}

	/**
	 * Override the "Add title" label.
	 *
	 * @since 1.0.0
	 *
	 * @param str $title The existing title - usually "Add title".
	 * @return str $title The modified title.
	 */
	public function post_type_title( $title ) {

		// Bail if not our post type.
		if ( get_post_type() !== $this->post_type_name ) {
			return $title;
		}

		// Overwrite with our string.
		$title = __( 'Add an identifying name for the Press Resource', 'sof-press' );

		// --<
		return $title;

	}

	// -------------------------------------------------------------------------

	/**
	 * Create our Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Arguments.
		$args = [

			// Same as "category".
			'hierarchical'      => true,

			// Labels.
			'labels'            => [
				'name'              => _x( 'Press Resource Types', 'taxonomy general name', 'sof-press' ),
				'singular_name'     => _x( 'Press Resource Type', 'taxonomy singular name', 'sof-press' ),
				'search_items'      => __( 'Search Press Resource Types', 'sof-press' ),
				'all_items'         => __( 'All Press Resource Types', 'sof-press' ),
				'parent_item'       => __( 'Parent Press Resource Type', 'sof-press' ),
				'parent_item_colon' => __( 'Parent Press Resource Type:', 'sof-press' ),
				'edit_item'         => __( 'Edit Press ResourceResource Type', 'sof-press' ),
				'update_item'       => __( 'Update Press Resource Type', 'sof-press' ),
				'add_new_item'      => __( 'Add New Press Resource Type', 'sof-press' ),
				'new_item_name'     => __( 'New Press Resource Type Name', 'sof-press' ),
				'menu_name'         => __( 'Press Resource Types', 'sof-press' ),
				'not_found'         => __( 'No Press Resource Types found', 'sof-press' ),
			],

			// Rewrite rules.
			'rewrite'           => [
				'slug' => 'press-resources/types',
			],

			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui'           => true,

			// REST setup.
			'show_in_rest'      => true,
			'rest_base'         => $this->taxonomy_rest_base,

		];

		// Register a taxonomy for this CPT.
		register_taxonomy( $this->taxonomy_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Fix the Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The existing arguments.
	 * @param int   $post_id The WordPress post ID.
	 */
	public function taxonomy_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] === $this->taxonomy_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}

	/**
	 * Add a filter for this Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'sof-press' ), $taxonomy->label ),
			'taxonomy'        => $this->taxonomy_name,
			'name'            => $this->taxonomy_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->taxonomy_name ] ) ? wp_unslash( $_GET[ $this->taxonomy_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

	// -------------------------------------------------------------------------

	/**
	 * Create our alternative Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_alt_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Arguments.
		$args = [

			// Same as "category".
			'hierarchical'      => true,

			// Labels.
			'labels'            => [
				'name'              => _x( 'Press Resource Tags', 'taxonomy general name', 'sof-press' ),
				'singular_name'     => _x( 'Press Resource Tag', 'taxonomy singular name', 'sof-press' ),
				'search_items'      => __( 'Search Press Resource Tags', 'sof-press' ),
				'all_items'         => __( 'All Press Resource Tags', 'sof-press' ),
				'parent_item'       => __( 'Parent Press Resource Tag', 'sof-press' ),
				'parent_item_colon' => __( 'Parent Press Resource Tag:', 'sof-press' ),
				'edit_item'         => __( 'Edit Press Resource Tag', 'sof-press' ),
				'update_item'       => __( 'Update Press Resource Tag', 'sof-press' ),
				'add_new_item'      => __( 'Add New Press Resource Tag', 'sof-press' ),
				'new_item_name'     => __( 'New Press Resource Tag Name', 'sof-press' ),
				'menu_name'         => __( 'Press Resource Tags', 'sof-press' ),
				'not_found'         => __( 'No Press Resource Tags found', 'sof-press' ),
			],

			// Rewrite rules.
			'rewrite'           => [
				'slug' => 'press-resources/tags',
			],

			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui'           => true,

			// REST setup.
			'show_in_rest'      => true,
			'rest_base'         => $this->taxonomy_alt_rest_base,

		];

		// Register a taxonomy for this CPT.
		register_taxonomy( $this->taxonomy_alt_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Fix the alternative Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The existing arguments.
	 * @param int   $post_id The WordPress post ID.
	 */
	public function taxonomy_alt_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] === $this->taxonomy_alt_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}

	/**
	 * Add a filter for the alternative Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_alt_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_alt_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'sof-press' ), $taxonomy->label ),
			'taxonomy'        => $this->taxonomy_alt_name,
			'name'            => $this->taxonomy_alt_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->taxonomy_alt_name ] ) ? wp_unslash( $_GET[ $this->taxonomy_alt_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

}
