<?php
defined('MOODLE_INTERNAL') || die();

function examexcuse_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_BACKUP_MOODLE2: return true;
        case FEATURE_MOD_PURPOSE: return MOD_PURPOSE_ADMINISTRATION;
        default: return null;
    }
}
function examexcuse_add_instance($moduleinstance, $mform = null) {
    global $DB;
    $moduleinstance->timecreated = time();
    $id = $DB->insert_record('examexcuse', $moduleinstance);
    return $id;
}

function examexcuse_update_instance($moduleinstance, $mform = null) {
    global $DB;
    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;
    return $DB->update_record('examexcuse', $moduleinstance);
}

function examexcuse_delete_instance($id) {
    global $DB;
    $exists = $DB->get_record('examexcuse', array('id' => $id));
    if (!$exists) {
        return false;
    }
    $DB->delete_records('examexcuse', array('id' => $id));
    return true;
}


function mod_examexcuse_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($filearea !== 'attachment') {
        return false;
    }

    require_login($course, true, $cm);

    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_examexcuse', $filearea, $itemid, $filepath, $filename);

    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

