{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="delete" size="small"}
    <h3>{$blockname|safetext}</h3>
</div>

<p class="z-warningmsg">{gt text="Do you really want to delete block '%s'?" tag1=$block.title|safetext}</p>

<form class="z-form" action="{modurl modname="Blocks" type="admin" func="delete"}" method="post" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" name="confirmation" value="1" />
        <input type="hidden" name="bid" value="{$block.bid|safetext}" />
        <fieldset>
            <legend>{gt text="Confirmation prompt"}</legend>
            <div class="z-buttons z-formbuttons">
                {button class="z-btgreen" src=button_ok.png set=icons/extrasmall __alt="Delete" __title="Delete" __text="Delete"}
                <a class="z-btred" href="{modurl modname=Blocks type=admin func=view}" title="{gt text="Cancel"}">{img modname=core src=button_cancel.png set=icons/extrasmall __alt="Cancel" __title="Cancel"} {gt text="Cancel"}</a>
            </div>
        </fieldset>
    </div>
</form>
{adminfooter}