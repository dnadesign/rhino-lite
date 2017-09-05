<?php

class RhinoAssessment extends UserDefinedForm {

	private static $db = array(
		'FeedbackOnPass' => 'HTMLText',
		'FeedbackOnFail' => 'HTMLText'
	);

	private static $defaults = array(
		'Content' => ''
	);

	private static $singular_name = 'Assessment';

	private static $plural_name = 'Assessments';

	public function getCMSFields() {
		$fields = parent::getCMSFields();

       $this->beforeUpdateCMSFields(function ($fields) {     
           
            $fields->removeByName('DisableSaveSubmissions');

    		// Feedback
    		$pass = HTMLEditorField::create('FeedbackOnPass');
    		$fail = HTMLEditorField::create('FeedbackOnFail');

    		$fields->addFieldsToTab('Root.FeedbackOnSubmission', array($pass, $fail));

    		// Allow subclass to create defaults fields
    		if (method_exists(get_class($this), 'createDefaultFields')) {
    			// Do nothing if the form already has fields set up
    			if ($this->getQuestions()->Count() == 0) {
    				$this->createDefaultFields($this->Fields());
    			}
    		}

            // Submissions must be RhinoSubmittedAssessments
            $gridField = $fields->fieldByName('Root.Submissions.Submissions');

            $list = RhinoSubmittedAssessment::get()->filter('ID', $this->Submissions()->column('ID'));
            $gridField->setList($list);

            // Summary Fields are hijacked by UserDefinedForm
            // So need to explicitely use the RhinoSubmittedAssessment ones
            $config = $gridField->getConfig();
            $dataColumns = $config->getComponentByType('GridFieldDataColumns');

            $columns = singleton('RhinoSubmittedAssessment')->summaryFields();

            // Still add the EditableFormField if required
            foreach(EditableFormField::get()->filter(array("ParentID" => $this->ID)) as $eff) {
                if($eff->ShowInSummary) {
                    $columns[$eff->Name] = $eff->Title ?: $eff->Name;
                }
            }
            
            $dataColumns->setDisplayFields($columns);

         });

		$this->updateEditableFields();

		return $fields;
	}

	public function updateEditableFields() {
		// Allow only certain fields to be created
		$allowedFields = $this->config()->allowed_field_types;
		if ($allowedFields) {
			$fieldClasses = singleton('EditableFormField')->getEditableFieldClasses();
			foreach($fieldClasses as $fieldClass => $fieldTitle) {
				if (!in_array($fieldClass, $allowedFields)) {
					Config::inst()->update($fieldClass, 'hidden', true);
				}
			}
			// Explicitely allow fields, so subclasses show up
			foreach($allowedFields as $fieldClass) {
				Config::inst()->update($fieldClass, 'hidden', false);
			}
		}
	}

	/**
	* Return all fields (questions)
	* besides the EditableFormStep (required)
	* @return Int
	*/
	public function getQuestions() {
		$fields = $this->Fields();
		// remove EditableFormStep
		$fields = $fields->exclude('ClassName', 'EditableFormStep');
		// return fields
		return $fields;
	}

    public function getMarkableQuestions() {
        $fields = $this->getQuestions()->filterByCallback(function($field) {
            return $field->hasMethod('pass_or_fail');
        });

        return $fields;
    }

    /**
    * Shortcode to display the feedback on the Submission screen
    */
    public static function assessment_feedback() {
        $request = Controller::curr()->getRequest();

        if($request->latestParam('Action') == 'finished' && $submissionID = $request->latestParam('ID')) {
            
            $submission = RhinoSubmittedAssessment::get()->filter('uid', $submissionID)->First();
            if($submission) {
                $assessment = $submission->Parent();
                if($assessment) {
                    $mark = $submission->getAssessmentMark();
                    $feedback = $assessment->dbObject('FeedbackOn'.$mark);
                    if ($feedback) {
                        return $feedback;
                    }
                }
            }
        }
    }
}

class RhinoAssessment_Controller extends UserDefinedForm_Controller {

	private static $allow_multiple_reviews = true;
	
	private static $submission_template = "ReceivedFormSubmission";
	
	private static $finished_anchor = '';

