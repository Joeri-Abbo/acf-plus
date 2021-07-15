<?php

namespace AcfPlus\FlexibleContent;

/**
 *
 * ACF Flexible Content Modules Class for preview
 *
 * Class to load flexible layouts With preview
 *
 * @author  Joeri Abbo
 * @since   1.0.0
 */
class FlexibleContentPreviews
{

	/*--------------------------------------------*
   * Attributes
   *--------------------------------------------*/

	/** Refers to a single instance of this class. */
	private static $init = null;

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return  FlexibleContentPreviews A single instance of this class.
	 * @since 1.0.0
	 *
	 * @author Joeri Abbo <joeri@rodesk.com>
	 */
	public static function init()
	{

		if (null == self::$init) {
			self::$init = new self;
		}

		return self::$init;

	}

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 *
	 * @author Joeri Abbo <joeri@rodesk.com>
	 * @since 1.0.0
	 */
	private function __construct()
	{
		add_action('acf/render_field_settings', [&$this, 'addPreviewImage'], 1,
			999); // Init admin JS

		add_filter('acf/fields/flexible_content/layout_title', [__CLASS__, 'addTitleField'], 1,
			999);
	}

	public function addPreviewImage($field)
	{
		acf_render_field_setting($field, array(
			'label'        => __('Add preview image'),
			'instructions' => 'Add preview image to this group',
			'name'         => 'preview_image',
			'type'         => 'image',
			'ui'           => 1,
		), true);
	}
	static public function addTitleField($title, $field, $layout, $i) {
		if (empty($layout['sub_fields'])){
			return $title;
		}

		$preview_image = false;

		foreach ($layout['sub_fields'] as $field) {
			if ($field['preview_image'] && $preview_image === false) {
				$preview_image = $field['preview_image'];
			}
		}

		if (!empty($preview_image)) {

			$image = wp_get_attachment_image (  $preview_image, array('25', '25'));
			$title = $image . $title;
		}
		return $title;
	}

}
