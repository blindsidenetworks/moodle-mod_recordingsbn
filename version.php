<?php
/**
 * View and administrate BigBlueButton playback recordings
 *
 * @package   mod_recordingsbn
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2011-2015 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2016051905;
$plugin->requires = 2013111800;
$plugin->cron = 0;
$plugin->component = 'mod_recordingsbn';
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '1.3.1';
$plugin->dependencies = array( 'mod_bigbluebuttonbn' => 2016051900 );
