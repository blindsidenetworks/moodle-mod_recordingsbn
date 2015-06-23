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
$r  = optional_param('r', 0, PARAM_INT);  // recordingsbn instance ID - it should be named as the first character of the module

$action  = optional_param('action', 0, PARAM_TEXT);
$recordingid  = optional_param('recordingid', 0, PARAM_TEXT);

if ($id) {
    $cm         = get_coursemodule_from_id('recordingsbn', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $recordingsbn  = $DB->get_record('recordingsbn', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($r) {
    $recordingsbn  = $DB->get_record('recordingsbn', array('id' => $r), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $recordingsbn->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('recordingsbn', $recordingsbn->id, $course->id, false, MUST_EXIST);
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

if ( $CFG->version < '2013111800' ) {
    //This is valid before v2.6
    $module = $DB->get_record('modules', array('name' => 'recordingsbn'));
    $module_version = $module->version;
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
} else {
    //This is valid after v2.6
    $module_version = get_config('mod_recordingsbn', 'version');
    $context = context_module::instance($cm->id);
}

if ( $CFG->version < '2014051200' ) {
    //This is valid before v2.7
    add_to_log($course->id, 'recordingsbn', 'resource viewed', "view.php?id={$cm->id}", $recordingsbn->name, $cm->id);
} else {
    //This is valid after v2.7
    $event = \mod_recordingsbn\event\recordingsbn_resource_page_viewed::create(
            array(
                    'context' => $context,
                    'objectid' => $recordingsbn->id
                    )
            );
    $event->trigger();
}

require_login($course, true, $cm);

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

$moderator = has_capability('mod/bigbluebuttonbn:moderate', $context);

///Set strings to show
$view_head_recording = get_string('view_head_recording', 'recordingsbn');
$view_head_course = get_string('view_head_course', 'recordingsbn');
$view_head_activity = get_string('view_head_activity', 'recordingsbn');
$view_head_description = get_string('view_head_description', 'recordingsbn');
$view_head_date = get_string('view_head_date', 'recordingsbn');
$view_head_length = get_string('view_head_length', 'recordingsbn');
$view_head_duration = get_string('view_head_duration', 'recordingsbn');
$view_head_actionbar = get_string('view_head_actionbar', 'recordingsbn');
$view_duration_min = get_string('view_duration_min', 'recordingsbn');

/// Print the page header
$PAGE->set_url($CFG->wwwroot.'/mod/recordingsbn/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($recordingsbn->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'recordingsbn')));
$PAGE->set_context($context);
$PAGE->set_cacheable(false);
//$PAGE->set_periodic_refresh_delay(60);

/// Output starts here
echo $OUTPUT->header();

/// Shows version as a comment
echo '
<!-- moodle-mod_recordingsbn ('.$module_version.') -->'."\n";

///Declare the table
$table = new html_table();

///Initialize table headers
if ( $moderator ) {
    $table->head  = array ($view_head_recording, $view_head_activity, $view_head_description, $view_head_date, $view_head_duration, $view_head_actionbar);
    $table->align = array ('left', 'left', 'left', 'left', 'center', 'left');
    $recordingsbn_columns = array(
            array("key" =>"recording", "label" => $view_head_recording, "width" => "125px", "allowHTML" => true),
            array("key" =>"activity", "label" => $view_head_activity, "sortable" => true, "width" => "175px"),
            array("key" =>"description", "label" => $view_head_description, "sortable" => true, "width" => "250px"),
            array("key" =>"date", "label" => $view_head_date, "sortable" => true, "width" => "220px"),
            array("key" =>"duration", "label" => $view_head_duration, "width" => "50px"),
            array("key" =>"actionbar", "label" => $view_head_actionbar, "width" => "75px", "allowHTML" => true)
            );
} else {
    $table->head  = array ($view_head_recording, $view_head_activity, $view_head_description, $view_head_date, $view_head_duration);
    $table->align = array ('left', 'left', 'left', 'left', 'center');
    $recordingsbn_columns = array(
            array("key" =>"recording", "label" => $view_head_recording, "width" => "125px", "allowHTML" => true),
            array("key" =>"activity", "label" => $view_head_activity, "sortable" => true, "width" => "175px"),
            array("key" =>"description", "label" => $view_head_description, "sortable" => true, "width" => "250px"),
            array("key" =>"date", "label" => $view_head_date, "sortable" => true, "width" => "220px"),
            array("key" =>"duration", "label" => $view_head_duration, "width" => "50px")
            );
}

///Initialize table data
$recordingsbn_data = array();

//Print page headers
echo $OUTPUT->heading($recordingsbn->name, 2);

// Recordings plugin code
$dbman = $DB->get_manager(); // loads ddl manager and xmldb classes
if ($dbman->table_exists('bigbluebuttonbn_log') ) {
    // BigBlueButton Setup
    if( isset($CFG->bigbluebuttonbn_server_url) ) {
        $url = trim(trim($CFG->bigbluebuttonbn_server_url),'/').'/';
        $shared_secret = trim($CFG->bigbluebuttonbn_shared_secret);
    } else {
        $url = trim(trim($CFG->BigBlueButtonBNServerURL),'/').'/';
        $shared_secret = trim($CFG->BigBlueButtonBNSecuritySalt);
    }

    //Execute actions if there is one and it is allowed
    if( isset($action) && isset($recordingid) && $moderator ){
        if( $action == 'show' ) {
            bigbluebuttonbn_doPublishRecordings($recordingid, 'true', $url, $shared_secret);
            if ( $CFG->version < '2014051200' ) {
                //This is valid before v2.7
                add_to_log($course->id, 'recordingsbn', 'recording published', "", $recordingsbn->name, $cm->id);
            } else {
                //This is valid after v2.7
                $event = \mod_recordingsbn\event\recordingsbn_recording_published::create(
                        array(
                                'context' => $context,
                                'objectid' => $recordingsbn->id,
                                'other' => array(
                                        //'title' => $title,
                                        'rid' => $recordingid
                                        )
                        )
                );
                $event->trigger();
            }
        } else if( $action == 'hide') {
            bigbluebuttonbn_doPublishRecordings($recordingid, 'false', $url, $shared_secret);
            if ( $CFG->version < '2014051200' ) {
                //This is valid before v2.7
                add_to_log($course->id, 'recordingsbn', 'recording unpublished', "", $recordingsbn->name, $cm->id);
            } else {
                //This is valid after v2.7
                $event = \mod_recordingsbn\event\recordingsbn_recording_unpublished::create(
                        array(
                                'context' => $context,
                                'objectid' => $recordingsbn->id,
                                'other' => array(
                                        //'title' => $title,
                                        'rid' => $recordingid
                                        )
                        )
                );
                $event->trigger();
            }
        } else if( $action == 'delete') {
            bigbluebuttonbn_doDeleteRecordings($recordingid, $url, $shared_secret);
            if ( $CFG->version < '2014051200' ) {
                //This is valid before v2.7
                add_to_log($course->id, 'recordingsbn', 'recording deleted', '', $recordingsbn->name, $cm->id);
            } else {
                //This is valid after v2.7
                $event = \mod_recordingsbn\event\recordingsbn_recording_deleted::create(
                        array(
                                'context' => $context,
                                'objectid' => $recordingsbn->id,
                                'other' => array(
                                        //'title' => $title,
                                        'rid' => $recordingid
                                        )
                        )
                );
                $event->trigger();
            }
        }
    }

    $meetingID='';
    $results = $DB->get_records('bigbluebuttonbn_log', array('courseid' => $course->id, 'record' => 1, 'event' => 'Create'));
    if( $results ){
        //Eliminates duplicates
        $mIDs = array();
        foreach ($results as $result) {
            $mIDs[$result->meetingid] = $result->meetingid;
        }
        //Generates the meetingID string
        foreach ($mIDs as $mID) {
            if (strlen($meetingID) > 0) $meetingID .= ',';
            $meetingID .= $mID;
        }
    }

    //If there are meetings with recordings load the data to the table
    if ( $meetingID != '' ){
        $recordingsbn = bigbluebuttonbn_getRecordingsArray($meetingID, $url, $shared_secret);

        if( isset($recordingsbn) && !isset($recordingsbn['messageKey']) ){
            foreach ( $recordingsbn as $recording ){
                if ( $moderator || $recording['published'] == 'true' ) {

                    $length = 0;
                    $endTime = isset($recording['endTime'])? floatval($recording['endTime']):0;
                    $endTime = $endTime - ($endTime % 1000);
                    $startTime = isset($recording['startTime'])? floatval($recording['startTime']):0;
                    $startTime = $startTime - ($startTime % 1000);
                    $duration = intval(($endTime - $startTime) / 60000);

                    //$meta_course = isset($recording['meta_context'])?str_replace('"', '\"', $recording['meta_context']):'';
                    $meta_activity = isset($recording['meta_contextactivity'])?str_replace('"', '\"', $recording['meta_contextactivity']):'';
                    $meta_description = isset($recording['meta_contextactivitydescription'])?str_replace('"', '\"', $recording['meta_contextactivitydescription']):'';

                    $actionbar = '';
                    $params['id'] = $cm->id;
                    $params['recordingid'] = $recording['recordID'];
                    if ( $moderator ) {
                        ///Set action [show|hide]
                        if ( $recording['published'] == 'true' ){
                            $params['action'] = 'hide';
                        } else {
                            $params['action'] = 'show';
                        }
                        $url = new moodle_url('/mod/recordingsbn/view.php', $params);
                        $action = null;
                        //With text
                        //$actionbar .= $OUTPUT->action_link(  $link, get_string( $params['action'] ), $action, array( 'title' => get_string($params['action'] ) )  );
                        //With icon
                        $attributes = array('title' => get_string($params['action']));
                        $icon = new pix_icon('t/'.$params['action'], get_string($params['action']), 'moodle', $attributes);
                        $actionbar .= $OUTPUT->action_icon($url, $icon, $action, $attributes, false);

                        ///Set action delete
                        $params['action'] = 'delete';
                        $url = new moodle_url('/mod/recordingsbn/view.php', $params);
                        $action = new component_action('click', 'M.util.show_confirm_dialog', array('message' => get_string('view_delete_confirmation', 'recordingsbn')));
                        //With text
                        //$actionbar .= $OUTPUT->action_link(  $link, get_string( $params['action'] ), $action, array( 'title' => get_string($params['action']) )  );
                        //With icon
                        $attributes = array('title' => get_string($params['action']));
                        $icon = new pix_icon('t/'.$params['action'], get_string($params['action']), 'moodle', $attributes);
                        $actionbar .= $OUTPUT->action_icon($url, $icon, $action, $attributes, false);

                    }

                    $type = '';
                    foreach ( $recording['playbacks'] as $playback ){
                    	if ($recording['published'] == 'true'){
                    		$type .= $OUTPUT->action_link($playback['url'], $playback['type'], null, array('title' => $playback['type'], 'target' => '_new') ).'&#32;';
                    	} else {
                    		$type .= $playback['type'].'&#32;';
                    	}
                    }

                    //Make sure the startTime is timestamp
                    if( !is_numeric($recording['startTime']) ){
                    	$date = new DateTime($recording['startTime']);
                    	$recording['startTime'] = date_timestamp_get($date);
                    } else {
                    	$recording['startTime'] = $recording['startTime'] / 1000;
                    }
                    //Set corresponding format
                    $format = get_string('strftimerecentfull', 'langconfig');
                    if( isset($format) ) {
                        $formatedStartDate = userdate($recording['startTime'], $format);
                    } else {
                        $format = '%a %h %d, %Y %H:%M:%S %Z';
                        $formatedStartDate = userdate($recording['startTime'], $format, usertimezone($USER->timezone) );
                    }

                    if ( $moderator ) {
                        $table->data[] = array ($type, $meta_activity, $meta_description, str_replace(" ", "&nbsp;", $formatedStartDate), $duration, $actionbar );
                        $recordingsbn_data_item = array(
                                "recording" => $type,
                                "activity" => $meta_activity,
                                "description" => $meta_description,
                                "date" => $formatedStartDate,
                                "duration" => $duration." ".$view_duration_min,
                                "actionbar" => $actionbar

                        );

                    } else {
                        $table->data[] = array ($type, $meta_activity, $meta_description, str_replace(" ", "&nbsp;", $formatedStartDate), $duration);
                        $recordingsbn_data_item = array(
                                "recording" => $type,
                                "activity" => $meta_activity,
                                "description" => $meta_description,
                                "date" => $formatedStartDate,
                                "duration" => $duration." ".$view_duration_min
                        );
                    }
                    array_push($recordingsbn_data, $recordingsbn_data_item);

                }
            }
        }
    }


    //Print the table
    if (isset($CFG->recordingsbn_ui) && strtolower($CFG->recordingsbn_ui) == 'yui'  && $CFG->version >= '2012062500' ) {
        //Shows javascript YUI version.
        echo '
        <style type="text/css">
            #recordingsbn_yui_paginator {
                margin-top: 15px;
                margin-bottom: 10px;
            }
        </style>'."\n";

        echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/mod/recordingsbn/yui/paginatorview/assets/paginatorview-core.css" />'."\n";
        echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/mod/recordingsbn/yui/paginatorview/assets/skins/sam/paginatorview-skin.css" />'."\n";

        echo $OUTPUT->box_start('generalbox boxaligncenter', 'recordingsbn_box')."\n";
        echo '<div class="yui3-skin-sam">'."\n";
        echo '  <div id="recordingsbn_yui_paginator"></div>'."\n";
        echo '  <div id="recordingsbn_yui_table"></div>'."\n";
        echo '</div>'."\n";
        echo $OUTPUT->box_end();

        $gallery_datatable_paginator = array(
                'name'      => 'datatablepaginator',
                'fullpath'  => '/mod/recordingsbn/yui/datatablepaginator/datatablepaginator.js'
        );
        $PAGE->requires->js_module($gallery_datatable_paginator);

        $gallery_paginator_view = array(
                'name'      => 'paginatorview',
                'fullpath'  => '/mod/recordingsbn/yui/paginatorview/paginatorview.js'
        );
        $PAGE->requires->js_module($gallery_paginator_view);

        //JavaScript variables
        $jsvars = array(
                'columns' => $recordingsbn_columns,
                'data' => $recordingsbn_data
        );
        $PAGE->requires->data_for_js('recordingsbn', $jsvars);

        $jsmodule = array(
                'name'     => 'mod_recordingsbn',
                'fullpath' => '/mod/recordingsbn/module.js',
                'requires' => array('datatable-sort', 'datasource-get', 'datasource-jsonschema', 'datasource-polling', 'datatablepaginator', 'paginatorview'),
        );
        $PAGE->requires->js_init_call('M.mod_recordingsbn.gallery_datatable_init', array(), false, $jsmodule);

    } else {
        //Shows HTML version.
        echo $OUTPUT->box_start('generalbox boxaligncenter', 'recordingsbn_box')."\n";
        echo '<div id="recordingsbn_html_table">'."\n";
        echo html_writer::table($table)."\n";
        echo '</div>'."\n";
        echo $OUTPUT->box_end();
    }

} else {
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');
    print_error(get_string('view_dependency_error', 'recordingsbn'));
    echo $OUTPUT->box_end();

}

// Finish the page
echo $OUTPUT->footer();