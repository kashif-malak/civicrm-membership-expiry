<?php

/**
 * A custom Membership search
 */
class CRM_Ext_Form_Search_extSearch extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface{
   
  protected $_query;
  protected $_aclFrom = NULL;
  protected $_aclWhere = NULL;

  function __construct(&$formValues) {
        parent::__construct($formValues);
    
        $this->_queryParams = CRM_Contact_BAO_Query::convertFormValues($this->_formValues);
        $this->_query = new CRM_Contact_BAO_Query($this->_queryParams,
        CRM_Member_BAO_Query::defaultReturnProperties(CRM_Contact_BAO_Query::MODE_MEMBER,
        FALSE), NULL, FALSE, FALSE,CRM_Contact_BAO_Query::MODE_MEMBER
        );
    
    }
 
    /**
   * Prepare a set of search fields
   *
   * @param  CRM_Core_Form $form modifiable
   * @return void
   */
    function buildForm(&$form){    
        CRM_Utils_System::setTitle(ts('Membership Search - php code'));
     
        CRM_Core_Form_Date::buildDateRange($form, 'member_start_date', 1, '_low', '_high', ts('From'), false);
        $form->addElement('hidden', 'member_start_date_range_error');

        CRM_Core_Form_Date::buildDateRange($form, 'member_end_date', 1, '_low', '_high', ts('From'), false);
        $form->addElement('hidden', 'member_end_date_range_error');
     }

    /**
   * Get a list of summary data points
   *
   * @return mixed; NULL or array with keys:
   *  - summary: string
   *  - total: numeric
   */
  
    public function count(){
        $sql = $this->all();

        $dao = CRM_Core_DAO::executeQuery(
            $sql,
            CRM_Core_DAO::$_nullArray
        );
        return $dao->N;
    }
    function summary(){
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
    function &columns(){
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
    function all($offset = 0, $rowcount = 0, $sort = null, $includeContactIDs = false, $justIDs = false){      
         return $this->sql($this->select(), $offset, $rowcount, $sort, $includeContactIDs, null);  
    }

    /**
   * Construct a SQL SELECT clause
   *
   * @return string, sql fragment with SELECT arguments
   */
    function select(){
        return "
            membership_type_id,
            civicrm_membership.id as 'membership_id',
            contact_a.id as contact_id,
            civicrm_membership.start_date,
            civicrm_membership.end_date,
            civicrm_membership_type.description,
            contact_a.display_name
    ";
    }

    /**
   * Construct a SQL FROM clause
   *
   * @return string, sql fragment with FROM and JOIN clauses
   */
    function from(){
     $this->buildACLClause('contact_a');
     $from = $this->_query->_fromClause;
     $from .= "{$this->_aclFrom}";    
     return $from;
    }

    /**
   * Construct a SQL WHERE clause
   *
   * @param  bool $includeContactIDs
   * @return string, sql fragment with conditional expressions
   */
    function where($includeContactIDs = false){
      if ($this->_query->_whereClause) {
          $whereClause = $this->_query->_whereClause;
      if ($this->_aclWhere) {
          $whereClause .= " AND {$this->_aclWhere}";
      }
      return $whereClause;
    }
    return ' (1) ';  
    }

    /**
   * Determine the Smarty template for the search screen
   *
   * @return string, template path (findable through Smarty template path)
   */
    function templateFile(){
        return 'CRM/Ext/Form/Search/Custom.tpl';
    }

    /**
   * Modify the content of each row
   *
   * @param  array $row modifiable SQL result row
   * @return void
   */
    function alterRow(&$row){
    
    }
    
  /**
   * @param string $tableAlias
   */
  public function buildACLClause($tableAlias = 'contact') {
    list($this->_aclFrom, $this->_aclWhere) = CRM_Contact_BAO_Contact_Permission::cacheClause($tableAlias);
  }
}
