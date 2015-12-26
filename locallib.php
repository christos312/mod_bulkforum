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
 * Internal library of functions for module bulkforum
 *
 * All the bulkforum specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_bulkforum
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/*
 * Return selected courses
 *
 * @param array $things
 * @return array
 */
 function get_selections(){
	global $SESSION, $DB;

	if(isset($SESSION->bulkforum['selected'])){

		$results = array();
		$courseids = $SESSION->bulkforum['selected'];

		foreach($courseids as $id){
			$course = $DB->get_record('course', array("id" => $id));
			$results[$id] = $course->fullname;
		}
		return $results;
	}
}

function get_posts($courseid, $instance){

	global $DB;
	//echo $courseid;
	 if (! $bulkforum = $DB->get_record('bulkforum', array('id' => $instance))) {
        return false;
    }
	$posts = $DB->get_records('bulkforum_threads', array("course" => $courseid), $sort='timecreated DESC');

	return $posts;

}

function get_post($postid, $courseid){
	
	global $DB;
	//echo $courseid;
	$sql = "id=".$postid." AND course=".$courseid;
	$post = $DB->get_record_select('bulkforum_threads', $sql, null);
	//echo "<pre>";
	//var_dump($post);
	//echo "</pre>";
	return $post;

}

