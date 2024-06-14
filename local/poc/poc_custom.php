<?php
require_once "../../config.php";
require_once $CFG->libdir . "/tablelib.php";
require_once "classes/table/poc_table.php";
global $page;
$page = optional_param('page', 0, PARAM_INT);
$context = context_system::instance();
$PAGE->set_url('/poc_custom.php');

$download = optional_param('download', '', PARAM_ALPHA);
$roleid = optional_param('roleid', 0, PARAM_INT);

$table = new poc_table('uniqueid');

$table->is_downloading($download, 'poc', 'poc_data');

if (!$table->is_downloading()) {
  
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title('poc');
    $PAGE->navbar->add('POC Table', new moodle_url('/poc_custom.php'));
    $PAGE->set_heading('POC Table');
    echo $OUTPUT->header();
 
    $heading_text = "POC Management";
    echo html_writer::tag('h2', $heading_text, array('class' => 'custom-heading add-poc-user'));

    echo html_writer::start_div('form-inline text-xs-right action-button-container');
   
    echo html_writer::link(new moodle_url('/local/poc/poc_form.php'), 'Add New poc', array('class' => 'btn btn-primary mr-2'));
  
        echo html_writer::end_div();
        
        $fields = "(@row_number := @row_number + 1) as serialno,tr.id as id,ar.name as role, tr.userid as userid,tr.firstname as firstname, tr.lastname as lastname, tr.contact_number as contact, tr.current_address as address, tr.designation as designation";
        $from =  "{poc} as tr left join {role} as ar on ar.id=tr.roleid";
        $where = "'1==1'";
        $perpage = 10;
        $table->set_sql($fields, $from, $where);
        $DB->execute('SET @row_number := ' . ($perpage * $page));
        $table->define_baseurl("$CFG->wwwroot/local/poc/poc_custom.php?page=$page");
        
        $table->out($perpage, true);

    if (!$table->is_downloading()) {
        echo $OUTPUT->footer();
    }
}
?>;
