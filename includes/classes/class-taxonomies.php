<?php
/**
 * Main plugin controller that manages the system.
 *
 * @package GatherPress_Productions
 */

declare(strict_types=1);

namespace GatherPress_Productions;

use GatherPress\Core;

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

		// Hook onto "Event ended" action to update the production-status term of the production.
		add_action( 'gatherpress_event_ended', array( $this, 'update_production_status_on_premiere_end' ) );
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
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'show_admin_column'  => true,
			'query_var'          => true,
			'rewrite'            => false,
			'default_term'       => array(
				'name' => 'Pre-Production',
				'slug' => 'pre-production',
			),
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
			_x( 'Pre-Production', 'Default term for production status', 'gatherpress-productions' ) => _x( 'pre-production', 'Default term for production status', 'gatherpress-productions' ),
			_x( 'In Rehearsal', 'Default term for production status', 'gatherpress-productions' ) => _x( 'in-rehearsal', 'Default term for production status', 'gatherpress-productions' ),
			_x( 'Running', 'Default term for production status', 'gatherpress-productions' ) => _x( 'running', 'Default term for production status', 'gatherpress-productions' ),
			_x( 'Closed', 'Default term for production status', 'gatherpress-productions' ) => _x( 'closed', 'Default term for production status', 'gatherpress-productions' ),
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
	 * Update the production-status term of the production, when its premiere ends.
	 *
	 * This method is hooked to the 'gatherpress_event_ended' action, which is triggered when an event-supporting post ends.
	 * This action is not part of gatherpress core, it's triggered by the "GatherPress Cache Invalidation Hooks" plugin.
	 *
	 * @since 0.1.0
	 *
	 * @param int $event_id The ID of the event-supporting post that ended.
	 *                      Can be an event, a season, a play or anything else.
	 *
	 * @return void
	 */
	public function update_production_status_on_premiere_end( int $event_id ): void {
		$pt_setup  = Setup::get_instance();
		$post_type = get_post_type( $event_id );
		if ( $pt_setup::POST_TYPE_NAME !== $post_type ) {
			return;
		}

		// Update the production-status term of the production, when its premiere ends.
		wp_set_object_terms(
			$event_id,
			_x( 'running', 'Default term for production status', 'gatherpress-productions' ),
			self::TAXONOMY_NAME,
			false
		);
	}
}
