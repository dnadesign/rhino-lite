<?php

class RhinoSubmittedAssessment extends SubmittedForm {

	private static $summary_fields = array(
		'ID' => 'ID',
		'uid' => 'uid',
		'Created.Nice' => 'Submitted on',
		'getAssessmentMark' =>'Assessment Mark'
	);

	private static $default_sort = 'Created DESC';

	/**
	* Helper method to find a submission by UID
	*/
	public static function get_by_uid($uid) {
		$type = get_called_class();
		return $type::get()->filter('uid', $uid)->First();
	}

	/**
	* Result is determine by whether or not all the submittedField have
	* a result set to pass or not
	*
	* @return String
	*/
	public function getAssessmentMark() {
		$unmarked = $this->getUnmarkedAnswersCount();
		if ($unmarked > 0) return sprintf('%s unmarked answers', $unmarked);

		if ($this->getAnswers()->Count() == 0) return sprintf('Answers deleted!');

		$mark = 'Pass';

		$wrong = $this->getWrongAnswers();
		if ($wrong && $wrong->Count() > 0) {
			$mark = 'Fail';
		}

		return $mark;
	}

	/**
	* Retrieves all of the questions that can be marked
	*
	* @return DataList
	*/
	public function getQuestionsToBeMarked() {
		$parent = $this->Parent();
		
		if ($parent->hasMethod('getMarkableQuestions')) {
			return $parent->getMarkableQuestions();
		}

		return null;
	}

	/**
	* Return the RhinoSubmittedFormField asssociated with this SubmittedForm
	*
	* @return DataList (RhinoSubmittedFormField)
	*/
	public function getAnswers() {
 		return $this->Values();
	}

	/**
	* Fetch all of the answers that have been marked
	*
	* @return DataList
	*/
	public function getMarkedAnswers() {
		// If there are no questions to be marked
		// there are no unmarked answers
		$questions = $this->getQuestionsToBeMarked();
		if (!$questions || $questions->Count() == 0) return null;

		// If there are no answers, they must have been deleted
		if (!$this->Values()->exists()) return null;

		// Get all the answers from markable questions
		$markedAnswers = $this->getAnswers()->filter(array('Name' => $questions->column('Name')))->exclude('Mark', 'none');

		return $markedAnswers;
	}

	/**
	* Checks if all SubmittedFields have been marked
	*
	* @return Int | Boolean
	*/
	public function getUnmarkedAnswersCount() {
		// If there are no questions to be marked
		// there are no unmarked answers
		$questions = $this->getQuestionsToBeMarked();
		if (!$questions || $questions->count() == 0) return false;

		// If there are no answers, they must have been deleted
		if (!$this->Values()->exists()) return false;

		// Get all the answers from markable questions
		$unmarkedAnswers = $this->getAnswers()->filter(array('Name' => $questions->column('Name'), 'Mark' => 'none'));

		return $unmarkedAnswers->Count();
	}

	public function hasUnmarkedAnswers() {
		$count = $this->getUnmarkedAnswersCount();
		return ($count !== false) ? ($count > 0) : false;
	}

	/**
	* Return the RhinoSubmittedFormField asssociated with this SubmittedForm
	* which have been correctly answered
	*
	* @return DataList (RhinoSubmittedFormField)
	*/
	public function getCorrectAnswers() {
		$marked = $this->getMarkedAnswers();
 		return ($marked) ? $marked->filter('Mark', 'pass') : null;
	}

	/**
	* Return the RhinoSubmittedFormField asssociated with this SubmittedForm
	* which have been incorrectly answered
	*
	* @return DataList (RhinoSubmittedFormField)
	*/
	public function getWrongAnswers() {
		$marked = $this->getMarkedAnswers();
 		return ($marked) ? $marked->filter('Mark', 'fail') : null;
	}

	/**
	* Return the link where this submission can be seen
	* ie the finished action on the parent controller
	*
	* @return String
	*/
	public function getLink() {
		$page = $this->Parent();
		if ($page && $page->exists()) {
			$controller = singleton($page->ClassName.'_Controller');
			if ($controller && $controller instanceof UserDefinedForm_Controller) {
				// TODO: Make sure the action is actually 'finished'
				$link = Controller::join_links($page->AbsoluteLink(), 'finished', $this->uid);
				return $link;
			}
		}

		return  null;
	}

}