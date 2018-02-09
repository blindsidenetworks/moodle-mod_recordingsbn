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
 * @copyright 2011-2018 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot.'/mod/bigbluebuttonbn/locallib.php');

$id = optional_param('id', 0, PARAM_INT);
$r = optional_param('r', 0, PARAM_INT);

$action = optional_param('action', 0, PARAM_TEXT);
$recordingid = optional_param('recordingid', 0, PARAM_TEXT);

if ($id) {
    $cm = get_coursemodule_from_id('recordingsbn', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $recordingsbn = $DB->get_record('recordingsbn', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($r) {
    $recordingsbn = $DB->get_record('recordingsbn', array('id' => $r), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $recordingsbn->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('recordingsbn', $recordingsbn->id, $course->id, false, MUST_EXIST);
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$versionmajor = recordingsbn_get_moodle_version_major();
if ( $versionmajor < '2013111800' ) {
    // This is valid before v2.6.
    $module = $DB->get_record('modules', array('name' => 'recordingsbn'));
    $moduleversion = $module->version;
    $dependency = $DB->get_record('modules', array('name' => 'bigbluebuttonbn'));
    $dependencyversion = $dependency->version;
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
} else {
    // This is valid after v2.6.
    $moduleversion = get_config('mod_recordingsbn', 'version');
    $dependencyversion = get_config('mod_bigbluebuttonbn', 'version');
    $context = context_module::instance($cm->id);
}

$PAGE->set_context($context);

// Register the event view.
if ( $versionmajor < '2014051200' ) {
    // This is valid before v2.7.
    add_to_log($course->id, 'recordingsbn', 'resource viewed', "view.php?id={$cm->id}", $recordingsbn->name, $cm->id);
} else {
    // This is valid after v2.7.
    $event = \mod_recordingsbn\event\recordingsbn_resource_page_viewed::create(
        array('context' => $context, 'objectid' => $recordingsbn->id));
    $event->trigger();
}

// Validates if user has permissions for managing recordings.
$bbbsession['administrator'] = has_capability('moodle/category:manage', $context);
$bbbsession['managerecordings'] = (has_capability('moodle/category:manage', $context) ||
    has_capability('mod/bigbluebuttonbn:managerecordings', $context));

// Additional info related to the course.
$bbbsession['course'] = $course;
$bbbsession['cm'] = $cm;

// Initialize session variable used across views.
$SESSION->bigbluebuttonbn_bbbsession = $bbbsession;

// Print the page header.
$PAGE->set_url($CFG->wwwroot.'/mod/recordingsbn/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($recordingsbn->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->set_cacheable(false);

// This module was deprecated when BigBlueButtonBN 2.2 was released (2017101000).
if ($dependencyversion >= '2017101000') {
    $PAGE->set_title(format_string($recordingsbn->name));
    echo $OUTPUT->header();
    echo $OUTPUT->box_start('generalbox boxaligncenter');
    recordingsbn_view_deprecation_messages($bbbsession, $dependencyversion);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

// Show some info for guests.
if (isguestuser()) {
    $PAGE->set_title(format_string($recordingsbn->name));
    echo $OUTPUT->header();
    echo $OUTPUT->confirm('<p>'.get_string('view_noguests', 'recordingsbn').'</p>'.get_string('liketologin'),
            get_login_url(), $CFG->wwwroot.'/course/view.php?id='.$course->id);
    echo $OUTPUT->footer();
    exit;
}

// Output starts here.
echo $OUTPUT->header();

// Shows version as a comment.
echo "\n" . '<!-- moodle-mod_recordingsbn (' . $moduleversion . ') -->' . "\n";

// Print page headers.
echo $OUTPUT->heading($recordingsbn->name, 2);

// Recordings plugin code.
$dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
if (!$dbman->table_exists('bigbluebuttonbn_logs')) {
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');
    print_error(get_string('view_dependency_error', 'recordingsbn'));
    echo $OUTPUT->box_end();
}

// BigBlueButton Setup.
$endpoint = bigbluebuttonbn_get_cfg_server_url();
$sharedsecret = bigbluebuttonbn_get_cfg_shared_secret();

// Execute actions if there is one and it is allowed.
if (!empty($action) && !empty($recordingid) && $bbbsession['managerecordings']) {
    if ($action == 'show') {
        bigbluebuttonbn_doPublishRecordings($recordingid, 'true', $endpoint, $sharedsecret);
        if ($versionmajor < '2014051200') {
            // This is valid before v2.7.
            add_to_log($course->id, 'recordingsbn', 'recording published', "", $recordingsbn->name, $cm->id);
        } else {
            // This is valid after v2.7.
            $event = \mod_recordingsbn\event\recordingsbn_recording_published::create(
                array('context' => $context, 'objectid' => $recordingsbn->id, 'other' => array('rid' => $recordingid)));
            $event->trigger();
        }

    } else if ($action == 'hide') {
        bigbluebuttonbn_doPublishRecordings($recordingid, 'false', $endpoint, $sharedsecret);
        if ( $versionmajor < '2014051200' ) {
            // This is valid before v2.7.
            add_to_log($course->id, 'recordingsbn', 'recording unpublished', "", $recordingsbn->name, $cm->id);
        } else {
            // This is valid after v2.7.
            $event = \mod_recordingsbn\event\recordingsbn_recording_unpublished::create(
                array('context' => $context, 'objectid' => $recordingsbn->id, 'other' => array('rid' => $recordingid)));
            $event->trigger();
        }

    } else if ($action == 'delete') {
        bigbluebuttonbn_doDeleteRecordings($recordingid, $endpoint, $sharedsecret);
        if ($versionmajor < '2014051200' ) {
            // This is valid before v2.7.
            add_to_log($course->id, 'recordingsbn', 'recording deleted', '', $recordingsbn->name, $cm->id);
        } else {
            // This is valid after v2.7.
            $event = \mod_recordingsbn\event\recordingsbn_recording_deleted::create(
                array('context' => $context, 'objectid' => $recordingsbn->id, 'other' => array('rid' => $recordingid)));
            $event->trigger();
        }
    }
}

$recordings = array();

// Get recorded meetings.
$results = bigbluebuttonbn_getRecordedMeetings($course->id);

if ($recordingsbn->include_deleted_activities) {
    $resultsdeleted = bigbluebuttonbn_getRecordedMeetingsDeleted($course->id);
    $results = array_merge($results, $resultsdeleted);
}

// Get actual recordings.
if ($results) {
    $mids = array();
    // Eliminates duplicates.
    foreach ($results as $result) {
        $mids[$result->meetingid] = $result->meetingid;
    }

    // If there are mIDs excecute a paginated getRecordings request.
    if (!empty($mids)) {
        $pages = floor(count($mids) / 25) + 1;
        for ($page = 1; $page <= $pages; $page++) {
            $meetingids = array_slice($mids, ($page - 1) * 25, 25);
            $fetchedrecordings = bigbluebuttonbn_getRecordingsArray(implode(',', $meetingids), $endpoint, $sharedsecret);
            $recordings = array_merge($recordings, $fetchedrecordings);
        }
    }
}

// Get recording links.
$recordingsimported = bigbluebuttonbn_getRecordingsImportedArray($bbbsession['course']->id);

// Merge the recordings and recording links.
$recordings = array_merge($recordings, $recordingsimported);

echo "\n".'  <div id="bigbluebuttonbn_html_table">'."\n";
if (isset($recordings) && !array_key_exists('messageKey', $recordings)) {  // There are recordings for this meeting
    // If there are meetings with recordings load the data to the table.
    if ($recordingsbn->ui_html) {
        // Shows HTML version.
        $table = bigbluebuttonbn_get_recording_table($bbbsession, $recordings);
        if (isset($table->data)) {
            // Print the table.
            echo html_writer::table($table)."\n";
        }
    } else {
        // Shows YUI version.
        $columns = bigbluebuttonbn_get_recording_columns($bbbsession, $recordings);
        $data = bigbluebuttonbn_get_recording_data($bbbsession, $recordings);

        echo '    <div id="recordingsbn_yui_table">'."\n";

        // JavaScript variables.
        $jsvars = array(
                'columns' => $columns,
                'data' => $data
        );
        $PAGE->requires->data_for_js('recordingsbn', $jsvars);

        $jsmodule = array(
                'name'     => 'mod_recordingsbn',
                'fullpath' => '/mod/recordingsbn/module.js',
                'requires' => array('datatable', 'datatable-sort', 'datatable-paginator', 'datatype-number'),
        );
        $PAGE->requires->js_init_call('M.mod_recordingsbn.datatableInit', array(), false, $jsmodule);
        echo '    </div>'."\n";
    }

} else {
    // There are no recordings to be shown.
    echo '  '.get_string('view_no_recordings', 'recordingsbn')."\n";
}
echo '  </div>'."\n";


// JavaScript variables.
$pinginterval = bigbluebuttonbn_get_cfg_waitformoderator_ping_interval();
list($lang, $localeencoder) = explode('.', get_string('locale', 'core_langconfig'));
list($localecode, $localesubcode) = explode('_', $lang);
$jsvars = array(
        'ping_interval' => ($pinginterval > 0 ? $pinginterval * 1000 : 15000),
        'locales' => bigbluebuttonbn_get_locales_for_ui(),
        'locale' => $localecode
);

$PAGE->requires->data_for_js('bigbluebuttonbn', $jsvars);

$jsmodule = array(
    'name'     => 'mod_bigbluebuttonbn',
    'fullpath' => '/mod/bigbluebuttonbn/module.js',
    'requires' => array('datasource-get', 'datasource-jsonschema', 'datasource-polling'));
$PAGE->requires->js_init_call('M.mod_bigbluebuttonbn.recordingsbn_init', array(), false, $jsmodule);

// Finish the page.
echo $OUTPUT->footer();


/**
 * Renders deprecation message.
 *
 * @param array $bbbsession
 * @param string $dependencyversion
 *
 * @return string
 */
function recordingsbn_view_deprecation_messages($bbbsession, $dependencyversion) {
    global $CFG;
    $message = get_string('view_deprecated_msg_user', 'recordingsbn');
    $info = get_string('view_deprecated_info_user', 'recordingsbn');
    if ($bbbsession['administrator'] || $bbbsession['managerecordings']) {
        $message = get_string('view_deprecated_msg_admin', 'recordingsbn');
        $info = get_string('view_deprecated_info_admin', 'recordingsbn');
    }
    if ($dependencyversion < '2017101009') {
        echo '<br><div class="alert alert-danger">' . $message . '</div>';
        echo '<br><div class="alert alert-info">' . $info . '</div>';
        return;
    }
    // Implements the use of new functions and option for migration.
    echo bigbluebuttonbn_render_warning($message, 'danger');
    echo bigbluebuttonbn_render_warning($info, 'info');
}
