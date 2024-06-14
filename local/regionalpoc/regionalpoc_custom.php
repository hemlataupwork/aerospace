<?php
require_once "../../config.php";
require_once $CFG->libdir . "/tablelib.php";
require_once "classes/table/regionalpoc_table.php";
global $page;
$page = optional_param('page', 0, PARAM_INT);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/regionalpoc_custom.php');

$download = optional_param('download', '', PARAM_ALPHA);
$roleid = optional_param('roleid', 0, PARAM_INT);

$table = new regionalpoc_table('uniqueid');

$table->is_downloading($download, 'regionalpoc', 'regionalpoc_data');

if (!$table->is_downloading()) {

    $PAGE->set_pagelayout('standard');
    $PAGE->set_title('regionalpoc');
    $PAGE->navbar->add('regionalpoc Table', new moodle_url('/regionalpoc_custom.php'));
    $PAGE->set_heading('regionalpoc Table');
    echo $OUTPUT->header();

    $heading_text = "Regional Poc Management";
    echo html_writer::tag('h2', $heading_text, array('class' => 'custom-heading add-regionalpoc-user'));

    echo html_writer::start_div('form-inline text-xs-right action-button-container');
    echo html_writer::link(new moodle_url('/local/regionalpoc/regionalpoc_form.php'), 'Add New RM/ARM', array('class' => 'btn btn-primary mr-2'));
    echo html_writer::end_div();


    $roles = $DB->get_records_sql("SELECT id FROM {role} WHERE name IN ('rm','arm')");
    $role = [];
    foreach ($roles as $role1) {
        $role[] = $role1->id;
    }

    $roleNames = [
        3 => 'Regional Manager',
        4 => 'Assistant Regional Manager'
    ];

    foreach ($role as $roleId) {
        if (isset($roleNames[$roleId])) {
        }
    }

    $selectedroleid = optional_param('roleid', 0, PARAM_INT);

    $defaultroleid = 3;

    // If no valid role ID is selected, use the default.
    if (!array_key_exists($selectedroleid, $roleNames)) {
        $selectedroleid = $defaultroleid;
    }
    echo $OUTPUT->single_select(
        new moodle_url("$CFG->wwwroot/local/regionalpoc/regionalpoc_custom.php", $urlparams),
        'roleid',

        $roleNames,
        $selectedroleid,
        '',
        null,
        ['label' => 'Select View']

    );

    $fields = "(@row_number := @row_number + 1) as serialno,rp.id as id,ar.name as role, rp.userid as userid,rp.firstname as firstname, rp.lastname as lastname, rp.contact_number as contact, rp.current_address as address, rp.designation as designation";
    $from =  "{regionalpoc} as rp left join {role} as ar on ar.id=rp.roleid";
    $where = "'1==1' ";
    if ($roleid == 3) {
        $where .= " AND rp.roleid=$roleid ";
    } elseif ($roleid == 4) {
        $where .= " AND rp.status=1 ";
    }

    $perpage = 10;
    $table->set_sql($fields, $from, $where);
    $DB->execute('SET @row_number := ' . ($perpage * $page));
    $table->define_baseurl("$CFG->wwwroot/local/regionalpoc/regionalpoc_custom.php?page=$page");

    $table->out($perpage, true);

    if (!$table->is_downloading()) {
        echo $OUTPUT->footer();
    }
}
?>;