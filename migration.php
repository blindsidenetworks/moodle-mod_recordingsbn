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

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

require_login();
$syscontext = context_system::instance();
require_capability('moodle/site:config', $syscontext);

$dependencyversion = recordingsbn_get_dependency_version();
if ($dependencyversion < '2017101009') {
    print_error('Migrations only work for BigBlueButtonBN 2.2 and later');
}


// Print the page header.
$PAGE->set_context($syscontext);
$PAGE->set_url($CFG->wwwroot.'/mod/recordingsbn/migration.php');
$PAGE->set_title("RecordingBN");
$PAGE->set_heading("RecordingBN");


// Output starts here.
echo $OUTPUT->header();

// Recordings plugin code.
$dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
if (!$dbman->table_exists('bigbluebuttonbn_logs')) {
    print_error(get_string('view_dependency_error', 'recordingsbn'));
}

// Proceed with the migration
$courses = get_courses('all', 'c.fullname ASC', 'c.id,c.shortname,c.fullname');
if (!$recordingsbns = get_all_instances_in_courses('recordingsbn', $courses)) {
    echo bigbluebuttonbn_render_warning(get_string('norecordingsbns', 'recordingsbn'), 'warning');
    // Finish the page.
    echo $OUTPUT->footer();
    die();
}

echo $OUTPUT->box_start('generalbox boxaligncenter');

foreach ($recordingsbns as $bn) {
    // New module data.
    $moduleinfo = new stdClass();
    // Always mandatory generic values to any module.
    $moduleinfo->modulename = 'bigbluebuttonbn';
    $moduleinfo->section = $bn->section; // This is the section number in the course. Not the section id in the database.
    $moduleinfo->course = $bn->course;
    $moduleinfo->groupmode = $bn->groupmode;
    $moduleinfo->groupingid = $bn->groupingid;
    $moduleinfo->visible = $bn->visible;
    $moduleinfo->visibleoncoursepage = 1;
    // Reguired values by BigBlueButtonBN.
    $moduleinfo->type = "2";
    $moduleinfo->name = $bn->name;
    $moduleinfo->recordings_html = $bn->ui_html;
    $moduleinfo->recordings_deleted = $bn->include_deleted_activities;
    $moduleinfo->participants = "[{&quot;selectiontype&quot;:&quot;all&quot;,&quot;selectionid&quot;:&quot;all&quot;,&quot;role&quot;:&quot;viewer&quot;}]";
    // Optional intro editor (depends of module).
    $draftid_editor = 0;
    file_prepare_draft_area($draftid_editor, null, null, null, null);
    $moduleinfo->introeditor = array('text' => '', 'format' => FORMAT_HTML, 'itemid' => $draftid_editor);
    $moduleinfo = create_module($moduleinfo);
}

echo $OUTPUT->box_end();
// Finish the page.
echo $OUTPUT->footer();
