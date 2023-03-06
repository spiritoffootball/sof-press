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
	 * @var object $plugin The plugin object.
	 */
	public $plugin;

	/**
	 * ACF Field Group prefix.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object $group_prefix The prefix of the ACF Field Group.
	 */
	public $group_prefix = 'group_sof_press_';

	/**
	 * Press Resource ACF Field prefix.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object $field_resource_prefix The unique prefix of the Press Resource ACF Fields.
	 */
	public $field_resource_prefix = 'field_sof_press_resource_';

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
		$this->plugin = $parent->plugin;

		// Init when this plugin is loaded.
		add_action( 'sof_press/resource/loaded', [ $this, 'register_hooks' ] );

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
		$this->field_group_press_resource_add();

	}

	/**
	 * Add Press Resources Field Group.
	 *
	 * @since 1.0.0
	 */
	public function field_group_press_resource_add() {

		// Attach the Field Group to our CPT.
		$field_group_location = [
			[
				[
					'param' => 'post_type',
					'operator' => '==',
					'value' => $this->resource->cpt->post_type_name,
				],
			],
		];

		// Hide UI elements on our CPT edit page.
		$field_group_hide_elements = [
			'the_content',
			'excerpt',
			'discussion',
			'comments',
			//'revisions',
			'author',
			'format',
			'page_attributes',
			//'featured_image',
			'tags',
			'send-trackbacks',
		];

		// Define Field Group.
		$field_group = [
			'key' => $this->group_prefix . 'resource',
			'title' => __( 'Press Resource Details', 'sof-press' ),
			'fields' => [],
			'location' => $field_group_location,
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
		$this->fields_resource_add();

	}

	/**
	 * Add "Press Resource" Fields.
	 *
	 * @since 1.0.0
	 */
	public function fields_resource_add() {

		// Define Field.
		$field = [
			'type' => 'text',
			'name' => 'publisher',
			'parent' => $this->group_prefix . 'resource',
			'key' => $this->field_resource_prefix . 'publisher',
			'label' => __( 'Publisher', 'sof-press' ),
			'instructions' => __( 'Who published this Press Resource? Examples: CNN, BBC, etc.', 'sof-press' ),
			'default_value' => '',
			'placeholder' => '',
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
			'type' => 'url',
			'name' => 'link',
			'parent' => $this->group_prefix . 'resource',
			'key' => $this->field_resource_prefix . 'link',
			'label' => __( 'Link', 'sof-press' ),
			'instructions' => __( 'Link to the published Press Resource.', 'sof-press' ),
			'wrapper' => [
				'width' => '',
				'class' => '',
				'id' => '',
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
			'type' => 'date_picker',
			'name' => 'date',
			'parent' => $this->group_prefix . 'resource',
			'key' => $this->field_resource_prefix . 'date',
			'label' => __( 'Date Published', 'sof-press' ),
			'instructions' => __( 'Date of the Press Resource.', 'sof-press' ),
			'display_format' => 'd/m/Y',
			'return_format' => 'd/m/Y',
			'first_day' => 1,
			'wrapper' => [
				'width' => '',
				'class' => '',
				'id' => '',
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
			'type' => 'wysiwyg',
			'name' => 'about',
			'parent' => $this->group_prefix . 'resource',
			'key' => $this->field_resource_prefix . 'about',
			'label' => __( 'About this Press Resource', 'sof-press' ),
			'instructions' => __( 'If you need to describe the Press Resource, use this field.', 'sof-press' ),
			'default_value' => '',
			'placeholder' => '',
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'type' => 'image',
			'name' => 'image',
			'parent' => $this->group_prefix . 'resource',
			'key' => $this->field_resource_prefix . 'image',
			'label' => __( 'Press Resource Image', 'sof-press' ),
			'instructions' => __( 'Feature Image of the Press Resource.', 'sof-press' ),
			'required' => 0,
			'conditional_logic' => 0,
			'preview_size' => 'medium',
			'acfe_thumbnail' => 0,
			//'uploader' => 'basic',
			//'min_size' => 0,
			//'max_size' => $this->civicrm->attachment->field_max_size_get(),
			//'mime_types' => $field['mime_types'],
			'library' => 'all',
			'return_format' => 'array',
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

}
