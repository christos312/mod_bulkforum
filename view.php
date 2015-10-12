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
 * Prints a particular instance of bulkforum
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_bulkforum
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace bulkforum with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require('./locallib.php');
$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... bulkforum instance ID - it should be named as the first character of the module.
$postid = optional_param('postid', 0, PARAM_INT);

if ($id) {
    $cm         = get_coursemodule_from_id('bulkforum', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $bulkforum  = $DB->get_record('bulkforum', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $bulkforum  = $DB->get_record('bulkforum', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $bulkforum->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('bulkforum', $bulkforum->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_bulkforum\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $bulkforum);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/bulkforum/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($bulkforum->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('bulkforum-'.$somevar);
 */

// Output starts here.


// Conditions to show the intro can change to look for own settings or whatever.
if ($bulkforum->intro) {
    echo $OUTPUT->box(format_module_intro('bulkforum', $bulkforum, $cm->id), 'generalbox mod_introbox', 'bulkforumintro');
}
$posts = get_posts($course->id, $cm->instance);
$table = new html_table();
$table->head = array('Post', 'Author', 'Date Created');

//Get the course context to work with cababilities
$coursecontext = context_course::instance($course->id);
$display_posts = array();
foreach($posts as $post){
	$subject = "<a href='$CFG->wwwroot/mod/bulkforum/view.php?id=".$cm->id."&postid=".$post->id."&courseid=".$course->id."'>".$post->subject."</a><br/>";

	$user = $DB->get_record_select('user', "id=".$post->userid);

	$singledata = array($subject, $user->firstname." ".$user->lastname, date("d/m/Y H:i", $post->timecreated));

	array_push($display_posts, $singledata);

}
$table->data = $display_posts;

echo $OUTPUT->header();
if(empty($postid)){
	echo html_writer::table($table);
}else{
	$context = context_system::instance();
	$posts = get_post($postid, $course->id);
	if(!$posts){
		print_error("You do not have permission to see this page");
	}

  $user = $DB->get_record_select('user', "id=".$posts->userid);

    $controls = '';
	if(has_capability('mod/bulkforum:editpost', $coursecontext)){
        
        $controls = html_writer::start_div('', array('class'=>'control-buttons'));
        $controls .= html_writer::tag('a', 'EDIT', array('href'=>'./edit_post.php?id='.$cm->id.'&postid='.$postid.'&courseid='.$course->id));
        $controls .= " | ";
        $controls .= html_writer::tag('a', 'EDIT ALL', array('href'=>'./edit_post_all.php?id='.$cm->id.'&postid='.$postid.'&courseid='.$course->id));
        $controls .= " | ";
        $controls .= html_writer::tag('a', 'DELETE ALL', array('href'=>'./delete.php?id='.$cm->id.'&postid='.$postid.'&courseid='.$course->id));
        $controls .= html_writer::end_div();//control-buttons

	}
    
    $output = html_writer::start_div('',array('class'=>'forumpost clearfix firstpost starter'));
    $output .= html_writer::tag('h1', $post->subject);
    $output .= html_writer::tag('h6', 'Posted by ' . $user->firstname . " " . $user->lastname . " on " . date("d/m/Y H:i", $posts->timecreated));
    $output .= html_writer::start_div('', array('class'=>'row'));
    $output .= html_writer::start_div('', array('class'=>'span8'));
    $output .= $posts->message;
    $output .= $controls;
    $output .= html_writer::end_div();//span8
    $output .= html_writer::end_div();//row
    $output .= html_writer::end_div();
    
    echo $output;
}
// Finish the page.
echo $OUTPUT->footer();
