
<script type="text/javascript" src="{$js_link}"></script>
{foreach $attribute_list as $attribute}
<div class="panel product-tab">
	<h3>{$attribute.name}</h3>
	<div class="form-group SPGroup">
		<label class="control-label col-lg-3" for="availableCarriers">{l s='Carriers'}</label>
		<div class="form-group" class="no-selected-carries-alert" style="display:none;">
			<div class="col-lg-offset-3">
				<div class="alert alert-warning">{l s='If no carrier is selected then all the carriers will be available for customers orders.'}</div>
			</div>
		</div>
		<div class="col-lg-9">
			<div class="form-control-static row">
				<div class="col-xs-6">
					<p>{l s='Available carriers'}</p>
					<select class="availableSPCarriers" name="availableCarriers" multiple="multiple">
						{foreach $attribute.carriers as $carrier}
							{if !isset($carrier.selected) || !$carrier.selected}
								<option value="{$carrier.id_reference}">{$carrier.name}</option>
							{/if}
						{/foreach}
					</select>
					<a href="#" class="addSPCarrier" class="btn btn-default btn-block">{l s='Add'} <i class="icon-arrow-right"></i></a>
				</div>
				<div class="col-xs-6">
					<p>{l s='Selected carriers'}</p>
					<select class="selectedSPCarriers" name="selectedCarriers[{$attribute.id_product_attribute}][]" multiple="multiple">
						{foreach $attribute.carriers as $carrier}
							{if isset($carrier.selected) && $carrier.selected}
								<option value="{$carrier.id_reference}">{$carrier.name}</option>
							{/if}
						{/foreach}
					</select>
					<a href="#" class="removeSPCarrier" class="btn btn-default btn-block"><i class="icon-arrow-left"></i> {l s='Remove'}</a>
				</div>
			</div>
		</div>
	</div>
</div>
{/foreach}