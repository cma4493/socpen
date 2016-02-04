<?php

// Global variable for table object
$Pensioner_Updates = NULL;

//
// Table class for Pensioner Updates
//
class cPensioner_Updates extends cTable {
	var $updatesID;
	var $PensionerID;
	var $status;
	var $dateUpdated;
	var $approved;
	var $deathDate;
	var $paymentmodeID;
	var $Createdby;
	var $CreatedDate;
	var $UpdatedBy;
	var $UpdatedDate;
	var $Remarks;
	var $_field;
	var $new_value;
	var $old_value;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'Pensioner_Updates';
		$this->TableName = 'Pensioner Updates';
		$this->TableType = 'CUSTOMVIEW';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// updatesID
		$this->updatesID = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_updatesID', 'updatesID', 'tbl_updates.updatesID', 'tbl_updates.updatesID', 3, -1, FALSE, 'tbl_updates.updatesID', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->updatesID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['updatesID'] = &$this->updatesID;

		// PensionerID
		$this->PensionerID = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_PensionerID', 'PensionerID', 'tbl_updates.PensionerID', 'tbl_updates.PensionerID', 200, -1, FALSE, 'tbl_updates.PensionerID', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['PensionerID'] = &$this->PensionerID;

		// status
		$this->status = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_status', 'status', 'tbl_updates.status', 'tbl_updates.status', 16, -1, FALSE, 'tbl_updates.status', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->status->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['status'] = &$this->status;

