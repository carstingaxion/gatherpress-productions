<?php
/**
 * Main plugin controller that manages the hierarchical location taxonomy system.
 *
 * @package GatherPress_Productions
 */

declare(strict_types=1);

namespace GatherPress_Productions;

use GatherPress\Core;
use GatherPress\Core\Settings;

/**
 * Main plugin class using Singleton pattern.
 *
 * @since 0.1.0
 */
class Setup {

	use Core\Traits\Singleton;

	const POST_TYPE_NAME = 'gatherpress_play'; // 'gatherpress_production' is too long, it allows up to 20 char
	const POST_TYPE_SLUG = 'production'; // @TODO Replace with a option-based value for better customization, similar to the one used for the 'event' post type in GatherPress.

	const TAXONOMY_NAME = '_gatherpress_play';

	/**
	 * Constructor for the Setup class.
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

		// Re-label Admin columns & Editor sidebar panel.
		add_filter( 'gatherpress_event_datetime_label', array( $this, 'change_event_datetime_label' ), 10, 2 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );

		add_action( 'init', array( $this, 'load_textdomain' ) );
		// Register productions post type.
		add_action( 'init', array( $this, 'register_post_type' ) );
		// Register production shadow taxonomy onto events.
		add_action( 'init', array( $this, 'register_post_tax_relations' ), 12 );

		// add_action( 'pre_get_posts', function ( \WP_Query $query ) {
		// 	if ( $query->is_main_query() && $query->is_post_type_archive( self::POST_TYPE_NAME ) ) {
		// 		// error_log( var_export( $query->query_vars, true ) );
		// 		error_log( var_export( $query, true ) );
		// 	}
		// }, 100 );

		// Add settings sub-page.
		add_action( 'gatherpress_sub_pages', array( $this, 'setup_sub_page' ) );

		// Setup starter patterns.
		// add_filter( 'gatherpress_event_starter_patterns', array( $this, 'setup_starter_patterns' ), 10, 2 );
		add_action( 'init', array( $this, 'register_starter_patterns_natively' ) );

	}

	/**
	 * Load the plugin's text domain for translations.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		\load_plugin_textdomain(
			'gatherpress-productions',
			false,
			'gatherpress-productions/languages'
		);
	}

	/**
	 * Register the custom post type for productions.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_post_type(): void {
		$settings     = Settings::get_instance();
		$rewrite_slug = $settings->get( 'productions_url' );

		$labels = array(
			'name'                     => __( 'Productions', 'gatherpress-productions' ),
			'singular_name'            => __( 'Production', 'gatherpress-productions' ),
			'add_new'                  => __( 'Add New', 'gatherpress-productions' ),
			'add_new_item'             => __( 'Add New Production', 'gatherpress-productions' ),
			'edit_item'                => __( 'Edit Production', 'gatherpress-productions' ),
			'new_item'                 => __( 'New Production', 'gatherpress-productions' ),
			'view_item'                => __( 'View Production', 'gatherpress-productions' ),
			'view_items'               => __( 'View Productions', 'gatherpress-productions' ),
			'search_items'             => __( 'Search Productions', 'gatherpress-productions' ),
			'not_found'                => __( 'No productions found', 'gatherpress-productions' ),
			'not_found_in_trash'       => __( 'No productions found in Trash', 'gatherpress-productions' ),
			'parent_item_colon'        => __( 'Parent Production:', 'gatherpress-productions' ),
			'all_items'                => __( 'All Productions', 'gatherpress-productions' ),
			'archives'                 => __( 'Production Archives', 'gatherpress-productions' ),
			'attributes'               => __( 'Production Attributes', 'gatherpress-productions' ),
			'insert_into_item'         => __( 'Insert into production', 'gatherpress-productions' ),
			'uploaded_to_this_item'    => __( 'Uploaded to this production', 'gatherpress-productions' ),
			'featured_image'           => __( 'Production Poster', 'gatherpress-productions' ),
			'set_featured_image'       => __( 'Set production poster', 'gatherpress-productions' ),
			'remove_featured_image'    => __( 'Remove production poster', 'gatherpress-productions' ),
			'use_featured_image'       => __( 'Use as production poster', 'gatherpress-productions' ),
			'menu_name'                => __( 'Productions', 'gatherpress-productions' ),
			'filter_items_list'        => __( 'Filter productions list', 'gatherpress-productions' ),
			'filter_by_date'           => __( 'Filter productions by date', 'gatherpress-productions' ),
			'items_list_navigation'    => __( 'Productions list navigation', 'gatherpress-productions' ),
			'items_list'               => __( 'Productions list', 'gatherpress-productions' ),
			'item_published'           => __( 'Production published.', 'gatherpress-productions' ),
			'item_published_privately' => __( 'Production published privately.', 'gatherpress-productions' ),
			'item_reverted_to_draft'   => __( 'Production reverted to draft.', 'gatherpress-productions' ),
			'item_trashed'             => __( 'Production moved to Trash.', 'gatherpress-productions' ),
			'item_scheduled'           => __( 'Production scheduled.', 'gatherpress-productions' ),
			'item_updated'             => __( 'Production updated.', 'gatherpress-productions' ),
			'item_link'                => __( 'Production Link', 'gatherpress-productions' ),
			'item_link_description'    => __( 'A link to a production.', 'gatherpress-productions' ),
		);

		\register_post_type(
			self::POST_TYPE_NAME,
			array(
				'labels'       => $labels,
				'supports'     => array(
					'title',
					'editor',
					'thumbnail',
					'excerpt',
					'custom-fields',
					'revisions',
					'gatherpress-event-date', // @see
					'gatherpress-shadow-source', // @see https://github.com/GatherPress/gatherpress/tree/develop/docs/developer/post-type-supports#gatherpress-shadow-source
				),
				'public'       => true,
				'show_in_rest' => true, // This in combination with  'supports' => array('editor') enables the Gutenberg editor.
				'hierarchical' => true, // (Note from Subsites plugin: Important for rewriting to work with 'parent' PT.)
				'description'  => '',

				'rewrite'      => [
					'slug'       => $rewrite_slug,
					'with_front' => false,      // Defaults to true.
					// 'feeds'   => false,      // Defaults to 'has_archive'.
					// 'pages'   => false,      // Defaults to true.
					// 'ep_mask' => 'EP_NONE',  // Defaults to EP_PERMALINK.

				],

				'has_archive'  => true,
				'can_export'   => true,
			)
		);
	}

	/**
	 * Register the custom post type for productions.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_post_tax_relations(): void {
		\register_taxonomy_for_object_type( self::TAXONOMY_NAME, 'gatherpress_event' );
	}

	/**
	 * Change the label for the event datetime column to "Premiere".
	 *
	 * @since 0.1.0
	 *
	 * @uses 'gatherpress_event_datetime_label' filter to modify the label for the event datetime column in the admin list table.
	 * @see  https://github.com/GatherPress/gatherpress/tree/develop/docs/developer/hooks/gatherpress_event_datetime_label.md
	 *
	 * @param string $label The original label for the event datetime column.
	 * @param string $post_type The post type for which to modify the label.
	 *
	 * @return string The modified label for the event datetime column.
	 */
	public function change_event_datetime_label( string $label, string $post_type ): string {
		if ( self::POST_TYPE_NAME === $post_type ) {
			return __( 'Premiere', 'gatherpress-productions' );
		}
		return $label;
	}

