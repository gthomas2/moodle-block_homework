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
 * Moodle event observers (2.5+), see classes/observers.php
 * @package    block_homework
 * @copyright  2016 Overnet Data Ltd. (@link http://www.overnetdata.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => \core\event\course_module_deleted::class,
        'callback' => '\block_homework\observers::course_module_deleted'
    ],
    [
        'eventname' => \core\event\message_viewed::class,
        'callback' => '\block_homework\observers::message_viewed'
    ],
    [
        'eventname' => \core\event\course_module_viewed::class,
        'callback' => '\block_homework\observers::course_module_viewed'
    ],
    [
        'eventname' => \mod_assign\event\submission_status_viewed::class,
        'callback' => '\block_homework\observers::submission_status_viewed'
    ]
];