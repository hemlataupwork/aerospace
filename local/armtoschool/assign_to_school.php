<?php
require_once('../../config.php');
require_once('classes/assign_school.php');
require_once('fetch_existing_arm.php');

global $PAGE, $CFG, $DB, $OUTPUT;
$PAGE->requires->js(new moodle_url('amd/src/assignedschool.js'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Assign School to Asst regional Manager');
$PAGE->set_heading('Assign School to Asst regional Manager');

$armid = $_POST['armid'] ?? null;

$mform = new assign_arm();

$mform->display();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        if (isset($_POST['addselect'])) {
            $selected_schools = $_POST['addselect'];
            foreach ($selected_schools as $ssid) {
               
                $DB->execute("INSERT INTO {assignedschool} (armid, schoolid) VALUES (?, ?)", [$armid, $ssid]);
                $DB->execute("UPDATE {assignedschool} SET status = 1 WHERE armid= ? ", [$armid]);
            }
        }
    } elseif (isset($_POST['remove'])) {
        if (isset($_POST['removeselect'])) {
            $selected_users = $_POST['removeselect'];
            foreach ($selected_users as $armid) {
                $DB->execute("DELETE FROM {assignedschool} WHERE schoolid = ? AND armid = ?", [$schoolid, $armid]);
                $DB->execute("UPDATE {assignedschool} SET status = 0 WHERE id = ? ", [$armid]);
            }
        }
    }
}

if ($mform->is_cancelled()) {
    // Handle form cancel
} else if ($fromform = $mform->get_data()) {
    $mform->set_data($fromform);
}

// Fetch users from the database

$school = $DB->get_records_sql("SELECT id, school_name FROM {school}");
// var_dump($school);die;
$existing_schools = $DB->get_records_sql("SELECT aa.schoolid AS id, s.school_name AS school_name FROM {assignedschool} aa JOIN {school} s ON aa.schoolid = s.id WHERE aa.schoolid = ?", [$schoolid]);

$mform->display();
?>

<form method="post" action="">
    <input type="hidden" name="armid" value="<?php echo htmlspecialchars($armid); ?>" id="armid">

    <table id="assigningschool" summary="" class="admintable schoolassigntable generaltable" cellspacing="0">
        <tbody>
            <tr>
                <td id="existingcell">
                    <p><label for="removeselect">Existing schools</label></p>
                    <div class="schoolselector" id="removeselect_wrapper">
                        <select name="removeselect[]" id="removeselect" multiple="multiple" size="20" class="form-control no-overflow">
                        <optgroup label="Potential users">
                                <?php foreach ($existing_schools as $user) { ?>
                                    <option value="<?= htmlspecialchars($user->id); ?>"><?= htmlspecialchars($user->firstname); ?></option>
                                <?php } ?>
                            </optgroup>
                        </select>
                        <div class="form-inline">
                            <label for="removeselect_searchtext">Search</label>
                            <input type="text" name="removeselect_searchtext" id="removeselect_searchtext" size="15" value="" class="form-control">
                            <input type="button" value="Clear" class="btn btn-secondary mx-1" id="removeselect_clearbutton" onclick="clearSearch('removeselect_searchtext')">
                        </div>
                    </div>
                </td>
                <td id="buttonscell">
                    <div id="addcontrols">
                        <input name="add" id="add" type="submit" value="◄&nbsp;Add" title="Add" class="btn btn-secondary" disabled><br>
                    </div>
                    <div id="removecontrols">
                        <input name="remove" id="remove" type="submit" value="Remove&nbsp;►" title="Remove" class="btn btn-secondary" disabled><br>
                    </div>
                </td>
                <td id="potentialcell">
                    <p><label for="addselect">Potential school</label></p>
                    <div class="schoolselector" id="addselect_wrapper">
                        <select name="addselect[]" id="addselect" multiple="multiple" size="20" class="form-control no-overflow">
                            <optgroup label="Potential schools">
                                <?php foreach ($school as $sc) { ?>
                                    <option value="<?= htmlspecialchars($sc->id); ?>"><?= htmlspecialchars($sc->school_name); ?></option>
                                <?php } ?>
                            </optgroup>
                        </select>
                        <div class="form-inline">
                            <label for="addselect_searchtext">Search</label>
                            <input type="text" name="addselect_searchtext" id="addselect_searchtext" size="15" value="" class="form-control">
                            <input type="button" value="Clear" class="btn btn-secondary mx-1" id="addselect_clearbutton" onclick="clearSearch('addselect_searchtext')">
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<?php
echo $OUTPUT->footer();
?>
