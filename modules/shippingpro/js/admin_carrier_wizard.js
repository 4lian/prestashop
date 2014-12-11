
var fees_is_hide = false;

function initRange() {
	$("*[id='step_carrier_ranges']").each(function(){
		var $is_free = $(this).find('input[name="is_free"]:checked');
		if (parseInt($is_free.val()))
			is_freeClick($is_free);
	});
	displayRangeType();
}

$(document).ready(function() {
	bind_inputs();
	initCarrierWizard();
	initRange();
});

function initCarrierWizard()
{
	displayRangeType();
}

function displayRangeType()
{
	$("*[id='step_carrier_ranges']").each(function(){
		var shipping_method = parseInt($(this).find('input[name="shipping_method"]:checked').val());
		switch (shipping_method) {
			case 1:
				string = string_weight;
				$(this).find('.weight_unit').show();
				$(this).find('.price_unit').hide();
				$(this).find('.price_quantity').hide();
				break;
			case 2:
				string = string_price;
				$(this).find('.weight_unit').hide();
				$(this).find('.price_unit').show();
				$(this).find('.price_quantity').hide();
				break;
			case 4:
				string = string_quantity;
				$(this).find('.weight_unit').hide();
				$(this).find('.price_unit').hide();
				$(this).find('.price_quantity').show();
				break;
			default:
		}
		is_freeClick($(this).find('input[name="is_free"]:checked'));
		$(this).find('.range_type').html(string);
	});
}

var kkee = true;
function onFinishCallback()
{
	$('.wizard_error').remove();

	if (kkee) {
		kkee = false;
		var range_index = 0;
		$("*[id='step_carrier_ranges']").each(function(){
			$(this).find('input, select').each(function(){
				var name = $(this).attr('name');
				if (name) {
					if (name.search('\\[\\]') != -1) 
						$(this).attr('name', 'rule_form['+range_index+']['+name.replace('\[\]', '')+'][]');
					else
						$(this).attr('name', 'rule_form['+range_index+']['+name+']');
				}
			});
			range_index++;
		});
	};

	$.ajax({
		type:"POST",
		url : validate_url,
		async: false,
		// dataType: 'json',
		data : $('#rule-content form').serialize() + '&action=finish&ajax=1&id_carrier='+id_carrier,
		success : function(data) {
			console.log(data);
			return
			if (data.has_error)
			{				
				displayError(data.errors, 2);
				resizeWizard();
			}
			else
				window.location.href = carrierlist_url;
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			debugger;
			jAlert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
		}
	});
}

function onLeaveStepCallback(obj, context)
{
	if (context.toStep == nbr_steps)
		displaySummary();

	return validateSteps(context.fromStep, context.toStep); // return false to stay on step and true to continue navigation 
}

