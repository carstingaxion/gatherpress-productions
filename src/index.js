/**
 * WordPress dependencies.
 */
import { registerBlockVariation } from '@wordpress/blocks';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

// Relabel the editor sidebar panel title for the same post type.
addFilter(
	'gatherpress.eventSettingsPanelTitle',
	'gatherpress-productions/relabel',
	( title, pt ) =>
		'gatherpress_play' === pt
			? __( 'Premiere', 'gatherpress-productions' )
			: title
);



/**
 * Extend 'gatherpress/venue' to provide production context.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-variations/
 */
registerBlockVariation( 'gatherpress/venue', {
	name: 'gatherpress-productions/details',
	title: __( 'Production Details', 'gatherpress' ),
	description: __( 'Provides production context.', 'gatherpress' ),
	category: 'gatherpress',
	isActive: [ 'sourcePostType' ],
	attributes: {
		sourcePostType: 'gatherpress_play',
	},
	innerBlocks: [
		[
			'core/post-title',
			{
				level: 3,
				isLink: true
			}
		],
		[
			'core/post-featured-image',
			{
				isLink: true
			}
		],
	],
	scope: [ 'inserter', 'block' ], // Defaults to 'block' and 'inserter'.
	example: {} // Disabled like the original 'core/post-terms' block.
} );
