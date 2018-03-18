<?php

class RhinoSubmittedFormField extends DataExtension {

	private static $db = array(
		'Mark' => "Enum('none, pass, fail')"
	);

	private static $has_one = array(
		'ParentField' => 'EditableFormField',
		'ParentOption' => 'EditableOption'
	);

	public function updateSummaryFields(&$fields) {
		$fields['Mark'] = 'Mark';
	}

	/**
	* Record Additional Information
	*/
	public function onPopulationFromField($field) {

		// Perform Marking
		if ($field->hasMethod('pass_or_fail')) {
			$this->owner->Mark = $field->pass_or_fail($this->owner->Value);
		}

		// Record Parent Field
		$this->owner->ParentFieldID = $field->ID;

		// Record Parent Option if applicable
		if ($field && $field instanceof EditableRadioField ) {
			$option = $field->Options()->filter('Value', $this->owner->Value)->First();

			if ($option && $option->exists()) {
				$this->owner->ParentOptionID = $option->ID;
			}
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