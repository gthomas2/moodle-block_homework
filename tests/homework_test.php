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
 * classes/edulink_classes/homework tests
 *
 * @package   block_homework
 * @author    Guy Thomas <brudinie@gmail.com>
 * @copyright Copyright (c) 2018 Overnetdata
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_homework\tests;

defined('MOODLE_INTERNAL') || die();

use block_homework_utils;


/**
 * classes/edulink_classes/homework tests
 *
 * @package   block_homework
 * @author    Guy Thomas <brudinie@gmail.com>
 * @copyright Copyright (c) 2018 Overnetdata
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_homework_homework_test extends \advanced_testcase {

    public function setUp() {
        global $CFG; // Even though not used here, we need to declare it for requires.

        require_once(__DIR__.'/../edulink_classes/homework.php');
    }

    public function test_get_unsent_assignment_notifications() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $unsent = \phpunit_util::call_internal_method(null, 'get_unsent_assignment_notifications', [],
            block_homework_utils::class);

        // Should be no notifications pending.
        $this->assertEmpty($unsent);

        $dg = $this->getDataGenerator();
        $course = $dg->create_course();
        $user = $dg->create_user();
        $dg->enrol_user($user->id, $course->id);

        // Create assignment in future and test for 1 notification pending.
        $record = [
            'allowsubmissionsfromdate' => time() - WEEKSECS,
            'course' => $course->id,
            'duedate' => time() + WEEKSECS
        ];
        $assign = $dg->create_module('assign', $record);
        list($course, $cm) = get_course_and_cm_from_instance($assign->id, 'assign');
        $subject = 'Assignment in future';
        $duration = WEEKSECS;
        $notifyother = 0;
        $notifyotheremail = null;
        $notifyparents = 0;
        $notesforparentssubject = null;
        $notesforparents = null;
        $notifylearners = 1;
        $notesforlearnerssubject = 'Learners notification subject';
        $notesforlearners = 'Some notes for learners';
        \block_homework_utils::add_homework_tracking_record($cm->id, $user->id, $subject, $duration,
            $notifyother, $notifyotheremail,
            $notifyparents, $notesforparentssubject, $notesforparents,
            $notifylearners, $notesforlearnerssubject, $notesforlearners);
        $unsent = \phpunit_util::call_internal_method(null, 'get_unsent_assignment_notifications', [],
            block_homework_utils::class);
        $this->assertCount(1, $unsent);

        // Create assignment in past and test for 2 notifications pending.
        $record = [
            'allowsubmissionsfromdate' => time() - WEEKSECS,
            'course' => $course->id,
            'duedate' => time() - YEARSECS
        ];
        $assign = $dg->create_module('assign', $record);
        list($course, $cm) = get_course_and_cm_from_instance($assign->id, 'assign');
        $subject = 'Assignment in past';
        \block_homework_utils::add_homework_tracking_record($cm->id, $user->id, $subject, $duration,
            $notifyother, $notifyotheremail,
            $notifyparents, $notesforparentssubject, $notesforparents,
            $notifylearners, $notesforlearnerssubject, $notesforlearners);
        $unsent = \phpunit_util::call_internal_method(null, 'get_unsent_assignment_notifications', [],
            block_homework_utils::class);
        $this->assertCount(2, $unsent);

        // Mark notificationssent and check that assignment disappears from unsent results.
        $first = reset($unsent);
        $first->notificationssent = 1;
        $DB->update_record('block_homework_assignment', $first);
        $unsent = \phpunit_util::call_internal_method(null, 'get_unsent_assignment_notifications', [],
            block_homework_utils::class);
        $this->assertCount(1, $unsent);
    }
}