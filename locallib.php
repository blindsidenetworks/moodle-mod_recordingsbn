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

/**
 * Returns default value for ui_html.
 *
 * @return boolean
 */
function recordingsbn_get_cfg_ui_html_default() {
    global $RECORDINGSBN_CFG, $CFG;
    if (isset($RECORDINGSBN_CFG->recordingsbn_ui_html_default)) {
        return $RECORDINGSBN_CFG->recordingsbn_ui_html_default;
    }
    if (isset($CFG->recordingsbn_ui_html_default)) {
        return $CFG->recordingsbn_ui_html_default;
    }
    return false;
}

/**
 * Returns value for ui_html_editable.
 *
 * @return boolean
 */
function recordingsbn_get_cfg_ui_html_editable() {
    global $RECORDINGSBN_CFG, $CFG;
    if (isset($RECORDINGSBN_CFG->recordingsbn_ui_html_editable)) {
        return $RECORDINGSBN_CFG->recordingsbn_ui_html_editable;
    }
    if (isset($CFG->recordingsbn_ui_html_editable)) {
        return $CFG->recordingsbn_ui_html_editable;
    }
    return false;
}

/**
 * Returns default value for include_deleted_activities.
 *
 * @return boolean
 */
function recordingsbn_get_cfg_include_deleted_activities_default() {
    global $RECORDINGSBN_CFG, $CFG;
    if (isset($RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_default)) {
        return $RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_default;
    }
    if (isset($CFG->recordingsbn_include_deleted_activities_default)) {
        return $CFG->recordingsbn_include_deleted_activities_default;
    }
    return false;
}

/**
 * Returns value for include_deleted_activities_editable.
 *
 * @return boolean
 */
function recordingsbn_get_cfg_include_deleted_activities_editable() {
    global $RECORDINGSBN_CFG, $CFG;
    if (isset($RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_editable)) {
        return $RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_editable;
    }
    if (isset($CFG->recordingsbn_include_deleted_activities_editable)) {
        return $CFG->recordingsbn_include_deleted_activities_editable;
    }
    return false;
}

/**
 * Returns moodle version.
 *
 * @return string
 */
function recordingsbn_get_moodle_version_major() {
    global $CFG;
    $versionarray = explode('.', $CFG->version);
    return $versionarray[0];
}

/**
 * Returns dependency version.
 *
 * @return string
 */
function recordingsbn_get_dependency_version() {
    $versionmajor = recordingsbn_get_moodle_version_major();
    if ( $versionmajor < '2013111800' ) {
        // This is valid before v2.6.
        $dependency = $DB->get_record('modules', array('name' => 'bigbluebuttonbn'));
        return $dependency->version;
    }
    // This is valid after v2.6.
    return get_config('mod_bigbluebuttonbn', 'version');
}