	/**
	 * Enqueues the editor script that registers label filter for the sidebar.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_editor_assets(): void {
		$asset_file = GATHERPRESS_PRODUCTIONS_CORE_PATH . '/build/index.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		/** @var mixed $asset */
		$asset = include $asset_file;

		if ( ! is_array( $asset ) || ! isset( $asset['dependencies'], $asset['version'] ) ) {
			return;
		}

		/** @var array{dependencies: string[], version: string} $asset */
		wp_enqueue_script(
			'gatherpress-productions-editor',
			plugins_url( 'build/index.js', dirname( __DIR__, 1 ) ),
			$asset['dependencies'],
			(string) $asset['version'],
			true
		);

		wp_set_script_translations(
			'gatherpress-productions-editor',
			'gatherpress-productions'
		);
	}


	/**
	 * Adds a sub-page for "Productions" to the existing sub-pages array.
	 *
	 * This function modifies the provided sub-pages array to include a new sub-page
	 * for GatherPress Productions with specified details such as name, priority, and sections.
	 *
	 * @param array $sub_pages An associative array of existing sub-pages.
	 * @return array Modified array of sub-pages including the new GatherPress Productions sub-page.
	 */
	public function setup_sub_page( array $sub_pages ): array {
		$sub_pages['productions'] = array(
			'name'     => __( 'Productions', 'gatherpress-productions' ),
			'priority' => 10,
			'sections' => array(
				'urls' => array(
					'name'        => __( 'Permalinks', 'gatherpress' ),
					'description' => __( 'Change permalink bases.', 'gatherpress' ),
					'options'     => array(
						'productions_url' => array(
							'labels' => array(
								'name' => __( 'Productions', 'gatherpress-productions' ),
							),
							'field'  => array(
								'type'    => 'text',
								'rewrite' => true,
								'options' => array(
									'label'   => __( 'Permalink base of Productions.', 'gatherpress-productions' ),
									// 'default' => Setup::get_instance()->get_localized_post_type_slug(),
									'default' => self::POST_TYPE_SLUG,
								),
								'preview' => array(
									'template' => 'url-rewrite-preview',
									'suffix'   => _x(
										'sample-production',
										'URL permalink structure example for productions',
										'gatherpress-productions'
									),
								),
							),
						),
					),
				),
			),
		);

		return $sub_pages;
	}

	/**
	 * Set up starter patterns FOR ALL post types using the 'gatherpress-event-date' post_type support.
	 *
	 * @since 0.1.0
	 *
	 * @uses 'gatherpress_event_starter_patterns' filter
	 * @see  https://github.com/GatherPress/gatherpress/blob/develop/docs/developer/hooks/gatherpress_event_starter_patterns.md
	 *
	 * @param  array $patterns
	 * @param  array $post_types
	 *
	 * @return array
	 */
	public function setup_starter_patterns( array $patterns, array $post_types ): array {
		$patterns[] = array(
			'name'        => 'gatherpress-productions/starter',
			'title'       => __( 'Productions Starter', 'gatherpress-productions' ),
			'description' => __( 'A starter pattern for productions.', 'gatherpress-productions' ),
			'content'     => '<!-- wp:paragraph --><p>' . esc_html__( 'This is a starter pattern for productions. Customize it to fit your needs!', 'gatherpress-productions' ) . '</p><!-- /wp:paragraph -->',
		);

		return $patterns;
    }

	/**
	 * Register the starter pattern natively using WordPress's block pattern API.
	 * This is an alternative to using the 'gatherpress_event_starter_patterns' filter and allows the pattern to be available only to selected post types.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_starter_patterns_natively() : void {

		$pattern = array(
			'name'        => 'gatherpress-productions/starter',
			'title'       => __( 'Productions Starter', 'gatherpress-productions' ),
			'description' => __( 'A starter pattern for productions.', 'gatherpress-productions' ),
			'post_types'  => array( self::POST_TYPE_NAME ),
			'content'     => '<!-- wp:paragraph --><p>' . esc_html__( 'This is a starter pattern for productions. Customize it to fit your needs!', 'gatherpress-productions' ) . '</p><!-- /wp:paragraph -->',
		);
		\register_block_pattern(
			$pattern['name'],
			array(
				'title'       => $pattern['title'] ?? '',
				'description' => $pattern['description'] ?? '',
				'content'     => $pattern['content'] ?? '',
				'blockTypes'  => array( 'core/post-content' ),
				'postTypes'   => array( self::POST_TYPE_NAME ),
				'source'      => 'plugin',
			)
		);
	}
}
