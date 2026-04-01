<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/repeatexam/lib.php');

$id = required_param('id', PARAM_INT); // Course ID

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_course_login($course);

$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/mod/repeatexam/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

if (! $repeatexams = get_all_instances_in_course('repeatexam', $course)) {
    notice(get_string('thereareno', 'moodle', get_string('modulenameplural', 'repeatexam')), 
        new moodle_url('/course/view.php', array('id' => $course->id)));
}

$table = new html_table();
$table->head  = array(get_string('name'), get_string('description'));
$table->align = array('left', 'left');

foreach ($repeatexams as $repeatexam) {
    $url = new moodle_url('/mod/repeatexam/view.php', array('id' => $repeatexam->coursemodule));
    $link = html_writer::link($url, format_string($repeatexam->name));
    $description = format_module_intro('repeatexam', $repeatexam, $repeatexam->coursemodule);
    
    $table->data[] = array($link, $description);
}

echo html_writer::table($table);
echo $OUTPUT->footer();
