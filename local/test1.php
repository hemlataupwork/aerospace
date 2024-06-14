<?php
require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/cohort/lib.php');

// Set up page context
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/path/to/your/script.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Select Cohort');
$PAGE->set_heading('Select Cohort');

// Start output
echo $OUTPUT->header();

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cohortid = required_param('cohortid', PARAM_INT);
    // Do something with the selected cohort ID, such as redirecting to another page or processing it further
    echo "Selected Cohort ID: " . $cohortid;
    echo $OUTPUT->footer();
    exit; // Prevent further execution
}

// Retrieve all cohorts
$cohorts = $DB->get_records('cohort');

// Start HTML form
echo '<form method="post">';

// Start HTML select dropdown
echo '<select name="cohortid">';

// Iterate through cohorts and create options
foreach ($cohorts as $cohort) {
    echo '<option value="' . $cohort->id . '">' . $cohort->name . '</option>';
}

// Close select dropdown
echo '</select>';

// Add submit button
echo '<input type="submit" value="Submit">';
echo '</form>';

// Output footer
echo $OUTPUT->footer();
?>
