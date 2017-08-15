title: Testing
summary: Unit and Behavior Testing

# Unit and Behavior Testing

As the application has so many cases and logic related to specific user 
accounts, we attempt to follow a test driven approach to building new features. 
That is, the test acts as our specification and design and then we finish the 
application features.

The unit tests are written in PHPUnit and are in the `rhino/tests/unit` folder. 
To run a specific test

	phpunit rhino/tests/unit/CapabilityTests.php

Or to run the whole suite run:
	
	make unittests

To run the behaviour tests:

	vendor/bin/behat @rhino/assignments.feature

And to run the whole suite:

	make behat

## Test Data

The data that is used for the tests varies depending on the test, but it uses
 the same fixture files as the application. For more information, see the 
 [Fixtures](fixtures) documentation.

## Coverage

A code coverage report makes sure that we have at least got tests running over 
the majority of the application. This prevents simple mistakes like typo's and 
incorrect names from getting into production. To generate a code coverage report 
run

	sake dev/tests/coverage/module/rhino

This will take some time to generate into your `assets` directory so should 
only be generated if you need to check that your tests cover the new 
functionality adequately. 