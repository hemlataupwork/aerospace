<?php

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class assign_arm extends moodleform {
   
    public function definition() {
  
        global $DB;

        $mform = $this->_form; 


        $arm = $DB->get_records_menu('regionalpoc', ['role' => 'arm'], '', 'userid, firstname');

        $mform->addElement('select', 'pocid', get_string('selectschool', 'local_armtoschool'), array('0' => get_string('selectschool', 'local_armtoschool')) + $arm);

        $mform->setType('armid', PARAM_INT);
        $mform->addRule('armid', get_string('required'), 'required', null, 'client');
      

    }

    function validation($data, $files) {
        return [];
    }
}