		// dateUpdated
		$this->dateUpdated = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_dateUpdated', 'dateUpdated', 'tbl_updates.dateUpdated', 'DATE_FORMAT(tbl_updates.dateUpdated, \'%m/%d/%Y\')', 133, 6, FALSE, 'tbl_updates.dateUpdated', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->dateUpdated->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['dateUpdated'] = &$this->dateUpdated;

		// approved
		$this->approved = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_approved', 'approved', 'tbl_updates.approved', 'tbl_updates.approved', 16, -1, FALSE, 'tbl_updates.approved', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->approved->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['approved'] = &$this->approved;

		// deathDate
		$this->deathDate = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_deathDate', 'deathDate', 'tbl_updates.deathDate', 'DATE_FORMAT(tbl_updates.deathDate, \'%m/%d/%Y\')', 133, 6, FALSE, 'tbl_updates.deathDate', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->deathDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['deathDate'] = &$this->deathDate;

		// paymentmodeID
		$this->paymentmodeID = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_paymentmodeID', 'paymentmodeID', 'tbl_updates.paymentmodeID', 'tbl_updates.paymentmodeID', 3, -1, FALSE, 'tbl_updates.paymentmodeID', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->paymentmodeID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['paymentmodeID'] = &$this->paymentmodeID;

		// Createdby
		$this->Createdby = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_Createdby', 'Createdby', 'tbl_updates.Createdby', 'tbl_updates.Createdby', 3, -1, FALSE, 'tbl_updates.Createdby', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Createdby->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Createdby'] = &$this->Createdby;

		// CreatedDate
		$this->CreatedDate = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_CreatedDate', 'CreatedDate', 'tbl_updates.CreatedDate', 'DATE_FORMAT(tbl_updates.CreatedDate, \'%m/%d/%Y\')', 135, 6, FALSE, 'tbl_updates.CreatedDate', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->CreatedDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['CreatedDate'] = &$this->CreatedDate;

		// UpdatedBy
		$this->UpdatedBy = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_UpdatedBy', 'UpdatedBy', 'tbl_updates.UpdatedBy', 'tbl_updates.UpdatedBy', 3, -1, FALSE, 'tbl_updates.UpdatedBy', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->UpdatedBy->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['UpdatedBy'] = &$this->UpdatedBy;

		// UpdatedDate
		$this->UpdatedDate = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_UpdatedDate', 'UpdatedDate', 'tbl_updates.UpdatedDate', 'DATE_FORMAT(tbl_updates.UpdatedDate, \'%m/%d/%Y\')', 135, 6, FALSE, 'tbl_updates.UpdatedDate', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->UpdatedDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['UpdatedDate'] = &$this->UpdatedDate;

		// Remarks
		$this->Remarks = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_Remarks', 'Remarks', 'tbl_updates.Remarks', 'tbl_updates.Remarks', 201, -1, FALSE, 'tbl_updates.Remarks', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Remarks'] = &$this->Remarks;

		// field
		$this->_field = new cField('Pensioner_Updates', 'Pensioner Updates', 'x__field', 'field', 'tbl_updates.field', 'tbl_updates.field', 200, -1, FALSE, 'tbl_updates.field', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['field'] = &$this->_field;

		// new_value
		$this->new_value = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_new_value', 'new_value', 'tbl_updates.new_value', 'tbl_updates.new_value', 201, -1, FALSE, 'tbl_updates.new_value', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['new_value'] = &$this->new_value;

		// old_value
		$this->old_value = new cField('Pensioner_Updates', 'Pensioner Updates', 'x_old_value', 'old_value', 'tbl_updates.old_value', 'tbl_updates.old_value', 201, -1, FALSE, 'tbl_updates.old_value', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['old_value'] = &$this->old_value;
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Table level SQL
	function SqlFrom() { // From
		return "tbl_updates";
	}

	function SqlSelect() { // Select
		return "SELECT tbl_updates.updatesID, tbl_updates.PensionerID, tbl_updates.status, tbl_updates.dateUpdated, tbl_updates.approved, tbl_updates.deathDate, tbl_updates.paymentmodeID, tbl_updates.Createdby, tbl_updates.CreatedDate, tbl_updates.UpdatedBy, tbl_updates.UpdatedDate, tbl_updates.Remarks, tbl_updates.field, tbl_updates.new_value, tbl_updates.old_value FROM " . $this->SqlFrom();
	}

	function SqlWhere() { // Where
		$sWhere = "approved=0";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlGroupBy() { // Group By
		return "";
	}

	function SqlHaving() { // Having
		return "";
	}

	function SqlOrderBy() { // Order By
		return "";
	}

	// Check if Anonymous User is allowed
	function AllowAnonymousUser() {
		switch (@$this->PageID) {
			case "add":
			case "register":
			case "addopt":
				return FALSE;
			case "edit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return FALSE;
			case "delete":
				return FALSE;
			case "view":
				return FALSE;
			case "search":
				return FALSE;
			default:
				return FALSE;
		}
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(), $this->SqlGroupBy(),
			$this->SqlHaving(), $this->SqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->SqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		global $conn;
		$cnt = -1;
		if ($this->TableType == 'TABLE' || $this->TableType == 'VIEW') {
			$sSql = "SELECT COUNT(*) FROM" . substr($sSql, 13);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		global $conn;
		$origFilter = $this->CurrentFilter;
		$this->Recordset_Selecting($this->CurrentFilter);
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Update Table
	var $UpdateTable = "tbl_updates";

	// INSERT statement
	function InsertSQL(&$rs) {
		global $conn;
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		global $conn;
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL) {
		global $conn;
		return $conn->Execute($this->UpdateSQL($rs, $where));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "") {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if ($rs) {
			if (array_key_exists('updatesID', $rs))
				ew_AddFilter($where, ew_QuotedName('updatesID') . '=' . ew_QuotedValue($rs['updatesID'], $this->updatesID->FldDataType));
		}
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->DeleteSQL($rs, $where));
	}

	// approve statement
	function ApproveSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET approved='1' WHERE ";
		if ($rs) {
			if (array_key_exists('updatesID', $rs))
				ew_AddFilter($where, ew_QuotedName('updatesID') . '=' . ew_QuotedValue($rs['updatesID'], $this->updatesID->FldDataType));
		}
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// approve
	function Approve(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->ApproveSQL($rs, $where));
	}

	// disapprove statement
	function DisapproveSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET approved='2' WHERE ";
		if ($rs) {
			if (array_key_exists('updatesID', $rs))
				ew_AddFilter($where, ew_QuotedName('updatesID') . '=' . ew_QuotedValue($rs['updatesID'], $this->updatesID->FldDataType));
		}
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// disapprove
	function Disapprove(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->DisapproveSQL($rs, $where));
	}

	//New Jayar
	// get old location
	function getOldLocation($PensionerID) {
		global $conn;

		$rs = $conn->Execute("Select `status`, firstname, middlename, lastname, psgc_region, psgc_province, psgc_municipality, psgc_brgy, given_add from tbl_pensioner where PensionerID = '".$PensionerID."'");

		$array = array("psgc_region" => $rs->fields('psgc_region'),
			"psgc_province" => $rs->fields('psgc_province'),
			"psgc_municipality" => $rs->fields('psgc_municipality'),
			"psgc_brgy" => $rs->fields('psgc_brgy'),
			"given_add" => $rs->fields('given_add'),
			"firstname" => $rs->fields('firstname'),
			"middlename" => $rs->fields('middlename'),
			"lastname" => $rs->fields('lastname'),
			"status" => $rs->fields('status')
		);

		return $array;
	}

	// Update Pensioner
	function UpdatePensionerSQL(&$rs, $where = "") { // $rs can get any column name on the specified table on info file
		//if($rs['approved'] == '1'){ // 1 - approved
		$sql = "UPDATE tbl_pensioner SET ".$rs['field']."='".$rs['new_value']."' WHERE PensionerID='".$rs['PensionerID']."' ";
		//}
		return $sql;
	}

	// Update Pensioner
	function UpdatePensioner(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->UpdatePensionerSQL($rs, $where));
	}
	//Jayar

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "tbl_updates.updatesID = @updatesID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->updatesID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@updatesID@", ew_AdjustSql($this->updatesID->CurrentValue), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "Pensioner_Updateslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "Pensioner_Updateslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("Pensioner_Updatesview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("Pensioner_Updatesview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "Pensioner_Updatesadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("Pensioner_Updatesedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("Pensioner_Updatesadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("Pensioner_Updatesdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->updatesID->CurrentValue)) {
			$sUrl .= "updatesID=" . urlencode($this->updatesID->CurrentValue);
		} else {
			return "javascript:alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET)) {
			$arKeys[] = @$_GET["updatesID"]; // updatesID

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		foreach ($arKeys as $key) {
			if (!is_numeric($key))
				continue;
			$ar[] = $key;
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->updatesID->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {
		global $conn;

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->updatesID->setDbValue($rs->fields('updatesID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->status->setDbValue($rs->fields('status'));
		$this->dateUpdated->setDbValue($rs->fields('dateUpdated'));
		$this->approved->setDbValue($rs->fields('approved'));
		$this->deathDate->setDbValue($rs->fields('deathDate'));
		$this->paymentmodeID->setDbValue($rs->fields('paymentmodeID'));
		$this->Createdby->setDbValue($rs->fields('Createdby'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
		$this->Remarks->setDbValue($rs->fields('Remarks'));
		$this->_field->setDbValue($rs->fields('field'));
		$this->new_value->setDbValue($rs->fields('new_value'));
		$this->old_value->setDbValue($rs->fields('old_value'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// updatesID
		// PensionerID
		// status
		// dateUpdated
		// approved
		// deathDate
		// paymentmodeID
		// Createdby
		// CreatedDate
		// UpdatedBy
		// UpdatedDate
		// Remarks
		// field
		// new_value
		// old_value
		// updatesID

		$this->updatesID->ViewValue = $this->updatesID->CurrentValue;
		$this->updatesID->ViewCustomAttributes = "";

		// PensionerID
		$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
		$this->PensionerID->ViewCustomAttributes = "";

		// status
		$this->status->ViewValue = $this->status->CurrentValue;
		$this->status->ViewCustomAttributes = "";

		// dateUpdated
		$this->dateUpdated->ViewValue = $this->dateUpdated->CurrentValue;
		$this->dateUpdated->ViewValue = ew_FormatDateTime($this->dateUpdated->ViewValue, 6);
		$this->dateUpdated->ViewCustomAttributes = "";

		// approved
		$this->approved->ViewValue = $this->approved->CurrentValue;
		$this->approved->ViewCustomAttributes = "";

		// deathDate
		$this->deathDate->ViewValue = $this->deathDate->CurrentValue;
		$this->deathDate->ViewValue = ew_FormatDateTime($this->deathDate->ViewValue, 6);
		$this->deathDate->ViewCustomAttributes = "";

		// paymentmodeID
		$this->paymentmodeID->ViewValue = $this->paymentmodeID->CurrentValue;
		$this->paymentmodeID->ViewCustomAttributes = "";

		// Createdby
		$this->Createdby->ViewValue = $this->Createdby->CurrentValue;
		if (strval($this->Createdby->CurrentValue) <> "") {
			$sFilterWrk = "`uid`" . ew_SearchString("=", $this->Createdby->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `uid`, `surname` AS `DispFld`, `firstname` AS `Disp2Fld`, `middlename` AS `Disp3Fld`, `extensionname` AS `Disp4Fld` FROM `tbl_user`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->Createdby, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `surname` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->Createdby->ViewValue = $rswrk->fields('DispFld');
				$this->Createdby->ViewValue .= ew_ValueSeparator(1,$this->Createdby) . $rswrk->fields('Disp2Fld');
				$this->Createdby->ViewValue .= ew_ValueSeparator(2,$this->Createdby) . $rswrk->fields('Disp3Fld');
				$this->Createdby->ViewValue .= ew_ValueSeparator(3,$this->Createdby) . $rswrk->fields('Disp4Fld');
				$rswrk->Close();
			} else {
				$this->Createdby->ViewValue = $this->Createdby->CurrentValue;
			}
		} else {
			$this->Createdby->ViewValue = NULL;
		}
		$this->Createdby->ViewCustomAttributes = "";

		// CreatedDate
		$this->CreatedDate->ViewValue = $this->CreatedDate->CurrentValue;
		$this->CreatedDate->ViewValue = ew_FormatDateTime($this->CreatedDate->ViewValue, 6);
		$this->CreatedDate->ViewCustomAttributes = "";

		// UpdatedBy
		$this->UpdatedBy->ViewValue = $this->UpdatedBy->CurrentValue;
		if (strval($this->UpdatedBy->CurrentValue) <> "") {
			$sFilterWrk = "`uid`" . ew_SearchString("=", $this->UpdatedBy->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `uid`, `surname` AS `DispFld`, `firstname` AS `Disp2Fld`, `middlename` AS `Disp3Fld`, `extensionname` AS `Disp4Fld` FROM `tbl_user`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->UpdatedBy, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `surname` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->UpdatedBy->ViewValue = $rswrk->fields('DispFld');
				$this->UpdatedBy->ViewValue .= ew_ValueSeparator(1,$this->UpdatedBy) . $rswrk->fields('Disp2Fld');
				$this->UpdatedBy->ViewValue .= ew_ValueSeparator(2,$this->UpdatedBy) . $rswrk->fields('Disp3Fld');
				$this->UpdatedBy->ViewValue .= ew_ValueSeparator(3,$this->UpdatedBy) . $rswrk->fields('Disp4Fld');
				$rswrk->Close();
			} else {
				$this->UpdatedBy->ViewValue = $this->UpdatedBy->CurrentValue;
			}
		} else {
			$this->UpdatedBy->ViewValue = NULL;
		}
		$this->UpdatedBy->ViewCustomAttributes = "";

		// UpdatedDate
		$this->UpdatedDate->ViewValue = $this->UpdatedDate->CurrentValue;
		$this->UpdatedDate->ViewValue = ew_FormatDateTime($this->UpdatedDate->ViewValue, 6);
		$this->UpdatedDate->ViewCustomAttributes = "";

		// Remarks
		$this->Remarks->ViewValue = $this->Remarks->CurrentValue;
		$this->Remarks->ViewCustomAttributes = "";

		// field
		$this->_field->ViewValue = $this->_field->CurrentValue;
		$this->_field->ViewCustomAttributes = "";

		// new_value
		$this->new_value->ViewValue = $this->new_value->CurrentValue;
		$this->new_value->ViewCustomAttributes = "";

		// old_value
		$this->old_value->ViewValue = $this->old_value->CurrentValue;
		$this->old_value->ViewCustomAttributes = "";

		// updatesID
		$this->updatesID->LinkCustomAttributes = "";
		$this->updatesID->HrefValue = "";
		$this->updatesID->TooltipValue = "";

		// PensionerID
		$this->PensionerID->LinkCustomAttributes = "";
		$this->PensionerID->HrefValue = "";
		$this->PensionerID->TooltipValue = "";

		// status
		$this->status->LinkCustomAttributes = "";
		$this->status->HrefValue = "";
		$this->status->TooltipValue = "";

		// dateUpdated
		$this->dateUpdated->LinkCustomAttributes = "";
		$this->dateUpdated->HrefValue = "";
		$this->dateUpdated->TooltipValue = "";

		// approved
		$this->approved->LinkCustomAttributes = "";
		$this->approved->HrefValue = "";
		$this->approved->TooltipValue = "";

		// deathDate
		$this->deathDate->LinkCustomAttributes = "";
		$this->deathDate->HrefValue = "";
		$this->deathDate->TooltipValue = "";

		// paymentmodeID
		$this->paymentmodeID->LinkCustomAttributes = "";
		$this->paymentmodeID->HrefValue = "";
		$this->paymentmodeID->TooltipValue = "";

		// Createdby
		$this->Createdby->LinkCustomAttributes = "";
		$this->Createdby->HrefValue = "";
		$this->Createdby->TooltipValue = "";

		// CreatedDate
		$this->CreatedDate->LinkCustomAttributes = "";
		$this->CreatedDate->HrefValue = "";
		$this->CreatedDate->TooltipValue = "";

		// UpdatedBy
		$this->UpdatedBy->LinkCustomAttributes = "";
		$this->UpdatedBy->HrefValue = "";
		$this->UpdatedBy->TooltipValue = "";

		// UpdatedDate
		$this->UpdatedDate->LinkCustomAttributes = "";
		$this->UpdatedDate->HrefValue = "";
		$this->UpdatedDate->TooltipValue = "";

		// Remarks
		$this->Remarks->LinkCustomAttributes = "";
		$this->Remarks->HrefValue = "";
		$this->Remarks->TooltipValue = "";

		// field
		$this->_field->LinkCustomAttributes = "";
		$this->_field->HrefValue = "";
		$this->_field->TooltipValue = "";

		// new_value
		$this->new_value->LinkCustomAttributes = "";
		$this->new_value->HrefValue = "";
		$this->new_value->TooltipValue = "";

		// old_value
		$this->old_value->LinkCustomAttributes = "";
		$this->old_value->HrefValue = "";
		$this->old_value->TooltipValue = "";

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
	}

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;

		// Write header
		$Doc->ExportTableHeader();
		if ($Doc->Horizontal) { // Horizontal format, write header
			$Doc->BeginExportRow();
			if ($ExportPageType == "view") {
				if ($this->updatesID->Exportable) $Doc->ExportCaption($this->updatesID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->status->Exportable) $Doc->ExportCaption($this->status);
				if ($this->dateUpdated->Exportable) $Doc->ExportCaption($this->dateUpdated);
				if ($this->approved->Exportable) $Doc->ExportCaption($this->approved);
				if ($this->deathDate->Exportable) $Doc->ExportCaption($this->deathDate);
				if ($this->paymentmodeID->Exportable) $Doc->ExportCaption($this->paymentmodeID);
				if ($this->Createdby->Exportable) $Doc->ExportCaption($this->Createdby);
				if ($this->CreatedDate->Exportable) $Doc->ExportCaption($this->CreatedDate);
				if ($this->UpdatedBy->Exportable) $Doc->ExportCaption($this->UpdatedBy);
				if ($this->UpdatedDate->Exportable) $Doc->ExportCaption($this->UpdatedDate);
				if ($this->Remarks->Exportable) $Doc->ExportCaption($this->Remarks);
				if ($this->_field->Exportable) $Doc->ExportCaption($this->_field);
				if ($this->new_value->Exportable) $Doc->ExportCaption($this->new_value);
				if ($this->old_value->Exportable) $Doc->ExportCaption($this->old_value);
			} else {
				if ($this->updatesID->Exportable) $Doc->ExportCaption($this->updatesID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->status->Exportable) $Doc->ExportCaption($this->status);
				if ($this->dateUpdated->Exportable) $Doc->ExportCaption($this->dateUpdated);
				if ($this->approved->Exportable) $Doc->ExportCaption($this->approved);
				if ($this->deathDate->Exportable) $Doc->ExportCaption($this->deathDate);
				if ($this->paymentmodeID->Exportable) $Doc->ExportCaption($this->paymentmodeID);
				if ($this->Createdby->Exportable) $Doc->ExportCaption($this->Createdby);
				if ($this->CreatedDate->Exportable) $Doc->ExportCaption($this->CreatedDate);
				if ($this->UpdatedBy->Exportable) $Doc->ExportCaption($this->UpdatedBy);
				if ($this->UpdatedDate->Exportable) $Doc->ExportCaption($this->UpdatedDate);
				if ($this->_field->Exportable) $Doc->ExportCaption($this->_field);
			}
			$Doc->EndExportRow();
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
				if ($ExportPageType == "view") {
					if ($this->updatesID->Exportable) $Doc->ExportField($this->updatesID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->status->Exportable) $Doc->ExportField($this->status);
					if ($this->dateUpdated->Exportable) $Doc->ExportField($this->dateUpdated);
					if ($this->approved->Exportable) $Doc->ExportField($this->approved);
					if ($this->deathDate->Exportable) $Doc->ExportField($this->deathDate);
					if ($this->paymentmodeID->Exportable) $Doc->ExportField($this->paymentmodeID);
					if ($this->Createdby->Exportable) $Doc->ExportField($this->Createdby);
					if ($this->CreatedDate->Exportable) $Doc->ExportField($this->CreatedDate);
					if ($this->UpdatedBy->Exportable) $Doc->ExportField($this->UpdatedBy);
					if ($this->UpdatedDate->Exportable) $Doc->ExportField($this->UpdatedDate);
					if ($this->Remarks->Exportable) $Doc->ExportField($this->Remarks);
					if ($this->_field->Exportable) $Doc->ExportField($this->_field);
					if ($this->new_value->Exportable) $Doc->ExportField($this->new_value);
					if ($this->old_value->Exportable) $Doc->ExportField($this->old_value);
				} else {
					if ($this->updatesID->Exportable) $Doc->ExportField($this->updatesID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->status->Exportable) $Doc->ExportField($this->status);
					if ($this->dateUpdated->Exportable) $Doc->ExportField($this->dateUpdated);
					if ($this->approved->Exportable) $Doc->ExportField($this->approved);
					if ($this->deathDate->Exportable) $Doc->ExportField($this->deathDate);
					if ($this->paymentmodeID->Exportable) $Doc->ExportField($this->paymentmodeID);
					if ($this->Createdby->Exportable) $Doc->ExportField($this->Createdby);
					if ($this->CreatedDate->Exportable) $Doc->ExportField($this->CreatedDate);
					if ($this->UpdatedBy->Exportable) $Doc->ExportField($this->UpdatedBy);
					if ($this->UpdatedDate->Exportable) $Doc->ExportField($this->UpdatedDate);
					if ($this->_field->Exportable) $Doc->ExportField($this->_field);
				}
				$Doc->EndExportRow();
			}
			$Recordset->MoveNext();
		}
		$Doc->ExportTableFooter();
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
