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

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Moodle class for mod_form.
 *
 * @copyright 2010-2017 Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
class mod_recordingsbn_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {

        // UI configuration options.
        $uihtmldefault = recordingsbn_get_cfg_ui_html_default();
        $uihtmleditable = recordingsbn_get_cfg_ui_html_editable();
        $activitiesdefault = recordingsbn_get_cfg_include_deleted_activities_default();
        $activitieseditable = recordingsbn_get_cfg_include_deleted_activities_editable();

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('recordingsbnname', 'recordingsbn'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        if ( $uihtmleditable ) {
            $mform->addElement('checkbox', 'ui_html', get_string('mod_form_field_ui_html', 'recordingsbn'));
            $mform->setDefault( 'ui_html', $uihtmldefault );
            $mform->setAdvanced('ui_html');
        } else {
            $mform->addElement('hidden', 'ui_html', $uihtmldefault);
        }
        $mform->setType('ui_html', PARAM_INT);

        if ( $activitieseditable ) {
            $mform->addElement('checkbox', 'include_deleted_activities',
                get_string('mod_form_field_include_deleted_activities', 'recordingsbn'));
            $mform->setDefault( 'include_deleted_activities', $uihtmldefault );
            $mform->setAdvanced('include_deleted_activities');
        } else {
            $mform->addElement('hidden', 'include_deleted_activities', $activitiesdefault);
        }
        $mform->setType('include_deleted_activities', PARAM_INT);

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();
        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
}
