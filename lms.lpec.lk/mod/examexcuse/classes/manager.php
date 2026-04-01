<?php

namespace mod_examexcuse;

defined('MOODLE_INTERNAL') || die();

class manager
{

    /**
     * Check if a user can submit a excuse exam request.
     */
    public static function can_submit_request(\context $context): bool
    {
        return has_capability('mod/examexcuse:submit', $context);
    }

    /**
     * Check if a user can manage excuse exam requests.
     */
    public static function can_manage_requests(\context $context): bool
    {
        return has_capability('mod/examexcuse:manage', $context);
    }

    /**
     * Submit a new excuse exam request.
     */
    public static function submit_request(int $examexcuseid, int $courseid, int $userid, string $reason): int
    {
        global $DB;

        $record = new \stdClass();
        $record->examexcuseid = $examexcuseid;
        $record->courseid = $courseid;
        $record->userid = $userid;
        $record->reason = $reason;
        $record->status = 0; // Pending
        $record->timecreated = time();
        $record->modifiedby = $userid;

        return $DB->insert_record('examexcuse_requests', $record, true); // return inserted id
    }



    /**
     * Get all requests for a given excuse exam activity.
     */
    public static function get_requests(int $examexcuseid): array
    {
        global $DB;

        return $DB->get_records('examexcuse_requests', ['examexcuseid' => $examexcuseid]);
    }

    /**
     * Update the status of a request (approve or reject).
     */
    public static function update_request_status(int $requestid, int $status, int $modifiedby): bool
    {
        global $DB;

        $record = $DB->get_record('examexcuse_requests', ['id' => $requestid], '*', MUST_EXIST);
        $record->status = $status;
        $record->modifiedby = $modifiedby;

        return $DB->update_record('examexcuse_requests', $record);
    }
}
