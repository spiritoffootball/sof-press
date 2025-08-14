<?php
/**
 * Press Custom Post Type Class.
 *
 * Handles providing an "Press" Custom Post Type.
 *
 * @package Spirit_Of_Football_Press
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Custom Post Type Class.
 *
 * A class that encapsulates an "Press" Custom Post Type.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Press_Coverage_CPT {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Press
	 */
	public $plugin;

	/**
	 * Coverage loader.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Press_Coverage
	 */
	public $coverage;

	/**
	 * Custom Post Type name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $post_type_name = 'press_coverage';

	/**
	 * Custom Post Type REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $post_type_rest_base = 'press-items';

	/**
	 * Taxonomy name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_name = 'press-item-type';

	/**
	 * Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_rest_base = 'press-item-types';

	/**
	 * Alternative Taxonomy name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_alt_name = 'press-item-tag';

	/**
	 * Alternative Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_alt_rest_base = 'press-item-tags';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Spirit_Of_Football_Press_Coverage $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->coverage = $parent;
		$this->plugin   = $parent->plugin;

		// Init when this plugin is loaded.
		add_action( 'sof_press/coverage/loaded', [ $this, 'register_hooks' ] );

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

	// -----------------------------------------------------------------------------------

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
				'name'               => __( 'Press Coverage', 'sof-press' ),
				'singular_name'      => __( 'Press Item', 'sof-press' ),
				'add_new'            => __( 'Add New', 'sof-press' ),
				'add_new_item'       => __( 'Add New Press Item', 'sof-press' ),
				'edit_item'          => __( 'Edit Press Item', 'sof-press' ),
				'new_item'           => __( 'New Press Item', 'sof-press' ),
				'all_items'          => __( 'All Press Items', 'sof-press' ),
				'view_item'          => __( 'View Press Item', 'sof-press' ),
				'search_items'       => __( 'Search Press Items', 'sof-press' ),
				'not_found'          => __( 'No matching Press Item found', 'sof-press' ),
				'not_found_in_trash' => __( 'No Press Items found in Trash', 'sof-press' ),
				'menu_name'          => __( 'Press Coverage', 'sof-press' ),
			],

			// Defaults.
			'menu_icon'           => 'dashicons-bell',
			'description'         => __( 'Press Coverage of Spirit of Football activities', 'sof-press' ),
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
				'slug'       => 'press-coverage',
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
				__( 'Press Item updated. <a href="%s">View Press Item</a>', 'sof-press' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Custom fields.
			2  => __( 'Custom field updated.', 'sof-press' ),
			3  => __( 'Custom field deleted.', 'sof-press' ),
			4  => __( 'Press Item updated.', 'sof-press' ),

			// Item restored to a revision.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			5  => isset( $_GET['revision'] ) ?

				// Revision text.
				sprintf(
					/* translators: %s: The date and time of the revision. */
					__( 'Press Item restored to revision from %s', 'sof-press' ),
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wp_post_revision_title( (int) $_GET['revision'], false )
				) :

				// No revision.
				false,

			// Item published.
			6  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Press Item published. <a href="%s">View Press Item</a>', 'sof-press' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Item saved.
			7  => __( 'Press Item saved.', 'sof-press' ),

			// Item submitted.
			8  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Press Item submitted. <a target="_blank" href="%s">Preview Press Item</a>', 'sof-press' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// Item scheduled.
			9  => sprintf(
				/* translators: 1: The date, 2: The permalink. */
				__( 'Press Item scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Press Item</a>', 'sof-press' ),
				/* translators: Publish box date format - see https://php.net/date */
				date_i18n( __( 'M j, Y @ G:i', 'sof-press' ), strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Draft updated.
			10 => sprintf(
				/* translators: %s: The permalink. */
				__( 'Press Item draft updated. <a target="_blank" href="%s">Preview Press Item</a>', 'sof-press' ),
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
	 * @param string $title The existing title - usually "Add title".
	 * @return string $title The modified title.
	 */
	public function post_type_title( $title ) {

		// Bail if not our post type.
		if ( get_post_type() !== $this->post_type_name ) {
			return $title;
		}

		// Overwrite with our string.
		$title = __( 'Add an identifying name for the Press Item', 'sof-press' );

		// --<
		return $title;

	}

	// -----------------------------------------------------------------------------------

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
				'name'              => _x( 'Press Item Types', 'taxonomy general name', 'sof-press' ),
				'singular_name'     => _x( 'Press Item Type', 'taxonomy singular name', 'sof-press' ),
				'search_items'      => __( 'Search Press Item Types', 'sof-press' ),
				'all_items'         => __( 'All Press Item Types', 'sof-press' ),
				'parent_item'       => __( 'Parent Press Item Type', 'sof-press' ),
				'parent_item_colon' => __( 'Parent Press Item Type:', 'sof-press' ),
				'edit_item'         => __( 'Edit Press Item Type', 'sof-press' ),
				'update_item'       => __( 'Update Press Item Type', 'sof-press' ),
				'add_new_item'      => __( 'Add New Press Item Type', 'sof-press' ),
				'new_item_name'     => __( 'New Press Item Type Name', 'sof-press' ),
				'menu_name'         => __( 'Press Item Types', 'sof-press' ),
				'not_found'         => __( 'No Press Item Types found', 'sof-press' ),
			],

			// Rewrite rules.
			'rewrite'           => [
				'slug' => 'press-coverage/types',
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

	// -----------------------------------------------------------------------------------

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
				'name'              => _x( 'Press Item Tags', 'taxonomy general name', 'sof-press' ),
				'singular_name'     => _x( 'Press Item Tag', 'taxonomy singular name', 'sof-press' ),
				'search_items'      => __( 'Search Press Item Tags', 'sof-press' ),
				'all_items'         => __( 'All Press Item Tags', 'sof-press' ),
				'parent_item'       => __( 'Parent Press Item Tag', 'sof-press' ),
				'parent_item_colon' => __( 'Parent Press Item Tag:', 'sof-press' ),
				'edit_item'         => __( 'Edit Press Item Tag', 'sof-press' ),
				'update_item'       => __( 'Update Press Item Tag', 'sof-press' ),
				'add_new_item'      => __( 'Add New Press Item Tag', 'sof-press' ),
				'new_item_name'     => __( 'New Press Item Tag Name', 'sof-press' ),
				'menu_name'         => __( 'Press Item Tags', 'sof-press' ),
				'not_found'         => __( 'No Press Item Tags found', 'sof-press' ),
			],

			// Rewrite rules.
			'rewrite'           => [
				'slug' => 'press-coverage/tags',
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
