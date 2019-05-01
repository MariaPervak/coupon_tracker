<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }


use Tygh\Registry;


function fn_is_promo_code_exist($coupon_code){
	$coupon_code = db_get_fields("SELECT coupon_code FROM ?:coupon_tracker WHERE coupon_code = ?s", $coupon_code);
	if (!empty($coupon_code)) {
		return true;
	} else{
		return false;
	}
}

function fn_get_coupon_statuses($type = STATUSES_ORDER, $additional_statuses = false, $exclude_parent = false, $lang_code = CART_LANGUAGE)
{
    $result = array();
    $statuses = fn_get_statuses($type, array('O', 'C', 'D'), $additional_statuses, $exclude_parent, $lang_code);

    foreach ($statuses as $key => $status) {
        $result[$key] = $status['description'];
    }

    return $result;
}

function fn_coupon_tracker_get_promotions_with_code($coupon_code = ''){
    list($promotions, $search) = fn_get_promotions($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    if ($promotions) {
        foreach ($promotions as $key => $promotion) {
            $conditions_variants = explode(';', $promotion['conditions_hash']);
            foreach ($conditions_variants as $variant) {
                $conditions_array = explode('=', $variant);
            }
            
            if (!empty($coupon_code)) {
                if (in_array($coupon_code, $conditions_array)) {
                    $promotions_with_code[$key] = $promotion;
                    $promotions_with_code[$key]['coupon_code'] = $conditions_array[1];
                }
                // $data['current_promotion'] = $current_promotion;
            } else {
            	if (in_array('coupon_code', $conditions_array) && $promotion['status'] == 'A') {
	                $promotions_with_code[$key]['promotion_id'] = $promotion['promotion_id'];
	                $promotions_with_code[$key]['coupon_code'] = $conditions_array[1];
	            }
            }
        }
        $data['promotions'] = $promotions_with_code;
        
        return $data['promotions'];
        
    }
}

function fn_coupon_tracker_get_promotion_by_id($promotion_id = ''){
	list($promotions, $search) = fn_get_promotions($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
	if ($promotions) {
        foreach ($promotions as $key => $promotion) {
        	// fn_print_r($promotion);
            $conditions_variants = explode(';', $promotion['conditions_hash']);
            foreach ($conditions_variants as $variant) {
                $conditions_array = explode('=', $variant);
            }
            if (in_array('coupon_code', $conditions_array) && $promotion['status'] == 'A' && $promotion['promotion_id'] == $promotion_id) {
                $promotions_with_code[$key]['promotion_id'] = $promotion['promotion_id'];
	            $promotions_with_code[$key]['coupon_code'] = $conditions_array[1];
            }
        }        
    }
	return $promotions_with_code;
}

function fn_coupon_tracker_get_order_data($coupon_code = ''){
    $params = array();
    list($orders, $search, $totals) = fn_get_orders($params);

    if (empty($coupon_code)) {
        $coupon_codes = fn_coupon_tracker_get_promotions_with_code();
    } else {
        $coupon_codes = fn_coupon_tracker_get_promotion_by_id($coupon_code);
    }
    

    foreach ($coupon_codes as $coupon_code) {
        foreach ($orders as $key => $order) {

            $order_info = @fn_get_order_info($order['order_id'], false, false, true, false);
            $promotion_ids = explode(',', $order_info['promotion_ids']);

            if (in_array($coupon_code['promotion_id'], $promotion_ids)) {
                $order_data[$order['order_id']]['order_id'] = $order['order_id'];
                $order_data[$order['order_id']]['total'] = $order['total'];
                $order_data[$order['order_id']]['promotion_id'] = $coupon_code['promotion_id'];
                $order_data[$order['order_id']]['coupon_code'] = $coupon_code['coupon_code'];
            }
        }
    }
    return($order_data);
}

