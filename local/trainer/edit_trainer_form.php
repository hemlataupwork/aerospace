<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once('classes/form/edit_trainer_form.php');

global $PAGE, $CFG, $DB;

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Trainer Registration');
$PAGE->set_heading('Trainer Update Form');

$id = optional_param('id', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);

$trainer_record = $DB->get_record('trainer', ['id' => $id]);
$trainer_record1 = $DB->get_record('user', ['id' => $userid]);

$trainer = new stdClass();
$trainer->id = $id;
$trainer->username = $trainer_record1->username;
$trainer->firstname = $trainer_record->firstname;
$trainer->lastname = $trainer_record->lastname;
$trainer->password = $trainer_record->password;
$trainer->dob = $trainer_record->dob;
$trainer->blood_group = $trainer_record->blood_group;
$trainer->email = $trainer_record->email;
$trainer->contact_number = $trainer_record->contact_number;
$trainer->permanent_address = $trainer_record->permanent_address;
$trainer->current_address = $trainer_record->current_address;
$trainer->alternative_address = $trainer_record->alternative_address;
$trainer->experience = $trainer_record->experience;
$trainer->ctc = $trainer_record->ctc;
$trainer->date_of_joining = $trainer_record->date_of_joining;
$trainer->designation = $trainer_record->designation;

$form = new edit_trainer_form(null, ['id' => $id, 'trainerid' => $id]);

$form->set_data($trainer);

if ($form->is_cancelled()) {
    redirect("$CFG->wwwroot/local/trainer/trainer_custom.php");
} elseif ($data = $form->get_data()) {
    $trainer = new stdClass();
    $trainer->id = $data->id; // Ensure the ID is set here
    $trainer->username = $data->username;
    $trainer->firstname = $data->firstname;
    $trainer->lastname = $data->lastname;
    $trainer->password = $data->password;
    $trainer->dob = $data->dob;
    $trainer->blood_group = $data->blood_group;
    $trainer->email = $data->email;
    $trainer->contact_number = $data->contact_number;
    $trainer->permanent_address = $data->permanent_address;
    $trainer->current_address = $data->current_address;
    $trainer->alternative_address = $data->alternative_address;
    $trainer->experience = $data->experience;
    $trainer->ctc = $data->ctc;
    $trainer->date_of_joining = $data->date_of_joining;
    $trainer->designation = $data->designation;

    $DB->update_record('trainer', $trainer);

    redirect("$CFG->wwwroot/local/trainer/trainer_custom.php", get_string('updatesuccess', 'local_trainer'), 2);
} else {
    echo $OUTPUT->header();
    $form->display();
    echo $OUTPUT->footer();
}
