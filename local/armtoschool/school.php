<?php
require_once "../../config.php";
require_login();
require_capability('moodle/site:config', context_system::instance());
global $DB, $OUTPUT, $PAGE;

$PAGE->requires->css(new moodle_url('/local/armtoschool/style.css'));
$PAGE->requires->js(new moodle_url('/local/armtoschool/script.js'));
$PAGE->set_pagelayout('standard');
$userId = $_GET['userId'];
echo $OUTPUT->header();

echo "<a href=\"{$CFG->wwwroot}/local/regionalpoc/regionalpoc_custom.php?roleid=4\" class='btn btn-primary'>BACK</a>";

$sql = "SELECT sc.course_cat_id, sc.school_name 
        FROM {school} sc
        LEFT JOIN {schoolassign} sa ON sc.course_cat_id = sa.schoolid 
        WHERE sa.schoolid IS NULL";
$schools = $DB->get_records_sql($sql);

$sql1 = "SELECT sc.course_cat_id, sc.school_name 
         FROM {schoolassign} sa 
         RIGHT JOIN {school} sc ON sc.course_cat_id = sa.schoolid 
         WHERE sa.schoolid IS NOT NULL AND sa.userid = :userid";
$exitingschools = $DB->get_records_sql($sql1, ['userid' => $userId]);

?>
<div class="top-container">
    <h2> POC Mapping</h2>
    <input type="hidden" id="userId" value='<?php echo($userId); ?>'>

    <div class="select-container">
        <div class="select-box box left-box" id="left-box">
            <h4>User Assigned Schools (<span id="assigned-count">0</span>)</h4>
            <input type="text" id="left-search" class="form-control" placeholder="Search...">
            <div id="left-list" class="scrollable-list">
                <?php
                    if (!empty($exitingschools)) {
                        foreach ($exitingschools as $exitingschool) {
                            echo "<label><input type='checkbox' value='$exitingschool->course_cat_id'> $exitingschool->school_name</label>";
                        }
                    }
                ?>
            </div>
        </div>
        <div class="middle-controls">
            <button id="move-right" class="btn btn-secondary">◄ Add</button>
            <button id="move-left" class="btn btn-secondary">Remove ►</button>
        </div>
        <div class="select-box box right-box form-control" id="right-box">
            <h4>Available Schools (<span id="available-count">0</span>)</h4>
            <input type="text" id="right-search" class="form-control" placeholder="Search...">
            <div id="right-list" class="scrollable-list">
                <?php
                if (!empty($schools)) {
                    foreach ($schools as $school) {
                        echo "<label><input type='checkbox' value='$school->course_cat_id'> $school->school_name</label>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
echo $OUTPUT->footer();
?>
