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

require_once('../../config.php');
require_once('profile_field_identifier_form.php');
require_once("lib.php");
global $DB;
$attributes = array();
$ftid = optional_param('id', null, PARAM_INT);
// Optional field on drop down.
if ($ftid == '10') {
    $attributes = array('picture' => 'Profile Picture', 'skype' => 'Skype', 'url' => 'Web page',
        'icq' => 'ICQ number', 'aim' => 'AIM ID', 'yahoo' => 'Yahoo ID', 'msn' => 'MSN'
        . 'ID', 'idnumber' => 'ID number', 'institution' => 'Institution', 'department'
        => 'Department', 'phone1' => 'Phone', 'phone2' => 'Mobile phone',
        'address' => 'Address');
} else if ($ftid == '20') {
    // Custom Field on drop down Query.
    $attributes = $DB->get_records_sql_menu('SELECT id, name FROM {user_info_field}', array($params = null), $limitfrom = 0, $limitnum = 0);
} else {
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

    $attributes = $DB->get_records_sql_menu('Select DISTINCT r.id, r.'.$rolenames .' FROM {course} c
                                            INNER JOIN {context} cx ON c.id = cx.instanceid AND
                                            c.id='.$ftid .' INNER JOIN {role_assignments} ra ON
                                            cx.id = ra.contextid INNER JOIN {role} r ON
                                            ra.roleid = r.id', null, $limitfrom = 0, $limitnum = 0);

    $attributes = array_map('ucfirst', $attributes);
}
$data = "";

foreach ($attributes as $key => $attrib) {
    $data .= $key . '~' . $attrib . '^';
}
return print_r($data);