	private static $allowed_actions = array(
		'finished'
	);

	public function init() {
		parent::init();
	}

	/**
	* Retrieve the latest submission from its uid
	* @return SubmittedForm
	*/
	public function getSubmission() {
		$submission = $this->getRequest()->param('ID');
        
        if ($submission) {
            return SubmittedForm::get()->filter('uid', $submission)->First();
        }

        return null;
	}

	/**
	* Allow to display a special template
	* upon form submission
	*/
	 public function finished()
    {
    	$submission = $this->getSubmission();

        $referrer = isset($_GET['referrer']) ? urldecode($_GET['referrer']) : null;

        if (!$this->DisableAuthenicatedFinishAction && !$submission) {
            $formProcessed = Session::get('FormProcessed');

            if (!isset($formProcessed)) {
                return $this->redirect($this->Link() . $referrer);
            } else {
                $securityID = Session::get('SecurityID');
                // make sure the session matches the SecurityID and is not left over from another form
                if ($formProcessed != $securityID) {
                    // they may have disabled tokens on the form
                    $securityID = md5(Session::get('FormProcessedNum'));
                    if ($formProcessed != $securityID) {
                        return $this->redirect($this->Link() . $referrer);
                    }
                }
            }

            Session::clear('FormProcessed');
        }

        $data = array(
                'Submission' => $submission,
                'Link' => $referrer
        );

        $this->extend('updateReceivedFormSubmissionData', $data);

        return $this->customise(array(
            'Content' => $this->customise($data)->renderWith($this->config()->submission_template),
            'Form' => '',
        ));
    }

