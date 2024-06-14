<?php
require_once('../config.php');
global $USER, $DB, $OUTPUT, $CFG;
require_login();
$studentid = $USER->id;
if (is_siteadmin()) {

  $title = 'Admin Dashboard';
} else {
  $title = 'Student Dashboard';
}
$pagetitle = $title;
$PAGE->set_title($title);
$PAGE->set_heading($title);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

// User Picture
$context = context_user::instance($USER->id);
$file_path = $USER->picture;
$user_picture_url = moodle_url::make_pluginfile_url(
  $context->id,
  'user',
  'icon',
  null,
  '/',
  $file_path
);
$picture_notfound_url = "https://static.vecteezy.com/system/resources/previews/000/439/863/original/vector-users-icon.jpg";
$user_picture_url = $file_path ? $user_picture_url : $picture_notfound_url;

// Role of User
$managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
$coursecreatorroleid = $DB->get_field('role', 'id', ['shortname' => 'coursecreator']);
$editingteacherroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
$teacherroleid = $DB->get_field('role', 'id', ['shortname' => 'teacher']);
$studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
$is_student = false;
if (is_siteadmin()) {
  $user_role = get_string('role_admin', 'local_runningbatch');
} else if (user_has_role_assignment($USER->id, $managerroleid)) {
  $user_role = get_string('role_manager', 'local_runningbatch');
} else if (user_has_role_assignment($USER->id, $coursecreatorroleid)) {
  $user_role = get_string('role_coursecreator', 'local_runningbatch');
} else if (user_has_role_assignment($USER->id, $editingteacherroleid)) {
  $user_role = get_string('role_editingteacher', 'local_runningbatch');
} else if (user_has_role_assignment($USER->id, $teacherroleid)) {
  $user_role = get_string('role_teacher', 'local_runningbatch');
} else if (user_has_role_assignment($USER->id, $studentroleid)) {
  $user_role = get_string('role_student', 'local_runningbatch');
  $is_student = true;
}

//////////////////////////////////////////////COURSE COMPLETION//////////////////////////////////////////////
$sql = "SELECT * FROM {course} WHERE format='topics'";
$courses = $DB->get_records_sql($sql);
$courseProgressArray = array();
$sno = 0;

//////////////////////////////////////////////COURSE COMPLETION END//////////////////////////////////////////////

// Get last seven days record.
$todaydate = strtotime(date('y-m-d', time()));
$now = time();
$previousdate = strtotime('-6 days', $todaydate);

$sevendaysrecord = $DB->get_records_sql("SELECT * FROM {logstore_standard_log} WHERE action = 'loggedin' AND timecreated BETWEEN $previousdate AND $now");
foreach ($sevendaysrecord as $value) {
  $day = date('l', $value->timecreated);
  if ($day == 'Sunday') {
    $sun[] = $value->userid;
  }
  if ($day == 'Monday') {
    $mon[] = $value->userid;
  }
  if ($day == 'Tuesday') {
    $tues[] = $value->userid;
  }
  if ($day == 'Wednesday') {
    $wed[] = $value->userid;
  }
  if ($day == 'Thursday') {
    $thurs[] = $value->userid;
  }
  if ($day == 'Friday') {
    $fri[] = $value->userid;
  }
  if ($day == 'Saturday') {
    $sat[] = $value->userid;
  }
}

$useractivity = [count(array_unique($mon)), count(array_unique($tues)), count(array_unique($wed)), count(array_unique($thurs)), count(array_unique($fri)), count(array_unique($sat)), count(array_unique($sun))];
$useractivity = implode(",", $useractivity);

$templatecontext = [
  'is_admin' => is_siteadmin(),
  'is_student' => $is_student,
  'userfirstname' => $USER->firstname,
  'userlastname' => $USER->lastname,
  'user_picture' => $user_picture_url,
  'user_role' => $user_role,
  'db' => $DB,
  'courseProgressArray' => $courseProgressArray,
  'useractivity' => $useractivity
];
$hourcount = 0;

