<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'local/trainer:view' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
        'manager' => CAP_ALLOW,
        ),
    ),
);
