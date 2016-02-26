<?php
/**
 * View and administrate BigBlueButton playback recordings
 *
 * @package   mod_recordingsbn
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2011-2014 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

global $RECORDINGSBN_CFG, $CFG;

require_once(dirname(__FILE__).'/lib.php');

function recordingsbn_get_cfg_ui_html_default() {
    global $RECORDINGSBN_CFG, $CFG;
    return (isset($RECORDINGSBN_CFG->recordingsbn_ui_html_default)? $RECORDINGSBN_CFG->recordingsbn_ui_html_default: (isset($CFG->recordingsbn_ui_html_default)? $CFG->recordingsbn_ui_html_default: false));
}

function recordingsbn_get_cfg_ui_html_editable() {
    global $RECORDINGSBN_CFG, $CFG;
    return (isset($RECORDINGSBN_CFG->recordingsbn_ui_html_editable)? $RECORDINGSBN_CFG->recordingsbn_ui_html_editable: (isset($CFG->recordingsbn_ui_html_editable)? $CFG->recordingsbn_ui_html_editable: false));
}

function recordingsbn_get_cfg_include_deleted_activities_default() {
    global $RECORDINGSBN_CFG, $CFG;
    return (isset($RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_default)? $RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_default: (isset($CFG->recordingsbn_include_deleted_activities_default)? $CFG->recordingsbn_include_deleted_activities_default: false));
}

function recordingsbn_get_cfg_include_deleted_activities_editable() {
    global $RECORDINGSBN_CFG, $CFG;
    return (isset($RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_editable)? $RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_editable: (isset($CFG->recordingsbn_include_deleted_activities_editable)? $CFG->recordingsbn_include_deleted_activities_editable: false));
}
