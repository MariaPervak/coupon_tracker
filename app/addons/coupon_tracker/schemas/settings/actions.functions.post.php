<?php 

use Tygh\Enum\UsergroupTypes;

function fn_settings_variants_addons_coupon_tracker_order_statuses()
{
    $statuses = fn_get_simple_statuses();
    foreach ($statuses as $status_code => $status) {
        $schema['fields'][$status_code]['title'] = $status;
    }

    $result = array();

    if (!empty($schema['fields'])) {
        foreach ($schema['fields'] as $field_id => $field) {
            $result[$field_id] = $field['title'];
        }
    }
    return $result;
}


function fn_settings_variants_addons_coupon_tracker_user_groups()
{
    $exclude_types = defined('RESTRICTED_ADMIN') ? array('A') : array();
    $customer_usergroups = fn_get_usergroups(array('exclude_types' => $exclude_types, 'type' => 'C', 'status' => 'A'), DESCR_SL);
    
    foreach ($customer_usergroups as $key => $group_data) {
        $schema['fields'][$group_data['usergroup_id']]['title'] = $group_data['usergroup'];
    }
    // $schema = array(
    //     'fields' => array(
    //         'product_id' => array('title' => __('product_id'), 'sort_by' => ''),
    //         'product' => array('title' => __('product_name'), 'sort_by' => 'product'),
    //     ),
    // );
    $result = array();

    if (!empty($schema['fields'])) {
        foreach ($schema['fields'] as $field_id => $field) {
            $result[$field_id] = $field['title'];
        }
    }

    return $result;
}

 ?>