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
 * ildembform form for settings
 *
 * @package     mod_ildembform
 * @copyright   2019 Stefan Bomanns, ILD, Technische Hochschule Lübeck, <stefan.bomanns@th-luebeck.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_ildembform_mod_form extends moodleform_mod
{
    public function definition() {

        $mform =& $this->_form;

        $ynoptions = array(0 => get_string('no'), 1 => get_string('yes'));
        $mform->addElement('select', 'contentview', get_string('contentview', 'ildembform'), $ynoptions);
        $mform->setDefault('contentview', 1);
        $mform->addHelpButton('contentview', 'contentviewhelp', 'ildembform');

        $mform->addElement('text', 'name', get_string('addheading', 'ildembform'), array('size' => '64'));
        $mform->setType('name', PARAM_RAW);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'description', get_string('adddescription', 'ildembform'));
        $mform->setType('description', PARAM_RAW);
        $mform->addRule('description', null, 'required', null, 'client');

        $mform->addElement('text', 'emails', get_string('addemail', 'ildembform'), array('size' => '64'));
        $mform->setType('emails', PARAM_RAW);
        $mform->addHelpButton('emails', 'addemailhelp', 'ildembform');

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }
}

