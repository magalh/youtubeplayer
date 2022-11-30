<h2>{$browsetitle}</h2>
{if $postmaxsize}<p align="right">{$postmaxsize}</p>{/if}
{$formstart}

{if $addfileinput}
<fieldset style="float: right; width: 30%; margin: 15px; padding: 5px;"><legend><b>{$ziptitle}</b></legend>
<p>{$zipnotice}</p>
{if $resizenotice}<p>{$resizenotice}</p>{/if}
<p>{$zipinput} {$zipsubmit}</p>
</fieldset>
{/if}
<fieldset style="margin: 15px; padding: 5px;"><legend><b>{$uploadtitle}</b></legend>
{if $resizenotice}<p>{$resizenotice}</p>{/if}
{$fileinput}
{if $addfileinput}<p>{$addfileinput}</p>{/if}
<p>{$submit}</p>
</fieldset>

{$formend}

<br/>
{if $instantsearch}<div style="float: right;">{$instantsearch}</div>{/if}
<p>{$showingdir}</p>
<div style="clear: right;">
<table id="filelist_table" cellspacing="0" class="pagetable">
<thead><tr>
	<th>{$sortlinks[0]}{$headers->filename}</th>
	<th>{$numeric_sortlinks[1]}{$headers->imagesize}</th>
	<th>{$sortlinks[2]}{$headers->ext}</th>
	<th>{$numeric_sortlinks[3]}{$headers->size}</th>
	<th>{$numeric_sortlinks[4]}{$headers->modified}</th>
	<th width="128">{$headers->deletelink}</th>
</tr></thead>
<tbody>
{cycle values="row2,row1" assign=rowclass reset=true}
{foreach from=$itemlist item=oneitem}
{cycle values="row2,row1" assign=rowclass}
<tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
	<td>{$oneitem->pic} {$oneitem->thelink}</td>
	<td class="ctlmm_nosearch">{$oneitem->imagesize}</td>
	<td class="ctlmm_nosearch">{$oneitem->ext}</td>
	<td class="ctlmm_nosearch">{$oneitem->size}</td>
	<td class="ctlmm_nosearch">{$oneitem->modified}</td>
	<td class="ctlmm_nosearch">{$oneitem->deletelink}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
