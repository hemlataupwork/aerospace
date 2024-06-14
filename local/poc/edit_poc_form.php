<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once('classes/form/edit_poc_form.php');

global $PAGE, $CFG, $DB;

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Poc Registration');
$PAGE->set_heading('Poc Update Form');

$id = optional_param('id', 0, PARAM_INT);

$poc_record = $DB->get_record('poc', ['id' => $id]);


$poc = new stdClass();
$poc->id = $id;
$poc->username = $poc_record->username;
$poc->firstname = $poc_record->firstname;
$poc->lastname = $poc_record->lastname;
$poc->password = $poc_record->password;
$poc->dob = $poc_record->dob;
$poc->blood_group = $poc_record->blood_group;
$poc->email = $poc_record->email;
$poc->contact_number = $poc_record->contact_number;
$poc->permanent_address = $poc_record->permanent_address;
$poc->current_address = $poc_record->current_address;
$poc->alternative_address = $poc_record->alternative_address;
$poc->experience = $poc_record->experience;
$poc->ctc = $poc_record->ctc;
$poc->date_of_joining = $poc_record->date_of_joining;
$poc->designation = $poc_record->designation;

$form = new edit_poc_form(null, ['id' => $id, 'pocid' => $id]);

$form->set_data($poc);

if ($form->is_cancelled()) {
    redirect("$CFG->wwwroot/local/poc/poc_custom.php");
} elseif ($data = $form->get_data()) {
    $poc = new stdClass();
    $poc->id = $data->id; // Ensure the ID is set here
    $poc->username = $data->username;
    $poc->firstname = $data->firstname;
    $poc->lastname = $data->lastname;
    $poc->password = $data->password;
    $poc->dob = $data->dob;
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

    $DB->update_record('poc', $poc);

    redirect("$CFG->wwwroot/local/poc/poc_custom.php", get_string('updatesuccess', 'local_poc'), 2);
} else {
    echo $OUTPUT->header();
    $form->display();
    echo $OUTPUT->footer();
}
