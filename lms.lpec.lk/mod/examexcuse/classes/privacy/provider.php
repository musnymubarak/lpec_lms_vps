<?php
namespace mod_examexcuse\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('examexcuse_requests', [
            'userid' => 'privacy:metadata:examexcuse_requests:userid',
            'reason' => 'privacy:metadata:examexcuse_requests:reason',
            'status' => 'privacy:metadata:examexcuse_requests:status',
            'timecreated' => 'privacy:metadata:examexcuse_requests:timecreated',
            'modifiedby' => 'privacy:metadata:examexcuse_requests:modifiedby',
        ], 'privacy:metadata:examexcuse_requests');

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