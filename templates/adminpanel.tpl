{if $filter || $instantsearch}<div style="float: right; text-align: right;">{$filter}{if $filter && $instantsearch}<br/>{/if}{$instantsearch}</div>{/if}

{cms_action_url action=editB assign='edit_url'}
{cms_action_url action=admin_deletecomment assign='delete_url'}

<p>{$addnew} {$reorder}</p>
{if !empty($itemlist)}
<table class="pagetable cms_sortable tablesorter">
 <thead>
 <tr>
	{foreach from=$adminshow item=column}
		<th>{$column[0]}</th>
	{/foreach}
 </tr>
 </thead>
 <tbody>
    {foreach from=$itemlist item=oneitem}
	<tr>
	{foreach from=$adminshow item=column}
		{assign var=oneval value=$column[1]}
		<td{if $column[2]} class="ctlmm_nosearch"{/if}>{$oneitem->$oneval}</td>
	{/foreach}
	</tr>
{/foreach}
  </tbody>
</table>
<p>{$addnew}</p>{/if}
