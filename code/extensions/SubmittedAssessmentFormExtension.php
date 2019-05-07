<?php

namespace DNADesign\Rhino\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataExtension;

class SubmittedAssessmentFormExtension extends DataExtension
{
    private static $db = [
        'uid' => 'Varchar(255)'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $uid = ReadOnlyField::create('uid', 'uid');
        $fields->insertAfter($uid, 'ParentID');
    }

    /**
     * Change class of SubmittedForm to be RhinoSubmittedAssessment if
     * Parent is a RhinoAssessment and not RhinoAssignment
     */
    public function updateAfterProcess($data = null, $form = null)
    {
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
            $this->owner = $this->owner->newClassInstance($parent->stat('submission_class'));
            $this->owner->write();

            // Marking happens on RhinoSubmittedAssessment
            if ($this->owner->hasMethod('onAfterUpdateAfterProcess')) {
                $this->owner->onAfterUpdateAfterProcess($data, $form);
            }
        }
    }
}
