<?php

namespace mod_repeatexam;

defined('MOODLE_INTERNAL') || die();

class manager
{

    /**
     * Check if a user can submit a repeat exam request.
     */
    public static function can_submit_request(\context $context): bool
    {
        return has_capability('mod/repeatexam:submit', $context);
    }

    /**
     * Check if a user can manage repeat exam requests.
     */
    public static function can_manage_requests(\context $context): bool
    {
        return has_capability('mod/repeatexam:manage', $context);
    }

    /**
     * Submit a new repeat exam request.
     */
    public static function submit_request(int $repeatexamid, int $courseid, int $userid, string $reason): int
    {
        global $DB;

        $record = new \stdClass();
        $record->repeatexamid = $repeatexamid;
        $record->courseid = $courseid;
        $record->userid = $userid;
        $record->reason = $reason;
        $record->status = 0; // Pending
        $record->timecreated = time();
        $record->modifiedby = $userid;

        return $DB->insert_record('repeatexam_requests', $record, true); // return inserted id
    }



    /**
     * Get all requests for a given repeat exam activity.
     */
    public static function get_requests(int $repeatexamid): array
    {
        global $DB;

        return $DB->get_records('repeatexam_requests', ['repeatexamid' => $repeatexamid]);
    }

    /**
     * Update the status of a request (approve or reject).
     */
    public static function update_request_status(int $requestid, int $status, int $modifiedby): bool
    {
        global $DB;

        $record = $DB->get_record('repeatexam_requests', ['id' => $requestid], '*', MUST_EXIST);
        $record->status = $status;
        $record->modifiedby = $modifiedby;

        return $DB->update_record('repeatexam_requests', $record);
    }
}
