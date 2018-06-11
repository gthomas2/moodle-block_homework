<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Event observers
 * @package    block_homework
 * @copyright  2016 Overnet Data Ltd. (@link http://www.overnetdata.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_homework;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/homework/edulink_classes/homework.php');

class observers {

    public static function course_module_deleted(\core\event\course_module_deleted $event) {
        $coursemoduleid = $event->get_data()["objectid"];
        \block_homework_utils::remove_homework_tracking_record($coursemoduleid);
    }

    public static function message_viewed(\core\event\message_viewed $event) {
        global $DB;
        $lognotifications = get_config('block_homework', 'log_notifications');
        if ($lognotifications) {
            $data = $event->get_data();
            $oldmessageid = $data["other"]["messageid"];
            $newmessageid = $data["objectid"];
            $sql = 'UPDATE {block_homework_notification} SET messagereadid = ? WHERE messageid = ?';
            $DB->execute($sql, array($newmessageid, $oldmessageid));
        }
    }

    private static function log_modulefirstviewed($cmid) {
        global $DB, $USER;

        // Update course module first viewed timestamp.
        $params = [$cmid, $USER->id];
        $rs = $DB->get_records_select('block_homework_notification',
            'coursemoduleid = ? AND recipientuserid = ? AND modulefirstviewed IS NULL',
            $params);
        foreach ($rs as $row) {
            $row->modulefirstviewed = time();
            $DB->update_record('block_homework_notification', $row);
        }
    }

    public static function course_module_viewed(\core\event\course_module_viewed $event) {
        $cmid = $event->objectid;
        self::log_modulefirstviewed($cmid);

    }

    public static function submission_status_viewed(\mod_assign\event\submission_status_viewed $event) {
        $cmid = $event->contextinstanceid;
        self::log_modulefirstviewed($cmid);
    }
}
