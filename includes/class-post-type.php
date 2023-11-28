<?php
/**
 * Documents Post Type
 *
 * @package   Documentation_Post_Type
 * @license   GPL-2.0+
 */

/**
 * Registration of CPT and related taxonomies.
 *
 * @since 0.1.0
 */
class Documentation_Post_Type {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 0.1.0
	 *
	 * @var string VERSION Plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const PLUGIN_SLUG = 'documentation-post-type';

	/*
	 * Documentation_Post_Type_Registrations
	 */
	protected $registration_handler;

	/**
	 * Initialize the plugin by setting localization and new site activation hooks.
	 *
	 * @since 0.1.0
	 */
	public function __construct( $registration_handler ) {

		$this->registration_handler = $registration_handler;

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );;

		add_filter( 'post_type_link', array( $this, 'post_type_link' ), 1, 2 );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since 0.1.0
	 */
	public function activate() {
		$this->registration_handler->register();
		flush_rewrite_rules();
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since 0.1.0
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.1.0
	 */
	public function load_plugin_textdomain() {
		$domain = self::PLUGIN_SLUG;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
	}

	/**
	 * Add a filter to post_type_link to substitute the category in individual permalinks
	 *
	 * @link https://wordpress.stackexchange.com/questions/108642/permalinks-custom-post-type-custom-taxonomy-post
	 *
	 */
	public function post_type_link(  $post_link, $post  ) {

		if ( is_object( $post ) && $post->post_type == $this->registration_handler->post_type ) {

			$terms = wp_get_object_terms( $post->ID, Documentation_Post_Type_Registrations::DOCUMENTATION_CATEGORY );

			if( $terms ) {
				return str_replace( '%documentation_category%' , $terms[0]->slug , $post_link );
			}
		}

		return $post_link;
	}

}
