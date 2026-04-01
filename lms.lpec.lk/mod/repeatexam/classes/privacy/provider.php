<?php
namespace mod_repeatexam\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('repeatexam_requests', [
            'userid' => 'privacy:metadata:repeatexam_requests:userid',
            'reason' => 'privacy:metadata:repeatexam_requests:reason',
            'status' => 'privacy:metadata:repeatexam_requests:status',
            'timecreated' => 'privacy:metadata:repeatexam_requests:timecreated',
            'modifiedby' => 'privacy:metadata:repeatexam_requests:modifiedby',
        ], 'privacy:metadata:repeatexam_requests');

        return $collection;
    }

    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        return new \core_privacy\local\request\contextlist();
    }

    public static function export_user_data(\core_privacy\local\request\approved_contextlist $contextlist) {
    }

    public static function delete_data_for_all_users_in_context(\context $context) {
    }

    public static function delete_data_for_user(\core_privacy\local\request\approved_contextlist $contextlist) {
    }
}