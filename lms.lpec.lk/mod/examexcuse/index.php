<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/examexcuse/lib.php');

$id = required_param('id', PARAM_INT); // Course ID

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_course_login($course);

$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/mod/examexcuse/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

if (! $examexcuses = get_all_instances_in_course('examexcuse', $course)) {
    notice(get_string('thereareno', 'moodle', get_string('modulenameplural', 'examexcuse')), 
        new moodle_url('/course/view.php', array('id' => $course->id)));
}

$table = new html_table();
$table->head  = array(get_string('name'), get_string('description'));
$table->align = array('left', 'left');

foreach ($examexcuses as $examexcuse) {
    $url = new moodle_url('/mod/examexcuse/view.php', array('id' => $examexcuse->coursemodule));
    $link = html_writer::link($url, format_string($examexcuse->name));
    $description = format_module_intro('examexcuse', $examexcuse, $examexcuse->coursemodule);
    
    $table->data[] = array($link, $description);
}

echo html_writer::table($table);
echo $OUTPUT->footer();
