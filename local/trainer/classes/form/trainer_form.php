<?php
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
$PAGE->requires->js(new moodle_url("$CFG->wwwroot/local/trainer/amd/src/numeric_validation.js"));
class trainer_form extends moodleform
{

    public function definition()
    {
        $mform = $this->_form;


        $mform->addElement('hidden', 'trainerid', $this->_customdata['trainerid']);
        $mform->setType('trainerid', PARAM_INT);

        $heading_text = "Add Trainer";
        $heading = html_writer::tag('h2', $heading_text, array('class' => 'custom-heading add-trainer'));
        $mform->addElement('html', $heading);

        // Add elements to your form.
        $mform->addElement('text', 'username', get_string('username', 'local_poc'));
        $mform->setType('username', PARAM_TEXT);
        $mform->addRule('username', get_string('required'), 'required', null, 'client');

        $js = <<<JS
        document.addEventListener('DOMContentLoaded', function() {
            var usernameField = document.getElementById('id_username');
            var errorMessage = document.getElementById('id_error_username');
            var form = document.querySelector('form'); // Assuming there's only one form or you can use an ID to select the specific form
        
            usernameField.addEventListener('input', function(e) {
                if (/[A-Z]/.test(usernameField.value)) {
                    errorMessage.textContent = 'Please enter lower case characters only';
                    errorMessage.style.display = 'block';
                } else {
                    errorMessage.style.display = 'none';
                }
            });
        
            form.addEventListener('submit', function(e) {
                if (/[A-Z]/.test(usernameField.value)) {
                    e.preventDefault(); // Prevent form submission
                    errorMessage.textContent = 'Please enter lower case characters only';
                    errorMessage.style.display = 'block';
                }
            });
        });
        JS;

        $mform->addElement('html', '<script type="text/javascript">' . $js . '</script>');

        $mform->addElement('html', '<script type="text/javascript">' . $js . '</script>');

        $mform->addElement('text', 'firstname', get_string('firstname', 'local_trainer'));
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'lastname', get_string('lastname', 'local_trainer'));
        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('required'), 'required', null, 'client');


        $mform->addElement('passwordunmask', 'password', get_string('password', 'local_trainer'));
        $mform->setType('password', PARAM_TEXT);
        $mform->addRule('password', get_string('required'), 'required', null, 'client');

        $mform->addElement('date_selector', 'dob', get_string('dob', 'local_trainer'), array('optional' => true));
        $mform->setType('dob', PARAM_INT);
        $mform->addRule('dob', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'blood_group', get_string('bloodgroup', 'local_trainer'));
        $mform->setType('blood_group', PARAM_TEXT);

        $mform->addElement('text', 'email', get_string('email', 'local_trainer'));
        $mform->setType('email', PARAM_EMAIL);
        $mform->addRule('email', null, 'required', null, 'client');
        $mform->addRule('email', 'Enter valid email', 'email', null, 'client');
        $mform->addRule('email', 'Enter valid email', 'maxlength', 100, 'client');

        $mform->addElement('text', 'contact_number', get_string('contactnumber', 'local_trainer'));
        $mform->setType('contact_number', PARAM_TEXT);
        $mform->addRule('contact_number', null, 'required', null, 'client');
        $mform->addRule('contact_number', 'Phone number should have 10 digits', 'minlength', 10, 'client');
        $mform->addRule('contact_number', 'Invalid phone number format', 'regex', '/^\+?\d{1,4}?[-.\s]?\(?\d{1,4}?\)?[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/i', 'client');

        $mform->addElement('text', 'permanent_address', get_string('permanentaddress', 'local_trainer'));
        $mform->setType('permanent_address', PARAM_TEXT);

        $mform->addElement('text', 'current_address', get_string('currentaddress', 'local_trainer'));
        $mform->setType('current_address', PARAM_TEXT);

        $mform->addElement('text', 'alternative_address', get_string('alternativeaddress', 'local_trainer'));
        $mform->setType('alternative_address', PARAM_TEXT);

        $mform->addElement('text', 'experience', get_string('experience', 'local_trainer'));
        $mform->setType('experience', PARAM_TEXT);
        $mform->addRule('experience', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'ctc', get_string('ctc', 'local_trainer'));
        $mform->setType('ctc', PARAM_INT);

        $mform->addElement('date_selector', 'date_of_joining', get_string('dateofjoining', 'local_trainer'), array('optional' => true));
        $mform->setType('date_of_joining', PARAM_INT);
        $mform->addRule('date_of_joining', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'designation', get_string('designation', 'local_trainer'), ['value' => 'Training & Development', 'readonly' => 'true']);
        $mform->setType('designation', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('savechanges', 'local_trainer'));
    }

    function validation($data, $files)
    {
        global $DB;

        $errors = [];
        if ($DB->record_exists('user', ['username' => $data['username']])) {
            $errors['username'] = get_string('userexists', 'local_trainer');
        }
        if (empty(trim($data['firstname']))) {
            $errors['firstname'] = get_string('required');
        }
        if (empty(trim($data['lastname']))) {
            $errors['lastname'] = get_string('required');
        }
        if (empty(trim($data['dob']))) {
            $errors['dob'] = get_string('required');
        }
        if (empty(trim($data['email']))) {
            $errors['email'] = get_string('required');
        }
        if (empty(trim($data['contact_number']))) {
            $errors['contact_number'] = get_string('required');
        }
        if (empty(trim($data['experience']))) {
            $errors['experience'] = get_string('required');
        }

        if (empty(trim($data['date_of_joining']))) {
            $errors['date_of_joining'] = get_string('required');
        }
        if (empty(trim($data['designation']))) {
            $errors['designation'] = get_string('required');
        }

        if ($data['password']) {
            if (!check_password_policy($data['password'], $errmsg, $tempuser)) {
                $errors['password'] = $errmsg;
            }
        }

        return $errors;
    }
}
