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
 * Class for the structure used for restore RecordingsBN.
 *
 * @package     mod_recordingsbn
 * @subpackage  backup-moodle2
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die;

/**
 * Define all the restore steps that will be used by the restore_url_activity_task
 *
 * @copyright 2010-2017 Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
class restore_recordingsbn_activity_structure_step extends restore_activity_structure_step {

    /**
     * Structure step to restore one recordingsbn activity
     */
    protected function define_structure() {
        $paths = array();
        $paths[] = new restore_path_element('recordingsbn', '/activity/recordingsbn');
        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Structure step to process one recordingsbn activity
     *
     * @param object $data
     */
    protected function process_recordingsbn($data) {
        global $DB;
        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        // Insert the recordingsbn record.
        $newitemid = $DB->insert_record('recordingsbn', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Structure step to process after executing the restore of one recordingsbn activity
     */
    protected function after_execute() {
        // Add recordingsbn related files, no need to match by itemname (just internally handled context).
    }
}
