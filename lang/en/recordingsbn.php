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
 * Language File
 *
 * @package   mod_recordingsbn
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2011-2014 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'RecordingsBN';
$string['modulenameplural'] = 'RecordingsBN';
$string['modulename_help'] = 'Use the RecordingsBN module as a resource to provide access to the BigBlueButton recordings related to this course.';
$string['modulename_link'] = 'RecordingsBN/view';
$string['recordingsbnname'] = 'Recordings name';
$string['recordingsbnname_help'] = 'RecordingsBN provides a list of playback recordings in a BigBlueButton Server providing direct access to them.';
$string['recordingsbn'] = 'RecordingsBN';
$string['pluginadministration'] = 'recordingsbn administration';
$string['pluginname'] = 'RecordingsBN';
$string['recordingsbn:addinstance'] = 'Add a new resource with playback recordings';
$string['recordingsbn:view'] = 'View recordings';

$string['mod_form_field_ui_html'] = 'Show the table in plain html';
$string['mod_form_field_include_deleted_activities'] = 'Include recordings from deleted activities';

$string['config_general'] = 'General configuration';
$string['config_general_description'] = 'These settings are <b>always</b> used';
$string['config_feature_ui_html_default'] = 'UI as html is enabled by default';
$string['config_feature_ui_html_default_description'] = 'If enabled the recording table is shown in plain HTML by default.';
$string['config_feature_ui_html_editable'] = 'UI as html feature can be edited';
$string['config_feature_ui_html_editable_description'] = 'UI as html value by default can be edited when the recordingbn is added or updated.';
$string['config_feature_include_deleted_activities_default'] = 'Include recordings from deleted activities enabled by default';
$string['config_feature_include_deleted_activities_default_description'] = 'If enabled the recording table will include the recordings belonging to deleted activities if there is any.';
$string['config_feature_include_deleted_activities_editable'] = 'Include recordings from deleted activities feature can be edited';
$string['config_feature_include_deleted_activities_editable_description'] = 'Include recordings from deleted activities by default can be edited when the recordingbn is added or updated.';

$string['view_noguests'] = 'The RecordingsBN module is not open to guests';
$string['view_delete_confirmation'] = 'Are you sure to delete this recording?';
$string['view_dependency_error'] = 'You must have BigBlueButtonBN Activity Module installed';
$string['view_head_actionbar'] = 'Toolbar';
$string['view_head_activity'] = 'Activity';
$string['view_head_course'] = 'Course';
$string['view_head_date'] = 'Date';
$string['view_head_description'] = 'Description';
$string['view_head_length'] = 'Length';
$string['view_head_duration'] = 'Duration';
$string['view_head_recording'] = 'Recording';
$string['view_duration_min'] = 'min';
$string['view_no_recordings'] = 'There are no recordings to show';
$string['view_deprecated'] = 'RecordingsBN has been deprecated and can only be used with BigBlueButtonBN 2.1.x.';
$string['view_deprecated_info'] = 'Since version 2.2 it is possible to add a BigBlueButtonBN instance in Recordings only mode for getting RecordingsBN functionality.';

$string['event_resource_page_viewed'] = 'RecordingsBN page viewed';
$string['event_recording_published'] = 'Recording published';
$string['event_recording_unpublished'] = 'Recording unpublished';
$string['event_recording_deleted'] = 'Recording deleted';
