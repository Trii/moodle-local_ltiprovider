<?php
/**
 * Kaplan, Inc
 *
 * PHP Version 5.3+
 *
 * Copyright (c) 2015-Present, Kaplan, Inc
 * All rights reserved.
 *
 * @category   Kaplan
 * @package    Kaplan
 * @subpackage
 * @author     Joshua Johnston <jojohnston@kaplan.edu>
 * @copyright  2015-Present, Kaplan, Inc
 */

/**
 * externallib - External webservice classes
 */

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . '/local/ltiprovider/lib.php');

class local_ltiprovider_external extends external_api {

    public static function list_course_tools_parameters() {
        return new external_function_parameters([
            'category' => new external_value( PARAM_INT, 'Limit to Category ID', false )
        ]);
    }

    public static function list_course_tools_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'course_name' => new external_value(PARAM_TEXT, 'Name of the Course exposed'),
                'course_summary' => new external_value(PARAM_RAW_TRIMMED, 'Course Summary'),
                'lti_launch_url' => new external_value(PARAM_TEXT, 'The URL to access the tool'),
                'role_name' => new external_value(PARAM_TEXT, 'Friendly role name when the user is added as an urn:lti:instrole:ims/lis/Instructor')
            ])
        );
    }

    public static function list_course_tools($category = null) {
        global $CFG;
        $roles = array_reduce(role_get_names(), function($result, $role) {
            $result[$role->id] = $role;
            return $result;
        });

        $tools = local_ltiprovider_get_tools_by_context_level($category);
        $uri = $CFG->wwwroot . '/local/ltiprovider/tool.php?';
        $output = array_map(function($tool) use ($roles, $uri) {
            return [
                'course_name'    => $tool->shortname,
                'course_summary' => $tool->summary,
                'lti_launch_url' => $uri . http_build_query(['id' => $tool->id]),
                'role_name'      => $roles[$tool->croleinst]->shortname
            ];
        }, iterator_to_array($tools));
        return $output ?: [];
    }
}

function local_ltiprovider_get_tools_by_context_level($category = null) {
    /* @var $DB moodle_database */
    global $DB;
    $params = [ 'contextlevel' => CONTEXT_COURSE ];

    $sql = 'SELECT l.*, c.shortname, c.idnumber, c.summary'
        . '   FROM {local_ltiprovider} l'
        . '   JOIN {context} ctx'
        . '     ON ctx.id = l.contextid'
        . '    AND ctx.contextlevel = :contextlevel'
        . '   JOIN {course} c'
        . '     ON c.id = l.courseid';
    if ($category) {
        $sql .= ' AND c.category = :category';
        $params['category'] = $category;
    }
    $items = $DB->get_recordset_sql($sql, $params);
    return $items;
}