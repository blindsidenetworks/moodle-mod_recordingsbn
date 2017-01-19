<?php
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

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$r = optional_param('r', 0, PARAM_INT);  // recordingsbn instance ID - it should be named as the first character of the module

$action = optional_param('action', 0, PARAM_TEXT);
$recordingid = optional_param('recordingid', 0, PARAM_TEXT);

if ($id) {
    $cm = get_coursemodule_from_id('recordingsbn', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $recordingsbn = $DB->get_record('recordingsbn', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($r) {
    $recordingsbn = $DB->get_record('recordingsbn', array('id' => $r), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $recordingsbn->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('recordingsbn', $recordingsbn->id, $course->id, false, MUST_EXIST);
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$version_major = bigbluebuttonbn_get_moodle_version_major();
if ( $version_major < '2013111800' ) {
    //This is valid before v2.6
    $module = $DB->get_record('modules', array('name' => 'recordingsbn'));
    $module_version = $module->version;
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
} else {
    //This is valid after v2.6
    $module_version = get_config('mod_recordingsbn', 'version');
    $context = context_module::instance($cm->id);
}

$PAGE->set_context($context);

// show some info for guests
if (isguestuser()) {
    $PAGE->set_title(format_string($recordingsbn->name));
    echo $OUTPUT->header();
    echo $OUTPUT->confirm('<p>'.get_string('view_noguests', 'recordingsbn').'</p>'.get_string('liketologin'),
            get_login_url(), $CFG->wwwroot.'/course/view.php?id='.$course->id);

    echo $OUTPUT->footer();
    exit;
}

// Register the event view
if ( $version_major < '2014051200' ) {
    //This is valid before v2.7
    add_to_log($course->id, 'recordingsbn', 'resource viewed', "view.php?id={$cm->id}", $recordingsbn->name, $cm->id);
} else {
    //This is valid after v2.7
    $event = \mod_recordingsbn\event\recordingsbn_resource_page_viewed::create(array('context' => $context, 'objectid' => $recordingsbn->id));
    $event->trigger();
}

//Validates if user has permissions for managing recordings
$bbbsession['administrator'] = has_capability('moodle/category:manage', $context);
$bbbsession['managerecordings'] = (has_capability('moodle/category:manage', $context) || has_capability('mod/bigbluebuttonbn:managerecordings', $context));

//Additional info related to the course
$bbbsession['course'] = $course;
$bbbsession['cm'] = $cm;

// Initialize session variable used across views
$SESSION->bigbluebuttonbn_bbbsession = $bbbsession;

///Set strings to show
$view_no_recordings = get_string('view_no_recordings', 'recordingsbn');

/// Print the page header
$PAGE->set_url($CFG->wwwroot.'/mod/recordingsbn/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($recordingsbn->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->set_cacheable(false);
//$PAGE->set_periodic_refresh_delay(60);

/// Output starts here
echo $OUTPUT->header();

/// Shows version as a comment
echo '
<!-- moodle-mod_recordingsbn ('.$module_version.') -->'."\n";


//Print page headers
echo $OUTPUT->heading($recordingsbn->name, 2);

// Recordings plugin code
$dbman = $DB->get_manager(); // loads ddl manager and xmldb classes
if ($dbman->table_exists('bigbluebuttonbn_logs') ) {
    // BigBlueButton Setup
    $endpoint = bigbluebuttonbn_get_cfg_server_url();
    $shared_secret = bigbluebuttonbn_get_cfg_shared_secret();

    //Execute actions if there is one and it is allowed
    if( !empty($action) && !empty($recordingid) && $bbbsession['managerecordings'] ){
        if( $action == 'show' ) {
            bigbluebuttonbn_doPublishRecordings($recordingid, 'true', $endpoint, $shared_secret);
            if ( $version_major < '2014051200' ) {
                //This is valid before v2.7
                add_to_log($course->id, 'recordingsbn', 'recording published', "", $recordingsbn->name, $cm->id);
            } else {
                //This is valid after v2.7
                $event = \mod_recordingsbn\event\recordingsbn_recording_published::create(array('context' => $context, 'objectid' => $recordingsbn->id, 'other' => array('rid' => $recordingid)));
                $event->trigger();
            }

        } else if( $action == 'hide') {
            bigbluebuttonbn_doPublishRecordings($recordingid, 'false', $endpoint, $shared_secret);
            if ( $version_major < '2014051200' ) {
                //This is valid before v2.7
                add_to_log($course->id, 'recordingsbn', 'recording unpublished', "", $recordingsbn->name, $cm->id);
            } else {
                //This is valid after v2.7
                $event = \mod_recordingsbn\event\recordingsbn_recording_unpublished::create(array('context' => $context, 'objectid' => $recordingsbn->id, 'other' => array('rid' => $recordingid)));
                $event->trigger();
            }

        } else if( $action == 'delete') {
            bigbluebuttonbn_doDeleteRecordings($recordingid, $endpoint, $shared_secret);
            if ( $version_major < '2014051200' ) {
                //This is valid before v2.7
                add_to_log($course->id, 'recordingsbn', 'recording deleted', '', $recordingsbn->name, $cm->id);
            } else {
                //This is valid after v2.7
                $event = \mod_recordingsbn\event\recordingsbn_recording_deleted::create(array('context' => $context,'objectid' => $recordingsbn->id,'other' => array('rid' => $recordingid)));
                $event->trigger();
            }
        }
    }

    $recordings = array();

    // Get recorded meetings
    $results = bigbluebuttonbn_getRecordedMeetings($course->id);

    if( $recordingsbn->include_deleted_activities ) {
        $results_deleted = bigbluebuttonbn_getRecordedMeetingsDeleted($course->id);
        $results = array_merge($results, $results_deleted);
    }

    // Get actual recordings
    if( $results ){
        $mIDs = array();
        //Eliminates duplicates
        foreach ($results as $result) {
            $mIDs[$result->meetingid] = $result->meetingid;
        }

        // If there are mIDs excecute a paginated getRecordings request
        if ( !empty($mIDs) ) {
            $pages = floor(sizeof($mIDs) / 25) + 1;
            for ( $page = 1; $page <= $pages; $page++ ) {
                $meetingIDs = array_slice($mIDs, ($page-1)*25, 25);
                $fetched_recordings = bigbluebuttonbn_getRecordingsArray(implode(',', $meetingIDs), $endpoint, $shared_secret);
                $recordings = array_merge($recordings, $fetched_recordings);
            }
        }
    }

    // Get recording links
    $recordings_imported = bigbluebuttonbn_getRecordingsImportedArray($bbbsession['course']->id);

    // Merge the recordings and recording links 
    $recordings = array_merge( $recordings, $recordings_imported );

    echo "\n".'  <div id="bigbluebuttonbn_html_table">'."\n";
    if ( isset($recordings) && !array_key_exists('messageKey', $recordings)) {  // There are recordings for this meeting
        //If there are meetings with recordings load the data to the table
        if( $recordingsbn->ui_html ) {
            //Shows HTML version.
            $table = bigbluebuttonbn_get_recording_table($bbbsession, $recordings);
            if( isset($table->data) ) {
                //Print the table
                echo html_writer::table($table)."\n";
            }

        } else {
            //Shows YUI version.
            $recordingsbn_columns = bigbluebuttonbn_get_recording_columns($bbbsession, $recordings);
            $recordingsbn_data = bigbluebuttonbn_get_recording_data($bbbsession, $recordings);

            echo '    <div id="recordingsbn_yui_table">'."\n";

            //JavaScript variables
            $jsvars = array(
                    'columns' => $recordingsbn_columns,
                    'data' => $recordingsbn_data
            );
            $PAGE->requires->data_for_js('recordingsbn', $jsvars);

            $jsmodule = array(
                    'name'     => 'mod_recordingsbn',
                    'fullpath' => '/mod/recordingsbn/module.js',
                    'requires' => array('datatable', 'datatable-sort', 'datatable-paginator', 'datatype-number'),
            );
            $PAGE->requires->js_init_call('M.mod_recordingsbn.datatable_init', array(), false, $jsmodule);
            echo '    </div>'."\n";
        }

    } else {
        //There are no recordings to be shown.
        echo '  '.$view_no_recordings."\n";
    }
    echo '  </div>'."\n";

} else {
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');
    print_error(get_string('view_dependency_error', 'recordingsbn'));
    echo $OUTPUT->box_end();
}

//JavaScript variables
$waitformoderator_ping_interval = bigbluebuttonbn_get_cfg_waitformoderator_ping_interval();
list($lang, $locale_encoder) = explode('.', get_string('locale', 'core_langconfig'));
list($locale_code, $locale_sub_code) = explode('_', $lang);
$jsVars = array(
        'ping_interval' => ($waitformoderator_ping_interval > 0? $waitformoderator_ping_interval * 1000: 15000),
        'locales' => bigbluebuttonbn_get_locales_for_ui(),
        'locale' => $locale_code
);

$PAGE->requires->data_for_js('bigbluebuttonbn', $jsVars);

$jsmodule = array(
        'name'     => 'mod_bigbluebuttonbn',
        'fullpath' => '/mod/bigbluebuttonbn/module.js',
        'requires' => array('datasource-get', 'datasource-jsonschema', 'datasource-polling'),
);
$PAGE->requires->js_init_call('M.mod_bigbluebuttonbn.recordingsbn_init', array(), false, $jsmodule);

// Finish the page
echo $OUTPUT->footer();
