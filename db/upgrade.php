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

/**
 * Execute recordingsbn upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_recordingsbn_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2012040200) {

        $table = new xmldb_table('recordingsbn');

        // Define field intro to be droped from recordingsbn.
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'name');

        // Drop field intro.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field, $continue = true, $feedback = true);
        }

        // Define field introformat to be droped from recordingsbn.
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

        // Drop field introformat.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field, $continue = true, $feedback = true);
        }

        // Once we reach this point, we can store the new version and consider the module.
        // Upgraded to the version 2012040200 so the next time this block is skipped.
        upgrade_mod_savepoint(true, 2012040200, 'recordingsbn');
    }

    if ($oldversion < 2012040210) {
        $table = new xmldb_table('recordingsbn');

        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'name');

        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field, $continue = true, $feedback = true);
        }

        upgrade_mod_savepoint(true, 2012040210, 'recordingsbn');
    }

    if ($oldversion < 2013071001) {
        $table = new xmldb_table('recordingsbn');

        // Define field intro to be re-added to recordingsbn.
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'name');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field introformat to be re-added to recordingsbn.
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2013071001, 'recordingsbn');
    }

    if ($oldversion < 2015080601) {
        $table = new xmldb_table('recordingsbn');

        $index = new xmldb_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        // Remove the index if exists.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index, $continue = true, $feedback = true);
        }

        // Update the course field.
        $field = new xmldb_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $dbman->change_field_type($table, $field, $continue = true, $feedback = true);

        // Recreate the index.
        $dbman->add_index($table, $index, $continue = true, $feedback = true);

        upgrade_mod_savepoint(true, 2015080601, 'recordingsbn');
    }

    if ($oldversion < 2015080603) {
        $table = new xmldb_table('recordingsbn');

        $field = new xmldb_field('ui_html');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field, $continue = true, $feedback = true);
        }

        upgrade_mod_savepoint(true, 2015080603, 'recordingsbn');
    }

    if ($oldversion < 2016011301) {
        $table = new xmldb_table('recordingsbn');

        $field = new xmldb_field('include_deleted_activities');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 1);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field, $continue = true, $feedback = true);
        }

        upgrade_mod_savepoint(true, 2016011301, 'recordingsbn');
    }

    // Final return of upgrade result (true, all went good) to Moodle.
    return true;
}
