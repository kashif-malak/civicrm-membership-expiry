<?php

/**
 * A custom Membership search
 */
class CRM_Ext_Form_Search_extSearch extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface
{
    function __construct(&$formValues) 
    {
        parent::__construct($formValues);
    }

    /**
   * Prepare a set of search fields
   *
   * @param  CRM_Core_Form $form modifiable
   * @return void
   */
    function buildForm(&$form) 
    {    
        CRM_Utils_System::setTitle(ts('Membership Search - php code'));
     
        CRM_Core_Form_Date::buildDateRange($form, 'member_start_date', 1, '_low', '_high', ts('From'), false);
        $form->addElement('hidden', 'member_start_date_range_error');

        CRM_Core_Form_Date::buildDateRange($form, 'member_end_date', 1, '_low', '_high', ts('From'), false);
        $form->addElement('hidden', 'member_end_date_range_error');

        $form->addFormRule(array('CRM_Member_BAO_Query', 'formRule'), $form);
       // $permission = CRM_Core_Permission::getPermission();

    //  $this->addTaskMenu(CRM_Member_Task::permissionedTaskTitles($permission));
    }

    /**
   * Get a list of summary data points
   *
   * @return mixed; NULL or array with keys:
   *  - summary: string
   *  - total: numeric
   */
  
    public function count() 
    {
        $sql = $this->all();

        $dao = CRM_Core_DAO::executeQuery(
            $sql,
            CRM_Core_DAO::$_nullArray
        );
        return $dao->N;
    }
    function summary() 
    {
        return null;
        // return array(
        //   'summary' => 'This is a summary',
        //   'total' => 50.0,
        // );
    }

    /**
   * Get a list of displayable columns
   *
   * @return array, keys are printable column headers and values are SQL column names
   */
    function &columns() 
    {
        // return by reference
        $columns = array(
        ts('Name') => 'display_name',
        ts('StartDate') => 'start_date',
        ts('EndDate') => 'end_date',
        ts('Description') => 'description',
        );
        return $columns;
    }

    /**
   * Construct a full SQL query which returns one page worth of results
   *
   * @param  int  $offset
   * @param  int  $rowcount
   * @param  null $sort
   * @param  bool $includeContactIDs
   * @param  bool $justIDs
   * @return string, sql
   */
    function all($offset = 0, $rowcount = 0, $sort = null, $includeContactIDs = false, $justIDs = false) 
    {
        // delegate to $this->sql(), $this->select(), $this->from(), $this->where(), etc.
   
     
        return $this->sql($this->select(), $offset, $rowcount, $sort, $includeContactIDs, null);

    }

    /**
   * Construct a SQL SELECT clause
   *
   * @return string, sql fragment with SELECT arguments
   */
    function select() 
    {
        return "
            civicrm_membership.membership_type_id,
            civicrm_membership.id as 'membership_id',
            civicrm_membership.contact_id,
            civicrm_membership.start_date,
            civicrm_membership.end_date,
            civicrm_membership_type.description,
            civicrm_contact.display_name
    ";
    }

    /**
   * Construct a SQL FROM clause
   *
   * @return string, sql fragment with FROM and JOIN clauses
   */
    function from() 
    {
        return "

            FROM civicrm_membership
            LEFT OUTER JOIN civicrm_membership_type
            ON civicrm_membership.membership_type_id = civicrm_membership_type.id
            LEFT OUTER JOIN civicrm_contact
            ON civicrm_membership.contact_id = civicrm_contact.id
           
            ";
    }
    function relativeToAbs($relative) 
    {
        if ($relative) {
            $split = CRM_Utils_System::explode('.', $relative, 3);
            $dateRange = CRM_Utils_Date::relativeToAbsolute($split[0],  $split[1]);
            $from = substr($dateRange['from'], 0, 8);
            $to = substr($dateRange['to'], 0, 8);
            return array($from, $to);
        }
        return null;
    }
    /**
   * Construct a SQL WHERE clause
   *
   * @param  bool $includeContactIDs
   * @return string, sql fragment with conditional expressions
   */
    function where($includeContactIDs = false) 
    {
    
        $where = "";

        $selectedDateStart = $this->_formValues['member_start_date_relative'];
        $selectedDateEnd   = $this->_formValues['member_end_date_relative'];
    
        if (empty($selectedDateStart) &&  empty($selectedDateEnd)) {
            CRM_Core_Error::statusBounce(
                ts("You must select a date."),
                CRM_Utils_System::url(
                    'civicrm/contact/search/custom',
                    "reset=1&csid="
                        . "{$this->_formValues['customSearchID']}",
                    false, null, false, true
                )
            );
        }
        if (!empty($selectedDateStart)) {
            $fixedStartDate    =  $this->relativeToAbs($selectedDateStart);
        };  
        if (!empty($selectedDateEnd)) {
             $fixedEndDate      =  $this->relativeToAbs($selectedDateEnd);
        };
        if (!empty($selectedDateStart) &&  !empty($selectedDateEnd)) { // if user select both start and end dates
            $where  .= " civicrm_membership.start_date  BETWEEN '{$fixedStartDate[0]}' AND '{$fixedStartDate[1]}'";
            $where  .= " AND civicrm_membership.end_date BETWEEN '{$fixedEndDate[0]}' AND '{$fixedEndDate[1]}'";
        }elseif (empty(!$selectedDateStart) &&  empty($selectedDateEnd)) { // if user does not select end date 
            $where  .= " civicrm_membership.start_date  BETWEEN '{$fixedStartDate[0]}' AND '{$fixedStartDate[1]}'";
        }elseif (empty($selectedDateStart) &&  empty(!$selectedDateEnd)) { // if user does not select Start date 
            $where  .= " civicrm_membership.end_date  BETWEEN '{$fixedEndDate[0]}' AND '{$fixedEndDate[1]}'";
        }

        return $where;
    }

    /**
   * Determine the Smarty template for the search screen
   *
   * @return string, template path (findable through Smarty template path)
   */
    function templateFile() 
    {
        return 'CRM/Ext/Form/Search/Custom.tpl';
    }

    /**
   * Modify the content of each row
   *
   * @param  array $row modifiable SQL result row
   * @return void
   */
    function alterRow(&$row) 
    {
    
    }


}
