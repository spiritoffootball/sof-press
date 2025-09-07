<?php
/**
 * SOF Press
 *
 * Plugin Name:       SOF Press
 * Description:       Provides "Press" functionality for the Spirit of Football website.
 * Plugin URI:        https://github.com/spiritoffootball/sof-press
 * GitHub Plugin URI: https://github.com/spiritoffootball/sof-press
 * Version:           1.0.4a
 * Author:            Christian Wach
 * Author URI:        https://haystack.co.uk
 * Text Domain:       sof-press
 * Domain Path:       /languages
 *
 * @package Spirit_Of_Football_Press
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set our version here.
define( 'SOF_PRESS_VERSION', '1.0.4a' );

// Store reference to this file.
if ( ! defined( 'SOF_PRESS_FILE' ) ) {
	define( 'SOF_PRESS_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'SOF_PRESS_URL' ) ) {
	define( 'SOF_PRESS_URL', plugin_dir_url( SOF_PRESS_FILE ) );
}

// Store PATH to this plugin's directory.
if ( ! defined( 'SOF_PRESS_PATH' ) ) {
	define( 'SOF_PRESS_PATH', plugin_dir_path( SOF_PRESS_FILE ) );
}

/**
 * Plugin Class.
 *
 * A class that encapsulates plugin functionality.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Press {

	/**
	 * Coverage loader.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Press_Coverage
	 */
	public $coverage;

	/**
	 * Resource loader object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Press_Resource
	 */
	public $resource;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Initialise when all plugins are loaded.
		add_action( 'plugins_loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this plugin.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap plugin.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Fires when this plugin is loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_press/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Includes plugin files.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {

		// Include class files.
		include SOF_PRESS_PATH . 'includes/class-coverage.php';
		include SOF_PRESS_PATH . 'includes/class-resource.php';

	}

	/**
	 * Sets up this plugin's objects.
	 *
	 * @since 1.0.0
	 */
	private function setup_objects() {

		// Init objects.
		$this->coverage = new Spirit_Of_Football_Press_Coverage( $this );
		$this->resource = new Spirit_Of_Football_Press_Resource( $this );

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks() {

		// Use translation.
		add_action( 'init', [ $this, 'translation' ] );

	}

	/**
	 * Enables translation.
	 *
	 * @since 1.0.0
	 */
	public function translation() {

		// Load translations.
		// phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
		load_plugin_textdomain(
			'sof-press', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( SOF_PRESS_FILE ) ) . '/languages/' // Relative path to files.
		);

	}

	/**
	 * Performs plugin activation tasks.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		// Maybe init.
		$this->initialise();

		/**
		 * Broadcast plugin activation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_press/activate' );

	}

	/**
	 * Performs plugin deactivation tasks.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		// Maybe init.
		$this->initialise();

		/**
		 * Broadcast plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_press/deactivate' );

	}

}

/**
 * Gets a reference to this plugin.
 *
 * @since 1.0.0
 *
 * @return Spirit_Of_Football_Press $plugin The plugin reference.
 */
function sof_press() {

	// Store instance in static variable.
	static $plugin = false;

	// Maybe return instance.
	if ( false === $plugin ) {
		$plugin = new Spirit_Of_Football_Press();
	}

	// --<
	return $plugin;

}

// Initialise plugin now.
sof_press();

// Activation.
register_activation_hook( __FILE__, [ sof_press(), 'activate' ] );

// Deactivation.
register_deactivation_hook( __FILE__, [ sof_press(), 'deactivate' ] );

/*
 * Uninstall uses the 'uninstall.php' method.
 *
 * @see https://codex.wordpress.org/Function_Reference/register_uninstall_hook
 */
