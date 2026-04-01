<?php
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Add any module settings here if needed
    $settings->add(new admin_setting_heading('remarking_settings', 
        get_string('settings', 'remarking'), 
        get_string('pluginname_desc', 'remarking')));
}
