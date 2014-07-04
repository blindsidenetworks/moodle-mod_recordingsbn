<?php
/**
 * View and administrate BigBlueButton playback recordings
 *
 * Authors:
 *    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)    
 *
 * @package   mod_recordingsbn
 * @copyright 2011-2013 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

$version = 2014070303;
$cron = 0;
$component = 'mod_recordingsbn';
$release = '1.0.11';
$mod_bigbluebuttonbn_dependency = 2013110100;
/// [MATURITY_STABLE | MATURITY_RC | MATURITY_BETA | MATURITY_ALPHA]
$maturity = MATURITY_BETA;

if ( $CFG->version < '2013111800' ) {
    $module->version = $version;
    $module->requires = 2010112400.1;
    $module->cron = $cron;
    $module->component = $component;
    $module->maturity = $maturity;
    $module->release = $release;
    $module->dependencies = array(
        'mod_bigbluebuttonbn' => $mod_bigbluebuttonbn_dependency,
    );
} else {
    $plugin->version  = $version;
    $plugin->requires = 2013101800;
    $plugin->cron     = $cron;
    $plugin->component = $component;
    $plugin->maturity = $maturity;
    $plugin->release  = $release;
    $plugin->dependencies = array(
        'mod_bigbluebuttonbn' => $mod_bigbluebuttonbn_dependency,
    );
}
