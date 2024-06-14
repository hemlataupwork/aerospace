<?php
function local_media_extend_navigation(global_navigation $navigation) {
    global $CFG, $PAGE;
  
    $icon = new pix_icon('key', '', 'local_media', array('class' => 'icon pluginicon', 'style' => 'width: 22px; height: 30px; object-fit: contain;'));

        $navigation->add(
            "Upload School Media",
            new moodle_url($CFG->wwwroot . '/local/media/media_form.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'local_media',
            new pix_icon('i/upload','')
        )->showinflatnavigation = true;
}
?>