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
 * ildembform module version information
 *
 * @package     mod_ildembform
 * @copyright   2019 oncampus GmbH, <support@oncampus.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once("../../config.php");

require_login();

require_once($CFG->dirroot . '/mod/ildembform/lib.php');
require_once($CFG->dirroot . '/mod/ildembform/locallib.php');

// Course Module ID.
$id = optional_param('id', 0, PARAM_INT);
// Ildembform instance ID.
$p = optional_param('p', 0, PARAM_INT);

// Special parameters for the mooc format.

// The current chapter.
$chapter = optional_param('chapter', '', PARAM_INT);
// The current week (lesson).
$week = optional_param('selected_week', '', PARAM_INT);

if ($p) {
    if (!$ildembform = $DB->get_record('ildembform', array('id' => $p))) {
        print_error(get_string('invalidaccessparameter', 'ildembform'));
    }
    $cm = get_coursemodule_from_instance('ildembform', $ildembform->id, $ildembform->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('ildembform', $id)) {
        print_error(get_string('invalidcoursemodule', 'ildembform'));
    }
    $ildembform = $DB->get_record('ildembform', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$context = context_course::instance($cm->course);

require_capability('mod/ildembform:view', $context);


// If the params $chapter and $lesson exist, add them to the $url for the redirect,
// (this params are only required for the ild specific mooc format).

if (!empty($chapter)) {
    $chapparam = '&chapter=' . $chapter;
} else {
    $chapparam = '';
}

if (!empty($week)) {
    $weekparam = '&selected_week=' . $week;
} else {
    $weekparam = '';
}

$url = new moodle_url($CFG->wwwroot . '/course/view.php?id=' . $cm->course . $chapparam . $weekparam);

$PAGE->set_url('/mod/ildembform/view.php', array('id' => $cm->id));
$PAGE->set_title($course->shortname . ': ' . $ildembform->name);
$PAGE->set_heading($course->fullname);


// Action url.
$actionurl = $CFG->wwwroot . '/mod/ildembform/view.php?id=' . $cm->id;

$embform = new ildembform_form($actionurl);

if ($fromform = $embform->get_data()) {

    $sendmessage = new ildembform_sendmail;

    $subject = $fromform->subject;
    $message = $fromform->message;

    $formdata = $DB->get_record('ildembform', array('id' => $cm->instance));
    if (!empty($formdata->emails)) {
        $receivers = explode(', ', $formdata->emails);
    } else {
        $receivers = array();
        $teachers = get_role_users(3, $context);
        foreach ($teachers as $tea) {
            $receivers[] = $tea->email;
        }
    }

    if ($sendmessage->sendmessage($subject, $message, $cm->course, $receivers, $url, $formdata->anonymized)) {
        redirect($url, get_string('sendsuccess', 'ildembform'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect($url, get_string('senderror', 'ildembform'), null, \core\output\notification::NOTIFY_ERROR);

    }

} else {

    // Set default data.
    $toform = array('courseid' => $id,
        'instanceid' => '');

    $embform->set_data($toform);


    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($ildembform->name), 2);
    echo html_writer::tag('p', $ildembform->description);

    // Displays the form.
    $embform->display();

    echo $OUTPUT->footer();
}