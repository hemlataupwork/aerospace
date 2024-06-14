<?php

function local_armtoschool_extend_navigation(global_navigation $navigation)
{
    global $CFG, $PAGE;

    $icon = new pix_icon('key', '', 'local_armtoschool', array('class' => 'icon pluginicon', 'style' => 'width: 22px; height: 30px; object-fit: contain;'));

    $previewnode = $PAGE->navigation->add(
        get_string('armtoschool', 'local_armtoschool'),
        new moodle_url($CFG->wwwroot . '/local/armtoschool/school.php'),
        navigation_node::TYPE_CONTAINER
    );
}