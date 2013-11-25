<h1>Admin-Panel</h1>
<!-- neuen User in die DB hinzufügen -->
{include "admin/insertUser.tpl"}

<!-- Fake submitts von Playern simulieren -->
{include "admin/fakeSubmits.tpl"}

<!-- Cronjobs starten -->
{*{include "admin/cronjobs.tpl"}*}

<!-- fakeMatches -->
{*{include "admin/fakeMatches.tpl"}*}