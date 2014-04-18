<?php
/**
 * type can be:
 * "box_begin" - begin of separate box
 * "box_end" - end of separate box
 * "text" - text input
 * "textarea" - textarea
 */
return array(
	array(
		'name' => 'Box Begin',
		'type' => 'box_begin'
	),
	array(
		'name' => 'Example theme option',
		'desc' => 'Description',
		'id' => 'example_theme_option',
		'std' => 'standard value',
		'type' => 'text',
		'size' => 30
	),
	array(
		'type' => 'box_end'
	),
);
