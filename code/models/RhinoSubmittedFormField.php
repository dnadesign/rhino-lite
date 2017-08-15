<?php

class RhinoSubmittedFormField extends DataExtension {

	private static $db = array(
		'Mark' => "Enum('none, pass, fail')"
	);

	public function updateSummaryFields(&$fields) {
		$fields['Mark'] = 'Mark';
	}

	/**
	* Perform marking on each field
	*/
	public function onPopulationFromField($field) {

		if ($field->hasMethod('pass_or_fail')) {
			$this->owner->Mark = $field->pass_or_fail($this->owner->Value);
		}
	}

}