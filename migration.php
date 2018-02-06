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
require_once($CFG->dirroot.'/mod/bigbluebuttonbn/locallib.php');

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

// Finish the page.
echo $OUTPUT->footer();
