<?php
function local_poc_extend_navigation(global_navigation $navigation) {
    global $CFG, $PAGE;
  
        $navigation->add(
            "Poc Management",
            new moodle_url($CFG->wwwroot . '/local/poc/poc_custom.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'local_poc',
            new pix_icon('i/users','')
        )->showinflatnavigation = true; 
}
?>