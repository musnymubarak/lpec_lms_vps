<?php
namespace mod_examexcuse\event;

defined('MOODLE_INTERNAL') || die();

class course_module_viewed extends \core\event\course_module_viewed {
    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r'; // read
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'examexcuse'; // ✅ Required
    }
}
