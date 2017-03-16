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
require_once('lib.php');
require_once("{$CFG->libdir}/formslib.php");
require_login();
$release = $CFG->release;
$release = explode(" (", $release);
if ($release[0] >= 2.2) {
    $PAGE->set_context(context_system::instance());
} else {
    $PAGE->set_context(get_system_context());
}
// Variable.
$send = optional_param('send', null, PARAM_RAW);
$viewpage = required_param('viewpage', PARAM_INT);
global $DB, $OUTPUT, $PAGE, $CFG;
$PAGE->set_url('/blocks/profile_field_identifier/view.php');
$PAGE->set_title(get_string("pluginname", 'block_profile_field_identifier'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string("pluginname", 'block_profile_field_identifier'));
$pageurl = new moodle_url('/blocks/profile_field_identifier/view.php?viewpage=' . $viewpage);
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string("pluginname", 'block_profile_field_identifier'));
echo $OUTPUT->header();
// Form Display.
if ($viewpage == 1) {
    $a = "";
    $form = new field_identifier_form();
    $form->display();
    echo "<form action='' method='post' name='tests'><div id='table-change'>" . $a . "</div>
             <input type='submit' style='margin-left:50%;' name='submit' id='sendnotice' value='" . get_string('notification', 'block_profile_field_identifier') . "'/>
             <input type='hidden' name='viewpage' id='viewpage' value='$viewpage'/>
             <input type='hidden' name='notification_msg' id='notification_msg' value='' />
             <input type='hidden' name='field' id='field' value='' /></form>";
}
if (isset($_POST['submit'])) {
    $user = 0;
    if (isset($_POST['user']))
        $user = $_POST['user'];
    else
        echo "<div style='border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 50px;background-repeat: no-repeat;background-position: 10px center;color: #00529B;background-image: url(" . 'pic/error.png' . "); background-color: #BDE5F8;border-color: #3b8eb5;'>You didn't select any user.</div>";
    if (isset($_POST['field']))
        $notification_field = $_POST['field'];
    else
        echo "<div style='border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 50px;background-repeat: no-repeat;background-position: 10px center;color: #00529B;background-image: url(" . 'pic/error.png' . "); background-color: #BDE5F8;border-color: #3b8eb5;'>You didn't select any field.</div>";
    if (isset($_POST['notification_msg']))
        $notification_msg = $_POST['notification_msg'];
    else
        echo "<div style='border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 50px;background-repeat: no-repeat;background-position: 10px center;color: #00529B;background-image: url(" . 'pic/error.png' . "); background-color: #BDE5F8;border-color: #3b8eb5;'>You didn't write any message.</div>";
    $number = count($user);
    for ($i = 0; $i < $number; $i++) {
        send_msg($user[$i], $notification_msg, $notification_field);
    }
}
$PAGE->requires->js_init_call('M.block_profile_field_identifier.init');
echo $OUTPUT->footer();
// End Form Display.