<h2>{$leveltitle}</h2>
<ul>
{foreach from=$itemlist item="item"}
<li {if $item->is_selected}class="active"{/if}>{$item->detaillink}
<br />
<a href="{$item->detailurl}">
<img src="http://img.youtube.com/vi/{$item->videoid}/1.jpg" width="130" height="97" border="0" alt="{$item->name}" title="{$item->name}"> 
</a>
<br /><br />
</li>
{/foreach}
</ul>
{if $page_pagenumbers}
<div id="pagemenu" style="text-align: center;">
{$page_previous}&nbsp; {$page_showing}/{$page_totalitems} &nbsp;{$page_next}<br/>
{$page_pagenumbers}
</div>
{/if}