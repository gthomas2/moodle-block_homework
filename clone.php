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

global $OUTPUT, $PAGE, $DB, $USER;

$cmid = required_param('cmid', PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_INT);

$row = $DB->get_record('block_homework_assignment', ['coursemoduleid' => $cmid]);
if (!$row) {
    print_error('invalidcmid', 'block_homework');
}
if ($row->userid !== $USER->id) {
    print_error('error:clonesrcnotyours', 'block_homework');
}

list ($course, $cm) = get_course_and_cm_from_cmid($row->coursemoduleid);

$assign = $DB->get_record('assign', ['id' => $cm->instance]);

if ($assign->allowsubmissionsfromdate > time() || $assign->duedate > time()) {
    print_error('error:cloneisnotinpast', 'block_homework');
}

$returl = new moodle_url('/blocks/homework/view.php', ['course' => $course->id]);

$PAGE->set_url('/blocks/homework/clone.php', ['cmid' => $cmid]);
$PAGE->set_context(CONTEXT_SYSTEM::instance());

$cloneurl = new moodle_url('/blocks/homework/clone.php', ['cmid' => $cmid, 'confirm' => 1]);

if ($confirm) {
    $newcm = duplicate_module($course, $cm);
    $clone = clone $row;
    unset($clone->id);
    $clone->coursemoduleid = $newcm->id;
    $DB->insert_record('block_homework_assignment', $clone);
    redirect($returl);
}

echo $OUTPUT->header();
echo $OUTPUT->confirm(get_string('clonehomework', 'block_homework', $row->subject), $cloneurl, $returl);
echo $OUTPUT->footer();