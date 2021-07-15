<?php

namespace AcfPlus\Builder\Fields;

/**
 *
 * ACF Flexible Content Modules Class for preview
 *
 * Class to load flexible layouts With preview
 *
 * @author  Joeri Abbo
 * @since   1.0.0
 */
class Field
{
	private static $init;
	protected $_label;
	protected $_field_width;
	protected $_description;

	public function new(): self
	{
		if (null == self::$init) {
			self::$init = new self;
		}

		return self::$init;
	}

	/**
	 * Set with for field
	 *
	 * @param $value
	 *
	 * @return Text
	 */
	public function withLabel($value): self
	{
		$this->_label = $value;

		return self::$init;
	}

	/**
	 * Set with for field
	 *
	 * @param $value
	 *
	 * @return object
	 */
	public function withField($value): self
	{
		$this->_label = $value;

		return self::$init;
	}

	/**
	 * Set with for field
	 *
	 * @param $value
	 *
	 * @return Text
	 */
	public function withDescription($value): self
	{
		$this->_description = $value;

		return self::$init;
	}

	/**
	 * Set with for field
	 *
	 * @param $value
	 *
	 * @return Text
	 */
	public function withFieldWidth($value): self
	{
		$this->_field_width = $value;

		return self::$init;
	}

}
