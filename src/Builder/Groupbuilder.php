<?php
namespace AcfPlus\Builder;

/**
 *
 * ACF Flexible Content Modules Class for preview
 *
 * Class to load flexible layouts With preview
 *
 * @author  Joeri Abbo
 * @since   1.0.0
 */
class Groupbuilder
{

	protected $_label;
	protected $_field;

	public function withLabel($value) {
		$this->_label = $value;
		return $value;
	}

	public function withField($field_name, $field) {
		$this->_field = [$field_name, $field];
		return $this->_field;
	}

	public function withField2($field_name, $field) {
		$this->_field = [$field_name, $field];
		return $this->_field;
	}

	public function register() {
		var_dump([
			$this->_label,
			$this->_field
		]);
exit;
	}

}
