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
 * Configuration file for recordingsbn.
 *
 * @package   mod_recordingsbn
 * @copyright 2010-2017 Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

defined('MOODLE_INTERNAL') || die();

unset($RECORDINGSBN_CFG);
global $RECORDINGSBN_CFG;
$RECORDINGSBN_CFG = new stdClass();

/*
 * Any parameter included in this fill will not be shown in the admin UI
 * If there was a previous configuration, the parameters here included
 * will override the parameters already configured (if they were
 * configured already)
 */

/*
 * 1. GENERAL CONFIGURATION
 */

/* When the value is set to 1 (checked) the recordingsbn resources
 *  will show the recodings in an html table by default.
 *    $RECORDINGSBN_CFG->recordingsbn_ui_html_default = 0;
 */

/* When the value is set to 1 (checked) the 'html ui' capability can be
 *  enabled/disabled by the user creating or editing the resource.
 *    $RECORDINGSBN_CFG->recordingsbn_ui_html_editable = 0;
 */

/* When the value is set to 1 (checked) the recordingsbn resources
 *  will show the recodings belonging to deleted activities as part of the list.
 *    $RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_default = 1;
 */

/* When the value is set to 1 (checked) the 'include recordings from deleted activities'
 *  capability can be enabled/disabled by the user creating or editing the resource.
 *    $RECORDINGSBN_CFG->recordingsbn_include_deleted_activities_editable = 0;
 */
