<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function module_color($param)
{
    switch ($param) {
        case 'tasks':
            return 'warning';
            break;
        case 'milestones':
            return 'primary';
            break;
        case 'files':
            return 'info';
            break;
        case 'bugs':
            return 'danger';
            break;
        case 'projects':
            return 'success';
            break;
        default:
            return 'default';
            break;
    }
}

function replace_email_tags($tag, $value, $string)
{
    return str_replace('{'.$tag.'}', $value, $string);
}

function create_email_logo()
{
    return '<img style="width:'.config_item('invoice_logo_width').'px" src="'.base_url().'/assets/images/logos/'.config_item('invoice_logo').'"/>';
}

function get_tags($template)
{
    switch ($template) {
        case 'activate_account':
            return array('INVOICE_LOGO', 'USERNAME', 'ACTIVATE_URL', 'ACTIVATION_PERIOD', 'EMAIL', 'PASSWORD', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'change_email':
            return array('INVOICE_LOGO', 'NEW_EMAIL', 'NEW_EMAIL_KEY_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'forgot_password':
            return array('INVOICE_LOGO', 'PASS_KEY_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'registration':
            return array('INVOICE_LOGO', 'USERNAME', 'SITE_URL', 'EMAIL', 'PASSWORD', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'reset_password':
            return array('INVOICE_LOGO', 'USERNAME', 'EMAIL', 'NEW_PASSWORD', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'bug_assigned':
           return array('INVOICE_LOGO', 'ISSUE_TITLE', 'ASSIGNED_BY', 'PROJECT_TITLE', 'SITE_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'bug_status':
            return array('INVOICE_LOGO', 'ISSUE_TITLE', 'STATUS', 'MARKED_BY', 'BUG_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'bug_comment':
            return array('INVOICE_LOGO', 'POSTED_BY', 'ISSUE_TITLE', 'COMMENT_URL', 'COMMENT_MESSAGE', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'bug_file':
            return array('INVOICE_LOGO', 'UPLOADED_BY', 'ISSUE_TITLE', 'BUG_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'bug_reported':
            return array('INVOICE_LOGO', 'ISSUE_TITLE', 'ADDED_BY', 'BUG_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'project_created':
            return array('INVOICE_LOGO', 'PROJECT_TITLE', 'CREATED_BY', 'PROJECT_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'project_assigned':
            return array('INVOICE_LOGO', 'PROJECT_TITLE', 'ASSIGNED_BY', 'PROJECT_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'project_comment':
            return array('INVOICE_LOGO', 'POSTED_BY', 'PROJECT_TITLE', 'COMMENT_URL', 'COMMENT_MESSAGE', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'project_complete':
            return array('INVOICE_LOGO', 'CLIENT_NAME', 'PROJECT_TITLE', 'PROJECT_CODE', 'PROJECT_URL', 'PROJECT_HOURS', 'PROJECT_COST', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'project_file':
            return array('INVOICE_LOGO', 'UPLOADED_BY', 'PROJECT_TITLE', 'PROJECT_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'project_updated':
            return array('INVOICE_LOGO', 'PROJECT_TITLE', 'ASSIGNED_BY', 'PROJECT_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'task_created':
            return array('INVOICE_LOGO', 'TASK_NAME', 'CREATED_BY', 'PROJECT_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'task_assigned':
            return array('INVOICE_LOGO', 'TASK_NAME', 'ASSIGNED_BY', 'PROJECT_TITLE', 'PROJECT_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'task_updated':
           return array('INVOICE_LOGO', 'TASK_NAME', 'PROJECT_TITLE', 'ASSIGNED_BY', 'PROJECT_URL', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'task_comment':
            return array('INVOICE_LOGO', 'POSTED_BY', 'TASK_NAME', 'COMMENT_URL', 'COMMENT_MESSAGE', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'invoice_message':
            return array('INVOICE_LOGO', 'REF', 'CLIENT', 'CURRENCY', 'AMOUNT', 'INVOICE_LINK', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'invoice_reminder':
            return array('INVOICE_LOGO', 'REF', 'CLIENT', 'CURRENCY', 'AMOUNT', 'INVOICE_LINK', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'payment_email':
           return array('INVOICE_LOGO', 'REF', 'INVOICE_CURRENCY', 'PAID_AMOUNT', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'ticket_client_email':
            return array('INVOICE_LOGO', 'CLIENT_EMAIL', 'SUBJECT', 'TICKET_LINK', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'ticket_closed_email':
            return array('INVOICE_LOGO', 'REPORTER_EMAIL', 'SUBJECT', 'STAFF_USERNAME', 'TICKET_CODE', 'TICKET_STATUS', 'TICKET_LINK', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'ticket_reply_email':
           return array('INVOICE_LOGO', 'SUBJECT', 'TICKET_CODE', 'TICKET_STATUS', 'TICKET_REPLY', 'TICKET_LINK', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'ticket_staff_email':
            return array('INVOICE_LOGO', 'USER_EMAIL', 'SUBJECT', 'REPORTER_EMAIL', 'TICKET_LINK', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'auto_close_ticket':
            return array('INVOICE_LOGO', 'REPORTER_EMAIL', 'SUBJECT', 'TICKET_CODE', 'TICKET_STATUS', 'TICKET_LINK', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'ticket_reopened_email':
            return array('INVOICE_LOGO', 'RECIPIENT', 'SUBJECT', 'USER', 'TICKET_LINK', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'estimate_email':
            return array('INVOICE_LOGO', 'ESTIMATE_REF', 'CREATED_DATE', 'DUE_DATE', 'CLIENT', 'CURRENCY', 'AMOUNT', 'ESTIMATE_LINK', 'SITE_NAME', 'SIGNATURE');
            break;
        case 'message_received':
            return array('INVOICE_LOGO', 'RECIPIENT', 'SENDER', 'MESSAGE', 'SITE_URL', 'SITE_NAME', 'SIGNATURE');
            break;

        default:
            return array();
            break;
    }
}
function extract_tags($string, $start = '{', $end = '}')
{
    $matches = array();
    $regex = "/$start([a-zA-Z0-9_]*)$end/";
    preg_match_all($regex, $string, $matches);

    return $matches[1];
}
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function is_json($str) {
 return is_string($str) && is_array(json_decode($str, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}
