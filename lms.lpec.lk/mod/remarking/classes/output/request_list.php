<?php
defined('MOODLE_INTERNAL') || die();

class remarking_request_list implements renderable {
    public $requests;
    public $cmid;
    
    public function __construct($requests, $cmid) {
        $this->requests = $requests;
        $this->cmid = $cmid;
    }
}

