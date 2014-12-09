		<script>var zones_nbr = {$zones|count +3} ; /*corresponds to the third input text (max, min and all)*/</script>
		<div id="zone_ranges" style="overflow:auto">
			<h4>{l s='Ranges'}</h4>
			<table id="zones_table" class="table" style="max-width:100%">
				<tbody>
					<tr class="range_inf">
						<td class="range_type"></td>
						<td class="border_left border_bottom range_sign">&gt;=</td>
						{foreach from=$ranges key=r item=range}
						<td class="border_bottom">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT}</span>
								<span class="input-group-addon price_unit">{$currency_sign}</span>
								<span class="input-group-addon price_quantity">item</span>
								<input class="form-control" name="range_inf[]" type="text" value="{$range.lower|string_format:"%.6f"}" />
							</div>
						</td>
						{foreachelse}
						<td class="border_bottom">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT}</span>
								<span class="input-group-addon price_unit">{$currency_sign}</span>
								<span class="input-group-addon price_quantity">item</span>
								<input name="form-control range_inf[]" type="text" />
							</div>
						</td>
						{/foreach}
					</tr>
					<tr class="range_sup">
						<td class="range_type"></td>
						<td class="border_left range_sign">&lt;</td>
						{foreach from=$ranges key=r item=range}
						<td class="range_data">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT}</span>
								<span class="input-group-addon price_unit">{$currency_sign}</span>
								<span class="input-group-addon price_quantity">item</span>
								<input class="form-control" name="range_sup[]" type="text" value="{$range.upper|string_format:"%.6f"}" />
							</div>
						</td>
						{foreachelse}
						<td class="range_data_new">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT}</span>
								<span class="input-group-addon price_unit">{$currency_sign}</span>
								<span class="input-group-addon price_quantity">item</span>
								<input class="form-control" name="range_sup[]" type="text" />
							</div>
						</td>
						{/foreach}
					</tr>
					<tr class="fees_all" style="display:none;">
						<td class="border_top border_bottom border_bold">
							<span class="fees_all">All</span>
						</td>
						<td style="">
							<input type="checkbox" onclick="checkAllZones(this);" class="form-control">
						</td>
						{foreach from=$ranges key=r item=range}
						<td class="border_top border_bottom" >
							<div class="input-group fixed-width-md">
								<span class="input-group-addon currency_sign">{$currency_sign}</span>
								<input class="form-control" type="text"/>
							</div>
						</td>
						{foreachelse}
						<td class="border_top border_bottom">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon currency_sign" style="display:none">{$currency_sign}</span>
								<input class="form-control" style="display:none" type="text"  />
							</div>
						</td>
						{/foreach}
					</tr>
					<tr class="fees">
						<td>
							
						</td>
						<td class="zone">
							<input class="form-control input_zone" value="1" type="checkbox" checked="checked" style="display:none;"/>
						</td>
						{foreach from=$ranges key=r item=range}
						<td>
							<div class="input-group fixed-width-md">
								<span class="input-group-addon">{$currency_sign}</span>
								<input class="form-control" name="fees[]" type="text"  value="{$range.price|string_format:"%.6f"}" />
							</div>
						</td>
						{foreachelse}
						<td>
							<div class="input-group fixed-width-md">
								<span class="input-group-addon">{$currency_sign}</span>
								<input class="form-control" name="fees[]" type="text" />
							</div>
						</td>
						{/foreach}
					</tr>
					<tr class="delete_range">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						{foreach from=$ranges name=ranges key=r item=range}
							{if $smarty.foreach.ranges.first}
								<td>&nbsp;</td>
							{else}
								<td>
									<button class="btn btn-default">{l s='Delete'}</button>
								</td>
							{/if}
						{/foreach}
					</tr>
				</tbody>
			</table>
		</div>
