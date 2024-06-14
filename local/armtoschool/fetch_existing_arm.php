<?php
require_once('../../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $armid = $_POST['armid'] ?? null;

    if ($armid) {
        global $DB;
        $existing_schools = $DB->get_records_sql("SELECT aa.armid AS id, s.school_name AS schoolname FROM {assignedschool} aa JOIN {school} s ON aa.schoolid = s.id WHERE aa.armid = ?", [$armid]);
        echo json_encode(array_values($existing_schools));
        } else {
       
        echo json_encode([]);
    }
}
?>
