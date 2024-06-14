<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_login();


global $CFG, $DB;
    
$id = optional_param('id', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);

// var_dump($userid);die;
$deleted1 = $DB->delete_records('user', array('id' => $userid));

if (optional_param('confirm', 0, PARAM_INT)) {
    $deleted = $DB->delete_records('poc', array('id' => $id));
    
    
    if ($deleted && $deleted1 !== false) {
        redirect("$CFG->wwwroot/local/poc/poc_custom.php", get_string('pocdelete', 'local_poc'), 2);
    } else {
        print_error('deletion_failed', 'local_poc', "$CFG->wwwroot/local/poc/poc_custom.php");
    }
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('deleteconfirm', 'local_poc'), 
                         new moodle_url("$CFG->wwwroot/local/poc/delete_poc.php?confirm=1&id=$id"), 
                         new moodle_url("$CFG->wwwroot/local/poc/poc_custom.php"));
    echo $OUTPUT->footer();
}
