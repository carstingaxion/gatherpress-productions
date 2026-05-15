<?php
/**
 * Main plugin controller that manages the system.
 *
 * @package GatherPress_Productions
 */

declare(strict_types=1);

namespace GatherPress_Productions;

use GatherPress\Core;
use GatherPress_Productions\Setup;

/**
 * Main plugin class using Singleton pattern.
 *
 * @since 0.1.0
 */
class Taxonomies {

	use Core\Traits\Singleton;

	const TAXONOMY_NAME = 'production_status';

	/**
	 * Constructor for the Taxonomies class.
	 *
	 * Initializes and sets up various components of the plugin.
	 */
	protected function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Set up hooks for various purposes.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'init', array( $this, 'add_default_terms' ) );
		add_action( 'wp_insert_post', array( $this, 'set_default_status' ), 10, 3 );
	}

	/**
	 * 1. Register Production Status Taxonomy
	 */
	public function register_taxonomy() {

		$labels = array(
			'name'          => _x( 'Production Statuses', 'taxonomy general name', 'gatherpress-productions' ),
			'singular_name' => _x( 'Production Status', 'taxonomy singular name', 'gatherpress-productions' ),
			'search_items'  => __( 'Search Production Statuses', 'gatherpress-productions' ),
			'all_items'     => __( 'All Production Statuses', 'gatherpress-productions' ),
			'edit_item'     => __( 'Edit Production Status', 'gatherpress-productions' ),
			'update_item'   => __( 'Update Production Status', 'gatherpress-productions' ),
			'add_new_item'  => __( 'Add New Production Status', 'gatherpress-productions' ),
			'new_item_name' => __( 'New Production Status Name', 'gatherpress-productions' ),
			'menu_name'     => __( 'Production Status', 'gatherpress-productions' ),
		);

		$args = array(
			'labels'       => $labels,
			'public'       => false,
			'publicly_queryable' => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'query_var'    => true,
			'rewrite'      => false,
		);

		register_taxonomy(
			self::TAXONOMY_NAME,
			Setup::POST_TYPE_NAME,
			$args
		);
	}

	/**
	 * 2. Add Default Terms
	 */
	public function add_default_terms() {

		$terms = array(
			'Pre-Production' => 'pre-production',
			'In Rehearsal'   => 'in-rehearsal',
			'Running'        => 'running',
			'Closed'         => 'closed',
		);

		foreach ( $terms as $name => $slug ) {
			if ( ! term_exists( $name, self::TAXONOMY_NAME ) ) {
				wp_insert_term(
					$name,
					self::TAXONOMY_NAME,
					array(
						'slug' => $slug,
					)
				);
			}
		}
	}

	/**
	 * 3. Set Default Status on New Event
	 */
	public function set_default_status( int $post_id, \WP_Post $post, bool $update ) {

		// Only target gatherpress_event post type
		if ( $post->post_type !== Setup::POST_TYPE_NAME ) {
			return;
		}

		// Only on initial creation
		if ( $update ) {
			return;
		}

		// Skip autosaves/revisions
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Check if terms already assigned
		$terms = wp_get_object_terms( $post_id, self::TAXONOMY_NAME );
		if ( ! empty( $terms ) ) {
			return;
		}

		// Assign default: Pre-Production
		wp_set_object_terms( $post_id, 'pre-production', self::TAXONOMY_NAME );
	}
}
