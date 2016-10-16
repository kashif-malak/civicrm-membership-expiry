
{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="top"}
{/if}

{strip}
<table class="selector row-highlight">
<thead class="sticky">
{if ! $single and $context eq 'Search' }
  <th scope="col" title="Select Rows">{$form.toggleSelect.html}</th>
{/if}
 
<th></>
<th></>
<th>Name</>
<th>Start Date</>
<th>End Date</>
<th>Description</>
</tr>
  </thead>

  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
  <tr id='rowid{$row.membership_id}' class="{cycle values="odd-row,even-row"} {*if $row.cancel_date} disabled{/if*} crm-membership_{$row.membership_id}">
     {if ! $single }
       {if $context eq 'Search' }
          {assign var=cbName value=$row.checkbox}
          <td>{$form.$cbName.html}</td>
       {/if}
       <td>{$row.contact_type}</td>
       <td>
            <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="{ts}View contact record{/ts}">{$row.sort_name}</a>
        </td>
    {/if}
   
     <td class="crm-membership-source">{$row.display_name}</td>
    <td class="crm-membership-start_date">{$row.start_date|truncate:10:''|crmDate}</td>
    <td class="crm-membership-end_date">{$row.end_date|truncate:10:''|crmDate}</td>
   
    <td class="crm-membership-status crm-membership-status">{$row.description}</td>
   
    <td>
        {$row.action|replace:'xx':$row.membership_id}
        
    </td>
   </tr>
  {/foreach}
{* Link to "View all memberships" for Contact Summary selector display *}
{if ($context EQ 'membership') AND $pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&force=1&selectedChild=member&cid=$contactId"}">&raquo; {ts}View all memberships for this contact{/ts}...</a></td></tr>
  </tr>
{/if}
{if ($context EQ 'dashboard') AND $pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="9"><a href="{crmURL p='civicrm/member/search' q='reset=1'}">&raquo; {ts}Find more members{/ts}...</a></td></tr>
  </tr>
{/if}
</table>
{/strip}



{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="bottom"}
{/if}
