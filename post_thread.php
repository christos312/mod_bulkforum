<?php

require_once ("./../../config.php");
require_once("$CFG->libdir/formslib.php");
require ('./custom_forms.php');
require ('./locallib.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Bulk Forum Post');
$PAGE->set_heading('Bulk Forum Post');
$PAGE->set_url($CFG->wwwroot.'/mod/bulkforum/post_thread.php');

require_login(0, false);   // Script is useless unless they're logged in

//Check permissions
$context = context_system::instance();
if (!has_capability("mod/bulkforum:addpost", $context)) {
    print_error("You do not have permission");
}

global $SESSION, $DB, $USER;

$mform = new post_form(null, get_selections());

if ($mform->is_cancelled()) {
	redirect($CFG->wwwroot.'/mod/bulkforum/select_courses.php');

}else if ($data = $mform->get_data()) {

  //get selected courses from the session
  $selectedcourses = $SESSION->bulkforum['selected'];
  $uniqueid = uniqid();
  //lets prepare the post to be saved on DB
  $post = new stdClass();
var_dump($selectedcourses);
  foreach($selectedcourses as $course){
		$post->userid 		= $USER->id;
		$post->subject 		= $data->subject;
		$post->message 		= $data->message['text'];
		$post->emailsent	= 0;
		$post->timecreated 	= time();
		$post->course 		= $course;
		$post->groupid		= $uniqueid;


		$postid = $DB->insert_record('bulkforum_threads', $post);
		$draftitemid = file_get_submitted_draft_itemid('bulk_forum');



		file_save_draft_area_files($draftitemid, $context->id , 'mod_bulkforum', 'bulk_forum', $postid, array('subdirs'=>true));
	}




}else {

}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
