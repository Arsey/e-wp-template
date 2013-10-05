<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
	<!--<![endif]-->
    <head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta name="viewport" content="width=device-width">

		<title><?php wp_title('|', true, 'right'); ?></title>

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<link rel="stylesheet" href="<?php td(); ?>/css/reset.css">
		<link rel="stylesheet" href="<?php td(); ?>/style.css">

<!--[if lt IE 9]><script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script><![endif]-->

		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div class="wrapper">