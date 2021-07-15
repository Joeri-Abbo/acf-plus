<?php

namespace AcfPlus\Models;

/**
 *
 * ACF Flexible Content Modules Class for preview
 *
 * Class to load flexible layouts With preview
 *
 * @author  Joeri Abbo
 * @since   1.0.0
 */
class BaseModel
{
	public const CPT = 'base_model';

//	public function setup(){
//
////
////use \AcfPlus\Classes\Groupbuilder as Group;
////use \AcfPlus\Fields\Text as Text;
////$group = new Group();
////$group->withLabel('USP Slider');
////$group->withField(
////	'name',
////	Text::new()
////	    ->withLabel('field')
////		->withDescription('Use {curly brackets} to underline the text')
////		->withFieldWidth(50)
////);
////var_dump($group->register());
//
////https://www.advancedcustomfields.com/resources/register-fields-via-php/
//
//	}
	/**
	 * @var WP_Post|\WP_Post
	 */
	private $post;
	private $_link;

	/**
	 * Product constructor.
	 *
	 * @param \WP_Post $post Post
	 */
	public function __construct($post) {
		$this->post = $post;
	}

	public function getId() {
		return $this->post->ID;
	}

	public function getPostTitle() {
		return $this->post->post_title;
	}
	public function getPostAuthor() {
		return $this->post->post_author;
	}

	public function getType() {
		return self::CPT;
	}

	public function getPermalink() {
		if (!$this->_link ) {
			$this->_link = get_the_permalink($this->post);
		}
		return $this->_link;
	}

	/**
	 * Get field of current post
	 *
	 * @param $selector
	 *
	 * @return mixed
	 */
	public function getField($selector) {
		return get_field($selector, $this->getId());
	}

	/**
	 * Get fields of current post
	 *
	 * @return mixed
	 */
	public function getFields() {
		return get_field( $this->getId() );
	}
}
