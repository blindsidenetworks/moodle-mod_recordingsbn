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

$id = optional_param('id', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('recordingsbn', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $recordingsbn = $DB->get_record('recordingsbn', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$versionmajor = recordingsbn_get_moodle_version_major();
if ( $versionmajor < '2013111800' ) {
    // This is valid before v2.6.
    $dependency = $DB->get_record('modules', array('name' => 'bigbluebuttonbn'));
    $dependencyversion = $dependency->version;
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
} else {
    // This is valid after v2.6.
    $dependencyversion = get_config('mod_bigbluebuttonbn', 'version');
    $context = context_module::instance($cm->id);
}

// Print the page header.
$PAGE->set_url($CFG->wwwroot.'/mod/recordingsbn/migration.php', array('id' => $cm->id));

// Output starts here.
echo $OUTPUT->header();

// Only Admins are allowed.
if (!has_capability('moodle/category:manage', $context)) {
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');
    //print_error(get_string('view_dependency_error', 'recordingsbn'));
    print_error('Only admin users are allowed to run migrations');
    echo $OUTPUT->box_end();
}

// Recordings plugin code.
$dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
if (!$dbman->table_exists('bigbluebuttonbn_logs')) {
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');
    print_error(get_string('view_dependency_error', 'recordingsbn'));
    echo $OUTPUT->box_end();
}

// Proceed with the migration
$courses = get_courses('all', 'c.fullname ASC', 'c.id,c.shortname,c.fullname');
//error_log(json_encode($courses));
$bns = get_all_instances_in_course('bigbluebuttonbn', $course);
//error_log(json_encode($bns));

if (! $recordingsbns = get_all_instances_in_courses('recordingsbn', $courses)) {
    echo $OUTPUT->heading(get_string('norecordingsbns', 'recordingsbn'), 2);
    echo $OUTPUT->continue_button("view.php?id=$course->id");
    echo $OUTPUT->footer();
    die();
}

//error_log(json_encode($recordingsbns));
foreach ($recordingsbns as $bn) {
    error_log(json_encode($bn));
    // Module test values.
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

    $modluleinfo = create_module($moduleinfo);
    error_log(json_encode($modluleinfo));
}



// Finish the page.
echo $OUTPUT->footer();

function bigbluebuttonbn_new($bn) {
    global $CFG;
/*
    $data = array(
        "type" => "2",
        "name" => $bn->name,
        "showdescription" => "0",
        "welcome" => "",
        "voicebridge" => "0",
        "userlimit" => $CFG->bigbluebuttonbn['userlimit_default'],
        "record" => $CFG->bigbluebuttonbn['recording_default'] == 'true' ? 1 : 0,
        "recordings_html" => (int)$bn->ui_html,
        "recordings_deleted" => (int)$bn->include_deleted_activities,
        "recordings_preview" => $CFG->bigbluebuttonbn['recordings_preview'] == 'true' ? 1 : 0,
        "mform_isexpanded_id_preuploadpresentation" => 1,
        "presentation" => null,
        "mform_isexpanded_id_permissions" => 1,
        "participants" => "[{&quot;selectiontype&quot;:&quot;all&quot;,&quot;selectionid&quot;:&quot;all&quot;,&quot;role&quot;:&quot;viewer&quot;}]",
        "openingtime" => 0,
        "closingtime" => 0,
        "visible" => (int)$bn->visible,
        "visibleoncoursepage" => 1,
        "cmidnumber" => "",
        "groupmode" => $bn->groupmode,
        "groupingid" => $bn->groupingid,
        "availabilityconditionsjson" => "{\\"op\\":\\"&\\",\\"c\\":[],\\"showc\\":[]}",
        "tags" => [],
        "course" => $bn->course,
        ////"coursemodule" => 40, // This is an assigned consecutive
        "section" => (int)$bn->section,
        ////"module" => 23,  // This is constant
        "modulename" => "bigbluebuttonbn",
        "instance" => "",
        "add" => "bigbluebuttonbn",
        "update" => 0,
        "return" => 0,
        "sr" => 0,
        "competency_rule" => "0",
        "submitbutton" => "Save and display",
        "completion" => 0,
        "completionview" => 0,
        "completionexpected" => 0,
        "completiongradeitemnumber" => null,
        "conditiongradegroup" => [],
        "conditionfieldgroup" => [],
        "intro" => "",
        "introformat" => "1"
    );
*/
}
