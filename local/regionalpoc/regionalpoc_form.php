<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once('classes/form/regionalpoc_form.php');

global $PAGE, $CFG,$DB;

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Regional Poc Registration');
$PAGE->set_heading('Regionalpoc Registration Form');

$mform = new regionalpoc_form();

if ($mform->is_cancelled()) {
    redirect("$CFG->wwwroot/local/regionalpoc/regionalpoc_custom.php?roleid=3");
} elseif ($data = $mform->get_data()) {
    $regionalpoc = new stdClass();
    $regionalpoc->username = $data->username;
    $regionalpoc->firstname = $data->firstname;
    $regionalpoc->lastname = $data->lastname;
    $regionalpoc->password = $data->password;
    $regionalpoc->dob = $data->dob;
    $regionalpoc->mnethostid = 1;
    $regionalpoc->confirmed = 1;
    $regionalpoc->blood_group = $data->blood_group;
    $regionalpoc->email = $data->email;
    $regionalpoc->contact_number = $data->contact_number;
    $regionalpoc->permanent_address = $data->permanent_address;
    $regionalpoc->current_address = $data->current_address;
    $regionalpoc->alternative_address = $data->alternative_address;
    $regionalpoc->experience = $data->experience;
    $regionalpoc->ctc = $data->ctc;
    $regionalpoc->role = $data->role;
    $regionalpoc->date_of_joining = $data->date_of_joining;
    $regionalpoc->designation = $data->designation;
    session_start();
    $_SESSION['password'] = $regionalpoc->password = $data->password;


    $user_id = user_create_user($regionalpoc);
    if ($regionalpoc->role == 'rm') {
        $context = context_system::instance();
        $role=$DB->get_record_sql("SELECT id from {role} where shortname = 'pocmanager'");
      var_dump($role);die;
        role_assign($role->id, $user_id, $context->id);
  
    } elseif ($regionalpoc->role == 'arm') {
        $context = context_system::instance();
        $role=$DB->get_record_sql("SELECT id from {role} where name = 'arm'");
       
        role_assign($role->id, $user_id, $context->id);

    }

    if ($user_id !== false) {
        $regionalpoc->userid = $user_id;
        $DB->insert_record('regionalpoc', $regionalpoc);
        redirect("$CFG->wwwroot/local/regionalpoc/regionalpoc_custom.php", get_string('regionalpocsuccess', 'local_regionalpoc'), 2);
    } else {
        print_error('usercreationerror', 'local_regionalpoc');
    }
} else {
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}
