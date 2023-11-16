<?php

namespace DNADesign\Rhino\Extensions;

use DNADesign\Rhino\Pagetypes\RhinoAssessment;
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
        $fields->insertAfter('ParentID', $uid);
    }

    /**
     * Figures out which is the parent page
     * that generated this submission
     *
     * @return DataObject
     */
    public function getParentPage()
    {
        $parent_class = $this->owner->ParentClass;
        $id = $this->owner->ParentID;
        if ($id && $parent_class && class_exists($parent_class)) {
            return $parent_class::get()->byID($id);
        }

        return null;
    }

    /**
     * Change class of SubmittedForm to be RhinoSubmittedAssessment if
     * Parent is a RhinoAssessment and not RhinoAssignment
     */
    public function updateAfterProcess($data = null, $form = null)
    {
        $parent = $this->owner->getParentPage();

        // Set a unique id if possible
        if ($this->owner->hasField('uid') && !$this->owner->uid) {
            $uid = sprintf('%s%s', $this->owner->ID, uniqid());
            $this->owner->uid = $uid;
            $this->owner->write();
        }

        // Transform SubmittedForm into RhinoSubmittedAssessment
        // for extra functionality
        if ($parent instanceof \DNADesign\Rhino\Pagetypes\RhinoAssessment) {
            // Make SubmittedForm actual RhinoSubmittedAssessment
            $this->owner = $this->owner->newClassInstance($parent->config()->get('submission_class'));
            $this->owner->write();

            // Marking happens on RhinoSubmittedAssessment
            if ($this->owner->hasMethod('onAfterUpdateAfterProcess')) {
                $this->owner->onAfterUpdateAfterProcess($data, $form);
            }
        }
    }
}
