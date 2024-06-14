<?php

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class edit_poc_form extends moodleform {

    public function definition() {
        $mform = $this->_form;

        // Add elements to your form.
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);

        $heading_text = "Edit POC User";
        $heading = html_writer::tag('h2', $heading_text, array('class' => 'custom-heading edit-poc-user'));
        $mform->addElement('html', $heading);

        $mform->addElement('text', 'username', get_string('username', 'local_poc'));
        $mform->setType('username', PARAM_TEXT);
        $mform->addRule('username', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'firstname', get_string('firstname', 'local_poc'));
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'lastname', get_string('lastname', 'local_poc'));
        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('required'), 'required', null, 'client');

        $mform->addElement('passwordunmask', 'password', get_string('password', 'local_poc'));
        $mform->setType('password', PARAM_TEXT);

        $mform->addElement('date_selector', 'dob', get_string('dob', 'local_poc'));
        $mform->setType('dob', PARAM_INT);

        $mform->addElement('text', 'blood_group', get_string('bloodgroup', 'local_poc'));
        $mform->setType('blood_group', PARAM_TEXT);

        $mform->addElement('text', 'email', get_string('email', 'local_poc'));
        $mform->setType('email', PARAM_EMAIL);
        $mform->addRule('email', null, 'required', null, 'client');
        $mform->addRule('email', 'Enter valid email', 'email', null, 'client');
        $mform->addRule('email', 'Enter valid email', 'maxlength', 100, 'client');

        $mform->addElement('text', 'contact_number', get_string('contactnumber', 'local_poc'));
        $mform->setType('contact_number', PARAM_TEXT);
        $mform->addRule('contact_number', null, 'required', null, 'client');
        $mform->addRule('contact_number', 'Phone number should have 10 digits', 'minlength', 10, 'client');
        $mform->addRule('contact_number', 'Invalid phone number format', 'regex', '/^\+?\d{1,4}?[-.\s]?\(?\d{1,4}?\)?[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/i', 'client');

        $mform->addElement('text', 'permanent_address', get_string('permanentaddress', 'local_poc'));
        $mform->setType('permanent_address', PARAM_TEXT);

        $mform->addElement('text', 'current_address', get_string('currentaddress', 'local_poc'));
        $mform->setType('current_address', PARAM_TEXT);

        $mform->addElement('text', 'alternative_address', get_string('alternativeaddress', 'local_poc'));
        $mform->setType('alternative_address', PARAM_TEXT);

        $mform->addElement('text', 'experience', get_string('experience', 'local_poc'));
        $mform->setType('experience', PARAM_TEXT);
        $mform->addRule('experience', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'ctc', get_string('ctc', 'local_poc'));
        $mform->setType('ctc', PARAM_TEXT);

        $mform->addElement('date_selector', 'date_of_joining', get_string('dateofjoining', 'local_poc'));
        $mform->setType('date_of_joining', PARAM_INT);
        $mform->addRule('date_of_joining', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'designation', get_string('designation', 'local_poc'));
        $mform->setType('designation', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('savechanges', 'local_poc'));
    }

    function validation($data, $files) {
        $errors = [];

        if (!empty($data['password']) && !check_password_policy($data['password'], $errmsg, $tempuser)) {
            $errors['password'] = $errmsg;
        }

        return $errors;
    }
}
