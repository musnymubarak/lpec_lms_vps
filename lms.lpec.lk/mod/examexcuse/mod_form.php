<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_examexcuse_mod_form extends moodleform_mod {
    public function definition() {
        $mform = $this->_form;

        // Activity name.
        $mform->addElement('text', 'name', get_string('examexcusename', 'mod_examexcuse'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // Activity description.
        $this->standard_intro_elements();

        // Standard grading and activity options.
        $this->standard_coursemodule_elements();

        // Add standard buttons (save, cancel).
        $this->add_action_buttons();
    }
}
