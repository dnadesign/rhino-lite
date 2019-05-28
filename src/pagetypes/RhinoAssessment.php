<?php

namespace DNADesign\Rhino\Pagetypes;

use DNADesign\Rhino\Control\RhinoAssessmentController;
use DNADesign\Rhino\Model\RhinoSubmittedAssessment;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\UserForms\Model\EditableFormField\EditableFormStep;
use SilverStripe\UserForms\Model\UserDefinedForm;

class RhinoAssessment extends UserDefinedForm
{
    private static $db = [
        'FeedbackOnPass' => 'HTMLText',
        'FeedbackOnFail' => 'HTMLText'
    ];

    private static $defaults = [
        'Content' => ''
    ];

    private static $singular_name = 'Assessment';

    private static $plural_name = 'Assessments';

    private static $table_name = 'RhinoAssessment';

    private static $submission_class = RhinoSubmittedAssessment::class;

    private static $controller_name = RhinoAssessmentController::class;

    /**
    * UserDefinedForm overrides this method from SitreTree
    * rather than using the config, so we need to override it again.
    * To Do: Get UDF to use the config (need to fix the unit test)
    */
    public function getControllerName()
    {
        if ($this->config()->controller_name) {
            return $this->config()->controller_name;
        }

        return parent::getControllerName();
    }

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {

            $fields->removeByName('DisableSaveSubmissions');

            // Feedback
            $pass = HTMLEditorField::create('FeedbackOnPass');
            $fail = HTMLEditorField::create('FeedbackOnFail');

            $fields->addFieldsToTab('Root.FeedbackOnSubmission', [$pass, $fail]);

            // Allow subclass to create defaults fields
            if (method_exists(get_class($this), 'createDefaultFields')) {
                // Do nothing if the form already has fields set up
                if ($this->getQuestions()->Count() == 0) {
                    $this->createDefaultFields($this->Fields());
                }
            }

            // Submissions must be RhinoSubmittedAssessments
            $gridField = $fields->fieldByName('Root.Submissions.Submissions');
            $submission_class = $this->config()->submission_class;
            $assessments = $submission_class::get();

            if ($assessments->count() > 0 && $this->Submissions()->Count() > 0) {
                $list = $assessments->filter('ID', $this->Submissions()->column('ID'));
                $gridField->setList($list);
            }

            // Summary Fields are hijacked by UserDefinedForm
            // So need to explicitely use the RhinoSubmittedAssessment ones
            $config = $gridField->getConfig();
            $dataColumns = $config->getComponentByType(GridFieldDataColumns::class);

            $columns = singleton($submission_class)->summaryFields();

            // Still add the EditableFormField if required
            foreach (EditableFormField::get()->filter(["ParentID" => $this->ID]) as $eff) {
                if ($eff->ShowInSummary) {
                    $columns[$eff->Name] = $eff->Title ?: $eff->Name;
                }
            }

            $dataColumns->setDisplayFields($columns);

        });

        $this->updateEditableFields();

        return parent::getCMSFields();
    }

    public function updateEditableFields()
    {
        // Allow only certain fields to be created
        $allowedFields = $this->config()->allowed_field_types;
        if ($allowedFields) {
            $fieldClasses = singleton(EditableFormField::class)->getEditableFieldClasses();
            foreach ($fieldClasses as $fieldClass => $fieldTitle) {
                if (!in_array($fieldClass, $allowedFields)) {
                    Config::inst()->update($fieldClass, 'hidden', true);
                }
            }
            // Explicitely allow fields, so subclasses show up
            foreach ($allowedFields as $fieldClass) {
                Config::inst()->update($fieldClass, 'hidden', false);
            }
        }
    }

    /**
     * Return all fields (questions)
     * besides the EditableFormStep (required)
     * @return Int
     */
    public function getQuestions()
    {
        $fields = $this->Fields();
        // remove EditableFormStep
        $fields = $fields->exclude('ClassName', EditableFormStep::class);

        // return fields
        return $fields;
    }

    public function getMarkableQuestions()
    {
        $fields = $this->getQuestions()->filterByCallback(function ($field) {
            return $field->hasMethod('pass_or_fail');
        });

        return $fields;
    }

    /**
     * Shortcode to display the feedback on the Submission screen
     */
    public static function assessment_feedback()
    {
        $request = Controller::curr()->getRequest();

        if ($request->latestParam('Action') == 'finished' && $submissionID = $request->latestParam('ID')) {

            $submission = RhinoSubmittedAssessment::get()->filter('uid', $submissionID)->First();
            if ($submission) {
                $assessment = $submission->Parent();
                if ($assessment) {
                    $mark = $submission->getAssessmentMark();
                    $feedback = $assessment->dbObject('FeedbackOn' . $mark);
                    if ($feedback) {
                        return $feedback;
                    }
                }
            }
        }
    }
}
