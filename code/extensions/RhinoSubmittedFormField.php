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

	/**
	* Return the actual form field that generated this answer
	*/
	public function getParentEditableFormField() {
		$submission = $this->owner->Parent();
		$form = $submission->Parent();
		$field = $form->Fields()->filter('Name', $this->owner->Name)->First();	

		return $field;
	}

}