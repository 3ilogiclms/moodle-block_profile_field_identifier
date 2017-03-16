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


/**
 * This function return custom field name on the base of input paramet custom field ID.
 *
 * @param int   $id custom field id
 * @return String custom field name.
 */
function get_custome_fieldname($id) {
    global $DB;
    $field_name = $DB->get_record('user_info_field', array('id' => $id));
    return $field_name->name;
}

/**
 * This function return query on the basis of field
 *
 * @param int $field field id
 * @return String query.
 */
function check_emtpy_field($field) {
    global $DB;
    if ($field = 'picture') {
        $q = "SELECT id, firstname, lastname, email, picture FROM {user} where picture = 0 AND deleted=0 AND firstname!='Guest user'";
    } else if ($optional_field == $field) {
        $q = "SELECT id, firstname, lastname, email, picture FROM {user} where $field = '' AND deleted=0 AND firstname!='Guest user'";
    } else {
        $q = "SELECT id, firstname, lastname, email from {user}";
    }
    return $q;
}

/**
 * This function return return query on basis of defined criteria.
 *
 * @param int $field
 * @param int $courseid
 * @param int $roleid
 * @return String query.
 */
function get_optional_field_query($field, $courseid, $roleid) {
    global $DB;
    if ($field == 'picture') {

    } else if (field_exist($field)) {
        $q = "SELECT usr.firstname, usr.id, usr.lastname, usr.email,usr.phone2,c.fullname
           FROM {course} c
           INNER JOIN {context} cx ON c.id = cx.instanceid
           AND cx.contextlevel = '50' and c.id=$courseid
           INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
           INNER JOIN {role} r ON ra.roleid = r.id
           INNER JOIN {user} usr ON ra.userid = usr.id
           WHERE r.id = $roleid";
    } else {
        $q = "SELECT id, firstname, lastname, email from {user}";
    }
    return $q;
}

/**
 * This function return value of field name on the basis of field id.
 *
 * @param int $id field id
 * @return String field name.
 */
function get_optional_field($id) {
    $optional_fields = array('Profile Picture', 'List of interests', 'Web page', 'ICQ number', 'AIM ID', 'Yahoo ID'
        , 'MSN ID', 'ID number', 'Institution', 'Department', 'Phone', 'Mobile phone', 'Address');
    $field_name = $optional_fields[$id];
    return $field_name;
}

/**
 * This function check the optional field exist or not.
 *
 * @param String   $id Optional field name
 * @return Boolean Optional field exist or not.
 */
function field_exist($field) {
    $optional_fields = array('Profile Picture', 'List of interests', 'Web page', 'ICQ number', 'AIM ID'
        , 'Yahoo ID', 'MSN ID', 'ID number', 'Institution', 'Department', 'Phone', 'Mobile phone', 'Address');
    if (in_array($field, $optional_fields)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Send notification to those who has not added their picture.
 *
 * @param null
 * @return null.
 */
function user_have_no_picture() {
    global $DB;
    $id = $DB->get_records_sql('SELECT id FROM {user} where picture = ? AND username != ?', array(0, 'guest'));
    foreach ($id as $ids) {
        global $DB;
        $user = $DB->get_record('user', array('id' => $ids->id));
        send_msg($user);
    }
}

/**
 * This function will send the message.
 *
 * @param int   $user1 Moodle user ID
 * @param int   $msg Notification Text
 * @param int   $fieldname Optional field name
 * @return Boolean Optional field exist or not.
 */
function send_msg($user1, $msg, $fieldname) {
    $eventdata = new \core\message\message();
    $eventdata->component = 'block_profile_field_identifier'; // Your component name.
    $eventdata->name = 'view'; // This is the message name from messages.php.
    $eventdata->userfrom = get_admin();
    $eventdata->userto = $user1;
    $eventdata->subject = get_string('notification', 'block_profile_field_identifier');
    $username = getuser($user1);
    $eventdata->fullmessagehtml = "Dear $username,<br/><br/>$msg<br/><br/>Thank you";
    $eventdata->fullmessageformat = FORMAT_HTML;
    $eventdata->smallmessage = '';
    $eventdata->notification = 1; // This is only set to 0 for personal messages between users.
    message_send($eventdata);
}

/**
 * This function return user full name.
 *
 * @param int $id user id
 * @return String user name.
 */
function getuser($id) {
    global $DB;
    $result = $DB->get_record_sql("SELECT concat (firstname,' ', lastname) as name FROM {user} WHERE id = ?", array($id));
    return $result->name;
}
