{assign var = is_slider value = false}
{if !empty($data_extend['slider'])}
    {assign var = is_slider value = true}
{/if}

{assign var = element value = "item"}
{if !empty($data_extend['element'])}
    {assign var = element value = {$data_extend['element']}}
{/if}

{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}

{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}

{strip}

{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <h3 class="title-section title-author mb-5 text-left">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </h3>
{/if}

{if !empty($data_block.data)}
<div class="author-view-detail author-view-detail-list">
    <div class="author">
        <div class="img-author">
            {if !empty($data_block.data.avatar)}
                {assign var = image_user value = "{CDN_URL}{$this->Utilities->getThumbs($data_block.data.avatar, 150)}"}
            {else}
                {assign var = image_user value = "{CDN_URL}/media/icon/user.webp"}
            {/if}
            <img src="{$image_user}" alt="{if !empty($data_block.data.full_name)}{$data_block.data.full_name}{/if}">
        </div>
        <div class="inner-user">
            {if !empty($data_block.data.full_name)}
                <div class="created-by-user font-weight-bold mb-2">
                    <span class="mr-2">
                        {__d('template', 'tac_gia')}:
                    </span>
                    {$data_block.data.full_name}
                </div>
            {/if}
            {if !empty($data_block.data.job_title)}
                <div class="job_title font-weight-bold mb-4">
                    <span class="mr-2">
                        {__d('template', 'chuc_vu')}:
                    </span>
                    {$data_block.data.job_title}
                </div>
            {/if}
            {if !empty($data_block.data.description)}
                <div class="dsc">
                    {$data_block.data.description}
                </div>
            {/if}
            
        </div>

    </div>
</div>
{/if}
{/strip}