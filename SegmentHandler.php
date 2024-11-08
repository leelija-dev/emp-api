<?php
// SegmentHandler.php

function getSegmentValues($segments) {
    // Initialize default segments
    $first_segment = $second_segment = $third_segment = $forth_segment = null;

    // Whitelist IP check
    $whitelist = array('127.0.0.1', '::1');
    $isLocalhost = in_array($_SERVER['REMOTE_ADDR'], $whitelist);

    // Assign segments based on IP check
    if ($isLocalhost) {
        $first_segment = $segments[0] ?? null;
        $second_segment = $segments[1] ?? null;
        $third_segment = $segments[2] ?? null;
        $forth_segment = $segments[3] ?? null;
        $fifth_segment = $segments[4] ?? null;
    } else {
        $second_segment = $segments[0] ?? null;
        $third_segment = $segments[1] ?? null;
        $forth_segment = $segments[2] ?? null;
        $fifth_segment = $segments[3] ?? null;
    }

    return [
        'first' => $first_segment,
        'second' => $second_segment,
        'third' => $third_segment,
        'forth' => $forth_segment,
        'fifth' => $fifth_segment,
    ];
}
