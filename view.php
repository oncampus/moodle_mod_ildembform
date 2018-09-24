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
 * @package mod_ildembform
 * @copyright  2018 Stefan Bomanns, ILD, Technische Hochschule Lübeck, <stefan.bomanns@th-luebeck.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/ildembform/lib.php');
require_once($CFG->dirroot.'/mod/ildembform/locallib.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p       = optional_param('p', 0, PARAM_INT);  // ildembform instance ID

// special parameters for the ild mooc format
$chapter       = optional_param('chapter', '', PARAM_INT);  // the current chapter
$week	       = optional_param('selected_week', '', PARAM_INT);  // the current week (lesson)

if ($p) {
    if (!$ildembform = $DB->get_record('ildembform', array('id'=>$p))) {
        print_error(get_string('invalidaccessparameter', 'ildembform'));
    }
    $cm = get_coursemodule_from_instance('ildembform', $ildembform->id, $ildembform->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('ildembform', $id)) {
        print_error(get_string('invalidcoursemodule', 'ildembform'));
    }
    $ildembform = $DB->get_record('ildembform', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$context = context_course::instance($cm->course);

require_capability('mod/ildembform:view', $context);
require_course_login($course, true, $cm);

// if the params $chapter and $lesson exist, add them to the $url for the redirect
// (this params are only required for the ild specific mooc format)
if (!empty($chapter)) {
	$chap_param = '&chapter=' . $chapter;
} else {
	$chap_param = '';
}

if (!empty($week)) {
	$week_param = '&selected_week=' . $week;
} else {
	$week_param = '';
}

$url = new moodle_url($CFG->wwwroot . '/course/view.php?id=' . $cm->course . $chap_param . $week_param);

$PAGE->set_url('/mod/ildembform/view.php', array('id' => $cm->id));
$PAGE->set_title($course->shortname.': '.$ildembform->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($ildembform);


// action url
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
		
	if ($sendmessage->sendmessage($subject, $message, $cm->course, $receivers, $url)) {
		redirect($url, get_string('sendsuccess', 'ildembform'), null, \core\output\notification::NOTIFY_SUCCESS);
	} else {	
		redirect($url, get_string('senderror', 'ildembform'), null, \core\output\notification::NOTIFY_ERROR);

	}
	
} else {
		
	//Set default data
	$toform = array('courseid' => $id,
					'instanceid' => '');
				
	$embform->set_data($toform);
		
	echo $OUTPUT->header();	
	echo $OUTPUT->heading(format_string($ildembform->name), 2);
	echo html_writer::tag('p', $ildembform->description);
	
	//displays the form
	$embform->display();	
	
	echo $OUTPUT->footer();
}