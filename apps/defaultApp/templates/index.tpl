{include file="index/header.tpl"}

<div id="wrapper">
    {if $_acl->getCurrentUser()->isAdmin()}
        {include file="index/navbar.tpl"}
    {/if}
    <div id="page-wrapper">
        {if isset($_template) && !empty($_template)}
            {include file=$_template}
        {else}
            {include file="shurl/home.tpl"}
        {/if}
    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->

<!--
{$_profiler::getStackAsString()}
-->
{include file="index/footer.tpl"}