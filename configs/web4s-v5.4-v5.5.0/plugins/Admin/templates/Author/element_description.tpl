<div class="form-group">
    <label>
        {__d('admin', 'mo_ta_ngan')}
    </label>
    <div class="clearfix">
        <textarea name="description" id="description" class="mce-editor-simple">{if !empty($author.description)}{$author.description}{/if}</textarea>
    </div>
</div>

<div class="form-group">
    <label>
        {__d('admin', 'gioi_thieu')}
    </label>
    <div class="clearfix">
        <textarea name="content" id="content" class="mce-editor">{if !empty($author.content)}{$author.content}{/if}</textarea>
    </div>
</div>


         


