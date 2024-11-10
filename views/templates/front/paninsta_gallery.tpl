{if $paninsta_photos|@count > 0}
    <div class="paninsta-gallery">
        {foreach from=$paninsta_photos item=photo}
            <div class="paninsta-photo">
                <a href="{$photo.permalink}" target="_blank">
                    <img src="{$photo.media_url}" alt="{$photo.caption|escape:'html':'UTF-8'}"/>
                </a>
            </div>
        {/foreach}
    </div>
{/if}