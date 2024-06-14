<?php

require_once("../../config.php");
require_once $CFG->libdir . '/formslib.php';
require_once "classes/forms/media_form.php";

global $PAGE, $OUTPUT, $DB;

$PAGE->requires->js(new moodle_url('/local/media/amd/src/form.js'));

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Add  Image');
$PAGE->set_url(new moodle_url('/local/media/media_form.php'));

$mform = new media_form();

echo $OUTPUT->header();

if ($fromform = $mform->get_data()) {
    $new_name = $mform->get_new_filename('school_image_file');

    $path = 'image/' . $new_name . '.png';
    $fullpath = '/local/media/image/' . $new_name;
    $success = $mform->save_file('school_image_file', $path, true);

    $new_name1 = $mform->get_new_filename('lab_material_file');
    $path1 = 'image/' . $new_name1;
    $fullpath1 = '/local/media/image/' . $new_name1;
    $success1 = $mform->save_file('lab_material_file', $path1, true);

    $new_name2 = $mform->get_new_filename('activity_file');
    $path2 = 'image/' . $new_name2;
    $fullpath2 = '/local/media/image/' . $new_name2;
    $success2 = $mform->save_file('activity_file', $path2, true);

    $schoolid = (int)$_POST['school'];
    $category = (int)$_POST['subcategory'];
    $category1 = (int)$_POST['subcategory1'];

    $record = new stdClass();
    $record->schoolid = $schoolid;
    $record->timecreated = time();


    if ($schoolid > 0 && $category <= 0 && $category1 <= 0) {
        if ($new_name) {
            $record->school_image = $fullpath;
            $DB->insert_record('image_school', $record);
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imageuploaded', 'local_media'), 2);
        } else {
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imagemissing', 'local_media'), 2);
        }
    } elseif ($category > 0 && $category1 <= 0) {
        if ($new_name && $new_name1) {
            $record->school_image = $fullpath;
            $DB->insert_record('image_school', $record);

            $record->lab_image = $fullpath1;
            $record->sub_category_id = $category;
            $DB->insert_record('image_lab', $record);
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imageuploaded', 'local_media'), 2);
        } else if ($new_name || $new_name1) {
            $record->lab_image = $fullpath1;
            $record->sub_category_id = $category;
            $DB->insert_record('image_lab', $record);
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imageuploaded', 'local_media'), 2);
        } else {
            die('hiiii');
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imagemissing', 'local_media'), 2);
        }
    } elseif ($category1 > 0 && $category <= 0) {
        if ($new_name && $new_name2) {

            $record->school_image = $fullpath;
            $DB->insert_record('image_school', $record);

            $record->act_image = $fullpath2;
            $record->sub_category_id = $category1;
            $DB->insert_record('image_activity', $record);
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imageuploaded', 'local_media'), 2);
        } else if ($new_name || $new_name2) {
            $record->act_image = $fullpath2;
            $record->sub_category_id = $category1;
            $DB->insert_record('image_activity', $record);
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imageuploaded', 'local_media'), 2);
        } else {
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imagemissing', 'local_media'), 2);
        }
    } else {
        if ($new_name && $new_name1 && $new_name2) {
            $record->school_image = $fullpath;
            $DB->insert_record('image_school', $record);

            $record->lab_image = $fullpath1;
            $record->sub_category_id = $category;
            $DB->insert_record('image_lab', $record);

            $record->act_image = $fullpath2;
            $record->sub_category_id = $category1;
            $DB->insert_record('image_activity', $record);
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imageuploaded', 'local_media'), 2);

            
        }else if($new_name1 && $new_name2) {
            // die('both');
            $record->lab_image = $fullpath1;
            $record->sub_category_id = $category;
            $DB->insert_record('image_lab', $record);

            $record->act_image = $fullpath2;
            $record->sub_category_id = $category1;
            $DB->insert_record('image_activity', $record);
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imageuploaded', 'local_media'), 2);

        }else{
            redirect(new moodle_url('/local/media/media_form.php'), get_string('imagemissing', 'local_media'), 2);
        }
    }
} else if ($mform->is_cancelled()) {
    redirect("$CFG->wwwroot/my/");
} else {
    $mform->display();
}

echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>';
echo $OUTPUT->footer();
