<?php
/**
 * Plugin Name: GatherPress Productions demo data helper
 * Description: Generates demo data for the plugin.
 * Version:     0.1.0
 * Author:      GatherPress Productions
 */


require_once '/wordpress/wp-load.php';

// Production 1
$post_id = wp_insert_post(
	array(
		'post_type'    => 'gatherpress_play',
		'post_title'   => 'The Glass Horizon',
		'post_content' => 'A futuristic drama exploring identity and memory.',
		'post_status'  => 'publish',
	)
);
if ( is_wp_error( $post_id ) ) {
	error_log( 'Error creating production: ' . $post_id->get_error_message() );
} else {
	$event = new \GatherPress\Core\Event( $post_id );
	$event->save_datetimes(
		array(
			'datetime_start' => '2025-11-10 19:00:00',
			'datetime_end'   => '2025-11-10 21:30:00',
			'timezone'       => 'Europe/Berlin',
		) 
	);
}

// Production 2
$post_id = wp_insert_post(
	array(
		'post_type'    => 'gatherpress_play',
		'post_title'   => 'Echoes of Verona',
		'post_content' => 'A modern reinterpretation of a classic love story.',
		'post_status'  => 'publish',
	)
);
if ( is_wp_error( $post_id ) ) {
	error_log( 'Error creating production: ' . $post_id->get_error_message() );
} else {
	$event = new \GatherPress\Core\Event( $post_id );
	$event->save_datetimes(
		array(
			'datetime_start' => '2025-12-05 18:30:00',
			'datetime_end'   => '2025-12-05 21:00:00',
			'timezone'       => 'Europe/Berlin',
		) 
	);
}

// Production 3
$post_id = wp_insert_post(
	array(
		'post_type'    => 'gatherpress_play',
		'post_title'   => 'Midnight in Dresden',
		'post_content' => 'A suspenseful tale set in post-war Germany.',
		'post_status'  => 'publish',
	)
);
if ( is_wp_error( $post_id ) ) {
	error_log( 'Error creating production: ' . $post_id->get_error_message() );
} else {
	$event = new \GatherPress\Core\Event( $post_id );
	$event->save_datetimes(
		array(
			'datetime_start' => '2027-01-15 20:00:00',
			'datetime_end'   => '2027-01-15 22:15:00',
			'timezone'       => 'Europe/Berlin',
		) 
	);
}

// Production 4
$post_id = wp_insert_post(
	array(
		'post_type'    => 'gatherpress_play',
		'post_title'   => 'The Last Monologue',
		'post_content' => 'An intimate solo performance about loss and resilience.',
		'post_status'  => 'publish',
	)
);
if ( is_wp_error( $post_id ) ) {
	error_log( 'Error creating production: ' . $post_id->get_error_message() );
} else {
	$event = new \GatherPress\Core\Event( $post_id );
	$event->save_datetimes(
		array(
			'datetime_start' => '2026-02-20 19:30:00',
			'datetime_end'   => '2026-02-20 21:00:00',
			'timezone'       => 'Europe/Berlin',
		) 
	);
}

// Production 5
$post_id = wp_insert_post(
	array(
		'post_type'    => 'gatherpress_play',
		'post_title'   => 'Carnival of Shadows',
		'post_content' => 'A dark comedy set during a mysterious traveling carnival.',
		'post_status'  => 'publish',
	)
);
if ( is_wp_error( $post_id ) ) {
	error_log( 'Error creating production: ' . $post_id->get_error_message() );
} else {
	$event = new \GatherPress\Core\Event( $post_id );
	$event->save_datetimes(
		array(
			'datetime_start' => '2027-03-12 18:00:00',
			'datetime_end'   => '2027-03-12 20:30:00',
			'timezone'       => 'Europe/Berlin',
		) 
	);
}

flush_rewrite_rules();

error_log( 'Demo data import complete.' );
