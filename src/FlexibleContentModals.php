<?php
namespace AcfPlus\Classes;

/**
 *
 * ACF Flexible Content Modules Class
 *
 * Class to load flexible layouts as modal boxes
 *
 * @author  Joeri Abbo
 * @since   1.0.0
 */
class FlexibleContentModals
{

	// ACF version
	static private $version = false;
	// ACF asset path
	static private $asset_path = '';

	/**
	 * Init class
	 */
	static public function init()
	{
		self::setup();

		if (!self::$version) {
			// No ACF version found. We dont need to add any scripts or function
			return;
		}
		add_action('admin_init',[__CLASS__, 'admin_css'], 1, 999); // Init admin CSS
		add_action('admin_init',[__CLASS__, 'admin_script'], 1, 999); // Init admin JS
		add_action('admin_init',[__CLASS__, 'localize'], 1, 999); // Init admin JS
		add_action('admin_head',[__CLASS__, 'add_post_status'], 1, 999); // Add post type status to localized text
		add_action('admin_head',[__CLASS__, 'add_post_type_label'], 1, 999); // Add post type label to localized text
		add_filter('acf/fields/flexible_content/layout_title',[__CLASS__, 'add_title_field'], 1, 999); // Add title field to flexible_content if the group has a filled title field.

	}

	/**
	 * Setup the settings
	 */
	private static function setup()
	{
		// Set the ACF version
		self::set_acf_version();
		self::asset_path();
	}

	/**
	 * Define ACF version
	 *
	 * @return void
	 */
	static public function set_acf_version()
	{
		global $acf;
		self::$version = $acf && !empty($acf->version) ? $acf->version : false;
	}

	/**
	 * Add localize values.
	 */
	static public function localize()
	{
		if (function_exists('acf_localize_text')) {
			// Add some strings to ACF for translation
			acf_localize_text([
				'Saving' 				=> __('Saving', ACF_PLUS_TEXT_DOMAIN),
				'Close & Save Draft' 	=> __('Close & Save Draft', ACF_PLUS_TEXT_DOMAIN),
				'Close & Update' 		=> __('Close & Update', ACF_PLUS_TEXT_DOMAIN),
				'Close' 				=> __('Close', ACF_PLUS_TEXT_DOMAIN),
				'Edit layout' 			=> __('Edit layout', ACF_PLUS_TEXT_DOMAIN)
			]);
		}
	}

	/**
	 * Add the post type label as a localized variable for use in JS
	 *
	 * @return void
	 */
	static public function add_post_type_label()
	{
		global $post;

		if (function_exists('acf_localize_data')) {

			if (!empty($post->post_type)) {
				$post_type_object = get_post_type_object($post->post_type);
			}

			if (!empty($post_type_object->labels->singular_name)) {
				acf_localize_data(['post_label' => strtolower($post_type_object->labels->singular_name)]);
			}
		}

	}

	/**
	 * Add the post status as a localized variable for use in JS
	 *
	 * @return void
	 */
	static public function add_post_status()
	{
		global $post;

		if (function_exists('acf_localize_data')) {
			if (!empty($post->ID) && get_post_status($post->ID)) {
				acf_localize_data(['post_status' => get_post_status($post->ID)]);
			}
		}
	}

	/**
	 * Set asset path
	 * In ACF 5.7 the JS API changed so use old path*
	 * @return void
	 */
	static function asset_path()
	{
		$base_path = 'assets';
		$base_path = version_compare(self::$version, '5.7.0', '<') ? $base_path . '/5.6' : $base_path;

		self::$asset_path = $base_path;
	}

	/**
	 * Add title field to flexible_content if the group has a filled title field.
	 *
	 * @param $title string default title
	 * @param $field array field_group
	 * @param $layout array all the flex modules
	 * @param $i int current field group
	 * @return string the new title
	 */
	static public function add_title_field($title, $field, $layout, $i)
	{

		if (version_compare(self::$version, '5.7.0', '<')) {
			return $title;
		}

		if (!empty($layout['sub_fields']) && is_countable($layout['sub_fields'])) {
			foreach ($layout['sub_fields'] as $f) {
				if (!empty($f['name']) && $f['name'] == 'title') {

					if (!empty($_POST['value'][$f['key']])) {
						$title_value = $_POST['value'][$f['key']];
					}

					if (!empty($field['value'][$i][$f['key']])) {
						$title_value = $field['value'][$i][$f['key']];
					}

					if (!empty($title_value)) {
						$max_length = 50;

						if (strlen($title_value) > $max_length) {
							$title_value = sprintf('%s...', substr($title_value, 0, $max_length));
						}
					}
				}
			}

			if (!empty($title_value)) {
				$append = sprintf(" : <span class='layout-title'>%s</span>", $title_value);
				return sprintf('<span>%s %s</span>', $title, $append);
			}
		}

		return $title;
	}

	/**
	 * Register admin styles
	 */
	static public function admin_css()
	{
		wp_enqueue_style(ACF_PLUS_PREFIX . '_fc_modal', plugins_url(self::$asset_path . '/css/acf-plus.css', __FILE__),['acf-pro-input']);
	}

	/**
	 * Register admin scripts
	 */
	static public function admin_script()
	{
		wp_enqueue_script(ACF_PLUS_PREFIX . '_fc_modal', plugins_url(self::$asset_path . '/js/acf-plus.js', __FILE__),['acf-pro-input']);
	}

}
