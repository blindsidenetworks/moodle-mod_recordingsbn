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
 * @copyright 2018 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

require_login();
$syscontext = context_system::instance();
require_capability('moodle/site:config', $syscontext);

$dependencyversion = recordingsbn_get_dependency_version();
if ($dependencyversion < '2017101000') {
    print_error(get_string('migration_error_dependency', 'recordingsbn'));
}


// Print the page header.
$PAGE->set_context($syscontext);
$PAGE->set_url($CFG->wwwroot.'/mod/recordingsbn/migration.php');
$PAGE->set_title(get_string('modulename', 'recordingsbn') . ' ' . get_string('migration_data_migration', 'recordingsbn'));
$PAGE->set_heading(get_string('modulename', 'recordingsbn') . ' ' . get_string('migration_data_migration', 'recordingsbn'));
$PAGE->set_pagelayout('maintenance');


// Output starts here.
echo $OUTPUT->header();

// Recordings plugin code.
$dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
if (!$dbman->table_exists('bigbluebuttonbn_logs')) {
    print_error(get_string('view_dependency_error', 'recordingsbn'));
}

// Proceed with the migration.
$courses = get_courses('all', 'c.fullname ASC', 'c.id,c.shortname,c.fullname');
if (!$recordingsbns = get_all_instances_in_courses('recordingsbn', $courses)) {
    echo bigbluebuttonbn_render_warning(get_string('norecordingsbns', 'recordingsbn'), 'warning');
    // Finish the page.
    echo $OUTPUT->footer();
    die();
}

echo $OUTPUT->box_start('generalbox boxaligncenter adminerror alert alert-info');
echo get_string('migration_status_inprocess', 'recordingsbn');
echo $OUTPUT->box_end();

echo html_writer::start_tag('div') . "\n";
echo html_writer::start_tag('table', array('class' => 'table')) . "\n";
echo html_writer::start_tag('thead', array()) . "\n";
echo html_writer::tag('th', 'Course', array()) . "\n";
echo html_writer::tag('th', 'Resource', array()) . "\n";
echo html_writer::tag('th', 'Status', array()) . "\n";
echo html_writer::tag('th', 'Migration', array()) . "\n";
echo html_writer::end_tag('thead') . "\n";
echo html_writer::start_tag('tbody', array('class' => 'table-striped')) . "\n";

foreach ($courses as $course) {
    $sources = get_all_instances_in_course('recordingsbn', $course);
    $indexedbns = recordingsbn_index_bigbluebuttonbn_instances(
      get_all_instances_in_course('bigbluebuttonbn', $course), 'name');
    foreach ($sources as $source) {
        // New module data.
        $moduleinfo = new stdClass();
        // Always mandatory generic values to any module.
        $moduleinfo->modulename = 'bigbluebuttonbn';
        $moduleinfo->section = $source->section; // This is the section number in the course. Not the section id in the database.
        $moduleinfo->course = $source->course;
        $moduleinfo->groupmode = $source->groupmode;
        $moduleinfo->groupingid = $source->groupingid;
        $moduleinfo->visible = $source->visible;
        $moduleinfo->visibleoncoursepage = 1;
        // Reguired values by BigBlueButtonBN.
        $moduleinfo->type = "2";
        $moduleinfo->name = $source->name;
        $moduleinfo->recordings_html = $source->ui_html;
        $moduleinfo->recordings_deleted = $source->include_deleted_activities;
        $moduleinfo->participants = "[{&quot;selectiontype&quot;:&quot;all&quot;,&quot;selectionid&quot;:&quot;all&quot;,&quot;role&quot;:&quot;viewer&quot;}]";
        // Optional intro editor (depends of module).
        $draftideditor = 0;
        file_prepare_draft_area($draftideditor, null, null, null, null);
        $moduleinfo->introeditor = array('text' => '', 'format' => FORMAT_HTML, 'itemid' => $draftideditor);
        echo html_writer::start_tag('tr') . "\n";
        echo html_writer::tag('td', $course->fullname, array('class' => 'cell')) . "\n";
        echo html_writer::tag('td', $source->name, array('class' => 'cell')) . "\n";
        if (array_key_exists($source->name, $indexedbns)) {
            echo html_writer::tag('td',
                html_writer::tag('span',
                  get_string('migration_status_found', 'recordingsbn'),
                    array('class' => 'text text-danger')),
                array('class' => 'cell')) . "\n";
            echo html_writer::tag('td',
                html_writer::tag('span',
                    get_string('migration_status_skipped', 'recordingsbn'),
                    array('class' => 'label label-warning')),
                array('class' => 'cell')) . "\n";
        } else {
            echo html_writer::tag('td',
                html_writer::tag('span',
                    get_string('migration_status_notfound', 'recordingsbn'),
                    array('class' => 'text text-info')),
                array('class' => 'cell')) . "\n";
            $moduleinfo = create_module($moduleinfo);
            echo html_writer::tag('td',
                html_writer::tag('span',
                    get_string('migration_status_migrated', 'recordingsbn'),
                    array('class' => 'label label-success')),
                array('class' => 'cell')) . "\n";
        }
        echo html_writer::end_tag('tr') . "\n";
    }
}
echo html_writer::end_tag('tbody') . "\n";
echo html_writer::end_tag('table') . "\n";
echo html_writer::end_tag('div') . "\n";

echo $OUTPUT->box_start('generalbox boxaligncenter adminerror alert alert-success');
echo get_string('migration_status_completed', 'recordingsbn');
echo $OUTPUT->box_end();

echo $OUTPUT->single_button(new moodle_url('/index.php'), get_string('ok', 'moodle'), 'get');

// Finish the page.
echo $OUTPUT->footer();

/**
 * Index the bigbluebutton instances based on a key.
 *
 * @param array $bigbluebuttonbns
 * @param string $key
 *
 * @return array
 */
function recordingsbn_index_bigbluebuttonbn_instances($bigbluebuttonbns, $key) {
    $indexedbns = array();
    foreach ($bigbluebuttonbns as $bn) {
        $indexedbns[$bn->$key] = $bn;
    }
    return $indexedbns;
}
