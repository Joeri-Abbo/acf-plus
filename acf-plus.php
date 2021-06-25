<?php

/**
 * Plugin Name: ACF Plus
 * Description: Add models to Flex layouts
 * Text Domain: acf-plus
 * Domain Path: /languages
 *
 * Author: Joeri Abbo
 * Author URI: https://nl.linkedin.com/in/joeri-abbo-43a457144
 *
 * Version: 1.0.0
 */

const ACF_PLUS_TEXT_DOMAIN = 'acf-plus';
const ACF_PLUS_PREFIX = 'acf-plus';

require_once 'vendor/autoload.php';

// File Security Check
defined('ABSPATH') or die("No script kiddies please!");

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */

add_action( 'init', function() {
	load_muplugin_textdomain( ACF_PLUS_TEXT_DOMAIN, basename( dirname(__FILE__) ) . '/languages' );
} );

/**
 * Init the additional flexible content modules functionality
 *
 * @since 1.0.0
 */

add_action('init', ['AcfPlus\Classes\FlexibleContentModals', 'init']); // Initialize