if (!is_siteadmin()) {

//   $query = $DB->get_records_sql("SELECT durationwatchinseconds FROM {course_video} WHERE userid='$studentid'");
//   foreach ($query as $que) {
//     $hourcount += $que->durationwatchinseconds;
//   }

  $hours = floor($hourcount / 3600);
  $remainingSeconds = $hourcount % 3600;
  $minutes = floor($remainingSeconds / 60);
  $percentage = ($hourcount / (5 * 3600)) * 100;
  $templatecontext['hours'] = $hours;
  $templatecontext['percentage'] = $percentage;
  $templatecontext['minutes'] = $minutes;

  $name = $DB->get_record_sql("SELECT username FROM {user} WHERE id='$studentid'");

  $templatecontext['username'] = $name->username;

  $que = $DB->get_records_sql("SELECT mc.fullname,mc.id from {course} mc join {enrol} me on mc.id=me.courseid join {user_enrolments} mue on me.id=mue.enrolid join {user} mu on mu.id=mue.userid where mc.id !=1 and mu.id=$USER->id ");

  function getcourseimg($val)
  {
    $course = get_course($val);
    $imageUrl = \core_course\external\course_summary_exporter::get_course_image($course);
    if ($imageUrl) {
      return $imageUrl;
    } else {
      return 'https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-9887654-8019228.png?f=webp';
    }
  }

  function col_coursestatus($val)
  {
    global $DB, $USER;

    $course = $DB->get_record('course', ['id' => $val]);

    $progress = \core_completion\progress::get_course_progress_percentage($course, $USER->id);

    // Round the $progress value to two decimal places
    $roundedProgress = round($progress, 2);

    return $roundedProgress;
  }

  $coursecount = 0;

  $data = array();

  foreach ($que as $field) {
    $coursecount += 1;
    $courseData = array(
      'fullname' => $field->fullname,
      'id' => $field->id,
      'coursestatus' => col_coursestatus($field->id), // Assuming col_coursestatus returns some data
      'courseimg' => getcourseimg($field->id), // Assuming col_coursestatus returns some data
    );

    $data[] = $courseData;
  }
  $templatecontext['coursecount'] = $coursecount;

  // // die;
  //       $course = $DB->get_record('course', ['id' => ]);
  //        var_dump($course);
  //        die;

  //         $progress = \core_completion\progress::get_course_progress_percentage($course, $USER->id);
  // //            var_dump($progress);
  // var_dump($fields);
  // die;

  $templatecontext['data'] =  $data;

  $time = time();
  $events = $DB->get_records_sql("SELECT name,timestart FROM {event} where timestart>$time ORDER BY timestart  ASC LIMIT 2  ");
  // var_dump($events);
  // die;

  $eventarr = [];

  foreach ($events as $event) {
    $eventdetails = new stdClass();
    $eventdetails->eventname = $event->name;
    $eventdetails->time = date('d-m-y', $event->timestart);
    $eventarr[] = $eventdetails;
  }

  $templatecontext['eventarr'] =  $eventarr;

  $courses = $DB->get_records_sql("SELECT fullname,summary,startdate,id from {course} where startdate > $time");

  // var_dump($courses);
  // die;
  $upcomingcourses = [];

  function get_course_image($courseid)
  {

    $course = get_course($courseid);
    $url = \core_course\external\course_summary_exporter::get_course_image($course);

    if ($url) {
      return $url;
    } else {
      return null;
    }
  }

//   $wishlistcount = $DB->get_record_sql("SELECT count(*) as wishlist from {order_item} where userid=$studentid");
//   $wishlistCountNumber = $wishlistcount->wishlist;

  function iswishlistAdded($id, $userid)
  {
    global $DB;
    $isadded = $DB->get_record_sql("SELECT wishlist from {order_item} where courseid = '$id' and  userid= '$userid'");
    if ($isadded) {

      return 'solid';
    } else {
      return 'regular';
    }
  }

  foreach ($courses as $course) {
    $upcomingcoursedetails = new stdClass();
    $upcomingcoursedetails->fullname = $course->fullname;
    $upcomingcoursedetails->summary = $course->summary;
    $upcomingcoursedetails->img = get_course_image($course->id);
    // $upcomingcoursedetails->isAdded = iswishlistAdded($course->id, $studentid);
    $upcomingcoursedetails->userid = $studentid;
    $upcomingcoursedetails->courseid = $course->id;

    $upcomingcourses[] = $upcomingcoursedetails;
  }

//   $payment = $DB->get_records_sql("
//     SELECT p.*, c.fullname
//     FROM {payment_razorpay} p
//     JOIN {course} c ON p.courseid = c.id
//     WHERE p.userid = $USER->id ORDER BY id DESC limit 8
// ");

  $paymentcourse = [];

  foreach ($payment as $course) {
    $paymentfield = new stdClass();
    $paymentfield->coursename = $course->fullname;
    $paymentfield->price = $course->amount;
    $paymentfield->paymenttime = date('d-m-y ', $course->paymenttime);;
    $paymentfield->status = $course->status;
    $paymentcourse[] = $paymentfield;
  }

  $templatecontext['paymenthistory'] =  $paymentcourse;
  $templatecontext['upcomingcourses'] =  $upcomingcourses;
  $templatecontext['wishlistCountNumber'] =  $wishlistCountNumber;
} else {

//   $payment = $DB->get_record_sql("SELECT id, SUM(amount) as total_amount FROM {payment_razorpay}");
  // var_dump($payment->total_amount);
  // die;

//   $transaction = $DB->get_records_sql("SELECT pr.id, pr.amount as price , c.fullname as coursename , u.username as username , pr.paymenttime as paymenttime
//                                     FROM {payment_razorpay} pr 
//                                     JOIN {course} c ON c.id = pr.courseid 
//                                     JOIN {user} u ON u.id = pr.userid 
//                                     ORDER BY pr.id DESC LIMIT 3");
  $transactionObjects = array();

  // Looping through each transaction record and creating objects
  foreach ($transaction as $data) {
    // Creating an object for each transaction
    $transactionObject = new stdClass();
    $transactionObject->id = $data->id;
    $transactionObject->amount = $data->price;
    $transactionObject->coursename = $data->coursename;
    $transactionObject->username = $data->username;
    $transactionObject->paymenttime = date("d-m-Y H:i", $data->paymenttime);

    // Adding the object to the array
    $transactionObjects[] = $transactionObject;
  }

  $registereduser = $DB->get_record_sql("SELECT count(distinct(userid)) as registereduser from {role_assignments}  where roleid =5 ");
  // var_dump(intval($registereduser->registereduser));
  // die;

//   $enrolleduser = $DB->get_record_sql("SELECT count(distinct(userid)) as enrolleduser FROM {payment_razorpay} ");
  // var_dump(intval($enrolleduser->enrolleduser));
  $percentage = intval($enrolleduser->enrolleduser) / intval($registereduser->registereduser) * 100;
  $notenrolled = 100 - $percentage;
  $notpaid = intval($registereduser->registereduser) - intval($enrolleduser->enrolleduser);
  // var_dump();
  // die;
  // echo count($totaluser);

  $total = $payment->total_amount;
  $maintotal = number_format($total, 2, '.', ','); // Format with 2 decimal places, '.' as decimal separator, and ',' as thousands separator
  $maintotal_with_symbol = '₹ ' . $maintotal; // Add Indian Rupee symbol

  $templatecontext['totalamount'] =  $maintotal_with_symbol;
  $templatecontext['transaction'] =  $transactionObjects;
  $templatecontext['enrolpercentage'] =  $percentage;
  $templatecontext['notregisteredpercentage'] =  $notenrolled;
  $templatecontext['paidstudents'] =  $enrolleduser->enrolleduser;
  $templatecontext['notpaid'] =  $notpaid;

  $course = $DB->get_record_sql("SELECT count(id) as totalcourse FROM {course} where id!=1");
//   $video = $DB->get_record_sql("SELECT count(id) as totalvideo FROM {course_video_detail} ");
  $user = $DB->get_record_sql("SELECT COUNT(distinct(userid)) AS totalstudent
FROM {role_assignments} ra
JOIN {context} ctx ON ra.contextid = ctx.id
JOIN {role} r ON ra.roleid = r.id
WHERE r.shortname = 'student' 
AND ctx.contextlevel = 50");

  $templatecontext['coursecount'] =  "$course->totalcourse";
  $templatecontext['videocount'] =  "$video->totalvideo";
  $templatecontext['studentcount'] =  "$user->totalstudent";
  $templatecontext['topuserid'] =  "$user->totalstudent";
  $templatecontext['studentcount'] =  "$user->totalstudent";
  $templatecontext['topusers'] =  $topuserObjects;
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_dashboard_main/admin_dashboard', $templatecontext);
echo $OUTPUT->footer();
