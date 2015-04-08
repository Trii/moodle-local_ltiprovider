<?php
/**
 * Kaplan, Inc
 *
 * PHP Version 5.3+
 *
 * Copyright (c) 2015-present, Kaplan, Inc
 * All rights reserved.
 *
 * @author     Joshua Johnston <jojohnston@kaplan.edu>
 * @copyright  2015-present, Kaplan, Inc
 */


/**
 * Webservice function map
 */
$functions = array(
    'local_ltiprovider_list_course_tools' => array(
        'classname'   => 'local_ltiprovider_external',
        'methodname'  => 'list_course_tools',
        'classpath'   => 'local/ltiprovider/externallib.php',
        'description' => 'Returns a list of all course tools where the contextlevel = ' . CONTEXT_COURSE,
        'type'        => 'read',
    )
);
