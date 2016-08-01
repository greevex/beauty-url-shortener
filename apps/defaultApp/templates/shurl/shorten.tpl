{include file="shurl/form.tpl"}

{if isset($data['error'])}
    <div class="alert alert-danger">
        {$data['error']}
    </div>
{elseif !$data['status']}
    <div class="alert alert-danger">
        {$data|json_encode:448}
    </div>
{else}
    <script type="application/javascript">
        function copyToClipboard(text) {
            window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
        }
    </script>
    <div class="mid-container">
        <h1>Result</h1>
        <div class="form-group input-group">
            <span class="input-group-addon"><i class="fa fa-link"></i>
            </span>
            <input readonly="readonly" type="text" class="search-group form-control" value="{$data['url']|htmlspecialchars}">
        </div>
        <div class="form-group input-group">
            <span class="input-group-addon"><i class="fa fa-link"></i>
            </span>
            <input readonly="readonly" type="text" class="search-group form-control" value="{$data['shlink']->getShortUrl()|htmlspecialchars}">
            <span class="input-group-btn">
                <button class="search-group search-button-text btn btn-default" type="button" onclick="copyToClipboard('{$data['shlink']->getShortUrl()|htmlspecialchars}')">
                    <i class="fa fa-link"></i> COPY
                </button>
            </span>
        </div>
    </div>
{/if}