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
    $deleted = $DB->delete_records('regionalpoc', array('id' => $id));
    $update=$DB->execute("UPDATE {regionalpoc} rp
JOIN {assigned_arm} aa ON aa.armid = rp.id
SET rp.status = 0
WHERE aa.rmid = $id
");
    $deleted = $DB->delete_records('assigned_arm', array('rmid' => $id));

    
    
    if ($deleted && $deleted1 !== false) {
        redirect("$CFG->wwwroot/local/regionalpoc/regionalpoc_custom.php", get_string('regionalpocdelete', 'local_regionalpoc'), 2);
    } else {
        print_error('deletion_failed', 'local_regionalpoc', "$CFG->wwwroot/local/regionalpoc/regionalpoc_custom.php");
    }
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('deleteconfirm', 'local_regionalpoc'), 
                         new moodle_url("$CFG->wwwroot/local/regionalpoc/delete_regionalpoc.php?confirm=1&id=$id"), 
                         new moodle_url("$CFG->wwwroot/local/regionalpoc/regionalpoc_custom.php"));
    echo $OUTPUT->footer();
}
