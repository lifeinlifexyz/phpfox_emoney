<?php

?>
<div style="display: table; width: 100%">
    <div class="cm-em-table-row cm-em-table-head">
        <div class="cm-em-table-cell">{_p('Title')}</div>
        <div class="cm-em-table-cell">{_p('Link')}</div>
        <div class="cm-em-table-cell">{_p('Percentage')}</div>
    </div>

    {foreach from=$aItems item=aItem}
        <div class="cm-em-table-row">
            <div class="cm-em-table-cell">{$aItem.title}</div>
            <div class="cm-em-table-cell">
                <input onclick="this.select();" type="text" readonly="readonly" value="{url link=$aItem.url elmoney_affiliate_code=$aItem.code}"/>
            </div>
            <div class="cm-em-table-cell">{$aItem.percent}%</div>
        </div>
    {/foreach}

</div>

