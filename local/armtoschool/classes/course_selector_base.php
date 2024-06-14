<?php
define('COURSE_SELECTOR_DEFAULT_ROWS', 20);

class course_selector_base
{
    /** @var string The control name (and id) in the HTML. */
    public $name;
    /** @var string The userid of the respected user. */
    public $userid;
    /** @var int The type of course (Either it is Trending or Our). */
    protected $coursetype;
    /** @var object Context used for capability checks regarding this selector (does
     * not necessarily restrict user list) */
    protected $accesscontext;
    /** @var boolean Whether the conrol should allow selection of many users, or just one. */
    protected $multiselect = true;
    /** @var int The height this control should have, in rows. */
    protected $rows = COURSE_SELECTOR_DEFAULT_ROWS;
    /** @var array A list of userids that should not be returned by this control. */
    protected $exclude = array();
    /** @var array|null A list of the users who are selected. */
    protected $selected = null;
    /** @var boolean If only one user matches the search, should we select them automatically. */
    protected $autoselectunique = false;
    /** @var mixed This is used by get selected users */
    protected $validatinguserids = null;

    /** @var array JavaScript YUI3 Module definition */
    protected static $jsmodule = array(
        'name' => 'user_selector',
        'fullpath' => '/user/selector/module.js',
        'requires'  => array('node', 'event-custom', 'datasource', 'json', 'moodle-core-notification'),
        'strings' => array(
            array('previouslyselectedusers', 'moodle', '%%SEARCHTERM%%'),
            array('nomatchingusers', 'moodle', '%%SEARCHTERM%%'),
            array('none', 'moodle')
        )
    );

    /** @var int this is used to define maximum number of users visible in list */
    public $maxusersperpage = 100;

    /**
     * Constructor. Each subclass must have a constructor with this signature.
     *
     * @param int $coursetype 1=Trending | 2=Our.
     * @param array $options other options needed to construct this selector.
     * You must be able to clone a userselector by doing new get_class($us)($us->get_name(), $us->get_options());
     */
    public function __construct($userid = null, $options = array())
    {
        global $CFG, $PAGE;

        $this->userid = $userid;

        // Use specified context for permission checks, system context if not specified.
        if (isset($options['accesscontext'])) {
            $this->accesscontext = $options['accesscontext'];
        } else {
            $this->accesscontext = context_system::instance();
        }

        // Check if some legacy code tries to override $CFG->showuseridentity.
        if (isset($options['extrafields'])) {
            debugging('The user_selector classes do not support custom list of extra identity fields any more. ' .
                'Instead, the user identity fields defined by the site administrator will be used to respect ' .
                'the configured privacy setting.', DEBUG_DEVELOPER);
            unset($options['extrafields']);
        }

        if (isset($options['exclude']) && is_array($options['exclude'])) {
            $this->exclude = $options['exclude'];
        }
        if (isset($options['multiselect'])) {
            $this->multiselect = $options['multiselect'];
        }

        if (!empty($CFG->maxusersperpage)) {
            $this->maxusersperpage = $CFG->maxusersperpage;
        }
    }

    /**
     * Invalidates the list of selected users.
     *
     * If you update the database in such a way that it is likely to change the
     * list of users that this component is allowed to select from, then you
     * must call this method. For example, on the role assign page, after you have
     * assigned some roles to some users, you should call this.
     */
    public function invalidate_selected_courses()
    {
        $this->selected = null;
    }