function displaySummary()
{
	// used as buffer - you must not replace directly in the translation vars
	var tmp;

	// Carrier name
	$('#summary_name').html($('#name').val());
	
	// Delay and pricing
	tmp = summary_translation_meta_informations.replace('@s2', '<strong>' + $('#delay_1').val() + '</strong>');
	if ($('#is_free_on').attr('checked'))
		tmp = tmp.replace('@s1', summary_translation_free);
	else
		tmp = tmp.replace('@s1', summary_translation_paid);
	$('#summary_meta_informations').html(tmp);
	
	// Tax and calculation mode for the shipping cost
	tmp = summary_translation_shipping_cost.replace('@s2', '<strong>' + $('#id_tax_rules_group option:selected').text() + '</strong>');	
	
		if ($('#billing_price').attr('checked'))
			tmp = tmp.replace('@s1', summary_translation_price);
		else if ($('#billing_weight').attr('checked'))
			tmp = tmp.replace('@s1', summary_translation_weight);
		else
			tmp = tmp.replace('@s1', '<strong>' + summary_translation_undefined + '</strong>');
	
	
	
	$('#summary_shipping_cost').html(tmp);
	
	// Weight or price ranges
	$('#summary_range').html(summary_translation_range+' '+summary_translation_range_limit);
	
	
	if ($('input[name="shipping_method"]:checked').val() == 1)
		unit = PS_WEIGHT_UNIT;
	else
		unit = currency_sign;

	var range_inf = summary_translation_undefined;
	var range_sup = summary_translation_undefined;
	
	$('tr.range_inf td input').each(function()
	{
		if (!isNaN(parseFloat($(this).val())) && (range_inf == summary_translation_undefined || parseFloat(range_inf) > parseFloat($(this).val())))
			range_inf = $(this).val();
	});

	$('tr.range_sup td input').each(function(){

		if (!isNaN(parseFloat($(this).val())) && (range_sup == summary_translation_undefined || parseFloat(range_sup) < parseFloat($(this).val())))
			range_sup = $(this).val();
	});
	
	$('#summary_range').html(
		$('#summary_range').html()
		.replace('@s1', '<strong>' + range_inf +' '+ unit + '</strong>')
		.replace('@s2', '<strong>' + range_sup +' '+ unit + '</strong>')
		.replace('@s3', '<strong>' + $('#range_behavior option:selected').text().toLowerCase() + '</strong>')
	);
	if ($('#is_free_on').attr('checked'))
		$('span.is_free').hide();
	// Delivery zones
	$('#summary_zones').html('');
	$('.input_zone').each(function(){
		if ($(this).attr('checked'))
			$('#summary_zones').html($('#summary_zones').html() + '<li><strong>' + $(this).closest('tr').find('label').text() + '</strong></li>');
	});
	
	// Group restrictions
	$('#summary_groups').html('');
	$('input[name$="groupBox[]"]').each(function(){
		if ($(this).attr('checked'))
			$('#summary_groups').html($('#summary_groups').html() + '<li><strong>' + $(this).closest('tr').find('td:eq(2)').text() + '</strong></li>');
	});
	
	// shop restrictions
	$('#summary_shops').html('');
	$('.input_shop').each(function(){
		if ($(this).attr('checked'))
			$('#summary_shops').html($('#summary_shops').html() + '<li><strong>' + $(this).closest().text() + '</strong></li>');
	});
}

