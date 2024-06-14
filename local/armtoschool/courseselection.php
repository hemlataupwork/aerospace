<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/local/armtoschool/lib.php');
require_once("$CFG->dirroot/local/armtoschool/classes/course_selector_base.php");
require_once("$CFG->libdir/formslib.php");

global $CFG, $DB;
require_login();

$courseselector = new course_selector_base(19);

if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
  $schools = optional_param('addselect', [], PARAM_RAW);
  if (!empty($schools)) {

    foreach ($schools as $school) {
      $courseselector->add_trending_course($school);
    }

    $courseselector->invalidate_selected_courses();
  }
}

if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
  $schools = optional_param('removeselect', [], PARAM_RAW);
  if (!empty($schools)) {

    foreach ($schools as $school) {
      $courseselector->remove_trending_course($school);
    }

    $courseselector->invalidate_selected_courses();
  }
}

$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();

?>
<form id="assignform" method="post" action="<?php echo "courseselection.php" ?>">
  <div>
    <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>" />

    <table id="assigningrole12" summary="" class="admintable roleassigntable generaltable" cellspacing="0">
      <tr>
        <td id="existingcell12">
          <p><label for="removeselect">External Users</label></p>
          <?php $courseselector->display('removeselect') ?>
        </td>
        <td id="buttonscell">
          <div id="addcontrols">
            <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow() . '&nbsp;' . get_string('add'); ?>" title="<?php print_string('add'); ?>" class="btn btn-secondary" /><br />
          </div>

          <div id="removecontrols">
            <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove') . '&nbsp;' . $OUTPUT->rarrow(); ?>" title="<?php print_string('remove'); ?>" class="btn btn-secondary" />
          </div>
        </td>
        <td id="potentialcell">
          <p><label for="addselect">Potential Users</label></p>
          <?php $courseselector->display('addselect') ?>
        </td>
      </tr>
    </table>
  </div>
</form>

<?php
echo $OUTPUT->footer();
