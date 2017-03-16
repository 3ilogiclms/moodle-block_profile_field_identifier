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

$release = $CFG->release;
$release = explode(" (", $release);
if ($release[0] >= 2.2) {
    $PAGE->set_context(context_system::instance());
} else {
    $PAGE->set_context(get_system_context());
}

$id_fid = required_param('id_fid', PARAM_TEXT);
$id_cid = required_param('id_cid', PARAM_INT);
$id_rid = required_param('id_rid', PARAM_INT);
$id_ftid = required_param('id_ftid', PARAM_INT);
$id_btn = required_param('id_btn', PARAM_TEXT);

$form = new field_identifier_form();

$table=$form->display_list($id_fid, $id_cid, $id_rid, $id_ftid, $id_btn);
$a = "";
$a= html_writer::table($table);
echo $a;