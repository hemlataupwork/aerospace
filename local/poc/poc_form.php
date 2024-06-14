<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once('classes/form/poc_form.php');

global $PAGE, $CFG;

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('poc Registration');
$PAGE->set_heading('poc Registration Form');
$mform = new poc_form();

if ($mform->is_cancelled()) {
    redirect("$CFG->wwwroot/local/poc/poc_custom.php");
} elseif ($data = $mform->get_data()) {
    $poc = new stdClass();
    $poc->username = $data->username; 
    $poc->firstname = $data->firstname;
    $poc->lastname = $data->lastname;
    $poc->password = $data->password;
    $poc->dob = $data->dob;
    $poc->mnethostid = 1;
    $poc->confirmed = 1;
    $poc->blood_group = $data->blood_group;
    $poc->email = $data->email;
    $poc->contact_number = $data->contact_number;
    $poc->permanent_address = $data->permanent_address;
    $poc->current_address = $data->current_address;
    $poc->alternative_address = $data->alternative_address;
    $poc->experience = $data->experience;
    $poc->ctc = $data->ctc;
    $poc->date_of_joining = $data->date_of_joining;
    $poc->designation = $data->designation;
    session_start();
    $_SESSION['password'] = $poc->password = $data->password;
            $user_id = user_create_user($poc);
            if ($user_id !== false) {
                $poc->userid = $user_id;
                $DB->insert_record('poc', $poc);
        redirect("$CFG->wwwroot/local/poc/poc_custom.php", get_string('pocsuccess', 'local_poc'), 2);
    } else {
        print_error('usercreationerror', 'local_poc'); 
    }
} else {
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}
