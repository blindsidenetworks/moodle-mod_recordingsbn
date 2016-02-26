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

require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_recordingsbn_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {

        //UI configuration options
        $ui_html_default = recordingsbn_get_cfg_ui_html_default();
        $ui_html_editable = recordingsbn_get_cfg_ui_html_editable();
        $include_deleted_activities_default = recordingsbn_get_cfg_include_deleted_activities_default();
        $include_deleted_activities_editable = recordingsbn_get_cfg_include_deleted_activities_editable();

        $mform = $this->_form;

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('recordingsbnname', 'recordingsbn'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        //$mform->addHelpButton('name', 'recordingsbnname', 'recordingsbn');

        if ( $ui_html_editable ) {
            $mform->addElement('checkbox', 'ui_html', get_string('mod_form_field_ui_html', 'recordingsbn'));
            $mform->setDefault( 'ui_html', $ui_html_default );
            $mform->setAdvanced('ui_html');
        } else {
            $mform->addElement('hidden', 'ui_html', $ui_html_default);
        }
        $mform->setType('ui_html', PARAM_INT);

        if ( $include_deleted_activities_editable ) {
            $mform->addElement('checkbox', 'include_deleted_activities', get_string('mod_form_field_include_deleted_activities', 'recordingsbn'));
            $mform->setDefault( 'include_deleted_activities', $ui_html_default );
            $mform->setAdvanced('include_deleted_activities');
        } else {
            $mform->addElement('hidden', 'include_deleted_activities', $include_deleted_activities_default);
        }
        $mform->setType('include_deleted_activities', PARAM_INT);

        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }
}