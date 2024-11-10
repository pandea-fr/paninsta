<form action="{$form_action}" method="post" class="form">
    <div class="form-group">
        <label for="{$config_name_token}">Instagram Access Token</label>
        <input type="text" name="{$config_name_token}" value="{$paninsta_token|escape:'html':'UTF-8'}" size="50" class="form-control"/>
    </div>
    <div class="form-group">
    <label for="{$config_name_photo_only}">Afficher seulement les photos</label>
    <select name="{$config_name_photo_only}" class="form-control">
        <option value="1" {if $paninsta_photo_only == "1"} selected {/if}>Oui</option>
        <option value="0" {if $paninsta_photo_only == '0'} selected {/if} >Non</option>
    </select>
    </div>
    <div class="form-group">
        <label for="{$config_name_photo_max}">Nombre de photo à afficher</label>
        <input type="text" name="{$config_name_photo_max}" value="{$paninsta_photo_max|escape:'html':'UTF-8'}" size="50"/>
    </div>
    <div class="form-group">
        <button type="submit" name="submitPaninstaGallery" class="btn btn-primary">Enregistrer</button>
    </div>
    {if $confirmation == 'ok'}
        <div class="form-group">
            <p>Configuration mise à jour.</p>
        </div>
    {/if}
</form>
