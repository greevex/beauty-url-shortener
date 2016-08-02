{include file="shurl/form.tpl"}

<div class="mid-container">
    {if isset($data['error'])}
        <div class="alert alert-danger">
            {$data['error']}
        </div>
    {elseif isset($status) && !$status}
        <div class="alert alert-danger">
            {$data|json_encode:448}
        </div>
    {else}
    <a href="{$data['url']}">moved here</a>

    {/if}
</div>