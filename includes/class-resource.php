<?php
/**
 * Press Resource loader class.
 *
 * Handles Press Resource functionality.
 *
 * @package Spirit_Of_Football_Press
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Press Resource class.
 *
 * A class that encapsulates Press Resource functionality.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Press_Resource {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;

	/**
	 * Custom Post Type object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object $cpt The Custom Post Type object.
	 */
	public $cpt;

	/**
	 * ACF object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object $acf The ACF object.
	 */
	public $acf;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->plugin = $parent;

		// Init when this plugin is loaded.
		add_action( 'sof_press/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this class.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Bootstrap object.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Broadcast that this class is now loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_press/resource/loaded' );

	}

	/**
	 * Include files.
	 *
	 * @since 1.0.0
	 */
	public function include_files() {

		// Include class files.
		include SOF_PRESS_PATH . 'includes/class-resource-cpt.php';
		include SOF_PRESS_PATH . 'includes/class-resource-acf.php';

	}

	/**
	 * Set up this plugin's objects.
	 *
	 * @since 1.0.0
	 */
	public function setup_objects() {

		// Init objects.
		$this->cpt = new Spirit_Of_Football_Press_Resource_CPT( $this );
		$this->acf = new Spirit_Of_Football_Press_Resource_ACF( $this );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

	}

}
