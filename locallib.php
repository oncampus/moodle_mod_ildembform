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

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class ildembform_sendmail {
	
	public function __construct() {
		global $CFG;
		
		if (!isloggedin() && isguestuser()) {
			print_error(get_string('loginerror', 'ildembform'));
		}
				
	}
	
	/**
	 * Function for sending email to 
	 *
	 * @see email_to_user
	 * @param string	$subject
	 * @param string	$message
	 * @param int		$cid
	 * @param string/array	$receivers
	 * @param string	$url
	 * @return bool		Returning status indicating success or failure
	 */
	
	public function sendmessage($subject, $message, $cid, $receivers, $url) {
		global $USER, $DB;
		
		$fromfirstname = trim($USER->firstname);
		$fromlastname = trim($USER->lastname);
		$frommail = trim($USER->email);		
		$fromusername = trim($USER->username);		
		
		$course = $DB->get_record('course', array('id'=>$cid), '*', MUST_EXIST);	
		
		$fromcourse = $course->fullname;
		
		// Start building the mail body
		$htmlmessage = '';
		$htmlmessage .= '<p><strong>Betreff:</strong><br>' . $subject . '</p>';
		$htmlmessage .= '<p><strong>Mitteilung gesendet aus dem Kurs:</strong><br>' . $fromcourse . '<br />' . $url . '</p>';
		$htmlmessage .= '<p><strong>Sie haben folgende Mitteilung erhalten:</strong><br>' . $message . '</p>';
		$htmlmessage .= '<hr />';
		$htmlmessage .= '<p><strong>Gesendet von:</strong><br>' . $fromfirstname . ' ' . $fromlastname . '<br>E-Mail: ' . $frommail . '</p>';
	
		// If all required data exists, check $receivers if is array or string
		if (is_array($receivers)) {
			foreach ($receivers as $rec) {
				$userobjectto = self::create_touser($rec, 'Moodle @ open vhb', 'Mitteilung', '', '-99', 1, true);
				$userobjectfrom = self::create_fromuser($frommail, $fromfirstname, $fromlastname, $fromusername, $USER->id, 1, true);
								
				if(email_to_user($userobjectto, $userobjectfrom, $subject, html_to_text($htmlmessage), $htmlmessage, true, '', '')) {
					$send = true;
				} else {
					$send = false;
				}	
			}
		} else {
			$userobjectto = self::create_touser($receivers, 'Moodle @ open vhb', 'Mitteilung', '', '-99', 1, true);
			$userobjectfrom = self::create_fromuser($frommail, $fromfirstname, $fromlastname, $fromusername, $USER->id, 1, true);
			
			if(email_to_user($userobjectto, $userobjectfrom, $subject, html_to_text($htmlmessage), $htmlmessage, true, '', '')) {
				$send = true;
			} else {
				$send = false;
			}	
		}

		return $send;
	}
	
	/**
	 * The function email_to_user needs an object for the first to parameters to and from
	 * Within this function we will build the required object
	 *
	 * @see email_to_user
	 * @param string	$receiver
	 * @param string	$firstname
	 * @param string	$lastname
	 * @param string	$username
	 * @param int		$id
	 * @param int		$mailformat (default = 1)
	 * @param int		$maildesplay (default = true)
	 * @return array	Returning the object of type stdClass
	 */
	
	private static function create_fromuser($receiver, $firstname, $lastname, $username = '', $id = '', $mailformat = 1, $maildisplay = true) {
				
		$emailfromuser = new stdClass;
		
		$emailfromuser->email		 		= $receiver;
		$emailfromuser->firstname	 		= $firstname;
		$emailfromuser->lastname	 		= $lastname;
		$emailfromuser->username	 		= $username;
		$emailfromuser->maildisplay	 		= $maildisplay;
		$emailfromuser->mailformat	 		= $mailformat;
		$emailfromuser->id 					= $id;
		$emailfromuser->firstnamephonetic	= '';
		$emailfromuser->lastnamephonetic	= '';
		$emailfromuser->middlename			= '';
		$emailfromuser->alternatename		= '';
		
		return $emailfromuser;
	}
	
	/**
	 * The function email_to_user needs an object for the first to parameters to and from
	 * Within this function we will build the required object
	 *
	 * @see email_to_user
	 * @param string	$receiver
	 * @param string	$firstname
	 * @param string	$lastname
	 * @param string	$username
	 * @param int		$id
	 * @param int		$mailformat (default = 1)
	 * @param int		$maildesplay (default = true)
	 * @return array	Returning the object of type stdClass
	 */
	 
	private static function create_touser($receiver, $firstname, $lastname, $username = '', $id = '', $mailformat = 1, $maildisplay = true) {
		
		$emailtouser = new stdClass;
		
		$emailtouser->email			 		= $receiver;
		$emailtouser->firstname	 			= $firstname;
		$emailtouser->lastname	 			= $lastname;
		$emailtouser->username	 			= $username;
		$emailtouser->maildisplay	 		= $maildisplay;
		$emailtouser->mailformat	 		= $mailformat;
		$emailtouser->id 					= $id;
		$emailtouser->firstnamephonetic		= '';
		$emailtouser->lastnamephonetic		= '';
		$emailtouser->middlename			= '';
		$emailtouser->alternatename			= '';
		
		return $emailtouser;
	}
	
}