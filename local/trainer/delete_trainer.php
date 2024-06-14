<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/user/lib.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Delete School');
$PAGE->set_heading('Delete School');
require_login();

global $CFG, $DB;
    
$id = optional_param('id', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);

$deleted1 = $DB->delete_records('user', array('id' => $userid));
if (optional_param('confirm', 0, PARAM_INT)) {
    
    $deleted = $DB->delete_records('trainer', array('id' => $id));
    if ($deleted !== false) {
        redirect("$CFG->wwwroot/local/trainer/trainer_custom.php", get_string('deletesuccess', 'local_trainer'), 2);
    } else {
        print_error('deletion_failed', 'local_trainer', "$CFG->wwwroot/my/");
    }
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('deleteconfirm', 'local_trainer'), 
                         new moodle_url("$CFG->wwwroot/local/trainer/delete_trainer.php?confirm=1&id=$id"), 
                         new moodle_url("$CFG->wwwroot/local/trainer/trainer_custom.php"));
    echo $OUTPUT->footer();
}
