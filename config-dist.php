<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// RecordingsBN configuration file for moodle                            //
//                                                                       //
// This file should be renamed "config.php" in the plugin directory      //
//                                                                       //
// It is intended to be used for setting configuration by default and    //
// also for enable/diable configuration options in the admin setting UI  //
// for those multitenancy deployments where the admin account is given   //
// to the tenant owner and some shared information like the              //
// bigbluebutton_server_url and bigbluebutton_shared_secret must been    //
// kept private. And also when some of the features are going to be      //
// disabled for all the tenants in that server                           //
//                                                                       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
//                                                                       //
///////////////////////////////////////////////////////////////////////////
/**
 * Configuration file for bigbluebuttonbn
 *
 * @package   mod_bigbluebuttonbn
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2015 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

unset($RECORDINGSBN_CFG);
global $RECORDINGSBN_CFG;
$RECORDINGSBN_CFG = new stdClass();

//=========================================================================
// Any parameter included in this fill will not be shown in the admin UI //
// If there was a previous configuration, the parameters here included   //
// will override the parameters already configured (if they were         //
// configured already)                                                   //
//=========================================================================



//=========================================================================
// 1. GENERAL CONFIGURATION
//=========================================================================

// When the value is set to 1 (checked) the recordingsbn resources
// will show the recodings in an html table by default.
#$RECORDINGSBN_CFG->recordingsbn_ui_html_default = 0;

// When the value is set to 1 (checked) the 'html ui' capability can be
// enabled/disabled by the user creating or editing the resource.
#$RECORDINGSBN_CFG->recordingsbn_ui_html_editable = 0;

// When the value is set to 1 (checked) the recordingsbn resources
// will show the recodings belonging to deleted activities as part of the list.
#$RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_default = 1;

// When the value is set to 1 (checked) the 'include recordings from deleted activities' 
// capability can be enabled/disabled by the user creating or editing the resource.
#$RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_editable = 0;

