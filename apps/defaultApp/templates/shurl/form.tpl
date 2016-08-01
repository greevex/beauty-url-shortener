<style>
    .mid-container {
        width: 50%;
        min-width: 400px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
        position: relative;
    }
    .search-group {
        height: 48px;
        font-size: 18px;
    }
    .search-button-text {
        padding: 0 auto;
        margin: 0 auto;
    }
    .full-width: {
        width: 100%;
    }
</style>
<div class="mid-container">
    <h1>Link shortener</h1>
    <form role="form" action="{$_application->getUrl("/defaultModule/shlink/shorten")}" method="post">
        <div class="form-group input-group">
            <input name="url" type="text" class="search-group form-control">
            <span class="input-group-btn">
                <button class="search-group search-button-text btn btn-default" type="submit">
                    <i class="fa fa-link"></i> SHORTEN
                </button>
            </span>
        </div>
    </form>
</div>
