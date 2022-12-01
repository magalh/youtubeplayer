<h2>{$leveltitle}</h2>
<ul class="ctl_searchresults">
{foreach from=$itemlist item="item"}
<li>{$item->detaillink}</li>
{/foreach}
</ul>
{if $pagemenu}<p>{$pagemenu}</p>{/if}
