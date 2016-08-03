{include file="shurl/form.tpl"}

{*<div class="mid-container">*}
    {*<div class="panel panel-default">*}
        {*<div class="panel-heading">*}
            {*<i class="fa fa-bookmark-o"></i> Last links*}
        {*</div>*}
        {*<!-- /.panel-heading -->*}
        {*<div class="panel-body">*}
            {*<div class="list-group">*}
                {*{foreach from=$data['shlinkMapper']->getAllBy([])->limit(10)->sort(["c_tm" => -1]) item="shlink"}*}
                    {*<a href="{$shlink['long']}" class="list-group-item">*}
                        {*<i class="fa fa-comment fa-fw"></i> <small>{$shlink['short']}</small> {$shlink['long']|htmlspecialchars|mb_substr:0:60}*}
                        {*<span class="pull-right text-muted small"><em>{"Y-m-d H:i:s"|date:$shlink['c_tm']}</em>*}
                                    {*</span>*}
                    {*</a>*}
                {*{/foreach}*}
            {*</div>*}
        {*</div>*}
        {*<!-- /.panel-body -->*}
    {*</div>*}
{*</div>*}