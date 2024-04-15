<?php
/**
 * Press ACF Class.
 *
 * Handles ACF functionality for Press Items.
 *
 * @package Spirit_Of_Football_Press
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ACF Class.
 *
 * A class that encapsulates ACF functionality for Press Items.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Press_Coverage_ACF {

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
	 * ACF Field Group prefix.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $group_prefix = 'group_sof_press_';

	/**
	 * Press Item ACF Field prefix.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $field_item_prefix = 'field_sof_press_item_';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $parent The parent object.
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

		// Add Field Group and Fields.
		add_action( 'acf/init', [ $this, 'field_groups_add' ] );
		add_action( 'acf/init', [ $this, 'fields_add' ] );

	}

	// -------------------------------------------------------------------------

	/**
	 * Add ACF Field Groups.
	 *
	 * @since 1.0.0
	 */
	public function field_groups_add() {

		// Add our ACF Fields.
		$this->field_group_press_item_add();

	}

	/**
	 * Add Press Items Field Group.
	 *
	 * @since 1.0.0
	 */
	public function field_group_press_item_add() {

		// Attach the Field Group to our CPT.
		$field_group_location = [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $this->coverage->cpt->post_type_name,
				],
			],
		];

		// Hide UI elements on our CPT edit page.
		$field_group_hide_elements = [
			'the_content',
			'excerpt',
			'discussion',
			'comments',
			// 'revisions',
			'author',
			'format',
			'page_attributes',
			// 'featured_image',
			'tags',
			'send-trackbacks',
		];

		// Define Field Group.
		$field_group = [
			'key'            => $this->group_prefix . 'item',
			'title'          => __( 'Press Item Details', 'sof-press' ),
			'fields'         => [],
			'location'       => $field_group_location,
			'hide_on_screen' => $field_group_hide_elements,
		];

		// Now add the Field Group.
		acf_add_local_field_group( $field_group );

		/*
		$e = new \Exception();
		$trace = $e->getTraceAsString();
		error_log( print_r( [
			'method' => __METHOD__,
			'field_group' => $field_group,
			//'backtrace' => $trace,
		], true ) );
		*/

	}

	/**
	 * Add ACF Fields.
	 *
	 * @since 1.0.0
	 */
	public function fields_add() {

		// Add our ACF Fields.
		$this->fields_item_add();

	}

	/**
	 * Add "Press Item" Fields.
	 *
	 * @since 1.0.0
	 */
	public function fields_item_add() {

		// Define Field.
		$field = [
			'type'          => 'text',
			'name'          => 'publisher',
			'parent'        => $this->group_prefix . 'item',
			'key'           => $this->field_item_prefix . 'publisher',
			'label'         => __( 'Publisher', 'sof-press' ),
			'instructions'  => __( 'Who published this Press Item? Examples: CNN, BBC, etc.', 'sof-press' ),
			'default_value' => '',
			'placeholder'   => '',
			'required'      => 1,
		];

		/*
		$e = new \Exception();
		$trace = $e->getTraceAsString();
		error_log( print_r( [
			'method' => __METHOD__,
			'field' => $field,
			//'backtrace' => $trace,
		], true ) );
		*/

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'type'         => 'url',
			'name'         => 'link',
			'parent'       => $this->group_prefix . 'item',
			'key'          => $this->field_item_prefix . 'link',
			'label'        => __( 'Link', 'sof-press' ),
			'instructions' => __( 'Link to the published Press Item.', 'sof-press' ),
			'wrapper'      => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		/*
		$e = new \Exception();
		$trace = $e->getTraceAsString();
		error_log( print_r( [
			'method' => __METHOD__,
			'field' => $field,
			//'backtrace' => $trace,
		], true ) );
		*/

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'type'           => 'date_picker',
			'name'           => 'date',
			'parent'         => $this->group_prefix . 'item',
			'key'            => $this->field_item_prefix . 'date',
			'label'          => __( 'Date Published', 'sof-press' ),
			'instructions'   => __( 'Date of the Press Item.', 'sof-press' ),
			'display_format' => 'd/m/Y',
			'return_format'  => 'd/m/Y',
			'first_day'      => 1,
			'wrapper'        => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		/*
		$e = new \Exception();
		$trace = $e->getTraceAsString();
		error_log( print_r( [
			'method' => __METHOD__,
			'field' => $field,
			//'backtrace' => $trace,
		], true ) );
		*/

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'type'          => 'wysiwyg',
			'name'          => 'about',
			'parent'        => $this->group_prefix . 'item',
			'key'           => $this->field_item_prefix . 'about',
			'label'         => __( 'About this Press Item', 'sof-press' ),
			'instructions'  => __( 'If you need to describe the Press Item, use this field.', 'sof-press' ),
			'default_value' => '',
			'placeholder'   => '',
			'wrapper'       => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'type'              => 'image',
			'name'              => 'image',
			'parent'            => $this->group_prefix . 'item',
			'key'               => $this->field_item_prefix . 'image',
			'label'             => __( 'Press Item Image', 'sof-press' ),
			'instructions'      => __( 'An Image of the Press Item.', 'sof-press' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'preview_size'      => 'medium',
			'acfe_thumbnail'    => 0,
			'library'           => 'all',
			'return_format'     => 'array',
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'type'              => 'file',
			'name'              => 'file',
			'parent'            => $this->group_prefix . 'item',
			'key'               => $this->field_item_prefix . 'file',
			'label'             => __( 'Press Item File', 'sof-press' ),
			'instructions'      => __( 'Downloadable File for the Press Item.', 'sof-press' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'acfe_thumbnail'    => 0,
			'return_format'     => 'array',
			'mime_types'        => '',
			'library'           => 'all',
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

}
