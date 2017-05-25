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
 * Block to send notification identified users based on missing or filled optional/custom fields.
 *
 * @package    block_profile_field_idntifier
 * @copyright  3i Logic<lms@3ilogic.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');
require_once("{$CFG->libdir}/formslib.php");
require_once("lib.php");

/**
 * Display form to send notification on the basis of filter option. It will also displayed user list.
 *
 * @copyright 3i Logic<lms@3ilogic.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_identifier_form extends moodleform {

    public function definition() {
        global $DB, $CFG;
        $mform = & $this->_form;
        $mform->addElement('header', 'profile_field_identifier', get_string('profile_field_identifier', 'block_profile_field_identifier'));
		
        $attributes = array('10' => 'Optional Field', '20' => 'Custom Field');
        $mform->addElement('select', 'ftid', get_string('selectfieldtype', 'block_profile_field_identifier'), $attributes);
        $attributes = array('picture' => 'Profile Picture', 'skype' => 'Skype', 'url' => 'Web page', 'icq' => 'ICQ number'
            , 'aim' => 'AIM ID', 'yahoo' => 'Yahoo ID', 'msn' => 'MSN ID', 'idnumber' => 'ID number'
            , 'institution' => 'Institution', 'department' => 'Department', 'phone1' => 'Phone', 'phone2' => 'Mobile phone', 'address' => 'Address');
        $mform->addElement('select', 'fid', get_string('selectfield', 'block_profile_field_identifier'), $attributes);
        // Show coursess on drop down.
        $attributes = $DB->get_records_sql_menu('SELECT id, fullname FROM {course} WHERE id != 1', array($params = null), $limitfrom = 0, $limitnum = 0);
        $mform->addElement('select', 'cid', get_string('selectcourse', 'block_profile_field_identifier'), $attributes);
        switch ($CFG->block_pfi_rolenames) {
            case '0':
                $rolenames = "shortname";
                break;
            case '1':
                $rolenames = "name";
                break;
            case '2':
                $rolenames = "archetype";
                break;
            default:
                $rolenames = "shortname";
        }



        $attributes = $DB->get_records_sql_menu('Select DISTINCT r.id, r.' . $rolenames . '  as fullname FROM {course} c
																								INNER JOIN {context} cx ON c.id = cx.instanceid
																								AND  c.id=1
																								INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
																								INNER JOIN {role} r ON ra.roleid = r.id', null, $limitfrom = 0, $limitnum = 0);
//        $attributes1 = array('teacher', 'student', 'manager', 'student');
//        $attributes = array_intersect($attributes, $attributes1);
        $attributes = array_map('ucfirst', $attributes);
        $mform->addElement('select', 'rid', get_string('selectrole', 'block_profile_field_identifier'), $attributes);
        $mform->addElement('textarea', 'msg', get_string('message', 'block_profile_field_identifier'), 'wrap="virtual" rows="8" cols="50"');
        $mform->addElement('hidden', 'viewpage', 1);
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('button', 'nextbtn', get_string('userswithfield', 'block_profile_field_identifier'), array("id" => "btnajax"));
        $mform->addElement('button', 'nextbtn2', get_string('missingfieldusers', 'block_profile_field_identifier'), array("id" => "btnajax2"));
    }

    public function display_list($fieldid, $courseid, $roleid, $fieldtype, $btn) {
        global $DB, $OUTPUT;
        if ($btn == 'btnajax2') {
            if ($fieldtype == 10) {
                $sql = "SELECT usr." . $fieldid . ", usr.firstname, usr.id, usr.lastname, usr.email,usr.phone2,c.fullname
										 FROM {course} c
										 INNER JOIN {context} cx ON c.id = cx.instanceid
										 AND cx.contextlevel = '50' and c.id=?
										 INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
										 INNER JOIN {role} r ON ra.roleid = r.id
										 INNER JOIN {user} usr ON ra.userid = usr.id
										 WHERE r.id =? and usr.deleted=0 and usr." . $fieldid;
                $sql .= ($fieldid == 'picture') ? " =0" : " =''";
                $field = $fieldid;
            } else {  //Show missing field users
                $sql = "SELECT usr.id FROM {course} c
					INNER JOIN {context} cx ON c.id = cx.instanceid
					 AND cx.contextlevel = '50' and c.id=?
					 INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
					 INNER JOIN {role} r ON ra.roleid = r.id
					 INNER JOIN {user} usr ON ra.userid = usr.id
					 INNER JOIN {user_info_data} dt ON usr.id = dt.userid
					 INNER JOIN {user_info_field} ucf ON dt.fieldid = ucf.id
					 WHERE (r.id =? and usr.deleted=0) and (dt.fieldid=" . $fieldid . " and dt.data!='') ";

                $rs = $DB->get_recordset_sql($sql, array($courseid, $roleid));
                $value = array(null);

                foreach ($rs as $log) {
                    $value[] = $log->id;
                }
                $value = "('" . implode($value, "', '") . "')";

                $field = get_custome_fieldname($fieldid);

                $sql = "SELECT usr.id,usr.firstname, usr.lastname, usr.email,usr.phone2,c.fullname
                  FROM {course} c
                  INNER JOIN {context} cx ON c.id = cx.instanceid
                  AND cx.contextlevel = '50' and c.id=?
                  INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
                  INNER JOIN {role} r ON ra.roleid = r.id
                  INNER JOIN {user} usr ON ra.userid = usr.id
                  WHERE r.id = ? and usr.id NOT IN $value";
            }
        } else if ($btn == 'btnajax') {
            if ($fieldtype == 10) {
                $sql = "SELECT usr." . $fieldid . ", usr.firstname, usr.id, usr.lastname, usr.email,usr.phone2,c.fullname
										 FROM {course} c
										 INNER JOIN {context} cx ON c.id = cx.instanceid
										 AND cx.contextlevel = '50' and c.id=?
										 INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
										 INNER JOIN {role} r ON ra.roleid = r.id
										 INNER JOIN {user} usr ON ra.userid = usr.id
										 WHERE r.id =? and usr.deleted=0 and usr." . $fieldid;
                $sql .= ($fieldid == 'picture') ? " >0" : " !=''";
                $field = $fieldid;
            } else {
                $sql = "SELECT ucf.name,dt.data, usr.firstname, usr.id, usr.lastname, usr.email,usr.phone2,c.fullname
											 FROM {course} c
											 INNER JOIN {context} cx ON c.id = cx.instanceid
											 AND cx.contextlevel = '50' and c.id=?
											 INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
											 INNER JOIN {role} r ON ra.roleid = r.id
											 INNER JOIN {user} usr ON ra.userid = usr.id
											 INNER JOIN {user_info_data} dt ON usr.id = dt.userid
											 INNER JOIN {user_info_field} ucf ON dt.fieldid = ucf.id
											 WHERE r.id =? and usr.deleted=0 and dt.fieldid=" . $fieldid . " and dt.data!=''";
                $field = get_custome_fieldname($fieldid);
            }
        }
        $table = new html_table();
        /*$table->attributes = array("class" => "table-sorter");*/
		$table->attributes = array("class" => "display");
		$table->head = array(get_string('profile_picture', 'block_profile_field_identifier'), get_string('fullname', 'block_profile_field_identifier'), ucfirst($field), "<a href='javascript:setCheckboxes();' style='color:#333;' class='chkmenu'>Select | unselect all</a>");
        $table->size = array('30%', '30%', '30%', '10%');
        $table->align = array('center', 'left', 'left', 'center');
        $table->width = '100%';
        $table->data = array();
        $rs = $DB->get_recordset_sql($sql, array($courseid, $roleid));
		
		if ($DB->record_exists_sql($sql, array($courseid, $roleid))) {
        foreach ($rs as $log) {
            $row = array();
            $user = $DB->get_record('user', array('id' => $log->id));
            $row[] = $OUTPUT->user_picture($user, array('size' => 100));
            $row[] = $log->firstname . " " . $log->lastname;
            if ($fieldtype == 10) {
                if ($fieldid == 'picture') {
                    $row[] = $field;
                } else {
                    $row[] = $log->$fieldid;
                }
            } else {

                if (!isset($log->data))
                    $log->data = '-';
                $row[] = $log->data;
            }
			
            $row[] = "<center><input style='width:20px; height:30px;' type='checkbox' class='check_list' name='user[]' value='$log->id'/></center>";
            $table->data[] = $row;
        }
		}
		else {
            $table->data[] = array('', '', 'Record not found!', '');
        }
        return $table;
    }

}
