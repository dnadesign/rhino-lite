<?php

class RhinoSubmittedAssessment extends SubmittedForm {

	private static $summary_fields = array(
		'ID' => 'ID',
		'uid' => 'uid',
		'Created.Nice' => 'Submitted on',
		'getResult' =>'Result'
	);

	/**
	* Result is determine by whether or not all the submittedField have
	* a result set to pass or not
	*
	* @return String
	*/
	public function getResult() {
		if ($this->hasUnmarkedAnswers() || !$this->Values()->exists()) return 'Unknown';

		$result = 'Pass';

		$wrongAnswers =  $this->getAnswers()->filter('Result', 'Fail');

		if ($wrongAnswers->Count() > 0) {
			$result = 'Fail';
		}

		return $result;
	}

	/**
	* Return the information required for the result tile
	*
	* @return ArrayData | false
	*/
	public function getScore() {
		$result = $this->getResult();
		$questions = $this->Parent()->getQuestions();
		$correctAnswers = $this->getCorrectAnswers();
		$time = $this->TimeToCompletion;
		$isMe = (Member::currentUserID() && $this->SubmittedByID == Member::currentUserID());

		$data = array(
			'Result' => $result,
			'TotalQuestions' => $questions->Count(),
			'CorrectAnswers' => $correctAnswers->Count(),
			'Time' => $time,			
			'SubmittedBy' => $this->SubmittedBy(),
			'IsMe' => $isMe
		);

		return new ArrayData($data);
	}

	/**
	* Checks if all SubmittedFields have been marked
	*
	* @return Boolean
	*/
	public function hasUnmarkedAnswers() {
		if (!$this->Values()->exists()) return true;

		$unmarkedAnswers = $this->getAnswers()->filter('Result' , array('null', 'Unknown'))->Count();
		return ($unmarkedAnswers > 0);
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
	* Return the RhinoSubmittedFormField asssociated with this SubmittedForm
	* which have been correctly answered
	*
	* @return DataList (RhinoSubmittedFormField)
	*/
	public function getCorrectAnswers() {
 		return $this->getAnswers()->filter('Result', 'Pass');
	}

	/**
	* Return the RhinoSubmittedFormField asssociated with this SubmittedForm
	* which have been incorrectly answered
	*
	* @return DataList (RhinoSubmittedFormField)
	*/
	public function getWrongAnswers() {
 		return $this->getAnswers()->filter('Result', 'Fail');
	}

}