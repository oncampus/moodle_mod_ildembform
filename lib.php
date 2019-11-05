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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/ildembform/locallib.php');
require_once($CFG->dirroot . '/mod/ildembform/contact_form.php');

/**
 * Add ildembform instance.
 * @param stdClass $data
 * @param mod_ildembform_mod_form $mform
 * @return int new ildembform instance id
 */
function ildembform_add_instance($data) {
    global $CFG, $DB;

    $cmid = $data->coursemodule;
    $data->timemodified = time();
    $data->description = $data->description['text'];

    $data->id = $DB->insert_record('ildembform', $data);

    // We need to use context now, so we need to make sure all needed info is already in db.
    $DB->set_field('course_modules', 'instance', $data->id, array('id' => $cmid));

    return $data->id;
}

/**
 * Update ildembform instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function ildembform_update_instance($data) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    $cmid = $data->coursemodule;
    $data->timemodified = time();
    $data->id = $data->instance;
    $data->description = $data->description['text'];

    $DB->update_record('ildembform', $data);

    return true;
}

/**
 * Delete ildembform instance.
 * @param int $id
 * @return bool true
 */
function ildembform_delete_instance($id) {
    global $DB;

    if (!$ildembform = $DB->get_record('ildembform', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('ildembform', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'ildembform', $id, null);

    // Note: all context files are deleted automatically.
    $DB->delete_records('ildembform', array('id' => $ildembform->id));

    return true;
}

/**
 * embedded instance of ildembform in course/view.php.
 * @param array $cm
 * @return no return set
 */

function ildembform_cm_info_dynamic(cm_info $cm) {
    global $CFG, $DB, $USER;

    $cmr = $cm->get_course_module_record();

    $id = $cmr->course;
    $instanceid = $cmr->instance;

    $formdata = $DB->get_record('ildembform', array('id' => $instanceid));
    $context = context_course::instance($id);

    // Special parameters for the ild mooc format.
    $chapter = optional_param('chapter', '', PARAM_INT);
    $week = optional_param('selected_week', '', PARAM_INT);

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

    // If $contentview true, the form will be embeded in the course view,
    // otherwise the form will be reached over the view.php of the plugin.
    $contentview = $formdata->contentview;

    $url = $CFG->wwwroot . '/mod/ildembform/view.php?id=' . $cmr->id;

    if ($USER->editing != 1) {
        // Add course id and instance id to $embform.
        $toform = array('courseid' => $id,
            'instanceid' => $instanceid,
            'chapter' => $chapparam,
            'selected_week' => $weekparam,
        );
    }

    // If $contentview is false, only a link to the ildembform view.php is visible.
    if ($USER->editing != 1) {
        if ($contentview) {
            $embform = new ildembform_form($url);
            $embform->set_data($toform);

            $out = '<h4>' . $formdata->name . '</h4>';
            $out .= $formdata->description;
            $out .= $embform->render();

            $cm->set_content($out);
            $cm->set_no_view_link();
        }
    }

}