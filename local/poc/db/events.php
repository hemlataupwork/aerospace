<?php

$observers = [
    [
        'eventname' => '\core\event\user_created',
        'callback' => '\local_poc\send_emailobserver::user_created',
    ],
];

