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
 * ildembform form for the contact form
 *
 * @package     mod_ildembform
 * @copyright   2019 Stefan Bomanns, ILD, Technische Hochschule Lübeck, <stefan.bomanns@th-luebeck.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir.'/formslib.php');

class ildembform_form extends moodleform
{
    public function definition() {

        $mform =& $this->_form;
        $mform->addElement('text', 'subject', get_string('addsubject', 'ildembform'), array('size' => '64'));
        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', null, 'required', null, 'client');

        $mform->addElement('textarea', 'message', get_string('addmessage', 'ildembform'), array('rows' => '8', 'cols' => '40'));
        $mform->setType('message', PARAM_TEXT);
        $mform->addRule('message', null, 'required', null, 'client');

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_RAW);

        $mform->addElement('hidden', 'instanceid');
        $mform->setType('instanceid', PARAM_RAW);

        $mform->addElement('hidden', 'chapter');
        $mform->setType('chapter', PARAM_RAW);

        $mform->addElement('hidden', 'selected_week');
        $mform->setType('selected_week', PARAM_RAW);

        // We use a custom buttonaarray instead of add_action_buttons.
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('sendform', 'ildembform'));
        $buttonarray[] = $mform->createElement('reset', 'resetbutton', get_string('revert'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
}