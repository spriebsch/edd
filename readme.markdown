Experiment-Driven Development with PHP
======================================

This code shows how experiment-driven development can be realized with PHP.
Experiment-driven development means to continuously run a/b tests, including
but not limited to trying out variations in the GUI, comparing one algorithm
or implementation against another, or ramping up a new feature.

The code does not make any assumptions about the persistence mechanism and/or
framework used.

The example demo/edd.php runs an experiment showing a new profile page to all
users with German as session language that have signed up more than one year
ago. These conditions have just been selected for the sake of the example,
and are completely pointless otherwise. The users are then asked to rate the
new profile page. The ratings are also purely random.

To run the demo, execute demo/edd.php at the command line. This will output
the outcome of one experiment, based solely on random data.
A logfile will be created in the project directory. Use demo/show_log.php to
display the log.

Requirements
------------

The code should work on any standard PHP 5.3.0 installation.

License
-------

The code is available under BSD license.

Disclaimer
----------

This code is example code and not to be considered production quality. 

Contact and Feedback
--------------------

Any feedback is truly appreciated. The author's email address is stefan@priebsch.de.
