<?php

class SubmittedAssessmentFormExtension extends DataExtension {

	private static $db = array(
		'uid' => 'Varchar(255)'
	);

	public function updateCMSFields(FieldList $fields) {
		$uid = ReadOnlyField::create('uid', 'uid');
		$fields->insertAfter($uid, 'ParentID');
	}

	/**
	* Change class of SubmittedForm to be RhinoSubmittedAssessment if 
	* Parent is a RhinoAssessment and not RhinoAssignment
	*/
	public function updateAfterProcess() {
		$parent = $this->owner->Parent();	

		// Set a unique id if possible
		if ($this->owner->hasField('uid') && !$this->owner->uid) {
			$uid = sprintf('%s%s', $this->owner->ID, uniqid());
			$this->owner->uid = $uid;
			$this->owner->write();
		}	

		// Transform SubmittedForm into RhinoSubmittedAssessment
		// for extra functionality
		if ( 
			(is_a($parent, 'RhinoAssessment') || is_subclass_of($parent, 'RhinoAssessment'))
			&& 
			(!is_a($parent, 'RhinoAssignment') && !is_subclass_of($parent, 'RhinoAssignment'))
		) {
			// Make SubmittedForm actual RhinoSubmittedAssessment
			$this->owner = $this->owner->newClassInstance('RhinoSubmittedAssessment');
			$this->owner->write();

			if ($this->owner->hasMethod('onAfterUpdateAfterProcess')) {
				$this->owner->onAfterUpdateAfterProcess();
			}
		}		
	}
}