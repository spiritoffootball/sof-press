<?php
/**
 * Press Resource ACF Class.
 *
 * Handles ACF functionality for "Press Resources".
 *
 * @package Spirit_Of_Football_Press
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ACF Class.
 *
 * A class that encapsulates ACF functionality for "Press Resources".
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Press_Resource_ACF {

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
	 * ACF Field Group prefix.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $group_prefix = 'group_sof_press_';

	/**
	 * Press Resource ACF Field prefix.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $field_resource_prefix = 'field_sof_press_resource_';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Spirit_Of_Football_Press_Resource $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->resource = $parent;
		$this->plugin   = $parent->plugin;

		// Init when this plugin is loaded.
		add_action( 'sof_press/resource/loaded', [ $this, 'register_hooks' ] );

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		// Add Field Group and Fields.
		add_action( 'acf/init', [ $this, 'field_groups_add' ] );
		add_action( 'acf/init', [ $this, 'fields_add' ] );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Adds ACF Field Groups.
	 *
	 * @since 1.0.0
	 */
	public function field_groups_add() {

		// Add our ACF Fields.
		$this->field_group_press_resource_add();

	}

	/**
	 * Adds Press Resources Field Group.
	 *
	 * @since 1.0.0
	 */
	private function field_group_press_resource_add() {

		// Attach the Field Group to our CPT.
		$field_group_location = [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $this->resource->cpt->post_type_name,
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
			'key'            => $this->group_prefix . 'resource',
			'title'          => __( 'Press Resource Details', 'sof-press' ),
			'fields'         => [],
			'location'       => $field_group_location,
			'hide_on_screen' => $field_group_hide_elements,
		];

		// Now add the Field Group.
		acf_add_local_field_group( $field_group );

	}

	/**
	 * Adds ACF Fields.
	 *
	 * @since 1.0.0
	 */
	public function fields_add() {

		// Add our ACF Fields.
		$this->fields_resource_add();

	}

	/**
	 * Adds "Press Resource" Fields.
	 *
	 * @since 1.0.0
	 */
	private function fields_resource_add() {

		// Define Field.
		$field = [
			'type'          => 'wysiwyg',
			'name'          => 'about',
			'parent'        => $this->group_prefix . 'resource',
			'key'           => $this->field_resource_prefix . 'about',
			'label'         => __( 'About this Press Resource', 'sof-press' ),
			'instructions'  => __( 'If you need a general description of this Press Resource, use this field.', 'sof-press' ),
			'default_value' => '',
			'placeholder'   => '',
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'type'              => 'image',
			'name'              => 'image',
			'parent'            => $this->group_prefix . 'resource',
			'key'               => $this->field_resource_prefix . 'image',
			'label'             => __( 'Press Resource Image', 'sof-press' ),
			'instructions'      => __( 'Feature Image of the Press Resource.', 'sof-press' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'preview_size'      => 'medium',
			'acfe_thumbnail'    => 1,
			// 'uploader' => 'basic',
			// 'min_size' => 0,
			// 'max_size' => $this->civicrm->attachment->field_max_size_get(),
			// 'mime_types' => $field['mime_types'],
			'library'           => 'all',
			'return_format'     => 'array',
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define Repeater Field.
		$repeater = [
			'type'                          => 'repeater',
			'name'                          => 'files',
			'parent'                        => $this->group_prefix . 'resource',
			'key'                           => $this->field_resource_prefix . 'file_repeater',
			'label'                         => __( 'Press Resource Files', 'sof-press' ),
			'instructions'                  => __( 'Downloadable Files for this Press Resource.', 'sof-press' ),
			'required'                      => 0,
			'conditional_logic'             => 0,
			'wrapper'                       => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'acfe_permissions'              => '',
			'acfe_repeater_stylised_button' => 0,
			'collapsed'                     => $this->field_resource_prefix . 'file',
			'min'                           => 0,
			'max'                           => 0,
			'layout'                        => 'block',
			'button_label'                  => __( 'Add Press Resource File', 'sof-press' ),
			'sub_fields'                    => [],
		];

		// Define File Field.
		$repeater['sub_fields'][] = [
			'type'              => 'file',
			'name'              => 'file',
			'key'               => $this->field_resource_prefix . 'file',
			'label'             => __( 'File', 'sof-press' ),
			'instructions'      => __( 'The Downloadable File itself.', 'sof-press' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'acfe_thumbnail'    => 0,
			'return_format'     => 'array',
			'mime_types'        => '',
			'library'           => 'all',
			'wrapper'           => [
				'width' => '50',
				'class' => '',
				'id'    => '',
			],
		];

		// Define File Preview Field.
		$repeater['sub_fields'][] = [
			'type'              => 'image',
			'name'              => 'file_preview',
			'key'               => $this->field_resource_prefix . 'file_preview',
			'label'             => __( 'File Preview', 'sof-press' ),
			'instructions'      => __( 'An optional Image that shows a preview of the Downloadable File.', 'sof-press' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'acfe_thumbnail'    => 0,
			'return_format'     => 'array',
			'mime_types'        => '',
			'library'           => 'all',
			'wrapper'           => [
				'width' => '50',
				'class' => '',
				'id'    => '',
			],
		];

		// Now add Field.
		acf_add_local_field( $repeater );

	}

}
