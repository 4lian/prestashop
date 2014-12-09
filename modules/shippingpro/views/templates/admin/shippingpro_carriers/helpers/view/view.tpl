<div class="panel">
	<h3>{l s="Add a new carrier"}</h3>
	<div id="" class="row">

		<div class="alert alert-info">
			<p>{l s='Your online store needs to have a proper carrier registered in PrestaShop as soon as you start shipping your products. This means sending yours parcels using your local postal service, or having a contract with a private carrier which in turn will ship your parcels to your customers. In order to have PrestaShop suggest the most adequate carrier to your customers during their order checkout process, you need to register all the carriers with which you have chosen to work.'}</p>
			<p>{l s='PrestaShop comes with a number of carrier modules that you can activate. You can also buy carrier modules on the PrestaShop Addons marketplace. Recommended modules are listed below: install the module that matches your carrier, and configure it!'}</p>
			<p>{l s='If there is no existing module for your carrier, then you can register that carrier by hand using the information that it can provide you: shipping rates, regional zones, size and weight limits, etc. Click on the "Add new carrier" button below to open the Carrier Wizard, which will help you register a new carrier in a few steps.'}</p>
			<p>{l s='Note: DO NOT register a new carrier if there already exists a module for it! Using a module will be much faster and more accurate!'}</p>
		</div>
	
		<div class="col-lg-12">
			<form action="" id="configuration_form" method="post" enctype="multipart/form-data" class="form-horizontal">
				<div class="form-group">
					<label class="control-label col-lg-3">{l s="Create a custom carrier"}</label>
					<a class="btn btn-default" href="{$link->getAdminLink('AdminShippingproCarrierWizard')|escape:'html':'UTF-8'}">{l s="Add new carrier"}</a>
					<div class="col-lg-9 col-lg-offset-3">
						<p class="help-block">{l s="Use this tool to allow PrestaShop to know how an unknown carrier works. You can configure its shipping rates, its regional zones, its size and weight limits, etc."}</p>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
{if is_null($modules_list)}
<div class="panel">
	<h3>{l s="Use one of our recommended carrier modules"}</h3>
	<p>{l s="It seems there are no recommended carriers for your country."}</p>
	<p><a href="http://www.prestashop.com/en/contribute-prestashop-localization">{l s="Do you think there should be one? Let us know!"}</a></p>
</div>
{else}
	{$modules_list}
{/if}



