<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once('classes/form/edit_regionalpoc_form.php');

global $PAGE, $CFG, $DB;

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('regionalpoc Registration');
$PAGE->set_heading('regionalpoc Update Form');

$id = optional_param('id', 0, PARAM_INT);

$regionalpoc_record = $DB->get_record('regionalpoc', ['id' => $id]);


$regionalpoc = new stdClass();
$regionalpoc->id = $id;
$regionalpoc->username = $regionalpoc_record->username;
$regionalpoc->firstname = $regionalpoc_record->firstname;
$regionalpoc->lastname = $regionalpoc_record->lastname;
$regionalpoc->password = $regionalpoc_record->password;
$regionalpoc->dob = $regionalpoc_record->dob;
$regionalpoc->blood_group = $regionalpoc_record->blood_group;
$regionalpoc->email = $regionalpoc_record->email;
$regionalpoc->contact_number = $regionalpoc_record->contact_number;
$regionalpoc->permanent_address = $regionalpoc_record->permanent_address;
$regionalpoc->current_address = $regionalpoc_record->current_address;
$regionalpoc->alternative_address = $regionalpoc_record->alternative_address;
$regionalpoc->experience = $regionalpoc_record->experience;
$regionalpoc->ctc = $regionalpoc_record->ctc;
$regionalpoc->date_of_joining = $regionalpoc_record->date_of_joining;
$regionalpoc->designation = $regionalpoc_record->designation;

$form = new edit_regionalpoc_form(null, ['id' => $id, 'regionalpocid' => $id]);

$form->set_data($regionalpoc);

if ($form->is_cancelled()) {
    redirect("$CFG->wwwroot/local/regionalpoc/regionalpoc_custom.php");
} elseif ($data = $form->get_data()) {
    $regionalpoc = new stdClass();
    $regionalpoc->id = $data->id; // Ensure the ID is set here
    $regionalpoc->username = $data->username;
    $regionalpoc->firstname = $data->firstname;
    $regionalpoc->lastname = $data->lastname;
    $regionalpoc->password = $data->password;
    $regionalpoc->dob = $data->dob;
    $regionalpoc->blood_group = $data->blood_group;
    $regionalpoc->email = $data->email;
    $regionalpoc->contact_number = $data->contact_number;
    $regionalpoc->permanent_address = $data->permanent_address;
    $regionalpoc->current_address = $data->current_address;
    $regionalpoc->alternative_address = $data->alternative_address;
    $regionalpoc->experience = $data->experience;
    $regionalpoc->ctc = $data->ctc;
    $regionalpoc->date_of_joining = $data->date_of_joining;
    $regionalpoc->designation = $data->designation;

    $DB->update_record('regionalpoc', $regionalpoc);

    redirect("$CFG->wwwroot/local/regionalpoc/regionalpoc_custom.php", get_string('updatesuccess', 'local_regionalpoc'), 2);
} else {
    echo $OUTPUT->header();
    $form->display();
    echo $OUTPUT->footer();
}
