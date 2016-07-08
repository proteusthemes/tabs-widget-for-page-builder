<?php
/*
Plugin Name: Tabs Widget for Page Builder
Plugin URI: http://www.proteusthemes.com
Description: Bootstrap tabs widget for use in Page Builder by SiteOrigin
Version: 1.2.1
Author: ProteusThemes
Author URI: http://www.proteusthemes.com
License: GPL3
License URI: http://www.gnu.org/licenses/gpl.html
Text domain: pt-tabs
*/


// Path/URL to root of this plugin, with trailing slash
define( 'PT_TABS_PATH', apply_filters( 'pt-tabs/plugin_dir_path', plugin_dir_path( __FILE__ ) ) );
define( 'PT_TABS_URL', apply_filters( 'pt-tabs/plugin_dir_url', plugin_dir_url( __FILE__ ) ) );

// Current version of the plugin
define( 'PT_TABS_VERSION', apply_filters( 'pt-tabs/version', '1.2.1' ) );

/**
 * Tabs Widget class, so we don't have to worry about namespaces
 */
class PT_Tabs {

	private $enqueue_admin_scripts;
	private $enqueue_frontend_scripts;

	function __construct() {

		// actions
		add_action( 'plugins_loaded', array( $this, 'setup_this_plugin' ) );
	}


	/**
	 * Setup this widget only if the Page Builder by SiteOrigin plugin is active
	 */
	function setup_this_plugin() {
		if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
			load_plugin_textdomain( 'pt-tabs', false, PT_TABS_PATH . 'languages/' );

			// actions to fire once we know, that Page Builder by SiteOrigin plugin is active
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_js_css' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js_css' ), 20 );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			add_action( 'after_setup_theme', array( $this, 'setup_filters' ) );
		}
	}


	/**
	 * Enqueue the JS and CSS files for backend (admin area)
	 * @see admin_enqueue_scripts
	 * @return void
	 */
	function admin_enqueue_js_css() {
		if ( 'mustache-js' === $this->enqueue_admin_scripts['mustache-js'] ) {
			// Register mustache.js
			wp_register_script( 'mustache-js', PT_TABS_URL . 'bower_components/mustache.js/mustache.min.js' );
		}

		if ( $this->enqueue_admin_scripts['admin-js'] ) {
			// Enqueue admin JS
			wp_enqueue_script( 'pt-tabs-admin-js', PT_TABS_URL . 'assets/admin/js/admin.js', array( 'jquery', 'underscore', 'backbone', 'jquery-ui-sortable', $this->enqueue_admin_scripts['mustache-js'] ), PT_TABS_VERSION );
		}

		// Enqueue admin CSS
		wp_enqueue_style( 'pt-tabs-admin-style', PT_TABS_URL . 'assets/admin/css/admin.css', array(), PT_TABS_VERSION );
	}


	/**
	 * Enqueue the JS and CSS files for frontend
	 * @see wp_enqueue_scripts
	 * @return void
	 */
	function enqueue_js_css() {
		if ( $this->enqueue_frontend_scripts['main-js'] ) {
			// Main JS (selected Bootstrap JS parts)
			wp_enqueue_script( 'pt-tabs-main-js', PT_TABS_URL . 'assets/js/main.min.js', array( 'jquery' ), PT_TABS_VERSION );
		}

		if ( $this->enqueue_frontend_scripts['main-css'] ) {
			// Main CSS (selected Bootstrap CSS parts)
			wp_enqueue_style( 'pt-tabs-style', PT_TABS_URL . 'assets/css/style.min.css', array(), PT_TABS_VERSION );
		}
	}


	/**
	 * Register the Tab widget
	 */
	function widgets_init() {
		require_once PT_TABS_PATH . 'inc/class-tabs-widget.php';
	}


	/**
	 * Setup filters after the theme loads
	 */
	function setup_filters() {
		$this->enqueue_admin_scripts = apply_filters( 'pt-tabs/enqueue_admin_scripts',
			array(
				'mustache-js' => 'mustache-js',
				'admin-js'    => true,
			)
		);

		$this->enqueue_frontend_scripts = apply_filters( 'pt-tabs/enqueue_frontend_scripts',
			array(
				'main-js'  => true,
				'main-css' => true,
			)
		);
	}

}

//initialize the plugin
new PT_Tabs();