<?php

require_once("$CFG->libdir/formslib.php");

class select_courses_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
        $mform = $this->_form; // Don't forget the underscore!

        $courses = $DB->get_records_menu('course', null, '', 'id,fullname', 0, 0);

        $selectedcourses = $this->_customdata;

        $objs = array();
        $objs[0] =& $mform->createElement('select', 'aforums', "Available Forums", $courses, 'size="15"');
        $objs[0]->setMultiple(true);

        $objs[1] =& $mform->createElement('select', 'sforums', "Selected Forums", $selectedcourses, 'size="15"');
        $objs[1]->setMultiple(true);

        $grp =& $mform->addElement('group', 'usersgrp', "Select", $objs, ' ', false);

        $objs = array();
        $objs[] =& $mform->createElement('submit', 'addsel', get_string('bulkforumaddselected', 'bulkforum'));
        $objs[] =& $mform->createElement('submit', 'removeall', get_string('bulkforumremoveselected', 'bulkforum'));
        $grp =& $mform->addElement('group', 'buttonsgrp', "Options", $objs, array(' ', '<br />'), false);


		$this->add_action_buttons();

    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}

class post_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
		$mform = $this->_form; // Don't forget the underscore!
		//var_dump( $this->_customdata);

		$selectedcourses = $this->_customdata;

    $mform->addElement('static', 'description', 'Selected Courses', implode("<br/>", $selectedcourses));
    $mform->addElement('text', 'subject', "Subject", array('size'=>'100'));
    $mform->setType('subject', PARAM_RAW);
    $mform->addRule('subject', "Please type a subject", 'required', null, 'client', false, false);

    $mform->addElement('editor', 'message', "Message");
    $mform->setType('message', PARAM_RAW);
    $mform->addRule('message', "Please type a message", 'required', null, 'client', false, false);

    $mform->addElement('filemanager', 'bulk_forum', 'Upload a file', null, array('maxbytes' => $CFG->maxbytes, 'maxfiles' => 3, 'accepted_types' => array('*')));



		$this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
