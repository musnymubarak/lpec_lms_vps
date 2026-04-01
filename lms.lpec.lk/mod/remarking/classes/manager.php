<?php

namespace mod_remarking;

defined('MOODLE_INTERNAL') || die();

class manager
{

    /**
     * Check if a user can submit a Remarking request.
     */
    public static function can_submit_request(\context $context): bool
    {
        return has_capability('mod/remarking:submit', $context);
    }

    /**
     * Check if a user can manage Remarking requests.
     */
    public static function can_manage_requests(\context $context): bool
    {
        return has_capability('mod/remarking:manage', $context);
    }

    /**
     * Submit a new Remarking request.
     */
    public static function submit_request(int $remarkingid, int $courseid, int $userid, string $reason): int
    {
        global $DB;

        $record = new \stdClass();
        $record->remarkingid = $remarkingid;
        $record->courseid = $courseid;
        $record->userid = $userid;
        $record->reason = $reason;
        $record->status = 0; // Pending
        $record->timecreated = time();
        $record->modifiedby = $userid;

        return $DB->insert_record('remarking_requests', $record, true); // return inserted id
    }



    /**
     * Get all requests for a given Remarking activity.
     */
    public static function get_requests(int $remarkingid): array
    {
        global $DB;

        return $DB->get_records('remarking_requests', ['remarkingid' => $remarkingid]);
    }

    /**
     * Update the status of a request (approve or reject).
     */
    public static function update_request_status(int $requestid, int $status, int $modifiedby): bool
    {
        global $DB;

        $record = $DB->get_record('remarking_requests', ['id' => $requestid], '*', MUST_EXIST);
        $record->status = $status;
        $record->modifiedby = $modifiedby;

        return $DB->update_record('remarking_requests', $record);
    }
}
