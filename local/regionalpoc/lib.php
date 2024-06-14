<?php

global $USER;

// var_dump($USER->id);
// die;

// if(!is_siteadmin()  user_has_role_assignment($USER->id, 1, '')){
function local_regionalpoc_extend_navigation(global_navigation $navigation) {
    global $CFG, $PAGE;
  
        $navigation->add(
            "Regional Poc Management",
            new moodle_url($CFG->wwwroot . '/local/regionalpoc/regionalpoc_custom.php?roleid=3'),
            navigation_node::TYPE_CUSTOM,
            null,
            'local_regionalpoc',
            new pix_icon('i/users','')
        )->showinflatnavigation = true; 
}

// }
?>