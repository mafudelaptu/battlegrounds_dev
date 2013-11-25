<div class="page-header">
<h1>News Panel</h1>
</div>

{include "admin/newsPanel/createNewNews.tpl" order=($newsCount+1)}

{include "admin/newsPanel/news.tpl" news=$news}

{*Modals*}
{include "admin/newsPanel/modal/editNewsModal.tpl"}