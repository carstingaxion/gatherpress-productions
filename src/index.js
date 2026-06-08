/**
 * WordPress dependencies.
 */
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
