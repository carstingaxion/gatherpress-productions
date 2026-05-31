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

	const TAXONOMY_NAME = '_gatherpress_play';

	/**
	 * Constructor for the Setup class.
	 *
	 * Initializes and sets up various components of the plugin.
	 *
	 * @return void
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
		// if ( $query->is_main_query() && $query->is_post_type_archive( self::POST_TYPE_NAME ) ) {
		// error_log( var_export( $query->query_vars, true ) );
		// error_log( var_export( $query, true ) );
		// }
		// }, 100 );

		// Add settings sub-page.
		add_action( 'gatherpress_sub_pages', array( $this, 'setup_sub_page' ) );

		// Setup starter patterns.
		// add_filter( 'gatherpress_event_starter_patterns', array( $this, 'setup_starter_patterns' ), 10, 2 );
		add_action( 'init', array( $this, 'register_starter_patterns_natively' ) );

		// Add block variations for the venue block when used within the context of productions.
		add_filter( 'get_block_type_variations', array( $this, 'add_block_type_variations' ), 10, 2 );
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
	 * Returns the post type slug localized for the site language and sanitized as URL part.
	 *
	 * Do not use this directly, use get( 'venues_url' ) instead.
	 *
	 * This method switches to the sites default language and gets the translation of 'venues' for the loaded locale.
	 * After that, the method sanitizes the string to be safely used within an URL,
	 * by removing accents, replacing special characters and replacing whitespace with dashes.
	 *
	 * @since 0.34.0
	 *
	 * @return string
	 */
	protected function get_localized_post_type_slug(): string {
		$switched_locale = switch_to_locale( get_locale() );
		$slug            = __( 'Production', 'gatherpress-productions' );
		$slug            = sanitize_title( $slug );

		if ( $switched_locale ) {
			restore_previous_locale();
		}

		return $slug;
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
	 * Adds a sub-page for "Theater" to the existing sub-pages array.
	 *
	 * This function modifies the provided sub-pages array to include a new sub-page
	 * for GatherPress Theater with specified details such as name, priority, and sections.
	 *
	 * @param array $sub_pages An associative array of existing sub-pages.
	 * @return array Modified array of sub-pages including the new GatherPress Theater sub-page.
	 */
	public function setup_sub_page( array $sub_pages ): array {
		$current_sub_pages = $sub_pages['theater']['sections'] ?? array();
		$sub_pages['theater'] = array(
			'name'     => __( 'Theater', 'gatherpress-seasons' ),
			'priority' => 10,
			'sections' => array_merge( $current_sub_pages, array(
				'production_urls' => array(
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
									'default' => $this->get_localized_post_type_slug(),
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
			) ),
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
	public function register_starter_patterns_natively(): void {

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

	/**
	 * Add block variations for the venue block when used within the context of productions.
	 *
	 * @param  array          $variations
	 * @param  \WP_Block_Type $block_type
	 *
	 * @return array All registered block variations, including the new one for the venue block in productions context.
	 */
	public function add_block_type_variations( array $variations, \WP_Block_Type $block_type ): array {

		if ( 'gatherpress/venue' === $block_type->name ) {
			$variations[] = array(
				'name'       => 'gatherpress/productions-details',
				'title'      => __( 'Productions Details', 'gatherpress-productions' ),
				'description' => __( 'Show details of the related production.', 'gatherpress-productions' ),
				'isActive'   => array(
					'sourcePostType',
				),
				'attributes' => array(
					'sourcePostType' => 'gatherpress_play',
				)
			);
		}

		return $variations;
	}
}
