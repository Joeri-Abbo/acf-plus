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
const ACF_PLUS_PREFIX      = 'acf-plus';
define("ACF_PLUS_BASE_DIR", plugin_dir_url(__FILE__));

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
class AcfPlusSetup
{

	public static function init()
	{

		if ( ! class_exists('AcfPlus\FlexibleContent\FlexibleContentModals')) {
			require_once 'vendor/autoload.php';
		}

		add_action('wp_head', function (){
			var_dump('test');
			require_once 'src/Models/BaseModel.php';
			$post = new \AcfPlus\Models\BaseModel(get_queried_object());
			dd($post);
			exit;
		});

		if ( ! class_exists('AcfPlus\FlexibleContent\FlexibleContentModals')) {
			return;
		}
		// File Security Check
		defined('ABSPATH') or die("No script kiddies please!");

		/**
		 * Init the additional flexible content modules model functionality
		 *
		 * @since 1.0.0
		 */
		add_action('init', ['AcfPlus\FlexibleContent\FlexibleContentModals', 'init']);

		/**
		 * Init the additional flexible content modules preview functionality
		 *
		 * @since 1.0.1
		 */
		add_action('init', ['AcfPlus\FlexibleContent\FlexibleContentPreviews', 'init']);

		add_action('init', [__CLASS__, 'addTextDomain']);

	}

	/**
	 * Add text domain
	 *
	 * @since 1.0.1
	 */
	public static function addTextDomain()
	{
		load_plugin_textdomain(ACF_PLUS_TEXT_DOMAIN, false, basename(dirname(__FILE__)) . '/languages');
	}

}

AcfPlusSetup::init();
