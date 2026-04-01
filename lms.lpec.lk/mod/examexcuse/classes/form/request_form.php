<?php
namespace mod_examexcuse\form;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

class request_form extends \moodleform {
    public function definition() {
        $mform = $this->_form;

        // Reason field.
        $mform->addElement('textarea', 'reason', get_string('reason', 'examexcuse'), ['rows' => 5, 'cols' => 60]);
        $mform->setType('reason', PARAM_TEXT);
        $mform->addRule('reason', get_string('required'), 'required', null, 'client');

        // File upload field.
        $mform->addElement('filepicker', 'attachment', get_string('attachment', 'examexcuse'), null, [
            'maxbytes' => 10485760, // 10MB
            'accepted_types' => '*'
        ]);

        $this->add_action_buttons(true, get_string('submitrequest', 'examexcuse'));
    }
}
