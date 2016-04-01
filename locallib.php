<?php
/**
 * View and administrate BigBlueButton playback recordings
 *
 * @package   mod_recordingsbn
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2011-2014 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

global $RECORDINGSBN_CFG, $CFG;

require_once(dirname(__FILE__).'/lib.php');

function recordingsbn_getRecordedMeetings( $courseID ) {
    global $DB;

    $records = $DB->get_records('bigbluebuttonbn_log', array('courseid' => $courseID, 'event' => 'Create'));

    //Remove duplicates
    $unique_records = array();
    foreach ($records as $key => $record) {
        $record_key = $record->meetingid.','.$record->courseid.','.$record->bigbluebuttonbnid.','.$record->meta;
        if( array_search($record_key, $unique_records) === false ) {
            array_push($unique_records, $record_key);
        } else {
            unset($records[$key]);
        }
    }

    //Remove the ones with record=false
    foreach ($records as $key => $record) {
        $meta = json_decode($record->meta);
        if ( !$meta->record ) {
            unset($records[$key]);
        }
    }

    return $records;
}

function recordingsbn_get_cfg_ui_html_default() {
    global $RECORDINGSBN_CFG, $CFG;
    return (isset($RECORDINGSBN_CFG->recordingsbn_ui_html_default)? $RECORDINGSBN_CFG->recordingsbn_ui_html_default: (isset($CFG->recordingsbn_ui_html_default)? $CFG->recordingsbn_ui_html_default: false));
}

function recordingsbn_get_cfg_ui_html_editable() {
    global $RECORDINGSBN_CFG, $CFG;
    return (isset($RECORDINGSBN_CFG->recordingsbn_ui_html_editable)? $RECORDINGSBN_CFG->recordingsbn_ui_html_editable: (isset($CFG->recordingsbn_ui_html_editable)? $CFG->recordingsbn_ui_html_editable: false));
}
