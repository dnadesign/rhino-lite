<?php

/* Manual require because autoloading won't pick this up */
require_once __DIR__ . '/../code/models/RhinoSubmittedAssessment.php';

class RhinoSubmittedAssessmentTest extends SapphireTest
{
	public function setUpOnce()
    {
		parent::setUpOnce();

		Phockito::include_hamcrest();
	}

    public function testAssessmentMark()
    {
        $assessmentA = Phockito::spy('RhinoSubmittedAssessment');
        Phockito::when($assessmentA)->getUnmarkedAnswersCount()->return(1);

        $mark = $assessmentA->getAssessmentMark();
        $this->assertEquals('1 unmarked answers', $mark);

        $assessmentB = Phockito::spy('RhinoSubmittedAssessment');
        Phockito::when($assessmentB)->getUnmarkedAnswersCount()->return(0);

        $mark = $assessmentB->getAssessmentMark();
        $this->assertEquals('Answers deleted!', $mark);
    }
}
