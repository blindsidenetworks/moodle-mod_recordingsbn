<?php
/**
 * View and administrate BigBlueButton playback recordings
 *
 * Authors:
 *    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 *
 * @package   mod_recordingsbn
 * @copyright 2011-2012 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot.'/mod/bigbluebuttonbn/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // recordingsbn instance ID - it should be named as the first character of the module

$action  = optional_param('action', 0, PARAM_TEXT);
$recordingid  = optional_param('recordingid', 0, PARAM_TEXT);
$cid  = optional_param('cid', 0, PARAM_INT);


if ($id) {
    $cm         = get_coursemodule_from_id('recordingsbn', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $recordingsbn  = $DB->get_record('recordingsbn', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $recordingsbn  = $DB->get_record('recordingsbn', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $recordingsbn->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('recordingsbn', $recordingsbn->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
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

add_to_log($course->id, 'recordingsbn', 'view', "view.php?id={$cm->id}", $recordingsbn->name, $cm->id);

//Set strings to show
$view_head_recording = get_string('view_recording_list_recording', 'bigbluebuttonbn');
$view_head_course = get_string('view_recording_list_course', 'bigbluebuttonbn');
$view_head_activity = get_string('view_recording_list_activity', 'bigbluebuttonbn');
$view_head_description = get_string('view_recording_list_description', 'bigbluebuttonbn');
$view_head_date = get_string('view_recording_list_date', 'bigbluebuttonbn');
$view_head_duration = get_string('view_recording_list_duration', 'bigbluebuttonbn');
$view_head_actionbar = get_string('view_recording_list_actionbar', 'bigbluebuttonbn');
$view_hint_actionbar_hide = get_string('view_recording_list_actionbar_hide', 'bigbluebuttonbn');
$view_hint_actionbar_show = get_string('view_recording_list_actionbar_show', 'bigbluebuttonbn');
$view_hint_actionbar_delete = get_string('view_recording_list_actionbar_delete', 'bigbluebuttonbn');

/// Print the page header
$PAGE->set_url($CFG->wwwroot.'/mod/recordingsbn/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($recordingsbn->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'recordingsbn')));
$PAGE->set_context($context);
$PAGE->set_cacheable(false);

//$PAGE->requires->js('/mod/recordingsbn/js/libs/jquery/1.7.2/jquery.min.js');    
$PAGE->requires->js('/mod/recordingsbn/js/recordingsbn.js');    
//echo '<script type="text/javascript" >var wwwroot = "'.$CFG->wwwroot.'";</script>'."\n";
//echo '<script type="text/javascript" >var view_hint_actionbar_hide = "'.$view_hint_actionbar_hide.'";</script>'."\n";
//echo '<script type="text/javascript" >var view_hint_actionbar_show = "'.$view_hint_actionbar_show.'";</script>'."\n";
//echo '<script type="text/javascript" >var view_hint_actionbar_delete = "'.$view_hint_actionbar_delete.'";</script>'."\n";

// Output starts here
echo $OUTPUT->header();

//Declare the table
$table = new html_table();

//Initialize table headers
$table->head  = array ($view_head_recording, $view_head_course, $view_head_activity, $view_head_description, $view_head_date, $view_head_duration, $view_head_actionbar);
$table->align = array ('center', 'center', 'center', 'center', 'center', 'center', 'left');

//Print page headers
echo $OUTPUT->heading(get_string('modulenameplural', 'recordingsbn'), 2);


// Recordings plugin code
$dbman = $DB->get_manager(); // loads ddl manager and xmldb classes
if ($dbman->table_exists('bigbluebuttonbn_log') ) {
    // BigBlueButton Setup
    $salt = trim($CFG->BigBlueButtonBNSecuritySalt);
    $url = trim(trim($CFG->BigBlueButtonBNServerURL),'/').'/';
    $logoutURL = $CFG->wwwroot;
    $username = $USER->firstname.' '.$USER->lastname;
    $userID = $USER->id;

    //Execute actions if there is one and it is allowed
    if( isset($action) && isset($recordingid) && $moderator ){
        if( $action == 'publish' )
            bigbluebuttonbn_doPublishRecordings($recordingid, 'true', $url, $salt);
        else if( $action == 'unpublish')
            bigbluebuttonbn_doPublishRecordings($recordingid, 'false', $url, $salt);
        else if( $action == 'delete')
            bigbluebuttonbn_doDeleteRecordings($recordingid, $url, $salt);
    }
    
    
    $meetingID='';
    $results = $DB->get_records_sql('SELECT DISTINCT meetingid, courseid, bigbluebuttonbnid FROM '.$CFG->prefix.'bigbluebuttonbn_log WHERE '.$CFG->prefix.'bigbluebuttonbn_log.courseid='.$course->id. ' AND '.$CFG->prefix.'bigbluebuttonbn_log.record = 1 AND '.$CFG->prefix.'bigbluebuttonbn_log.event = \'Create\';' );
    
    $groups = groups_get_all_groups($course->id);
    if( isset($groups) && count($groups) > 0 ){  //If the course has groups include groupid in the name to look for possible recordings related to the sub-activities
        foreach ($results as $result) {
            if (strlen($meetingID) > 0) $meetingID .= ',';
            $meetingID .= $result->meetingid;
            foreach ( $groups as $group ){
                $meetingID .= ','.$result->meetingid.'['.$group->id.']';
            }
        }
    
    } else {                                    // No groups means that it wont check any other sub-activity
        foreach ($results as $result) {
            if (strlen($meetingID) > 0) $meetingID .= ',';
            $meetingID .= $result->meetingid;
        }
    
    }

    
    //If there are meetings with recordings load the data to the table 
    if ( $meetingID != '' ){
        $recordingsbn = bigbluebuttonbn_getRecordingsArray($meetingID, $url, $salt);
    
        if( isset($recordingsbn) && !isset($recordingsbn['messageKey']) ){
            foreach ( $recordingsbn as $recording ){
                if ( $moderator || $recording['published'] == 'true' ) {
    
                    $endTime = isset($recording['endTime'])? intval(str_replace('"', '\"', $recording['endTime'])):0;
                    $endTime = $endTime - ($endTime % 1000);
                    $startTime = isset($recording['startTime'])? intval(str_replace('"', '\"', $recording['startTime'])):0;
                    $startTime = $startTime - ($startTime % 1000);
                    $duration = intval(($endTime - $startTime) / 60000);
    
                    $meta_course = isset($recording['meta_context'])?str_replace('"', '\"', $recording['meta_context']):'';
                    $meta_activity = isset($recording['meta_contextactivity'])?str_replace('"', '\"', $recording['meta_contextactivity']):'';
                    $meta_description = isset($recording['meta_contextactivitydescription'])?str_replace('"', '\"', $recording['meta_contextactivitydescription']):'';
    
                    $actionbar = '';
                    if ( $moderator ) {
                        //$deleteURL = bigbluebuttonbn_getDeleteRecordingsURL($recording['recordID'], $url, $salt);
                        if ( $recording['published'] == 'true' ){
                            //$publishURL = bigbluebuttonbn_getPublishRecordingsURL($recording['recordID'], 'false', $url, $salt);
                            //$actionbar = "<a id=\"actionbar-publish-a-".$recording['recordID']."\" title=\"".$view_hint_actionbar_hide."\" href=\"#\"><img id=\"actionbar-publish-img-".$recording['recordID']."\" src=\"pix/hide.gif\" class=\"iconsmall\" onClick=\"recordingsbn_actionCall('unpublish', '".$recording['recordID']."', '".$course->id."'); return false;\" /></a>";
                            //$actionbar = "<a id='actionbar-publish-a-".$recording['recordID']."' title='".$view_hint_actionbar_hide."' href='".$CFG->wwwroot."/mod/bigbluebuttonbn/bbb-broker.php?action=unpublish&recordingid=".$recording['recordID']."&cid=".$course->id."'><img id='actionbar-publish-img-".$recording['recordID']."' src='pix/hide.gif' class='iconsmall' /></a>";
                            //$actionbar = "<a id='actionbar-publish-a-".$recording['recordID']."' title='".$view_hint_actionbar_hide."' href='".$publishURL."'><img id='actionbar-publish-img-".$recording['recordID']."' src='pix/hide.gif' class='iconsmall' /></a>";
                            $actionbar = "<a id='actionbar-publish-a-".$recording['recordID']."' title='".$view_hint_actionbar_hide."' href='".$CFG->wwwroot."/mod/recordingsbn/view?id=".$cm->id."&action=unpublish&recordingid=".$recording['recordID']."&cid=".$course->id."'><img id='actionbar-publish-img-".$recording['recordID']."' src='pix/hide.gif' class='iconsmall' /></a>";
                        } else {
                            //$publishURL = bigbluebuttonbn_getPublishRecordingsURL($recording['recordID'], 'true', $url, $salt);
                            //$actionbar = "<a id=\"actionbar-publish-a-".$recording['recordID']."\" title=\"".$view_hint_actionbar_show."\" href=\"#\"><img id=\"actionbar-publish-img-".$recording['recordID']."\" src=\"pix/show.gif\" class=\"iconsmall\" onClick=\"recordingsbn_actionCall('publish', '".$recording['recordID']."', '".$course->id."'); return false;\" /></a>";
                            //$actionbar = "<a id='actionbar-publish-a-".$recording['recordID']."' title='".$view_hint_actionbar_show."' href='".$CFG->wwwroot."/mod/bigbluebuttonbn/bbb-broker.php?action=publish&recordingid=".$recording['recordID']."&cid=".$course->id."'><img id='actionbar-publish-img-".$recording['recordID']."' src='pix/show.gif' class='iconsmall' /></a>";
                            //$actionbar = "<a id='actionbar-publish-a-".$recording['recordID']."' title='".$view_hint_actionbar_show."' href='".$publishURL."'><img id='actionbar-publish-img-".$recording['recordID']."' src='pix/show.gif' class='iconsmall' /></a>";
                            $actionbar = "<a id='actionbar-publish-a-".$recording['recordID']."' title='".$view_hint_actionbar_show."' href='".$CFG->wwwroot."/mod/recordingsbn/view?id=".$cm->id."&action=publish&recordingid=".$recording['recordID']."&cid=".$course->id."'><img id='actionbar-publish-img-".$recording['recordID']."' src='pix/show.gif' class='iconsmall' /></a>";
                        }
                        //$actionbar .= "<a id=\"actionbar-delete-a-".$recording['recordID']."\" title=\"".$view_hint_actionbar_delete."\" href=\"#\"><img id=\"actionbar-delete-img-".$recording['recordID']."\" src=\"pix/delete.gif\" class=\"iconsmall\" onClick=\"recordingsbn_actionCall('delete', '".$recording['recordID']."', '".$course->id."'); return false;\" /></a>";
                        //$actionbar .= "<a id='actionbar-delete-a-".$recording['recordID']."' title='".$view_hint_actionbar_delete."' href='".$deleteURL."'><img id='actionbar-delete-img-".$recording['recordID']."' src='pix/delete.gif' class='iconsmall' /></a>";
                        
                        
                        $actionbar .= "<a id='actionbar-delete-a-".$recording['recordID']."' title='".$view_hint_actionbar_delete."' href='".$CFG->wwwroot."/mod/recordingsbn/view?id=".$cm->id."&action=delete&recordingid=".$recording['recordID']."&cid=".$course->id."'><img id='actionbar-delete-img-".$recording['recordID']."' src='pix/delete.gif' class='iconsmall' /></a>";
                        //$actionbar .= "<a id='actionbar-delete-a-".$recording['recordID']."' title='".$view_hint_actionbar_delete."' href='#'><img id='actionbar-delete-img-".$recording['recordID']."' src='pix/delete.gif' class='iconsmall' onclick='if(confirm(\"Are you sure to delete this recording?\")) load(\"".$CFG->wwwroot."/mod/recordingsbn/view?id=".$cm->id."&action=delete&recordingid=".$recording['recordID']."&cid=".$course->id."\"); return false;' /></a>";
                        
                    }
    
                    $type = '';
                    foreach ( $recording['playbacks'] as $playback ){
                        $type .= '<a href=\"'.$playback['url'].'\" target=\"_new\">'.$playback['type'].'</a>&#32;';
                    }
    
                    //Make sure the startTime is timestamp
                    if( !is_numeric($recording['startTime']) ){
                        $date = new DateTime($recording['startTime']);
                        $recording['startTime'] = date_timestamp_get($date);
                    } else {
                        $recording['startTime'] = $recording['startTime'] / 1000;
                    }
                    //Set corresponding format
                    //$format = isset(get_string('strftimerecentfull', 'langconfig'));
                    //if( !isset($format) )
                    $format = '%a %h %d %H:%M:%S %Z %Y';
                    //Format the date
                    $formatedStartDate = userdate($recording['startTime'], $format, usertimezone($USER->timezone) );
                    
                    $table->data[] = array ($type, $meta_course, $meta_activity, $meta_description, str_replace( " ", "&nbsp;", $formatedStartDate), $duration, $actionbar );
                    
                }
            }
        }
    
    }
    
    //Print the table
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');
    echo html_writer::table($table);
    echo $OUTPUT->box_end();
        
} else {
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');
    print_error('You must to have BigBlueButtonBN Activity Module installed');
    echo $OUTPUT->box_end();
    
}

// Finish the page
echo $OUTPUT->footer();

?>