	 /**
     * Process the form that is submitted through the site
     *
     * {@see UserForm::validate()} for validation step prior to processing
     *
     * @param array $data
     * @param Form $form
     *
     * @return Redirection
     */
    public function process($data, $form)
    {
        $submittedForm = Object::create('SubmittedForm');
        $submittedForm->SubmittedByID = ($id = Member::currentUserID()) ? $id : 0;
        $submittedForm->ParentID = $this->ID;

        // if saving is not disabled save now to generate the ID
        if (!$this->DisableSaveSubmissions) {
            $submittedForm->write();
        }

        $attachments = array();
        $submittedFields = new ArrayList();

        foreach ($this->Fields() as $field) {
            // var_dump($field->Title, $field->->showInReports());


            if (!$field->showInReports()) {
                continue;
            }

            $submittedField = $field->getSubmittedFormField();
            $submittedField->ParentID = $submittedForm->ID;
            $submittedField->Name = $field->Name;
            $submittedField->Title = $field->getField('Title');

            // save the value from the data
            if ($field->hasMethod('getValueFromData')) {
                $submittedField->Value = $field->getValueFromData($data);
            } else {
                if (isset($data[$field->Name])) {
                    $submittedField->Value = $data[$field->Name];
                }
            }

            if (!empty($data[$field->Name])) {
                if (in_array("EditableFileField", $field->getClassAncestry())) {
                    if (!empty($_FILES[$field->Name]['name'])) {
                        $foldername = $field->getFormField()->getFolderName();

                        // create the file from post data
                        $upload = new Upload();
                        $file = new File();
                        $file->ShowInSearch = 0;
                        try {
                            $upload->loadIntoFile($_FILES[$field->Name], $file, $foldername);
                        } catch (ValidationException $e) {
                            $validationResult = $e->getResult();
                            $form->addErrorMessage($field->Name, $validationResult->message(), 'bad');
                            Controller::curr()->redirectBack();
                            return;
                        }

                        // write file to form field
                        $submittedField->UploadedFileID = $file->ID;

                        // attach a file only if lower than 1MB
                        if ($file->getAbsoluteSize() < 1024*1024*1) {
                            $attachments[] = $file;
                        }
                    }
                }
            }

            // Perform Marking on each field
            $submittedField->extend('onPopulationFromField', $field);

            if (!$this->DisableSaveSubmissions) {
                $submittedField->write();
            }

            $submittedFields->push($submittedField);
        }

        $emailData = array(
            "Sender" => Member::currentUser(),
            "Fields" => $submittedFields
        );

        $this->extend('updateEmailData', $emailData, $attachments);

        // email users on submit.
        if ($recipients = $this->FilteredEmailRecipients($data, $form)) {
            foreach ($recipients as $recipient) {
                $email = new UserFormRecipientEmail($submittedFields);
                $mergeFields = $this->getMergeFieldsMap($emailData['Fields']);

                if ($attachments) {
                    foreach ($attachments as $file) {
                        if ($file->ID != 0) {
                            $email->attachFile(
                                $file->Filename,
                                $file->Filename,
                                HTTP::get_mime_type($file->Filename)
                            );
                        }
                    }
                }

                $parsedBody = SSViewer::execute_string($recipient->getEmailBodyContent(), $mergeFields);

                if (!$recipient->SendPlain && $recipient->emailTemplateExists()) {
                    $email->setTemplate($recipient->EmailTemplate);
                }

                $email->populateTemplate($recipient);
                $email->populateTemplate($emailData);
                $email->setFrom($recipient->EmailFrom);
                $email->setBody($parsedBody);
                $email->setTo($recipient->EmailAddress);
                $email->setSubject($recipient->EmailSubject);

                if ($recipient->EmailReplyTo) {
                    $email->setReplyTo($recipient->EmailReplyTo);
                }

                // check to see if they are a dynamic reply to. eg based on a email field a user selected
                if ($recipient->SendEmailFromField()) {
                    $submittedFormField = $submittedFields->find('Name', $recipient->SendEmailFromField()->Name);

                    if ($submittedFormField && is_string($submittedFormField->Value)) {
                        $email->setReplyTo($submittedFormField->Value);
                    }
                }
                // check to see if they are a dynamic reciever eg based on a dropdown field a user selected
                if ($recipient->SendEmailToField()) {
                    $submittedFormField = $submittedFields->find('Name', $recipient->SendEmailToField()->Name);

                    if ($submittedFormField && is_string($submittedFormField->Value)) {
                        $email->setTo($submittedFormField->Value);
                    }
                }

                // check to see if there is a dynamic subject
                if ($recipient->SendEmailSubjectField()) {
                    $submittedFormField = $submittedFields->find('Name', $recipient->SendEmailSubjectField()->Name);

                    if ($submittedFormField && trim($submittedFormField->Value)) {
                        $email->setSubject($submittedFormField->Value);
                    }
                }

                $this->extend('updateEmail', $email, $recipient, $emailData);

                if ($recipient->SendPlain) {
                    $body = strip_tags($recipient->getEmailBodyContent()) . "\n";
                    if (isset($emailData['Fields']) && !$recipient->HideFormData) {
                        foreach ($emailData['Fields'] as $Field) {
                            $body .= $Field->Title .': '. $Field->Value ." \n";
                        }
                    }

                    $email->setBody($body);
                    $email->sendPlain();
                } else {
                    $email->send();
                }
            }
        }

        $submittedForm->extend('updateAfterProcess');

        Session::clear("FormInfo.{$form->FormName()}.errors");
        Session::clear("FormInfo.{$form->FormName()}.data");

        $referrer = (isset($data['Referrer'])) ? '?referrer=' . urlencode($data['Referrer']) : "";

        // set a session variable from the security ID to stop people accessing
        // the finished method directly.
        if (!$this->DisableAuthenicatedFinishAction) {
            if (isset($data['SecurityID'])) {
                Session::set('FormProcessed', $data['SecurityID']);
            } else {
                // if the form has had tokens disabled we still need to set FormProcessed
                // to allow us to get through the finshed method
                if (!$this->Form()->getSecurityToken()->isEnabled()) {
                    $randNum = rand(1, 1000);
                    $randHash = md5($randNum);
                    Session::set('FormProcessed', $randHash);
                    Session::set('FormProcessedNum', $randNum);
                }
            }
        }

        if (!$this->DisableSaveSubmissions) {
            Session::set('userformssubmission'. $this->ID, $submittedForm->ID);
        }

        $link = $this->Link('finished') . $referrer . $this->config()->finished_anchor;
        if($this->config()->allow_multiple_reviews == true) {
        	$link = Controller::join_links($link, '/'.$submittedForm->uid);
        }

        return $this->redirect($link);
    }
}