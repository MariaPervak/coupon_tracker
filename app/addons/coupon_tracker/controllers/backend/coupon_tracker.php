<?php

use Tygh\Enum\UsergroupTypes;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'update') {
        if (!empty($_REQUEST['coupon_data']) && !empty($_REQUEST['coupon_data']['partner_name']) && !empty($_REQUEST['coupon_data']['coupon_code']) && !empty($_REQUEST['coupon_data']['commission_amount'])) {
            $group_id = Registry::get('addons.coupon_tracker.user_groups');
        	$coupon_data = array(
			    'partner_name' => $_REQUEST['coupon_data']['partner_name'],
			    'coupon_code' => $_REQUEST['coupon_data']['coupon_code'],
			    'commission' => $_REQUEST['coupon_data']['commission_amount'],
			);

			if(fn_is_promo_code_exist($_REQUEST['coupon_data']['coupon_code'])){
				$msg = 'Промокод уже используется';
    			fn_set_notification('E', fn_get_lang_var('warning'), $msg, true);
			} else{
				$coupon_data = db_query('INSERT INTO ?:coupon_tracker ?e', $coupon_data);

                $order_data = fn_coupon_tracker_get_order_data();
                foreach ($order_data as $key => $order) {
                    if ($order['coupon_code'] == $_REQUEST['coupon_data']['coupon_code']) {
                        $coupon_data_comission = array(
                            'order_id' => $order['order_id'],
                            'coupon_code' => $_REQUEST['coupon_data']['coupon_code'],
                            'commission_status' => 'O',
                            'user_group' => $group_id,
                        );
                        $order_data_q = db_query('INSERT INTO ?:coupon_tracker_comission ?e', $coupon_data_comission);
                    }
                }
			}
        }
    }
    if ($mode == 'update_status') {

        $order_data = fn_coupon_tracker_get_order_data();
        $coupon_code = $order_data[$_REQUEST['id']]['coupon_code'];

        $old_comittion_data = db_get_array("SELECT * FROM ?:coupon_tracker_comission WHERE order_id = ?s", $_REQUEST['id']);
        
        if ($old_comittion_data) {
            if ($old_comittion_data[0]['order_id'] == $_REQUEST['id']) {
                $comittion_data = array(
                    'commission_status' => $_REQUEST['status'],
                );
                $comittion_data = db_query('UPDATE ?:coupon_tracker_comission SET ?u WHERE order_id = ?i', $comittion_data, $_REQUEST['id']);
            }
        } else {
            $comittion_data = array(
                'order_id' => $_REQUEST['id'],
                'coupon_code' => $coupon_code,
                'commission_status' => $_REQUEST['status'],
            );
            $comittion_data = db_query('INSERT INTO ?:coupon_tracker_comission ?e', $comittion_data);
        }
        return true;
    }
    return array(CONTROLLER_STATUS_OK, "coupon_tracker.manage");
}

if ($mode == 'manage' || $mode == 'details') {

    $group_id = Registry::get('addons.coupon_tracker.user_groups');
    $users_in_group = db_get_fields("SELECT user_id FROM ?:usergroup_links WHERE usergroup_id = ?s", $group_id);
    if ($users_in_group) {
    	foreach ($users_in_group as $key => $user_id) {
	    	$user_name = fn_get_user_name($user_id);
	    	$data['users'][$user_id] = $user_name;
	    }
    }

    $order_data = fn_coupon_tracker_get_order_data();

    $data['promotions'] = fn_coupon_tracker_get_promotions_with_code();

    $fields = array(
        '?:coupon_tracker.*',
        '?:coupon_tracker_comission.order_id',
        '?:coupon_tracker_comission.commission_status',
        '?:coupon_tracker_comission.commission_points',
        '?:coupon_tracker_comission.commission_credits',
        '?:coupon_tracker_comission.user_group',
    );
    $fields = implode(',', $fields);

    $join = db_quote(' LEFT JOIN ?:coupon_tracker_comission ON ?:coupon_tracker.coupon_code = ?:coupon_tracker_comission.coupon_code');


    $coupon_data = db_get_array("SELECT ?p FROM ?:coupon_tracker ?p", $fields, $join);

    if ($coupon_data) {
        foreach ($coupon_data as $key => $coupon) {
            if ($coupon['user_group'] == $group_id) {
                $user_name = fn_get_user_name($coupon['partner_name']);
                foreach ($order_data as $order) {                
                    if ($order['coupon_code'] == $coupon['coupon_code'] && $order['order_id'] == $coupon['order_id']) {
                        $order_data_by_partner[$order['order_id']]['order_id'] = $order['order_id'];
                        $order_data_by_partner[$order['order_id']]['total'] = $order['total'];
                        $order_data_by_partner[$order['order_id']]['promotion_id'] = $order['promotion_id'];
                        $order_data_by_partner[$order['order_id']]['coupon_code'] = $order['coupon_code'];
                        $order_data_by_partner[$order['order_id']]['partner_name'] = $user_name;
                        $order_data_by_partner[$order['order_id']]['commission_points'] = $coupon['commission_points'];
                        $order_data_by_partner[$order['order_id']]['commission_credits'] = $coupon['commission_credits'];
                        $order_data_by_partner[$order['order_id']]['commission_status'] = $coupon['commission_status'];
                        
                    }
                }
                $data['coupon_data'][$key] = $coupon;
                $data['coupon_data'][$key]['user_name'] = $user_name;
            }
            
        }
        if (!empty($order_data_by_partner)) {
             $data['order_data'] = $order_data_by_partner;
        } 
    }

    if (isset($_REQUEST['coupon_code'])) {
        $data['current_coupon_code'] = $_REQUEST['coupon_code'];
    }

    if (isset($data)) {
    	Tygh::$app['view']->assign('data', $data);
    }
}

