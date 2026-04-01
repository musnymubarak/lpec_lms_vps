<?php
use mod_repeatexam\manager;
use mod_repeatexam\form\request_form;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/repeatexam/lib.php');
require_once($CFG->dirroot . '/mod/repeatexam/classes/form/request_form.php');

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$requestid = optional_param('requestid', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

[$course, $cm] = get_course_and_cm_from_cmid($id, 'repeatexam');
$repeatexam = $DB->get_record('repeatexam', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/repeatexam:view', $context);

$PAGE->set_url('/mod/repeatexam/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($repeatexam->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->add_body_class('limitedwidth');
$PAGE->activityheader->set_attrs([
    'title' => format_string($repeatexam->name),
    'description' => ''
]);

$event = \mod_repeatexam\event\course_module_viewed::create([
    'objectid' => $repeatexam->id,
    'context' => $context,
]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('repeatexam', $repeatexam);
$event->trigger();

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

echo $OUTPUT->header();

if (trim($repeatexam->intro)) {
    echo $OUTPUT->box(format_module_intro('repeatexam', $repeatexam, $cm->id), 'generalbox mod_introbox', 'repeatexamintro');
}

if (manager::can_submit_request($context) && !manager::can_manage_requests($context)) {
    // Only students can submit request
    if ($action === 'submit') {
        $mform = new request_form(new moodle_url('/mod/repeatexam/view.php', ['id' => $id, 'action' => 'submit']));

        if ($mform->is_cancelled()) {
            redirect(new moodle_url('/mod/repeatexam/view.php', ['id' => $id]));
        } else if ($data = $mform->get_data()) {
    // Insert record and get new request id
    $requestid = manager::submit_request($repeatexam->id, $course->id, $USER->id, $data->reason);

    // Save uploaded file, correctly linked with $requestid
    file_save_draft_area_files(
        $data->attachment,
        $context->id,
        'mod_repeatexam',
        'attachment',
        $requestid,
        ['subdirs' => 0, 'maxbytes' => 10485760, 'accepted_types' => '*']
    );

    \core\notification::success(get_string('requestsubmitted', 'repeatexam'));
    redirect(new moodle_url('/mod/repeatexam/view.php', ['id' => $id]));
}
 else {
            echo $OUTPUT->heading(get_string('requestexam', 'repeatexam'), 3);
            $mform->display();
            echo $OUTPUT->footer();
            exit;
        }
    }

    // Show request button for student
    $url = new moodle_url('/mod/repeatexam/view.php', ['id' => $id, 'action' => 'submit']);
    echo $OUTPUT->single_button($url, get_string('requestexam', 'repeatexam'), 'get');
    echo html_writer::empty_tag('hr');

    // Show student's own requests
    $userrequests = $DB->get_records('repeatexam_requests', [
        'repeatexamid' => $repeatexam->id,
        'userid' => $USER->id
    ], 'timecreated DESC');

    if (!empty($userrequests)) {
        echo $OUTPUT->heading(get_string('yourrequests', 'repeatexam'), 3);

        $table = new html_table();
    $table->head = [
    get_string('reason', 'repeatexam'),
    get_string('attachment', 'repeatexam'),
    get_string('date', 'repeatexam'),
    get_string('status', 'repeatexam')
];


        $table->attributes['class'] = 'generaltable';
foreach ($userrequests as $request) {
    switch ($request->status) {
        case 0:
            $status = html_writer::span(get_string('pending', 'repeatexam'), 'badge badge-warning');
            break;
        case 1:
            $status = html_writer::span(get_string('approved', 'repeatexam'), 'badge badge-success');
            break;
        case 2:
            $status = html_writer::span(get_string('rejected', 'repeatexam'), 'badge badge-danger');
            break;
        default:
            $status = html_writer::span(get_string('unknown', 'repeatexam'), 'badge badge-secondary');
            break;
    }

    // Handle file attachments
    $fs = get_file_storage();
    $files = $fs->get_area_files(
        \context_module::instance($cm->id)->id,
        'mod_repeatexam',
        'attachment',
        $request->id,
        'filename',
        false
    );

    $attachmentlinks = [];
    foreach ($files as $file) {
        $fileurl = moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        );
        $attachmentlinks[] = html_writer::link($fileurl, $file->get_filename());
    }

    $attachments = !empty($attachmentlinks)
        ? implode('<br>', $attachmentlinks)
        : get_string('nofiles', 'repeatexam');

    $table->data[] = [
        format_text($request->reason),
        $attachments,
        userdate($request->timecreated),
        $status
    ];
}



        echo html_writer::table($table);
    }
}

if (in_array($action, ['approve', 'reject']) && $requestid && manager::can_manage_requests($context)) {
    if ($confirm && confirm_sesskey()) {
        $status = ($action === 'approve') ? 1 : 2;
        manager::update_request_status($requestid, $status, $USER->id);

        $message = ($action === 'approve') ? 'requestapproved' : 'requestrejected';
        \core\notification::success(get_string($message, 'repeatexam'));
        redirect(new moodle_url('/mod/repeatexam/view.php', ['id' => $id]));
    } else {
        $message = get_string('confirm' . $action . 'request', 'repeatexam');
        $continueurl = new moodle_url('/mod/repeatexam/view.php', [
            'id' => $id,
            'action' => $action,
            'requestid' => $requestid,
            'confirm' => 1,
            'sesskey' => sesskey()
        ]);
        $cancelurl = new moodle_url('/mod/repeatexam/view.php', ['id' => $id]);

        echo $OUTPUT->confirm($message, $continueurl, $cancelurl);
        echo $OUTPUT->footer();
        exit;
    }
}

if (manager::can_manage_requests($context)) {
    $requests = manager::get_requests($repeatexam->id);

    if (!empty($requests)) {
        echo $OUTPUT->heading(get_string('viewrequests', 'repeatexam'), 3);

        $table = new html_table();
        $table->head = [
            get_string('student', 'repeatexam'),
            get_string('reason', 'repeatexam'),
            get_string('attachment', 'repeatexam'),  // Add attachment column header here
            get_string('date', 'repeatexam'),
            get_string('status', 'repeatexam'),
            get_string('action', 'repeatexam')
        ];
        $table->attributes['class'] = 'generaltable';

        foreach ($requests as $request) {
            $user = $DB->get_record('user', ['id' => $request->userid]);

            // Status badge
            switch ($request->status) {
                case 0:
                    $status = html_writer::span(get_string('pending', 'repeatexam'), 'badge badge-warning');
                    break;
                case 1:
                    $status = html_writer::span(get_string('approved', 'repeatexam'), 'badge badge-success');
                    break;
                case 2:
                    $status = html_writer::span(get_string('rejected', 'repeatexam'), 'badge badge-danger');
                    break;
                default:
                    $status = html_writer::span(get_string('unknown', 'repeatexam'), 'badge badge-secondary');
                    break;
            }

            // Get attachment files for this request
            $fs = get_file_storage();
            $files = $fs->get_area_files(
                $context->id,
                'mod_repeatexam',
                'attachment',
                $request->id,
                'filename',
                false
            );

            $attachmentlinks = [];
            foreach ($files as $file) {
                $fileurl = moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename()
                );
                $attachmentlinks[] = html_writer::link($fileurl, $file->get_filename());
            }

            $attachments = !empty($attachmentlinks)
                ? implode('<br>', $attachmentlinks)
                : get_string('nofiles', 'repeatexam');

            $actions = '';
            if ($request->status == 0) {
                $approveurl = new moodle_url('/mod/repeatexam/view.php', [
                    'id' => $id,
                    'action' => 'approve',
                    'requestid' => $request->id,
                    'sesskey' => sesskey()
                ]);
                $rejecturl = new moodle_url('/mod/repeatexam/view.php', [
                    'id' => $id,
                    'action' => 'reject',
                    'requestid' => $request->id,
                    'sesskey' => sesskey()
                ]);

                $actions .= $OUTPUT->action_icon($approveurl, new pix_icon('t/check', get_string('approve', 'repeatexam')));
                $actions .= ' ';
                $actions .= $OUTPUT->action_icon($rejecturl, new pix_icon('t/delete', get_string('reject', 'repeatexam')));
            }

            $table->data[] = [
                fullname($user),
                format_text($request->reason),
                $attachments,  // <-- add attachments here
                userdate($request->timecreated),
                $status,
                $actions
            ];
        }

        echo html_writer::table($table);
    } else {
        \core\notification::info(get_string('norequests', 'repeatexam'));
    }
}


echo $OUTPUT->footer();
