{assign var="coupon_status_descr" value=$smarty.const.STATUSES_ORDER|fn_get_coupon_statuses:$get_additional_statuses:true}
{assign var="extra_status" value=$config.current_url|escape:"url"}
{assign var="comission_statuses" value=$smarty.const.STATUSES_ORDER|fn_get_coupon_statuses:$get_additional_statuses:true}


{capture name="add_coupon_parameters"}

<form action="{""|fn_url}" method="post" name="coupon_add_var" class="form-horizontal form-edit">
    {* {$data|print_r} *}
    <input type="hidden" name="page" value="{$smarty.request.page}" />

    {if $data['users']}
        <div class="control-group">
            <label class="control-label cm-required" for="rule_name">{__("coupon_tracker.partner_name")}:</label>
            <div class="controls">
                <select name="coupon_data[partner_name]" id="partner-name">
                    {foreach from=$data['users'] key=user_id item=user_name}
                        <option value="{$user_id}">{$user_name}</option>
                    {/foreach}
                </select> 
            </div>
        </div>
    {/if} 

    {if $data['promotions']}
        <div class="control-group">
            <label class="control-label cm-required" for="coupon_code">{__("coupon_tracker.coupon_code")}:</label>
            <div class="controls">
                <select name="coupon_data[coupon_code]" id="coupon_code">
                    {foreach from=$data['promotions'] key=id item=promotion}
                        <option value="{$promotion['coupon_code']}">{$promotion['coupon_code']}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    {/if}
    <div class="control-group">
        <label class="control-label cm-required" for="commission_amount">{__("coupon_tracker.commission_amount")}:</label>
        <div class="controls">
            <input type="text" name="coupon_data[commission_amount]" id="commission_amount" value="" class="span9" />
        </div>
    </div>

    <div class="buttons-container">
        {include file="buttons/save_cancel.tpl" but_name="dispatch[coupon_tracker.update]" cancel_action="close"}
    </div>
</form>

{/capture}

{capture name="mainbox"}

{if $data.order_data}
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
            {* {$o|print_r} *}
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
{/if}

{/capture}


{capture name="adv_buttons"}
    {include file="common/popupbox.tpl" id="add_coupon_parameters" text=__("new_rule") title=__("add_new") content=$smarty.capture.add_coupon_parameters act="general" icon="icon-plus"}
{/capture}


{include file="common/mainbox.tpl" title="Coupon code tracking" content=$smarty.capture.mainbox  adv_buttons=$smarty.capture.adv_buttons content_id="manage_coupon_tracker"}