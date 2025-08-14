<?php
/**
 * Press Coverage loader class.
 *
 * Handles Press Coverage functionality.
 *
 * @package Spirit_Of_Football_Press
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Press Coverage class.
 *
 * A class that encapsulates Press Coverage functionality.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Press_Coverage {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Press
	 */
	public $plugin;

	/**
	 * Custom Post Type object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Press_Coverage_CPT
	 */
	public $cpt;

	/**
	 * ACF object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Press_Coverage_ACF
	 */
	public $acf;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Spirit_Of_Football_Press $parent The parent object.
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
		do_action( 'sof_press/coverage/loaded' );

	}

	/**
	 * Include files.
	 *
	 * @since 1.0.0
	 */
	public function include_files() {

		// Include class files.
		include SOF_PRESS_PATH . 'includes/class-coverage-cpt.php';
		include SOF_PRESS_PATH . 'includes/class-coverage-acf.php';

	}

	/**
	 * Set up this plugin's objects.
	 *
	 * @since 1.0.0
	 */
	public function setup_objects() {

		// Init objects.
		$this->cpt = new Spirit_Of_Football_Press_Coverage_CPT( $this );
		$this->acf = new Spirit_Of_Football_Press_Coverage_ACF( $this );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

	}

}
