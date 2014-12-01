{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
<div class="panel">
	<h3>{l s='Products'} <span class="badge">{count($products)}</span></h3>

	{foreach $products AS $product}
		{if !$product->hasAttributes()}
			<div class="panel">
				<div class="panel-heading">
					{$product->name}
					<div class="pull-right">
						<a href="?tab=AdminProducts&amp;id_product={$product->id|intval}&amp;updateproduct&amp;token={getAdminToken tab='AdminProducts'}" class="btn btn-default btn-sm">
							<i class="icon-edit"></i> {l s='Edit'}
						</a>
						<a href="?tab=AdminProducts&amp;id_product={$product->id|intval}&amp;deleteproduct&amp;token={getAdminToken tab='AdminProducts'}" class="btn btn-default btn-sm" onclick="return confirm('{l s='Delete item #'}{$product->id} ?');">
							<i class="icon-trash"></i> {l s='Delete'}
						</a>
					</div>
				</div>

				<table class="table">
					<thead>
						<tr>
							{if !empty($product->reference)}<th><span class="title_box">{l s='Ref:'}</span> {$product->reference}</th>{/if}
							{if !empty($product->ean13)}<th><span class="title_box">{l s='EAN13:'}</span> {$product->ean13}</th>{/if}
							{if !empty($product->upc)}<th><span class="title_box">{l s='UPC:'}</span> {$product->upc}</th>{/if}
							{if $stock_management}<th><span class="title_box">{l s='Qty:'}</span> {$product->quantity}</th>{/if}
						</tr>
					</thead>
				</table>
			</div>
		{else}
			<div class="panel">
				<div class="panel-heading">

					<a href="?tab=AdminProducts&amp;id_product={$product->id|intval}&amp;updateproduct&amp;token={getAdminToken tab='AdminProducts'}">
						{$product->name}
					</a>
					<div class="pull-right">
						<a href="?tab=AdminProducts&amp;id_product={$product->id|intval}&amp;updateproduct&amp;token={getAdminToken tab='AdminProducts'}" class="btn btn-default btn-sm">
							<i class="icon-edit"></i>
							{l s='Edit'}
						</a>
						<a href="?tab=AdminProducts&amp;id_product={$product->id|intval}&amp;deleteproduct&amp;token={getAdminToken tab='AdminProducts'}" class="btn btn-default btn-sm" onclick="return confirm('{l s='Delete item #'}{$product->id} ?');">
							<i class="icon-trash"></i>
							{l s='Delete'}
						</a>
					</div>

				</div>

				<table class="table">
					<thead>
						<tr>
							<th><span class="title_box">{l s='Attribute name'}</span></th>
							<th><span class="title_box">{l s='Reference'}</span></th>
							<th><span class="title_box">{l s='EAN13'}</span></th>
							<th><span class="title_box">{l s='UPC'}</span></th>
							{if $stock_management && $shopContext != Shop::CONTEXT_ALL}
								<th><span class="title_box">{l s='Available Quantity'}</span></th>
							{/if}
						</tr>
					</thead>
					<tbody>
					{foreach $product->combination AS $id_product_attribute => $product_attribute}
						<tr {if $id_product_attribute %2}class="alt_row"{/if} >
							<td>{$product_attribute.attributes}</td>
							<td>{$product_attribute.reference}</td>
							<td>{$product_attribute.ean13}</td>
							<td>{$product_attribute.upc}</td>
							{if $stock_management && $shopContext != Shop::CONTEXT_ALL}
								<td class="right">{$product_attribute.quantity}</td>
							{/if}
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		{/if}
	{/foreach}
</div>
{/block}