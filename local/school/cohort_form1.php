<?php
require_once('../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once('classes/form/cohort_form1.php');
global $DB, $OUTPUT;

$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('cohort', 'local_school'));
$PAGE->set_heading(get_string('cohort', 'local_school'));
$PAGE->set_pagelayout('standard');

$form = new cohort_form();
if ($form->is_cancelled()) {
    redirect("$CFG->wwwroot/my/");
} elseif ($data = $form->get_data()) {
    $selected_cohort_id = $data->cohortid;
    redirect("$CFG->wwwroot/local/school/cohort_form1.php?selected_cohort_id=$selected_cohort_id");
} else {
    
    echo $OUTPUT->header();
    $form->display();
    require_once($CFG->dirroot.'/cohort/locallib.php'); 
    // $selected_cohort_id = required_param('selected_cohort_id', PARAM_INT);
    $record = $DB->get_record_sql("SELECT * FROM {cohort} LIMIT 1");
   
    $selected_cohort_id = isset($_GET['selected_cohort_id']) ? (int)$_GET['selected_cohort_id'] : $record->id;

    $returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

    require_login();

    $cohort = $DB->get_record('cohort', array('id' => $selected_cohort_id), '*', MUST_EXIST);
    $context = context::instance_by_id($cohort->contextid, MUST_EXIST);

    require_capability('moodle/cohort:assign', $context);

    $PAGE->set_context($context);
    $PAGE->set_url('/cohort/assign.php', array('id' => $selected_cohort_id));
    $PAGE->set_pagelayout('admin');

    if ($returnurl) {
        $returnurl = new moodle_url($returnurl);
    } else {
        $returnurl = new moodle_url('/cohort/index.php', array('contextid' => $cohort->contextid));
    }

    if (!empty($cohort->component)) {
        // We can not manually edit cohorts that were created by external systems, sorry.
        redirect($returnurl);
    }

    if (optional_param('cancel', false, PARAM_BOOL)) {
        redirect($returnurl);
    }

    if ($context->contextlevel == CONTEXT_COURSECAT) {
        $category = $DB->get_record('course_categories', array('id' => $context->instanceid), '*', MUST_EXIST);
        navigation_node::override_active_url(new moodle_url('/cohort/index.php', array('contextid' => $cohort->contextid)));
    } else {
        navigation_node::override_active_url(new moodle_url('/cohort/index.php', array()));
    }
    $PAGE->navbar->add(get_string('assign', 'cohort'));

    $PAGE->set_title(get_string('assigncohorts', 'cohort'));
    $PAGE->set_heading($COURSE->fullname);

    echo $OUTPUT->heading(get_string('assignto', 'cohort', format_string($cohort->name)));

    echo $OUTPUT->notification(get_string('removeuserwarning', 'core_cohort'));

    // Get the user_selector we will need.
    $potentialuserselector = new cohort_candidate_selector('addselect', array('cohortid' => $cohort->id, 'accesscontext' => $context));
    $existinguserselector = new cohort_existing_selector('removeselect', array('cohortid' => $cohort->id, 'accesscontext' => $context));

    // Process incoming user assignments to the cohort

    if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
        $userstoassign = $potentialuserselector->get_selected_users();
        if (!empty($userstoassign)) {

            foreach ($userstoassign as $adduser) {
                cohort_add_member($cohort->id, $adduser->id);
            }

            $potentialuserselector->invalidate_selected_users();
            $existinguserselector->invalidate_selected_users();
        }
    }

    // Process removing user assignments to the cohort
    if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
        $userstoremove = $existinguserselector->get_selected_users();
        if (!empty($userstoremove)) {
            foreach ($userstoremove as $removeuser) {
                cohort_remove_member($cohort->id, $removeuser->id);
            }
            $potentialuserselector->invalidate_selected_users();
            $existinguserselector->invalidate_selected_users();
        }
    }

    // Print the form.
?>
    <form id="assignform" method="post" action="<?php echo $PAGE->url ?>">
        <div>
            <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
            <input type="hidden" name="returnurl" value="<?php echo $returnurl->out_as_local_url() ?>" />

            <table summary="" class="generaltable generalbox boxaligncenter" cellspacing="0">
                <tr>
                    <td id="existingcell">
                        <p><label for="removeselect"><?php print_string('currentusers', 'cohort'); ?></label></p>
                        <?php $existinguserselector->display() ?>
                    </td>
                    <td id="buttonscell">
                        <div id="addcontrols">
                            <input class="btn btn-secondary" name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow() . '&nbsp;' .
                            s(get_string('add')); ?>" title="<?php p(get_string('add')); ?>" /><br />
                        </div>

                        <div id="removecontrols">
                            <input class="btn btn-secondary" name="remove" id="remove" type="submit" value="<?php echo s(get_string('remove')) . '&nbsp;' . $OUTPUT->rarrow(); ?>" title="<?php p(get_string('remove')); ?>" />
                        </div>
                    </td>
                    <td id="potentialcell">
                        <p><label for="addselect"><?php print_string('potusers', 'cohort'); ?></label></p>
                        <?php $potentialuserselector->display() ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" id='backcell'>
                        <input class="btn btn-secondary" type="submit" name="cancel" value="<?php p(get_string('backtocohorts', 'cohort')); ?>" />
                    </td>
                </tr>
            </table>
        </div>
    </form>

<?php


    echo $OUTPUT->footer();
}
