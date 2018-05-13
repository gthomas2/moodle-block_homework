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
 * Clone assignment page
 *
 * @package    block_homework
 * @copyright  2018 Overnet Data Ltd. (@link http://www.overnetdata.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->dirroot.'/course/lib.php');

global $OUTPUT, $PAGE, $DB, $USER, $SITE;

$cmid = required_param('cmid', PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_INT);

$row = $DB->get_record('block_homework_assignment', ['coursemoduleid' => $cmid]);
if (!$row) {
    print_error('invalidcmid', 'block_homework');
}
list ($course, $cm) = get_course_and_cm_from_cmid($row->coursemoduleid);

$coursecontext = context_course::instance($course->id);

if ($row->userid !== $USER->id && !has_capability('block/homework:deleteany', context_system::instance())) {
    print_error('error:deleteitemnotyours', 'block_homework');
    if ($SITE->id == $course->id || !can_delete_course($course->id)) {
        print_error('cannotdeletecourse');
    }
}

$assign = $DB->get_record('assign', ['id' => $cm->instance]);

if ($assign->duedate < time()) {
    print_error('error:onlydeletefutureassignments', 'block_homework');
}

$returl = new moodle_url('/blocks/homework/view.php', ['course' => $course->id]);

$PAGE->set_url('/blocks/homework/delete.php', ['cmid' => $cmid]);
$PAGE->set_context(CONTEXT_SYSTEM::instance());

$delurl = new moodle_url('/blocks/homework/delete.php', ['cmid' => $cmid, 'confirm' => 1]);

if ($confirm) {
    assign_delete_instance($assign->id);
    $DB->delete_records('block_homework_assignment', ['id' => $row->id]);
    redirect($returl);
}

echo $OUTPUT->header();
echo $OUTPUT->confirm(get_string('deletehomework', 'block_homework', $row->subject), $delurl, $returl);
echo $OUTPUT->footer();