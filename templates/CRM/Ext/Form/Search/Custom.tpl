
<div class="crm-form-block crm-search-form-block">
  <div class="crm-accordion-wrapper crm-member_search_form-accordion {if $rows}collapsed{/if}">
   <div class="crm-accordion-header crm-master-accordion-header">
      {ts}Edit Search Criteria{/ts}
    </div><!-- /.crm-accordion-header -->
  <div class="crm-accordion-body">
  {strip}
       <table class="form-layout">
          <tr>
              
          </tr>

<tr><td><label>{ts}Start Date{/ts}</label></td></tr>
<tr>
{include file="CRM/Core/DateRange.tpl" fieldName="member_start_date" from='_low' to='_high'}
</tr>

<tr><td><label>{ts}End Date{/ts}</label></td></tr>
<tr>
{include file="CRM/Core/DateRange.tpl" fieldName="member_end_date" from='_low' to='_high'}
</tr>

          <tr>
              <td colspan="2"><input id="btnmemebersearch" type="submit" class="crm-button" value="Search"/> </td>
          </tr>
      </table>
  {/strip}
   </div><!-- /.crm-accordion-body -->
  </div><!-- /.crm-accordion-wrapper -->
</div><!-- /.crm-form-block -->
<div class="crm-content-block">
  {if $rowsEmpty}
    <div class="crm-results-block crm-results-block-empty">
      {include file="CRM/Member/Form/Search/EmptyResults.tpl"}
      </div>
  {/if}

  {if $rows}
  <div class="crm-results-block">
      {* Search request has returned 1 or more matching rows. *}

         {* This section handles form elements for action task select and submit *}
             <div class="crm-search-tasks">
             {include file="CRM/common/searchResultTasks.tpl"}
             </div>

         {* This section displays the rows along and includes the paging controls *}
             <div id ="memberSearch" class="crm-search-results">
             {include file="CRM/Ext/Form/Search/Selector.tpl" context="Search"}
             </div>
      {* END Actions/Results section *}
  </div>
  {/if}
</div>