function validateSteps(fromStep, toStep)
{
	var is_ok = true;
	if ((multistore_enable && fromStep == 3) || (!multistore_enable && fromStep == 2))
	{
		if (toStep > fromStep) {
			$("*[id='step_carrier_ranges']").each(function(){
				if (!$(this).find('#is_free_on').attr('checked') && !validateRange(2, $(this))) {
					is_ok = false;
				}
			});
		} 
	}
	
	$('.wizard_error').remove();
	
	if (is_ok && isOverlapping())
		is_ok = false;
	
	if (is_ok)
	{
		form = $('#carrier_wizard #step-'+fromStep+' form');
		$.ajax({
			type:"POST",
			url : validate_url,
			async: false,
			dataType: 'json',
			data : form.serialize()+'&step_number='+fromStep+'&action=validate_step&ajax=1',
			success : function(datas)
			{
				if (datas.has_error)
				{
					is_ok = false;
					$('div.input-group input').focus(function () {
						$(this).closest('div.input-group').removeClass('has-error');
					});
					displayError(datas.errors, fromStep);
					resizeWizard();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				jAlert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}
	return is_ok;
}

function displayError(errors, step_number)
{
	$('#carrier_wizard .actionBar a.btn').removeClass('disabled');
	$('.wizard_error').remove();
	str_error = '<div class="error wizard_error" style="display:none"><ul>';
	for (var error in errors)
	{
		$('#carrier_wizard .actionBar a.btn').addClass('disabled');
		$('input[name="'+error+'"]').closest('div.input-group').addClass('has-error');
		str_error += '<li>'+errors[error]+'</li>';
	}
	$('#step-'+step_number).prepend(str_error+'</ul></div>');
	$('.wizard_error').fadeIn('fast');
	bind_inputs();
}

function resizeWizard()
{
	// resizeInterval = setInterval(function (){$("#carrier_wizard").smartWizard('fixHeight'); clearInterval(resizeInterval)}, 100);
}

function bind_inputs()
{
	$('input').focus(function () {
		$(this).closest('div.input-group').removeClass('has-error');
		$('#carrier_wizard .actionBar a.btn').not('.buttonFinish').removeClass('disabled');
		$('.wizard_error').fadeOut('fast', function () { $(this).remove()});
	});
	
	$('tr.delete_range td button').off('click').on('click', function () {
		if (confirm(delete_range_confirm))
		{
			var $form = $(this).closest("*[id='step_carrier_ranges']");
			index = $(this).closest('td').index();
			$form.find('tr.range_sup td:eq('+index+'), tr.range_inf td:eq('+index+'), tr.fees_all td:eq('+index+'), tr.delete_range td:eq('+index+')').remove();
			$form.find('tr.fees').each(function () {
				$(this).find('td:eq('+index+')').remove();
			});
			rebuildTabindex();
		}
		return false;
	});
	
	$('tr.fees td input:checkbox').off('change').on('change', function () 
	{
		if($(this).is(':checked'))
		{
			$(this).closest('tr').find('td').each(function () {
				index = $(this).index();
				if ($('tr.fees_all td:eq('+index+')').hasClass('validated'))
				{
					enableGlobalFees(index);
					$(this).find('div.input-group input:text').removeAttr('disabled');
				}
				else
					disabledGlobalFees(index);
			});
		}
		else
			$(this).closest('tr').find('td').find('div.input-group input:text').attr('disabled', 'disabled').val('');
			
		return false;
	});
	
	$('tr.range_sup td input:text, tr.range_inf td input:text').focus(function () {
		$(this).closest('div.input-group').removeClass('has-error');
	});
	
	// $('tr.range_sup td input:text, tr.range_inf td input:text').keypress(function (evn) {
	// 	index = $(this).closest('td').index();
	// 	if (evn.keyCode == 13)
	// 	{
	// 		if (validateRange(index))
	// 			enableRange(index);
	// 		else
	// 			disableRange(index);
	// 		return false;
	// 	}
	// });
	
	// $('tr.fees_all td input:text').keypress(function (evn) {
	// 	index = $(this).parent('td').index();
	// 	if (evn.keyCode == 13)
	// 		return false;
	// });
	
	$('tr.range_sup td input:text, tr.range_inf td input:text').typeWatch({
		captureLength: 0,
		highlight: false,
		wait: 1000,
		callback: function() { 
			var index = $(this.el).closest('td').index();
			var $form = $(this.el).closest("*[id='step_carrier_ranges']");
			var range_sup = $form.find('tr.range_sup td:eq('+index+')').find('div.input-group input:text').val().trim();
			var range_inf = $form.find('tr.range_inf td:eq('+index+')').find('div.input-group input:text').val().trim();
			if (range_sup != '' && range_inf != '')
			{
				if (validateRange(index, $form))
					enableRange(index, $form);
				else
					disableRange(index, $form);
			}
		}
	});
	
	$(document.body).off('change', 'tr.fees_all td input').on('change', 'tr.fees_all td input', function() {
		index = $(this).closest('td').index();
		val = $(this).val();
		$(this).val('');
		$(this).closest("*[id='step_carrier_ranges']").find('tr.fees').each(function () {
			$(this).find('td:eq('+index+') input:text:enabled').val(val);
		});
		
		return false;
	});
	
	$('input[name="is_free"]').off('click').on('click', function() {
		is_freeClick($(this));
	});
		
	$('input[name="shipping_method"]').off('click').on('click', function() {
		displayRangeType();
		bind_inputs();
	});
	
	$('#zones_table td input[type=text]').off('change').on('change', function () {
		checkAllFieldIsNumeric();
	});	

	$('*[name="submitAddcarrier"]').off('click').on('click', function(e) {
		e.preventDefault();
		if (confirm(delete_rule_confirm)) {
			$(this).closest('#step_carrier_ranges').remove();
		}
	});

}

function is_freeClick(elt)
{
	var is_free = $(elt);
	var $form = is_free.closest("*[id='step_carrier_ranges']");
	if (parseInt(is_free.val()))
		hideFees($form);
	else if (fees_is_hide)
		showFees($form);
}

function hideFees($form)
{
	$form.find('tr.range_inf td, tr.range_sup td, tr.fees_all td, tr.fees td').each(function () {
		if ($(this).index() >= 2)
		{
			$(this).find('input:text, button').val('').attr('disabled', 'disabled').css('background-color', '#999999').css('border-color', '#999999');
			$(this).css('background-color', '#999999');
		}
	});
	fees_is_hide = true;
}

function showFees($form)
{
	$form.find('tr.range_inf td, tr.range_sup td, tr.fees_all td, tr.fees td').each(function () {
		if ($(this).index() >= 2)
		{
			//enable only if zone is active
			tr = $(this).closest('tr');
			validate = $form.find('tr.fees_all td:eq('+$(this).index()+')').hasClass('validated');
			if ($(tr).index() > 2 && $(tr).find('td:eq(1) input').attr('checked') && validate || !$(tr).hasClass('range_sup') || !$(tr).hasClass('range_inf'))
				$(this).find('div.input-group input:text').removeAttr('disabled');
			$(this).find('input:text, button').css('background-color', '').css('border-color', '');
			$(this).find('button').css('background-color', '').css('border-color', '').removeAttr('disabled');
			$(this).css('background-color', '');
		}
	});
}

function validateRange(index, $form)
{
	$('#carrier_wizard .actionBar a.btn').removeClass('disabled');
	$('.wizard_error').remove();
	//reset error css
	$form.find('tr.range_sup td input:text').closest('div.input-group').removeClass('has-error');
	$form.find('tr.range_inf td input:text').closest('div.input-group').removeClass('has-error');
	
	var is_valid = true;
	range_sup = parseFloat($form.find('tr.range_sup td:eq('+index+')').find('div.input-group input:text').val().trim());
	range_inf = parseFloat($form.find('tr.range_inf td:eq('+index+')').find('div.input-group input:text').val().trim());

	if (isNaN(range_sup) || range_sup.length === 0)
	{
		$form.find('tr.range_sup td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		is_valid = false;
		displayError([invalid_range], $("#carrier_wizard").smartWizard('currentStep'));
	}
	else if (is_valid && (isNaN(range_inf) || range_inf.length === 0))
	{
		$form.find('tr.range_inf td:eq('+index+')').closest('div.input-group input:text').closest('div.input-group').addClass('has-error');
		is_valid = false;
		displayError([invalid_range], $("#carrier_wizard").smartWizard('currentStep'));
	}
	else if (is_valid && range_inf >= range_sup)
	{
		$form.find('tr.range_sup td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		$form.find('tr.range_inf td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		is_valid = false;
		displayError([invalid_range], $("#carrier_wizard").smartWizard('currentStep'));
	}
	else if (is_valid && index > 2) //check range only if it's not the first range
	{	
		$form.find('tr.range_sup td').not('.range_type, .range_sign, tr.range_sup td:last').each(function () 
		{
			if ($form.find('tr.fees_all td:eq('+index+')').hasClass('validated'))
			{
				is_valid = false;
				curent_index = $(this).index();
	
				current_sup = $(this).find('div.input-group input').val();
				current_inf = $form.find('tr.range_inf td:eq('+curent_index+') input').val();
				
				if ($form.find('tr.range_inf td:eq('+curent_index+1+') input').length)
					next_inf = $form.find('tr.range_inf td:eq('+curent_index+1+') input').val();
				else
					next_inf = false;
				
				//check if range already exist
				//check if ranges is overlapping
				if ((range_sup != current_sup && range_inf != current_inf) && ((range_sup > current_sup || range_sup <= current_inf) && (range_inf < current_inf || range_inf >= current_sup)))
					is_valid = true;
			}
			
		});
		
		if (!is_valid)
		{
			$form.find('tr.range_sup td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
			$form.find('tr.range_inf td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
			displayError([range_is_overlapping], $("#carrier_wizard").smartWizard('currentStep'));
		}
		else
			isOverlapping();
	}
	return is_valid;
}

function enableZone(index, $form)
{
	$form.find('tr.fees').each(function () {
		if ($(this).find('td:eq(1)').find('input[type=checkbox]:checked').length)	
			$(this).find('td:eq('+index+')').find('div.input-group input').removeAttr('disabled');
	});
}

function disableZone(index, $form)
{
	$form.find('tr.fees').each(function () {
		$(this).find('td:eq('+index+')').find('div.input-group input').attr('disabled', 'disabled');
	});
}

function enableRange(index, $form)
{
	$form.find('tr.fees').each(function () {
		// if ($(this).find('td').find('input:checkbox').attr('checked') == 'checked')
			enableZone(index, $form);
	});
	var td = $form.find('tr.fees_all td:eq('+index+')');
	td.addClass('validated').removeClass('not_validated');
	
	//if ($('.zone input[type=checkbox]:checked').length)
		enableGlobalFees(index, $form);
	bind_inputs();
}

function enableGlobalFees(index, $form)
{
	$form.find('span.fees_all').show();
	$form.find('tr.fees_all td:eq('+index+')').find('div.input-group input').show().removeAttr('disabled');
	$form.find('tr.fees_all td:eq('+index+')').find('div.input-group .currency_sign').show();	
}

function disabledGlobalFees(index, $form)
{
	$form.find('span.fees_all').hide();
	$form.find('tr.fees_all td:eq('+index+')').find('div.input-group input').hide().attr('disabled', 'disabled');
	$form.find('tr.fees_all td:eq('+index+')').find('div.input-group .currency_sign').hide();	
}


function disableRange(index, $form)
{
	$form.find('tr.fees').each(function () {
		//only enable fees for enabled zones
		if ($(this).find('td').find('input:checkbox').attr('checked') == 'checked')
			disableZone(index);
	});
	$form.find('tr.fees_all td:eq('+index+')').find('div.input-group input').attr('disabled', 'disabled');
	$form.find('tr.fees_all td:eq('+index+')').removeClass('validated').addClass('not_validated');
}

function add_new_range(ele)
{
	var $form = $(ele).closest("*[id='step_carrier_ranges']");
	if (!$form.find('tr.fees_all td:last').hasClass('validated'))
	{
		alert(need_to_validate);
		return false;
	}
	
	last_sup_val = $form.find('tr.range_sup td:last input').val();
	//add new rand sup input
	$form.find('tr.range_sup td:last').after('<td class="range_data"><div class="input-group fixed-width-md"><span class="input-group-addon weight_unit" style="display: none;">'+PS_WEIGHT_UNIT+'</span><span class="input-group-addon price_unit" style="display: none;">'+currency_sign+'</span><span class="input-group-addon price_quantity">'+item_unit+'</span><input class="form-control" name="range_sup[]" type="text" /></div></td>');
	//add new rand inf input
	$form.find('tr.range_inf td:last').after('<td class="border_bottom"><div class="input-group fixed-width-md"><span class="input-group-addon weight_unit" style="display: none;">'+PS_WEIGHT_UNIT+'</span><span class="input-group-addon price_unit" style="display: none;">'+currency_sign+'</span><span class="input-group-addon price_quantity">'+item_unit+'</span><input class="form-control" name="range_inf[]" type="text" value="'+last_sup_val+'" /></div></td>');
	$form.find('tr.fees_all td:last').after('<td class="border_top border_bottom"><div class="input-group fixed-width-md"><span class="input-group-addon currency_sign" style="display:none" >'+currency_sign+'</span><input class="form-control" style="display:none" type="text" /></div></td>');

	$form.find('tr.fees').each(function () {
		$(this).find('td:last').after('<td><div class="input-group fixed-width-md"><span class="input-group-addon currency_sign">'+currency_sign+'</span><input class="form-control" disabled="disabled" name="fees['+$(this).data('zoneid')+'][]" type="text" /></div></td>');
	});
	// $form.find('tr.delete_range td:last').after('<td><button class="btn btn-default">'+labelDelete+'</button</td>');
	
	bind_inputs();
	rebuildTabindex();
	displayRangeType();
	resizeWizard();
	return false;
}

function delete_new_range()
{
	if ($('#new_range_form_placeholder').find('td').length = 1)
		return false;
}

function checkAllFieldIsNumeric()
{
	$('#carrier_wizard .actionBar a.btn').removeClass('disabled');
	$('#zones_table td input[type=text]').each(function () {
		if (!$.isNumeric($(this).val()) && $(this).val() != '')
			$(this).closest('div.input-group').addClass('has-error');
	});
}

function rebuildTabindex()
{
	$("*[id='step_carrier_ranges']").each(function(){		
		var i = 1, j;
		$(this).find('#zones_table tr').each(function () 
		{	
			j = i;
			$(this).find('td').each(function () 
			{
				j = 4 + j;
				if ($(this).index() >= 2 && $(this).find('div.input-group input'))
					$(this).find('div.input-group input').attr('tabindex', j);
			});
			i++;
		});
	});
}

function repositionRange(current_index, new_index)
{
	$('tr.range_sup, tr.range_inf, tr.fees_all, tr.fees, tr.delete_range ').each(function () {
		$(this).find('td:eq('+current_index+')').each(function () {
			$(this).closest('tr').find('td:eq('+new_index+')').after(this.outerHTML);
			$(this).remove();
		});
	});
}

function checkRangeContinuity(reordering)
{
	debugger;
	reordering = typeof reordering !== 'undefined' ? reordering : false;
	res = true;

	$('tr.range_sup td').not('.range_type, .range_sign').each(function () 
	{
		index = $(this).index();
		if (index > 2)
		{
			range_sup = parseFloat($('tr.range_sup td:eq('+index+')').find('div.input-group input:text').val().trim());
			range_inf = parseFloat($('tr.range_inf td:eq('+index+')').find('div.input-group input:text').val().trim());
			prev_index = index-1;
			prev_range_sup = parseFloat($('tr.range_sup td:eq('+prev_index+')').find('div.input-group input:text').val().trim());
			prev_range_inf = parseFloat($('tr.range_inf td:eq('+prev_index+')').find('div.input-group input:text').val().trim());
			
			if (range_inf < prev_range_inf || range_sup < prev_range_sup)
			{
				res = false;
				if (reordering)
				{
					new_position = getCorrectRangePosistion(range_inf, range_sup);
					if (new_position)
						repositionRange(index, new_position);
				}
			}	
		}
	});
	if (res)
		$('.ranges_not_follow').fadeOut();
	else
		$('.ranges_not_follow').fadeIn();
	resizeWizard();
}

function getCorrectRangePosistion(current_inf, current_sup)
{
	new_position = false;
	$('tr.range_sup td').not('.range_type, .range_sign').each(function () 
	{
		index = $(this).index();
		range_sup = parseFloat($('tr.range_sup td:eq('+index+')').find('div.input-group input:text').val().trim());
		next_range_inf = 0
		if ($('tr.range_inf td:eq('+index+1+')').length)
			next_range_inf = parseFloat($('tr.range_inf td:eq('+index+1+')').find('div.input-group input:text').val().trim());
		if (current_inf >= range_sup && current_sup < next_range_inf)
			new_position = index;
	});
	return new_position;
}

function isOverlapping()
{
	var is_valid = false;
	$('#carrier_wizard .actionBar a.btn').removeClass('disabled');
	$("*[id='step_carrier_ranges']").each(function(){
		var $form = $(this);
		$form.find('tr.range_sup td').not('.range_type, .range_sign').each( function ()
		{
			index = $(this).index();
			current_inf = parseFloat($form.find('.range_inf td:eq('+index+') input').val());
			current_sup = parseFloat($form.find('.range_sup td:eq('+index+') input').val());

			$form.find('tr.range_sup td').not('.range_type, .range_sign').each( function ()
			{
				testing_index = $(this).index();
				
				if (testing_index != index) //do not test himself
				{
					testing_inf = parseFloat($form.find('.range_inf td:eq('+testing_index+') input').val());
					testing_sup = parseFloat($form.find('.range_sup td:eq('+testing_index+') input').val());

					if ((current_inf >= testing_inf && current_inf < testing_sup) || (current_sup > testing_inf && current_sup < testing_sup))
					{
						$form.find('tr.range_sup td:eq('+testing_index+') div.input-group, tr.range_inf td:eq('+testing_index+') div.input-group').addClass('has-error');
						displayError([overlapping_range], $("#carrier_wizard").smartWizard('currentStep'));
						is_valid = true;
					}
				}
			});
		});
	});
	return is_valid;
}

function checkAllZones(elt)
{
	debugger;
	if($(elt).is(':checked'))
	{
		$('.input_zone').attr('checked', 'checked');
		$('.fees div.input-group input:text').each(function () {
			index = $(this).closest('td').index();
			enableGlobalFees(index);
			if ($('tr.fees_all td:eq('+index+')').hasClass('validated'))
			{
				$(this).removeAttr('disabled');
				$('.fees_all td:eq('+index+') div.input-group input:text').removeAttr('disabled');
			}
		});
	}
	else
	{
		$('.input_zone').removeAttr('checked');
		$('.fees div.input-group input:text, .fees_all div.input-group input:text').attr('disabled', 'disabled');
	}
	
}


function add_new_rule(ele) {
	$.ajax({
		type:"POST",
		url : validate_url,
		async: false,
		data : 'action=new_rule&ajax=1',
		success : function(data) {
			$('#rule-content').append(data);
			bind_inputs();
			initRange();
			resizeWizard();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			jAlert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
		}
	});


	return false;
}