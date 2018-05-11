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
 * Settings for RecordingsBN
 *
 * @package   mod_recordingsbn
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2011-2014 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die;

global $BIGBLUEBUTTONBN_CFG;

require_once(dirname(__FILE__).'/locallib.php');

if ($ADMIN->fulltree) {
    $dependencyversion = recordingsbn_get_dependency_version();
    if ($dependencyversion >= '2017101000') {
        $renderer = new \mod_recordingsbn\settings\renderer('recordingsbn', $settings);
        // Renders deprecation messages.
        $renderer->render_warning_message('deprecated_warning',
            get_string('view_deprecated_msg_admin', 'recordingsbn'), 'danger', false);
        $renderer->render_warning_message('deprecated_info',
            get_string('view_deprecated_info_admin', 'recordingsbn') . '<br><br>' .
            get_string('view_deprecated_migrate', 'recordingsbn'), 'info', false);
        // Renders call to action button.
        $renderer->render_group_element_button('deprecated_action',
            $CFG->wwwroot . '/mod/recordingsbn/migration.php',
            get_string('view_deprecated_call_to_action', 'recordingsbn'));
        return;
    }
    // Render this when dependency is older than v2.2.1.
    if (!isset($BIGBLUEBUTTONBN_CFG->recordingsbn_ui_html_default) ||
        !isset($BIGBLUEBUTTONBN_CFG->recordingsbn_ui_html_editable) ||
        !isset($BIGBLUEBUTTONBN_CFG->recordingsbn_include_deleted_activities_default) ||
        !isset($BIGBLUEBUTTONBN_CFG->recordingsbn_include_deleted_activities_editable)) {
        $renderer->render_group_header('general');
    }
    if (!isset($BIGBLUEBUTTONBN_CFG->recordingsbn_ui_html_default) ) {
        $renderer->render_group_element('ui_html_default',
            $renderer->render_group_element_checkbox('ui_html_default', 1));
    }
    if (!isset($BIGBLUEBUTTONBN_CFG->recordingsbn_ui_html_editable)) {
        $renderer->render_group_element('ui_html_editable',
            $renderer->render_group_element_checkbox('ui_html_editable', 0));
    }
    if (!isset($BIGBLUEBUTTONBN_CFG->recordingsbn_include_deleted_activities_default)) {
        $renderer->render_group_element('include_deleted_activities_default',
            $renderer->render_group_element_checkbox('include_deleted_activities_default', 1));
    }
    if (!isset($BIGBLUEBUTTONBN_CFG->recordingsbn_include_deleted_activities_editable)) {
        $renderer->render_group_element('include_deleted_activities_editable',
            $renderer->render_group_element_checkbox('include_deleted_activities_editable', 0));
    }
}
