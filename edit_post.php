<?php
require_once ("./../../config.php");
require_once("$CFG->libdir/formslib.php");
require ('./custom_forms.php');
require ('./locallib.php');

$id 		= optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  		= optional_param('n', 0, PARAM_INT);  // ... unicforum instance ID - it should be named as the first character of the module.
$postid 	= optional_param('postid', 0, PARAM_INT);
$courseid 	= optional_param('courseid', 0, PARAM_INT);

global $SESSION;

if ($postid) {
    $course     = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
} else if ($n) {
    $course     = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Post an announcement');
$PAGE->set_heading('Post an announcement');
$PAGE->set_url($CFG->wwwroot.'/mod/unicforum/edit_post.php');

$context = context_system::instance();
$post = get_post($postid, $courseid);

$mform = new edit_form($CFG->wwwroot . '/mod/bulkforum/edit_post.php?mode=edit&id='.$id.'&postid='.$postid.'&courseid='.$courseid, array('post'=>$post));


if ($mform->is_cancelled()){


}else if ($data = $mform->get_data()) {

    $subject = $data->subject;
    $message = $data->message['text'];
    $timemodified = time();

    $newpost = new stdClass();
    $newpost->id = $post->id;
    $newpost->subject = $subject;
    $newpost->message =  $message;
    $newpost->timemodified = $timemodified;
    $DB->update_record('bulkforum_threads', $newpost);

    $draftitemid = file_get_submitted_draft_itemid('bulk_forum');

    file_save_draft_area_files($draftitemid, $context->id , 'mod_bulkforum', 'bulk_forum', $postid, array('subdirs'=>true));





    //unset($_POST);
    //redirect($CFG->wwwroot.'/mod/unicforum/view.php?id='.$id.'&postid='.$postid.'&courseid='.$courseid, "Post Successfull, redirecting", 1);

}else {

}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();