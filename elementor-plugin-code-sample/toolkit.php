<?php 
/*
Plugin Name: Avocado Toolkit
Version: 1.0
Description: This plugin used for Avocado WordPress Theme.
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


final class Avocado_Elementor_Dependency {
	const VERSION = '1.0.0';
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
	const MINIMUM_PHP_VERSION = '5.6';
	private static $_instance = null;
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'init' ] );
	}
	public function init() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
	}

	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			esc_html__( '%1$s requires "%2$s" to be installed and activated.', 'elementor-test-extension' ),
			'<strong>' . esc_html__( 'Theme', 'elementor-test-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-test-extension' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			esc_html__( '%1$s requires "%2$s" version %3$s or greater.', 'elementor-test-extension' ),
			'<strong>' . esc_html__( 'Theme', 'elementor-test-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-test-extension' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			esc_html__( '%1$s requires "%2$s" version %3$s or greater.', 'elementor-test-extension' ),
			'<strong>' . esc_html__( 'Avocado Theme', 'elementor-test-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'elementor-test-extension' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function init_widgets() {

		require_once( __DIR__ . '/addons.php' );

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Avocado_Slider_Widget() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Blog_Posts() );
		
        
        if ( class_exists( 'WooCommerce' ) ) {
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Avocado_Categories_Widget() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Avocado_Product_Carousel() ); 
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Avocado_Product_List() ); 
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Avocado_ProductHoverCard_Carousel() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \StepCheckOut() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Product_Reviews_Carousel() );
        } 

	}
}

Avocado_Elementor_Dependency::instance();


function avocado_toolkit_scripts() {
	wp_enqueue_style( 'avacado-toolkit', plugin_dir_url( __FILE__ ) . '/assets/css/avocado-toolkit.css', array(), '20151215' );
	wp_enqueue_style( 'slick', plugin_dir_url( __FILE__ ) . '/assets/css/slick.css', array(), '1.3.15' );

	wp_enqueue_script( 'slick', plugin_dir_url( __FILE__ ) . '/assets/js/slick.js', array('jquery'), '1.3.15' );
}
add_action( 'wp_enqueue_scripts', 'avocado_toolkit_scripts' );



