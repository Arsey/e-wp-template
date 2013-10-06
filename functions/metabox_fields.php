<?php

$prefix = 'pmf_'; //This prefix will be added before all of our custom fields. Using prefix can prevent us from conflicting with other scritps that also use custom fields.

return array(
	'id' => 'my-meta-box', // just the ID of meta box, each meta box has an unique ID
	'title' => 'Custom meta box', //the meta box title
	'page' => 'post', //post, page, link or custom post type name
	'context' => 'normal', //normal, advanced or (since 2.7) side
	'priority' => 'high', //high, low
	'fields' => array(
		array(
			'name' => 'Text box',
			'desc' => 'Enter something here',
			'id' => $prefix . 'text',
			'type' => 'text',
			'std' => 'Default value 1'
		),
		array(
			'name' => 'Textarea',
			'desc' => 'Enter big text here',
			'id' => $prefix . 'textarea',
			'type' => 'textarea',
			'std' => 'Default value 2'
		),
		array(
			'name' => 'Select box',
			'id' => $prefix . 'select',
			'type' => 'select',
			'options' => array('Option 1', 'Option 2', 'Option 3')
		),
		array(
			'name' => 'Radio',
			'id' => $prefix . 'radio',
			'type' => 'radio',
			'options' => array(
				array('name' => 'Name 1', 'value' => 'Value 1'),
				array('name' => 'Name 2', 'value' => 'Value 2')
			)
		),
		array(
			'name' => 'Checkbox',
			'id' => $prefix . 'checkbox',
			'type' => 'checkbox'
		)
	)
);
