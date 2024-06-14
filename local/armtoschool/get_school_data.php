<?php
// require_once(__DIR__ . '/../../config.php');
// require_login();

// require_capability('moodle/site:config', context_system::instance());
// global $DB, $OUTPUT, $PAGE;
// $action = $_POST['action'];
// $data = json_decode($_POST['data']);
// $context = context_coursecat::instance($key);
// $userId = $_POST['userId'];
// $roleid = 4;
// $timecreated = time();
// if ($action == 'assign') {
//     foreach ($data as $key){
//         role_assign($roleid, $userId, $context->id);
//         $sql = "INSERT INTO {schoolassign} (userid, schoolid, timecreated) VALUES ($userId  , $key, $timecreated)";
//         $success = $DB->execute($sql);
//     }
// } elseif ($action == 'remove') {
//     foreach ($data as $key){
//         $sql = "DELETE FROM {schoolassign} WHERE userid = $userId AND schoolid = $key";
//         $success = $DB->execute($sql);
//     }
// }

// if($success){
//     echo json_encode(['status' => 'success']);
// }else{
//     echo json_encode(['status' => 'failed']);
// }

require_once(__DIR__ . '/../../config.php');
require_login();

require_capability('moodle/site:config', context_system::instance());

global $DB, $OUTPUT, $PAGE;

$action = required_param('action', PARAM_ALPHA);
$data = json_decode(required_param('data', PARAM_RAW));
$userId = required_param('userId', PARAM_INT);
$roleid = 4;
$timecreated = time();
$success = false;

if ($action == 'assign') {
    foreach ($data as $key){
        $context = context_coursecat::instance($key);
        role_unassign($roleid, $userId, $context->id);
        $record = new stdClass();
        $record->userid = $userId;
        $record->schoolid = $key;
        $record->timecreated = $timecreated;
        $success = $DB->insert_record('schoolassign', $record);
    }
} elseif ($action == 'remove') {
    foreach ($data as $key){
        $context = context_coursecat::instance($key);
        role_assign($roleid, $userId, $context->id);
        $success = $DB->delete_records('schoolassign', ['userid' => $userId, 'schoolid' => $key]);
    }
}

$response = ['status' => $success ? 'success' : 'failed'];
echo json_encode($response);
