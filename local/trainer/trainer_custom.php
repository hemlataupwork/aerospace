<?php

require_once "../../config.php";
require_once $CFG->libdir . "/tablelib.php";
require_once "classes/table/trainer_table.php";

require_login();

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
// if (!has_capability('local/trainer:view', $context)) {
//     print_error('nopermission', 'error', '', 'You do not have permission to view this page.');
// }


$page = optional_param('page', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);
$search = optional_param('search', '', PARAM_TEXT);

$table = new trainer_table('uniqueid');

$table->is_downloading($download, 'trainer_data', 'trainer_data');

if (!$table->is_downloading()) {
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title('Trainer');
    $PAGE->set_heading('Trainer Table');
    $PAGE->navbar->add('Trainer Table', new moodle_url('/trainer_custom.php'));

    echo $OUTPUT->header();

    echo html_writer::start_div('form-inline text-xs-right action-button-container');
    echo html_writer::link(new moodle_url('/local/trainer/trainer_form.php'), 'Add New Trainer', array('class' => 'btn btn-primary mr-10'));
    echo html_writer::end_div();

    $heading_text = "Trainer Management";
    echo html_writer::tag('h2', $heading_text, array('class' => 'custom-heading add-trainer'));

    echo "<form method='post' class='d-flex' action='$CFG->wwwroot/local/trainer/trainer_custom.php'>";
    echo "<input type='search' class='ml-auto rounded mr-2' name='search' placeholder='Search...' value='" . s($search) . "'>";
    echo '<input type="submit" value="Search" class="btn btn-primary mr-2">';
    echo '<a href="' . $CFG->wwwroot . '/local/trainer/trainer_custom.php" class="btn btn-secondary mr-2">Clear</a>';
    echo '</form>';
}

$fields = "(@row_number := @row_number + 1) as serialno, tr.id as id, tr.firstname as firstname, tr.lastname as lastname, tr.contact_number as contact, tr.current_address as address, tr.designation as designation, tr.userid";
$from = "aero_trainer as tr, (SELECT @row_number := 0) as rn";
$where = "1=1";
$params = [];

if ($search) {
    $where .= " AND (tr.firstname LIKE :search1 OR tr.lastname LIKE :search2 OR tr.contact_number LIKE :search3)";
    $params = ['search1' => "%$search%", 'search2' => "%$search%", 'search3' => "%$search%"];
}

$perpage = 10;

$table->set_sql($fields, $from, $where, $params);
$table->define_baseurl("$CFG->wwwroot/local/trainer/trainer_custom.php?page=$page");

if ($table->is_downloading()) {
    $table->out($perpage, true);
    exit;
} else {
    $table->out($perpage, true);
    echo $OUTPUT->footer();
}

?>
