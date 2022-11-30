{if $filter || $instantsearch}<div style="float: right; text-align: right;">{$filter}{if $filter && $instantsearch}<br/>{/if}{$instantsearch}</div>{/if}
<p>{$addnew} {$reorder}</p>
{if count($itemlist) > 0}
<div style="clear: right;">
<table {if $tableid}id="{$tableid}" {/if}cellspacing="0" class="pagetable">
<thead><tr>
	{foreach from=$adminshow item=column}
		<th>{$column[0]}</th>
	{/foreach}
</tr></thead>
<tbody>
{cycle values="row2,row1" assign=rowclass reset=true}
{foreach from=$itemlist item=oneitem}
{cycle values="row2,row1" assign=rowclass}
<tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
	{foreach from=$adminshow item=column}
		{assign var=oneval value=$column[1]}
		<td{if $column[2]} class="ctlmm_nosearch"{/if}>{$oneitem->$oneval}</td>
	{/foreach}
</tr>
{/foreach}
</tbody>
</table>
</div>
<p>{$addnew}</p>{/if}
