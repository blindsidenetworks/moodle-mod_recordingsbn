RecordingsBN Resource Module for Moodle
=======================================
BigBlueButton is an open source web conferencing system that enables universities and colleges to deliver a high-quality learning experience to remote students.  

These instructions describe how to install the BigBlueButtonBN Activity Module for Moodle.  This module is developed and supported by Blindside Networks, the company that started the BigBlueButton project in 2007.

With the latest version of this plugin you can
	- Create resource links to recordings made with BigBlueButton (requires bigbluebuttonbn plugin to be installed)
	- Publish/unpublish and delete recordings

Prerequisites
=============
You need:

        1.  A server running Moodle
        2.  A BigBlueButton 0.8 (or later) server running on a separate server (not on the same server as your Moodle site)
        3.  BigBlueButtonBN installed

Note that on previous versions of Moodle you will need to use the specific version of this plugin. 

    - For Moodle 1.9 use version 1.0.9 (2013071001).
    - For Moodle 2.0, 2.1, 2.2, 2.3, 2.4 and 2.5 use version 1.1.0 (2015062100)
    - For Moodle 2.6+ use version 1.2.0 (20150806xx).

Blindside Networks provides you a test BigBlueButton server for testing this plugin.  To use this test server, just accept the default settings when configuring the activity module.  The default settings are

	url: http://test-install.blindsidenetworks.com/bigbluebutton/

	salt: 8cd8ef52e8e101574e400365b55e11a6

For information on how to setup your own BigBlueButton server see

Obtaining the source
====================
This GitHub repostiory at

  https://github.com/blindsidenetworks/moodle-mod_recordingsbn

contains the latest source. We recomend to download the latest snapshot from the Moodle Plugin Database.

Installation
============

These instructions assume your Moodle server is installed at /var/www/moodle.

1.  Copy recordingsbn.zip to /var/www/moodle/mod

2.  Enter the following commands

	cd /var/www/moodle/mod
    	sudo unzip recordingsbn.zip

    This will create the directory

        ./recordingsbn

3.  Login to your moodle site as administrator

	Moodle will detect the new module and prompt you to Upgrade.

4.  Click the 'Upgrade' button.  

	The activity module will install mod_recordingsbn.

5.  Click the 'Continue' button. 

At this point, you can enter any course, turn editing on, and add a recordingsbn resource link to the class.

For a video overview of installing and using this plugin,

	http://blindsidenetworks.com/integration


Contact Us
==========
If you have feedback, enhancement requests, or would like commercial support for hosting, integrating, customizing, branding, or scaling BigBlueButton, contact us at

	http://blindsidenetworks.com/

Regards,... Fred Dixon
ffdixon [at] blindsidenetworks [dt] com