    /**
     * Output this user_selector as HTML.
     *
     * @param string $name type of block
     * @param boolean $return if true, return the HTML as a string instead of outputting it.
     * @param string $search the courses to search.
     * @return mixed if $return is true, returns the HTML as a string, otherwise returns nothing.
     */
    public function display($name, $return = false, $search = '')
    {
        global $PAGE;

        $this->name = $name;
        $courses = $this->find_courses($search);

        $multiselect = '';
        if ($this->multiselect) {
            $name .= '[]';
            $multiselect = 'multiple="multiple" ';
        }
        $output = '<div class="userselector" id="' . $this->name . '_wrapper">' . "\n" .
            '<select name="' . $name . '" id="' . $this->name . '" ' .
            $multiselect . 'size="' . $this->rows . '" class="form-control no-overflow">' . "\n";

        // Populate the select.
        $output .= $this->output_options($courses, $search);

        // Output the search controls.
        $output .= "</select>\n<div class=\"form-inline\">\n";
        $output .= '<input type="text" name="' . $this->name . '_searchtext" id="' .
            $this->name . '_searchtext" size="15" value="' . s($search) . '" class="form-control"/>';
        $output .= '<input type="submit" name="' . $this->name . '_searchbutton" id="' .
            $this->name . '_searchbutton" value="' . $this->search_button_caption() . '" class="btn btn-secondary"/>';
        $output .= '<input type="submit" name="' . $this->name . '_clearbutton" id="' .
            $this->name . '_clearbutton" value="' . get_string('clear') . '" class="btn btn-secondary"/>';

        $output .= "</div>\n</div>\n\n";

        // Initialise the ajax functionality.
        $output .= $this->initialise_javascript($search);
        // Return or output it.
        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output the list of <optgroup>s and <options>s that go inside the select.
     *
     * This method should do the same as the JavaScript method
     * user_selector.prototype.handle_response.
     *
     * @param array $courses an array, as returned by find_courses.
     * @param string $search
     * @return string HTML code.
     */
    public function output_options($courses, $search)
    {
        $output = '';

        // If $groupedusers is empty, make a 'no matching users' group. If there is
        // only one selected user, set a flag to select them if that option is turned on.
        $select = false;
        if (empty($courses)) {
            if (!empty($search)) {
                $courses = array(get_string('nomatchingusers', '', $search) => array());
            } else {
                $courses = array(get_string('none') => array());
            }
        } else if (
            $this->autoselectunique && count($courses) == 1 &&
            count(reset($courses)) == 1
        ) {
            $select = true;
            if (!$this->multiselect) {
                $this->selected = array();
            }
        }

        // Output each option.
        foreach ($courses as $id => $course) {
            $output .= $this->output_opt($id, $course, $select);
        }

        // This method trashes $this->selected, so clear the cache so it is rebuilt before anyone tried to use it again.
        $this->selected = null;

        return $output;
    }

    /**
     * Output one particular optgroup. Used by the preceding function output_options.
     *
     * @param string $groupname the label for this optgroup.
     * @param array $users the users to put in this optgroup.
     * @param boolean $select if true, select the users in this group.
     * @return string HTML code.
     */
    protected function output_opt($id, $course, $select)
    {
        if (!empty($course)) {
            $output = '';
            $attributes = '';
            unset($this->selected[$id]);
            $output .= '    <option' . $attributes . ' value="' . $id . '">' .
                $course->school_name . "</option>\n";
        } else {
            $output = '';
            $output .= '    <option disabled="disabled">&nbsp;</option>' . "\n";
        }
        return $output;
    }

    /**
     * The height this control will be displayed, in rows.
     *
     * @param integer $numrows the desired height.
     */
    public function set_rows($numrows)
    {
        $this->rows = $numrows;
    }

    /**
     * Returns the number of rows to display in this control.
     *
     * @return integer the height this control will be displayed, in rows.
     */
    public function get_rows()
    {
        return $this->rows;
    }

    /**
     * Whether this control will allow selection of many, or just one user.
     *
     * @param boolean $multiselect true = allow multiple selection.
     */
    public function set_multiselect($multiselect)
    {
        $this->multiselect = $multiselect;
    }

    /**
     * Returns true is multiselect should be allowed.
     *
     * @return boolean whether this control will allow selection of more than one user.
     */
    public function is_multiselect()
    {
        return $this->multiselect;
    }

    /**
     * Returns the id/name of this control.
     *
     * @return string the id/name that this control will have in the HTML.
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * Note: this function must be implemented if you use the search ajax field
     *       (e.g. set $options['file'] = '/admin/filecontainingyourclass.php';)
     * @return array the options needed to recreate this user_selector.
     */
    protected function get_options()
    {
        return array(
            'class' => get_class($this),
            'name' => $this->name,
            'exclude' => $this->exclude,
            'multiselect' => $this->multiselect,
            'accesscontext' => $this->accesscontext,
        );
    }

    /**
     * Returns true if this control is validating a list of users.
     *
     * @return boolean if true, we are validating a list of selected users,
     *      rather than preparing a list of uesrs to choose from.
     */
    protected function is_validating()
    {
        return !is_null($this->validatinguserids);
    }

    /**
     * Used to generate a nice message when there are too many users to show.
     *
     * The message includes the number of users that currently match, and the
     * text of the message depends on whether the search term is non-blank.
     *
     * @param string $search the search term, as passed in to the find users method.
     * @param int $count the number of users that currently match.
     * @return array in the right format to return from the find_courses method.
     */
    protected function too_many_results($search, $count)
    {
        if ($search) {
            $a = new stdClass;
            $a->count = $count;
            $a->search = $search;
            return array(
                get_string('toomanyusersmatchsearch', '', $a) => array(),
                get_string('pleasesearchmore') => array()
            );
        } else {
            return array(
                get_string('toomanyuserstoshow', '', $count) => array(),
                get_string('pleaseusesearch') => array()
            );
        }
    }

    /**
     * Returns the string to use for the search button caption.
     *
     * @return string the caption for the search button.
     */
    protected function search_button_caption()
    {
        return get_string('search');
    }

    /**
     * Initialises JS for this control.
     *
     * @param string $search
     * @return string any HTML needed here.
     */
    protected function initialise_javascript($search)
    {
        global $USER, $PAGE, $OUTPUT;
        $output = '';

        // Put the options into the session, to allow search.php to respond to the ajax requests.
        $options = $this->get_options();
        $hash = md5(serialize($options));
        $USER->userselectors[$hash] = $options;
        return $output;
    }

    /**
     * Returns the user selector JavaScript module
     * @return array
     */
    public function get_js_module()
    {
        return self::$jsmodule;
    }

    /**
     * Finds users to display in this control.
     *
     * @param string $search
     * @return array
     */
    public function find_courses($search = '')
    {
        global $DB, $USER, $CFG;
        // Build the SQL.
        $wheres = '1=1';
        $join = '';
        $courseids = [];

        // Approved Course request ids
        // $approvedcourses = $DB->get_record_sql("SELECT GROUP_CONCAT(ms.id) as ids FROM {school} ms")->ids;
        // if (!empty($approvedcourses)) $courseids[] = $approvedcourses;

        // // Admin created course ids
        // $admincourses = $DB->get_record_sql("SELECT GROUP_CONCAT(ms.id) as ids FROM {school} ms")->ids;
        // if (!empty($admincourses)) $courseids[] = $admincourses;

        // if (!empty($courseids)) {
        //     $courseids = implode(',', $courseids);
        //     $wheres .= " AND ms.id IN ($courseids)";
        // }
        $wheres .= " AND (ms.school_name LIKE '%$search%' OR ms.school_sortname LIKE '%$search%')";


        if ($this->name == 'addselect') {
            $join .= " JOIN {schoolassign} msa ON ms.id = msa.schoolid AND msa.userid = $USER->id";
            // $wheres .= " AND msa.userid = $USER->id";
        } else {
            $join .= ' JOIN {schoolassign} msa ON ms.id = msa.schoolid';
            $wheres .= "  AND msa.userid = $this->userid";
        }

        $fields = "SELECT ms.id, ms.school_sortname, ms.school_name";
        $sql = "   FROM {school} ms
                   $join
                   WHERE $wheres";

        $orderby = ' ORDER BY ms.timecreated DESC';
        $rs = $DB->get_records_sql("$fields $sql $orderby");
        return $rs;
    }

    /**
     * Add the course to the trending courses or our courses section of the user
     * @param array $coursid
     */
    public function add_trending_course($schoolid)
    {
        global $DB, $USER;

        $obj = new stdClass();
        $obj->userid = $this->userid;
        $obj->schoolid = $schoolid;
        if (!$DB->record_exists('schoolassign', ['schoolid' => $schoolid])) {
            $obj->timemodified = time();
            $DB->insert_record('schoolassign', $obj);
        } else {
            $timemodified = time();
            $DB->execute("UPDATE {schoolassign} msa 
              SET msa.userid = :userid, 
                  msa.timemodified = :timemodified 
              WHERE msa.schoolid = :schoolid", [
                  'userid' => $this->userid,
                  'timemodified' => $timemodified,
                  'schoolid' => $schoolid
              ]);
        }
    }

    /**
     * Remove the course from the trending courses or our courses of the user
     * @param array $coursid
     */
    public function remove_trending_course($schoolid)
    {
        global $DB, $USER;

        $timemodified = time();

        $DB->execute("UPDATE {schoolassign} msa 
              SET msa.userid = :userid, 
                  msa.timemodified = :timemodified 
              WHERE msa.schoolid = :schoolid", [
                  'userid' => $USER->id,
                  'timemodified' => $timemodified,
                  'schoolid' => $schoolid
              ]);
    }
}
