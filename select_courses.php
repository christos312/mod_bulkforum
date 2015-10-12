<?php
require_once ("./../../config.php");
require_once("$CFG->libdir/formslib.php");
require ('./custom_forms.php');
require('./locallib.php');


$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Select Courses');
$PAGE->set_heading('Select Courses');
$PAGE->set_url($CFG->wwwroot.'/mod/forumbulk/select_courses.php');

$context = context_system::instance();
global $SESSION;


if(!has_capability('mod/bulkforum:view', $context)){
    print_error("You do not have permission");
}

if (!has_capability("mod/bulkforum:addpost", $context)) {
    print_error("You do not have permission");
}



if(!isset($SESSION->bulkforum['selected'])){
	$SESSION->bulkforum['selected'] = array();
}
$mform = new select_courses_form();



if ($mform->is_cancelled()) {
	unset($_POST);
	$SESSION->bulkforum['selected'] = array();
}else if ($data = $mform->get_data()) {


	if(isset($data->submitbutton) && $data->submitbutton == "Save changes"){
		redirect($CFG->wwwroot.'/mod/bulkforum/post_thread.php');

	}elseif(isset($data->submitbutton) && $data->submitbutton == "Post"){

	}
	else{
		$selected = array();

    if(!empty($data->addsel) && $data->addsel== "Add Selected"){
			foreach($data->aforums as $item){
				array_push($selected, $item);
			}
			$SESSION->bulkforum['selected'] = $selected;
		}elseif(!empty($data->removeall) && $data->removeall== "Remove"){
			$SESSION->bulkforum['selected'] = array();
		}elseif(!empty($data->removesel) && $data->removesel== "Remove Selected"){
			echo "Remove Selected";
		}

		$mform = new select_courses_form(null, get_selections());

	}
}else {

}
//getCoursePendingSentMail();
echo $OUTPUT->header();
$mform->display();


echo $OUTPUT->footer();
