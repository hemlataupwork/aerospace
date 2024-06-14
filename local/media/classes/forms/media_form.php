<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class media_form extends moodleform {
   
    public function definition() {
        global $DB, $OUTPUT; 
        // $schoolid=optional_param('id', 0, PARAM_INT);
        $mform = $this->_form;
       
        // $school = $DB->get_records_menu('school', null, '', 'id, school_name');

        $school = $DB->get_records_sql_menu("SELECT cc.id, ss.school_name FROM {school} ss JOIN {course_categories} cc ON ss.school_sortname = cc.name");

        $heading_text = "Upload Media For School";
        $heading = html_writer::tag('h2', $heading_text, array('class' => 'custom-heading upload-school-media'));
        $mform->addElement('html', $heading);

        $mform->addElement('select', 'school', get_string('selectschool', 'local_media'), array('' => get_string('selectschool', 'local_media')) + $school);
        $mform->setType('school', PARAM_INT);
        $mform->addRule('school', null, 'required', null, 'client');
        
        // Add search and clear buttons using HTML
        $sc_url = new moodle_url('/local/media/sc_custom.php');
        $lab_url = new moodle_url('/local/media/lab_custom.php');
        $act_url = new moodle_url('/local/media/act_custom.php');
        
        $sc_link = html_writer::link($sc_url, get_string('schoolimagelist', 'local_media'), array('class' => 'btn btn-primary mr-2'));
        $lab_link = html_writer::link($lab_url, get_string('labimagelist', 'local_media'), array('class' => 'btn btn-primary mr-2'));
        $act_link = html_writer::link($act_url, get_string('activityimagelist', 'local_media'), array('class' => 'btn btn-primary mr-2'));
        
        // Wrap links inside a div
        $links_div = html_writer::div($sc_link . $lab_link . $act_link, 'links-wrapper');
        $mform->addElement('html', $links_div);
        
        // Add three upload buttons
        $mform->addElement('header', 'school_images', get_string('schoolimages', 'local_media'));
        $mform->addElement('filepicker', 'school_image_file', get_string('uploadschoolimg', 'local_media'));
        $mform->setAdvanced('school_image_file', array('disabled' => 'disabled'));
        


        $mform->addElement('header', 'lab_materials', get_string('labimage', 'local_media'));
        $mform->addElement('select', 'subcategory', get_string('selectsubcategory', 'local_media'), array('' => get_string('selectsubcategory', 'local_media')));
        // $mform->setType('subcategory', PARAM_INT);
        $mform->addElement('filepicker', 'lab_material_file', get_string('uploadlabimage', 'local_media'));
        $mform->setAdvanced('lab_material_file', array('disabled' => 'disabled'));
        
        $mform->addElement('header', 'activity_files', get_string('activityimage', 'local_media'));
        $mform->addElement('select', 'subcategory1', get_string('selectsubcategory', 'local_media'), array('' => get_string('selectsubcategory', 'local_media')));
        // $mform->setType('subcategory1', PARAM_INT);
        $mform->addElement('filepicker', 'activity_file', get_string('uploadactivityimage', 'local_media'));
        $mform->setAdvanced('activity_file', array('disabled' => 'disabled'));

        $mform->disabledIf('school_image_file', 'school', 'eq', '');
        $mform->disabledIf('lab_material_file', 'subcategory', 'eq', '');
        $mform->disabledIf('activity_file', 'subcategory1', 'eq', '');
        
        $this->add_action_buttons(true, get_string('submit'));

        
    }

    
}
