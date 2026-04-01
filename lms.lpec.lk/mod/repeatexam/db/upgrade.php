<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_repeatexam_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2023061800) {
        // Initial install version
        upgrade_mod_savepoint(true, 2023061800, 'repeatexam');
    }

    return true;
}
