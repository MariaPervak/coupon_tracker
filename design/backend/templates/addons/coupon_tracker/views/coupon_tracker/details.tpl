{capture name="mainbox"}

{assign var="coupon_status_descr" value=$smarty.const.STATUSES_ORDER|fn_get_coupon_statuses:$get_additional_statuses:true}
{assign var="extra_status" value=$config.current_url|escape:"url"}
{assign var="comission_statuses" value=$smarty.const.STATUSES_ORDER|fn_get_coupon_statuses:$get_additional_statuses:true}


<div class="table-responsive-wrapper">
    <table width="100%" class="table table-middle table-responsive">
        <thead>
            <tr>
                <th  class="left mobile-hide">
                    {include file="common/check_items.tpl" check_statuses=$coupon_status_descr}
                    </th>
                    <th width="17%">{__("coupon_tracker.partner_name")}</th>
                    <th width="17%">{__("coupon_tracker.coupon_code")}</th>
                    <th width="15%">{__("coupon_tracker.order_id")}</th>
                    <th width="10%">{__("coupon_tracker.order_price")}</th>
                    <th width="11%">{__("coupon_tracker.commission_points")}</th>
                    <th width="11%">{__("coupon_tracker.commission_credits")}</th>
                    <th width="19%">{__("coupon_tracker.commission_status")}</th>
            </tr>
        </thead>
        {foreach from=$data.order_data item="o"}
        <tr>
            <td class="left mobile-hide">
                <input type="checkbox" name="coupon_ids[]" value="{$o.order_id}" class="cm-item-status-{$o.commission_status|lower}" /></td>
            <td data-th="{__("coupon_tracker.partner_name")}">
                <bdi>{$o.partner_name}</bdi>
            </td>
            <td data-th="{__("coupon_tracker.coupon_code")}">
               <a href="{"coupon_tracker.details?coupon_code=`$o.coupon_code`"|fn_url}" class="underlined"><bdi>#{$o.coupon_code}</bdi></a> 
            </td>
            <td class="nowrap" data-th="{__("coupon_tracker.order_id")}">{$o.order_id}</td>
            <td data-th="{__("total")}">
                {include file="common/price.tpl" value=$o.total}
            </td>
            <td data-th="{__("coupon_tracker.commission_points")}"><bdi>{$o.commission_points}</bdi></td>
            <td data-th="{__("coupon_tracker.commission_credits")}"><bdi>{$o.commission_credits}</bdi></td>
            <td class="right" data-th="{__("coupon_tracker.commission_status")}">
                {include file="common/select_popup.tpl"
                         suffix="o"
                         order_info=$o
                         id=$o.order_id
                         status=$o.commission_status
                         items_status=$coupon_status_descr
                         update_controller="coupon_tracker"
                         notify=false
                         notify_department=false
                         notify_vendor=false
                         status_target_id=false
                         extra="&return_url=`$extra_status`"
                         statuses=$comission_statuses
                         btn_meta="btn btn-info o-status-`$o.commission_status` btn-small"|lower
                }
            </td>
        </tr>
        {/foreach}
    </table>
</div>
{/capture}
{capture name="mainbox_title"}
    {__("coupon_tracker.coupon_code")} &lrm;#{$data.current_coupon_code}
{/capture}

{include file="common/mainbox.tpl" title=$smarty.capture.mainbox_title content=$smarty.capture.mainbox}