<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_login();

global $CFG, $DB;


// Ensure this script is accessed within Moodle.
// require_once(__DIR__ . '/../../../config.php');
// global $DB;

// Check for required parameter 'id'.
$imageid = required_param('id', PARAM_INT);

// Your SQL query to delete the record.
$DB->delete_records('image_school', array('id' => $imageid));

// Send success response.
http_response_code(200);



