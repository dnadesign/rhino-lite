<?php

// the current release version. We plan on doing regular releases. This flag is
// used so we can tell what remote instances are running.
define('RHINO_VERSION', '3.0');

Object::useCustomClass('ManyManyList', 'RhinoManyManyList', true);

ShortcodeParser::get('default')->register('assessment_feedback', array('RhinoAssessment', 'assessment_feedback'));