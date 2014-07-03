<?php
/**
 * View and administrate BigBlueButton playback recordings
 *
 * Authors:
 *    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)    
 *
 * @package   mod_recordingsbn
 * @copyright 2011-2013 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2014070300;
$plugin->requires  = 2013101800;
$plugin->cron      = 0;
$plugin->component = 'mod_recordingsbn';
$plugin->maturity = MATURITY_RC;  // [MATURITY_STABLE | MATURITY_RC | MATURITY_BETA | MATURITY_ALPHA]
$plugin->release  = '1.0.11';
$plugin->dependencies = array(
    'mod_bigbluebuttonbn' => 2013110100,
);
