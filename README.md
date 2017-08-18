# Rhino Lite

## Introduction

Rhino Lite is a module that provides an enhanced UserDefinedForm that can be use as a self assessment. RhinoAssessment includes the logic necessary to provide a user with adequate feedback in regards to the answered they supplied.

## Requirements

 * SilverStripe 3.2+
 * silverstripe/userforms

## Installation

Installation can be done either by composer or by manually downloading the
release from Github.

### Via composer

`composer require "dnadesign/rhino-lite"`

### Manually

 1.  Download the module from [the releases page](https://github.com/silverstripe/silverstripe-siteconfig/releases).
 2.  Extract the file (if you are on windows try 7-zip for extracting tar.gz files
 3.  Place this directory in your sites root directory. This is the one with framework and cms in it.
 4. Run dev/build

## How it works

Rhino Assessments accept any type of EditableFormField.
Upon submission, the contoller redirects to the `finished` method, adding the last submission unique id to the url eg: http://mysite.com/myassessment/finished/ABC12345.

This allow to reliably get the last submission from the url parameters.
If you add the shortcode [assessment_feedback] in the OnCompleteMessage of the RhinoAssessment,
the final mark of the assement will be determined by wether or not all of the markable fields have been marked as pass or not. From there, the corresponding feedback will be displayed.

The url of the finished assessment can be shared as it does not rely on the session variables.

