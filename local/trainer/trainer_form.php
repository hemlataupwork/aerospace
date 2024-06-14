<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once('classes/form/trainer_form.php');

global $PAGE, $CFG;

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Trainer Registration');
$PAGE->set_heading('Trainer Registration Form');

$mform = new trainer_form();

if ($mform->is_cancelled()) {
    redirect("$CFG->wwwroot/local/trainer/trainer_custom.php");
} elseif ($data = $mform->get_data()) {
    $trainer = new stdClass();
    $trainer->username = $data->username; 
    $trainer->firstname = $data->firstname;
    $trainer->lastname = $data->lastname;
    $trainer->password = $data->password;
    $trainer->mnethostid = 1;
    $trainer->dob = $data->dob;
    $trainer->blood_group = $data->blood_group;
    $trainer->email = $data->email;
    $trainer->contact_number = $data->contact_number;
    $trainer->permanent_address = $data->permanent_address;
    $trainer->current_address = $data->current_address;
    $trainer->alternative_address = $data->alternative_address;
    $trainer->experience = $data->experience;
    $trainer->ctc = $data->ctc;
    $trainer->state = $data->state;
    $trainer->date_of_joining = $data->date_of_joining;
    $trainer->designation = $data->designation;
    $trainer->confirmed = 1;
    session_start();
    $_SESSION['password'] = $trainer->password = $data->password;
    
    $user_id = user_create_user($trainer);
    if ($user_id !== false) {
        $trainer->userid = $user_id;
        $DB->insert_record('trainer', $trainer);
        redirect("$CFG->wwwroot/local/trainer/trainer_custom.php", get_string('trainersuccess', 'local_trainer'), 2);
    } else {
        print_error('usercreationerror', 'local_trainer'); 
    }
} else {
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}
