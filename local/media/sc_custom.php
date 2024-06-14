<?php

global $CFG, $DB;
require_once "../../config.php";
require_once "$CFG->libdir/tablelib.php";
require_once "classes/table/sc_table.php";


$context = context_system::instance();
$PAGE->set_context($context);

$table = new image_list('uniqueid');
$table->is_downloading($download, 'test', 'testing123');

if (!$table->is_downloading()) {
    $PAGE->set_title('School');
    $PAGE->set_heading('School Image');
    $PAGE->navbar->add('School Image', new moodle_url('sc_custom.php'));
    $PAGE->set_pagelayout('standard');
    echo $OUTPUT->header();
    $heading_text = "School Image List";
    echo html_writer::tag('h2', $heading_text, array('class' => 'custom-heading school-image-list'));
    
    echo "<a href=$wwwroot./media_form.php><button class='btn btn-primary'>Back</button></a>";

}



$fields = "(@row_number := @row_number + 1) as serialno, 
SUBSTRING_INDEX(img.school_image, '/', -1) as name, sc.name as school_name, img.id as imageid";
$from = "{image_school} img
JOIN {course_categories} sc ON img.schoolid = sc.id";
$where = 'img.school_image IS NOT NULL order by img.id DESC';



$perpage = 10;
$page = optional_param('page', 0, PARAM_INT);
$DB->execute('SET @row_number = ' . (($perpage * $page)));

$table->set_sql($fields, $from, $where);


$table->define_baseurl("$CFG->wwwroot/local/media/sc_custom.php");

$table->out(10, true);

if (!$table->is_downloading()) {
    
    echo $OUTPUT->footer();
}
?>
<script>
    $(document).ready(function() {
        $('.region-main').find('table').DataTable();
        const data = document.querySelectorAll('a')
        data.forEach((e) => {
            if (e.innerText.includes("Action")) {
                e.href = ''
            }

        })
    });
</script>