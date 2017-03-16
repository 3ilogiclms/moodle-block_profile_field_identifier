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
 * Simple display access page.
 *
 * @param null
 * @return String notification page.
 */
class block_profile_field_identifier extends block_base {

    public function init() {
        $this->title = get_string('profile_field_identifier', 'block_profile_field_identifier');
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }
        global $CFG;
        $pageurl = new moodle_url('/blocks/profile_field_identifier/view.php');
        if (has_capability('block/profile_field_identifier:sendmessages', $this->context)) {
            $this->content = new stdClass;
            $this->content->text = '<ul><li class="listitem" style="border-width: 1px 0 0 1px;">
                                 <a href="' . $pageurl . '?viewpage=1">' . get_string('notification_page', 'block_profile_field_identifier') . '</a></li></ul>';
            return $this->content;
        }
    }

    public function has_config() {
        return true;
    }

}
