<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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
    return (isset($RECORDINGSBN_CFG->recordingsbn_ui_html_default) ? $RECORDINGSBN_CFG->recordingsbn_ui_html_default : (isset($CFG->recordingsbn_ui_html_default) ? $CFG->recordingsbn_ui_html_default : false));
}

function recordingsbn_get_cfg_ui_html_editable() {
    global $RECORDINGSBN_CFG, $CFG;
    return (isset($RECORDINGSBN_CFG->recordingsbn_ui_html_editable) ? $RECORDINGSBN_CFG->recordingsbn_ui_html_editable: (isset($CFG->recordingsbn_ui_html_editable) ? $CFG->recordingsbn_ui_html_editable : false));
}

function recordingsbn_get_cfg_include_deleted_activities_default() {
    global $RECORDINGSBN_CFG, $CFG;
    return (isset($RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_default) ? $RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_default : (isset($CFG->recordingsbn_include_deleted_activities_default) ? $CFG->recordingsbn_include_deleted_activities_default : false));
}

function recordingsbn_get_cfg_include_deleted_activities_editable() {
    global $RECORDINGSBN_CFG, $CFG;
    return (isset($RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_editable) ? $RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_editable : (isset($CFG->recordingsbn_include_deleted_activities_editable) ? $CFG->recordingsbn_include_deleted_activities_editable : false));
}

function recordingsbn_get_moodle_version_major() {
    global $CFG;
    $versionarray = explode('.', $CFG->version);
    return $versionarray[0];
}
