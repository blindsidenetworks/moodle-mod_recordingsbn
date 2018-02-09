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
 * The mod_recordingsbn settings/renderer.
 *
 * @package   mod_recordingsbn
 * @copyright 2010-2017 Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

namespace mod_recordingsbn\settings;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/bigbluebuttonbn/locallib.php');
require_once($CFG->dirroot . '/mod/recordingsbn/locallib.php');
require_once($CFG->libdir.'/adminlib.php');

/**
 * Helper class for rendering HTML for settings.php.
 *
 * @copyright 2018 Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
class renderer {

    /**
     * @var $settings stores the settings as they come from settings.php
     */
    private $settings;

    /**
     * @var $module stores the plugin name where the renderer is going to be used
     */
    private $module;

    /**
     * Constructor.
     *
     * @param string $module
     * @param object $settings
     */
    public function __construct($module, &$settings) {
        $this->module = $module;
        $this->settings = $settings;
    }

    /**
     * Render the header for a group.
     *
     * @param string $name
     * @param string $itemname
     * @param string $itemdescription
     *
     * @return void
     */
    public function render_group_header($name, $itemname = null, $itemdescription = null) {
        if ($itemname === null) {
            $itemname = get_string('config_' . $name, $this->module);
        }
        if ($itemdescription === null) {
            $itemdescription = get_string('config_' .$name . '_description', $this->module);
        }
        $item = new \admin_setting_heading($this->module . '_config_' . $name, $itemname, $itemdescription);
        $this->settings->add($item);
    }

    /**
     * Render an element in a group.
     *
     * @param string $name
     * @param object $item
     *
     * @return void
     */
    public function render_group_element($name, $item) {
        global $CFG;
        if (!isset($CFG->recordingsbn[$name])) {
            $this->settings->add($item);
        }
    }

    /**
     * Render a text element in a group.
     *
     * @param string    $name
     * @param object    $default
     * @param string    $type
     *
     * @return Object
     */
    public function render_group_element_text($name, $default = null, $type = PARAM_RAW) {
        $item = new \admin_setting_configtext($this->module . '_' . $name,
                get_string('config_' . $name, $this->module),
                get_string('config_' . $name . '_description', $this->module),
                $default, $type);
        return $item;
    }

    /**
     * Render a checkbox element in a group.
     *
     * @param string    $name
     * @param object    $default
     *
     * @return Object
     */
    public function render_group_element_checkbox($name, $default = null) {
        $item = new \admin_setting_configcheckbox($this->module . '_' . $name,
                get_string('config_' . $name, $this->module),
                get_string('config_' . $name . '_description', $this->module),
                $default);
        return $item;
    }

    /**
     * Render a multiselect element in a group.
     *
     * @param string    $name
     * @param object    $defaultsetting
     * @param object    $choices
     *
     * @return Object
     */
    public function render_group_element_configmultiselect($name, $defaultsetting, $choices) {
        $item = new \admin_setting_configmultiselect($this->module . '_' . $name,
                get_string('config_' . $name, $this->module),
                get_string('config_' . $name . '_description', $this->module),
                $defaultsetting, $choices);
        return $item;
    }

    /**
     * Render a general warning message.
     *
     * @param string    $name
     * @param string    $message
     * @param string    $type
     * @param boolean   $closable
     *
     * @return Object
     */
    public function render_warning_message($name, $message, $type = 'warning', $closable = true) {
        global $OUTPUT;
        $output = $OUTPUT->box_start('box boxalignleft adminerror alert alert-' . $type . ' alert-block fade in', $name) . "\n";
        if ($closable) {
            $output .= '  <button type="button" class="close" data-dismiss="alert">&times;</button>' . "\n";
        }
        $output .= '  ' . $message . "\n";
        $output .= $OUTPUT->box_end() . "\n";
        $item = new \admin_setting_heading($this->module . '_' . $name, '', $output);
        $this->settings->add($item);
        return $item;
    }

    /**
     * Render a multiselect element in a group.
     *
     * @param string    $name
     * @param string    $url
     * @param string    $caption
     * @param string    $title
     * @param string    $class
     *
     * @return Object
     */
    public function render_group_element_button($name, $url, $caption = '', $title = '', $class = 'btn btn-secondary') {
        global $OUTPUT;
        if ($caption == '') {
            $caption = get_string('ok', 'moodle');
        }
        if ($title == '') {
            $title = $caption;
        }
        $output  = $OUTPUT->box_start('box boxalignleft fade in', $this->module . '_' . $name) . "\n";
        $output .= '  <a href="' . $url . '" class="' . $class . '" title="' . $title . '">' . $caption . '</a>' . "\n";
        $output .= $OUTPUT->box_end() . "\n";
        $item = new \admin_setting_heading($this->module . '_' . $name, '', $output, 'center-block');
        $this->settings->add($item);
        return $item;
    }
}
