<?php
require_once('../config.php');
require_once($CFG->libdir . '/adminlib.php'); // Include admin library for admin_externalpage_setup

// Ensure the script is accessed as an admin
// admin_externalpage_setup('managecategories');

// // Check if the user has the required capability to manage categories
// if (!has_capability('moodle/category:manage', context_system::instance())) {
//     print_error('accessdenied', 'admin');
// }

// Attempt to establish a database connection
// $DB = \moodle_database::get_instance();
// if (!$DB) {
//     print_error('databasenotresponding', 'error'); // Display error if unable to connect to the database
// }

// Create the category object
$category = new stdClass();
$category->name = 'WORKING ON CATEGORY'; // Name of the category
// $category->idnumber = 'main_category'; // ID number for the category
$category->description = 'This is the main category'; // Description of the category
$category->parent = 0; // 0 for a top-level category

// Attempt to create the category
try {
    $categoryid = core_course_category::can_create_top_level_category($category);
  
    // $categoryid = core_course_category::create($category);
    // var_dump($categoryid);die;
    if ($categoryid) {
        // Category created successfully
        echo 'Main category created successfully with ID: ' . $categoryid;
    } else {
        // Failed to create the category
        echo 'Failed to create main category';
    }
} 
?>
