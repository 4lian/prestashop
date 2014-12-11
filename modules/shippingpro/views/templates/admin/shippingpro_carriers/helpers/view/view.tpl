
{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
<script>
	var validate_url = '{$validate_url|addslashes}';
	var id_carrier = '{$id_carrier}';
	var need_to_validate = '{l s='Please validate the last range before create a new one.' js=1}';
	var delete_range_confirm = '{l s='Are you sure to delete this range ?' js=1}';
	var currency_sign = '{$currency_sign}';
	var PS_WEIGHT_UNIT = '{$PS_WEIGHT_UNIT}';
	var item_unit = '{$item_unit}';
	var invalid_range = '{l s='This range is not valid' js=1}';
	var overlapping_range = '{l s='Ranges are overlapping' js=1}';
	var range_is_overlapping = '{l s='Ranges are overlapping' js=1}';
	var string_price = '{l s='Will be applied when the price is' js=1}';
	var string_weight = '{l s='Will be applied when the weight is' js=1}';
	var string_quantity = '{l s='Will be applied when the quantity is' js=1}';
	var delete_rule_confirm = '{l s='Are you sure to delete this rule ?' js=1}';
</script>

<div class="row">
	<div id="rule-content" class="col-sm-10">
		{$content}
	</div>
</div>
<div class="actionBar">
	<a href="#" onclick="add_new_rule(this);return false;" class="btn btn-default" id="add_new_rule">{l s='Add new rule'}</a>
	<a href="#" class="buttonFinish btn btn-success" onclick="onFinishCallback(); return false;">完成</a>
</div>
{/block}
