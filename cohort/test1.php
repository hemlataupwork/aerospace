<?php
require_once('../config.php');
require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->dirroot.'/cohort/test1.php');
require_once "classes/form/school_form.php";

// Check for required capabilities
require_capability('moodle/cohort:manage', context_system::instance());

$mform = new school_form();

// Check if the form is being submitted
if ($mform->is_cancelled()) {
    // Handle form cancellation
    redirect(new moodle_url('/cohort/index.php'));
} else if ($data = $mform->get_data()) {
    // Form data processing when submitted
    $cohort = new stdClass();
    $cohort->name = "$data->school_sortname";
    $cohort->description = "NOTIHNG";
    $cohort->contextid = context_system::instance()->id; // Adjust the context ID as needed

    // Add the cohort to the database
    $cohortid = cohort_add_cohort($cohort);
    // $cohortid = cohort_add_member(22,35)
// var_dump($cohortid);die;
    // Check if cohort was added successfully
    if ($cohortid) {
        // Redirect to cohort listing page
        redirect(new moodle_url('/cohort/index.php'));
    } else {
        // Handle cohort addition failure
        print_error('erroraddingcohort', 'cohort');
    }
}

// Set page context and title
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('addcohort', 'cohort'));

// Output header and form
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
?>
