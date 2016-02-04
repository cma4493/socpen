<?php

// Global variable for table object
$tbl_updates = NULL;

//
// Table class for tbl_updates
//
class ctbl_updates extends cTable {
	var $updatesID;
	var $PensionerID;
	var $status;
	var $Remarks;
	var $approved;
	var $dateUpdated;
	var $_field;
	var $new_value;
	var $old_value;
	var $paymentmodeID;
	var $deathDate;
	var $Createdby;
	var $CreatedDate;
	var $UpdatedBy;
	var $UpdatedDate;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'tbl_updates';
		$this->TableName = 'tbl_updates';
		$this->TableType = 'TABLE';
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
		$this->updatesID = new cField('tbl_updates', 'tbl_updates', 'x_updatesID', 'updatesID', '`updatesID`', '`updatesID`', 3, -1, FALSE, '`updatesID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->updatesID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['updatesID'] = &$this->updatesID;

		// PensionerID
		$this->PensionerID = new cField('tbl_updates', 'tbl_updates', 'x_PensionerID', 'PensionerID', '`PensionerID`', '`PensionerID`', 200, -1, FALSE, '`PensionerID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['PensionerID'] = &$this->PensionerID;

		// status
		$this->status = new cField('tbl_updates', 'tbl_updates', 'x_status', 'status', '`status`', '`status`', 16, -1, FALSE, '`status`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->status->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['status'] = &$this->status;

		// Remarks
		$this->Remarks = new cField('tbl_updates', 'tbl_updates', 'x_Remarks', 'Remarks', '`Remarks`', '`Remarks`', 201, -1, FALSE, '`Remarks`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Remarks'] = &$this->Remarks;

		// approved
		$this->approved = new cField('tbl_updates', 'tbl_updates', 'x_approved', 'approved', '`approved`', '`approved`', 16, -1, FALSE, '`approved`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->approved->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['approved'] = &$this->approved;

		// dateUpdated
		$this->dateUpdated = new cField('tbl_updates', 'tbl_updates', 'x_dateUpdated', 'dateUpdated', '`dateUpdated`', 'DATE_FORMAT(`dateUpdated`, \'%m/%d/%Y\')', 133, 6, FALSE, '`dateUpdated`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->dateUpdated->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['dateUpdated'] = &$this->dateUpdated;

		// field
		$this->_field = new cField('tbl_updates', 'tbl_updates', 'x__field', 'field', '`field`', '`field`', 200, -1, FALSE, '`field`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['field'] = &$this->_field;

		// new_value
		$this->new_value = new cField('tbl_updates', 'tbl_updates', 'x_new_value', 'new_value', '`new_value`', '`new_value`', 201, -1, FALSE, '`new_value`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['new_value'] = &$this->new_value;

		// old_value
		$this->old_value = new cField('tbl_updates', 'tbl_updates', 'x_old_value', 'old_value', '`old_value`', '`old_value`', 201, -1, FALSE, '`old_value`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['old_value'] = &$this->old_value;

		// paymentmodeID
		$this->paymentmodeID = new cField('tbl_updates', 'tbl_updates', 'x_paymentmodeID', 'paymentmodeID', '`paymentmodeID`', '`paymentmodeID`', 3, -1, FALSE, '`paymentmodeID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->paymentmodeID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['paymentmodeID'] = &$this->paymentmodeID;

		// deathDate
		$this->deathDate = new cField('tbl_updates', 'tbl_updates', 'x_deathDate', 'deathDate', '`deathDate`', 'DATE_FORMAT(`deathDate`, \'%m/%d/%Y\')', 133, 6, FALSE, '`deathDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->deathDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['deathDate'] = &$this->deathDate;

		// Createdby
		$this->Createdby = new cField('tbl_updates', 'tbl_updates', 'x_Createdby', 'Createdby', '`Createdby`', '`Createdby`', 3, -1, FALSE, '`Createdby`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Createdby->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Createdby'] = &$this->Createdby;

		// CreatedDate
		$this->CreatedDate = new cField('tbl_updates', 'tbl_updates', 'x_CreatedDate', 'CreatedDate', '`CreatedDate`', 'DATE_FORMAT(`CreatedDate`, \'%m/%d/%Y\')', 135, 6, FALSE, '`CreatedDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->CreatedDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['CreatedDate'] = &$this->CreatedDate;

		// UpdatedBy
		$this->UpdatedBy = new cField('tbl_updates', 'tbl_updates', 'x_UpdatedBy', 'UpdatedBy', '`UpdatedBy`', '`UpdatedBy`', 3, -1, FALSE, '`UpdatedBy`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->UpdatedBy->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['UpdatedBy'] = &$this->UpdatedBy;

		// UpdatedDate
		$this->UpdatedDate = new cField('tbl_updates', 'tbl_updates', 'x_UpdatedDate', 'UpdatedDate', '`UpdatedDate`', 'DATE_FORMAT(`UpdatedDate`, \'%m/%d/%Y\')', 135, 6, FALSE, '`UpdatedDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->UpdatedDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['UpdatedDate'] = &$this->UpdatedDate;
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

	// Current master table name
	function getCurrentMasterTable() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_MASTER_TABLE];
	}

	function setCurrentMasterTable($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_MASTER_TABLE] = $v;
	}

	// Session master WHERE clause
	function GetMasterFilter() {

		// Master filter
		$sMasterFilter = "";
		if ($this->getCurrentMasterTable() == "tbl_pensioner") {
			if ($this->PensionerID->getSessionValue() <> "")
				$sMasterFilter .= "`PensionerID`=" . ew_QuotedValue($this->PensionerID->getSessionValue(), EW_DATATYPE_STRING);
			else
				return "";
		}
		return $sMasterFilter;
	}

	// Session detail WHERE clause
	function GetDetailFilter() {

		// Detail filter
		$sDetailFilter = "";
		if ($this->getCurrentMasterTable() == "tbl_pensioner") {
			if ($this->PensionerID->getSessionValue() <> "")
				$sDetailFilter .= "`PensionerID`=" . ew_QuotedValue($this->PensionerID->getSessionValue(), EW_DATATYPE_STRING);
			else
				return "";
		}
		return $sDetailFilter;
	}

	// Master filter
	function SqlMasterFilter_tbl_pensioner() {
		return "`PensionerID`='@PensionerID@'";
	}

	// Detail filter
	function SqlDetailFilter_tbl_pensioner() {
		return "`PensionerID`='@PensionerID@'";
	}

	// Table level SQL
	function SqlFrom() { // From
		return "`tbl_updates`";
	}

	function SqlSelect() { // Select
		return "SELECT * FROM " . $this->SqlFrom();
	}

	function SqlWhere() { // Where
		$sWhere = "";
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
	var $UpdateTable = "`tbl_updates`";

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

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`updatesID` = @updatesID@";
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
			return "tbl_updateslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "tbl_updateslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("tbl_updatesview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("tbl_updatesview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "tbl_updatesadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("tbl_updatesedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("tbl_updatesadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("tbl_updatesdelete.php", $this->UrlParm());
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
		$this->Remarks->setDbValue($rs->fields('Remarks'));
		$this->approved->setDbValue($rs->fields('approved'));
		$this->dateUpdated->setDbValue($rs->fields('dateUpdated'));
		$this->_field->setDbValue($rs->fields('field'));
		$this->new_value->setDbValue($rs->fields('new_value'));
		$this->old_value->setDbValue($rs->fields('old_value'));
		$this->paymentmodeID->setDbValue($rs->fields('paymentmodeID'));
		$this->deathDate->setDbValue($rs->fields('deathDate'));
		$this->Createdby->setDbValue($rs->fields('Createdby'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
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
		// Remarks
		// approved
		// dateUpdated
		// field
		// new_value
		// old_value
		// paymentmodeID
		// deathDate
		// Createdby
		// CreatedDate
		// UpdatedBy
		// UpdatedDate
		// updatesID

		$this->updatesID->ViewValue = $this->updatesID->CurrentValue;
		$this->updatesID->ViewCustomAttributes = "";

		// PensionerID
		$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
		$this->PensionerID->ViewCustomAttributes = "";

		// status
		$this->status->ViewValue = $this->status->CurrentValue;
		$this->status->ViewCustomAttributes = "";

		// Remarks
		$this->Remarks->ViewValue = $this->Remarks->CurrentValue;
		$this->Remarks->ViewCustomAttributes = "";

		// approved
		$this->approved->ViewValue = $this->approved->CurrentValue;
		$this->approved->ViewCustomAttributes = "";

		// dateUpdated
		$this->dateUpdated->ViewValue = $this->dateUpdated->CurrentValue;
		$this->dateUpdated->ViewValue = ew_FormatDateTime($this->dateUpdated->ViewValue, 6);
		$this->dateUpdated->ViewCustomAttributes = "";

		// field
		$this->_field->ViewValue = $this->_field->CurrentValue;
		$this->_field->ViewCustomAttributes = "";

		// new_value
		$this->new_value->ViewValue = $this->new_value->CurrentValue;
		$this->new_value->ViewCustomAttributes = "";

		// old_value
		$this->old_value->ViewValue = $this->old_value->CurrentValue;
		$this->old_value->ViewCustomAttributes = "";

		// paymentmodeID
		$this->paymentmodeID->ViewValue = $this->paymentmodeID->CurrentValue;
		$this->paymentmodeID->ViewCustomAttributes = "";

		// deathDate
		$this->deathDate->ViewValue = $this->deathDate->CurrentValue;
		$this->deathDate->ViewValue = ew_FormatDateTime($this->deathDate->ViewValue, 6);
		$this->deathDate->ViewCustomAttributes = "";

		// Createdby
		$this->Createdby->ViewValue = $this->Createdby->CurrentValue;
		$this->Createdby->ViewCustomAttributes = "";

		// CreatedDate
		$this->CreatedDate->ViewValue = $this->CreatedDate->CurrentValue;
		$this->CreatedDate->ViewValue = ew_FormatDateTime($this->CreatedDate->ViewValue, 6);
		$this->CreatedDate->ViewCustomAttributes = "";

		// UpdatedBy
		$this->UpdatedBy->ViewValue = $this->UpdatedBy->CurrentValue;
		$this->UpdatedBy->ViewCustomAttributes = "";

		// UpdatedDate
		$this->UpdatedDate->ViewValue = $this->UpdatedDate->CurrentValue;
		$this->UpdatedDate->ViewValue = ew_FormatDateTime($this->UpdatedDate->ViewValue, 6);
		$this->UpdatedDate->ViewCustomAttributes = "";

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

		// Remarks
		$this->Remarks->LinkCustomAttributes = "";
		$this->Remarks->HrefValue = "";
		$this->Remarks->TooltipValue = "";

		// approved
		$this->approved->LinkCustomAttributes = "";
		$this->approved->HrefValue = "";
		$this->approved->TooltipValue = "";

		// dateUpdated
		$this->dateUpdated->LinkCustomAttributes = "";
		$this->dateUpdated->HrefValue = "";
		$this->dateUpdated->TooltipValue = "";

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

		// paymentmodeID
		$this->paymentmodeID->LinkCustomAttributes = "";
		$this->paymentmodeID->HrefValue = "";
		$this->paymentmodeID->TooltipValue = "";

		// deathDate
		$this->deathDate->LinkCustomAttributes = "";
		$this->deathDate->HrefValue = "";
		$this->deathDate->TooltipValue = "";

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
				if ($this->Remarks->Exportable) $Doc->ExportCaption($this->Remarks);
				if ($this->approved->Exportable) $Doc->ExportCaption($this->approved);
				if ($this->dateUpdated->Exportable) $Doc->ExportCaption($this->dateUpdated);
				if ($this->_field->Exportable) $Doc->ExportCaption($this->_field);
				if ($this->new_value->Exportable) $Doc->ExportCaption($this->new_value);
				if ($this->old_value->Exportable) $Doc->ExportCaption($this->old_value);
				if ($this->paymentmodeID->Exportable) $Doc->ExportCaption($this->paymentmodeID);
				if ($this->deathDate->Exportable) $Doc->ExportCaption($this->deathDate);
				if ($this->Createdby->Exportable) $Doc->ExportCaption($this->Createdby);
				if ($this->CreatedDate->Exportable) $Doc->ExportCaption($this->CreatedDate);
				if ($this->UpdatedBy->Exportable) $Doc->ExportCaption($this->UpdatedBy);
				if ($this->UpdatedDate->Exportable) $Doc->ExportCaption($this->UpdatedDate);
			} else {
				if ($this->updatesID->Exportable) $Doc->ExportCaption($this->updatesID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->status->Exportable) $Doc->ExportCaption($this->status);
				if ($this->approved->Exportable) $Doc->ExportCaption($this->approved);
				if ($this->dateUpdated->Exportable) $Doc->ExportCaption($this->dateUpdated);
				if ($this->_field->Exportable) $Doc->ExportCaption($this->_field);
				if ($this->paymentmodeID->Exportable) $Doc->ExportCaption($this->paymentmodeID);
				if ($this->deathDate->Exportable) $Doc->ExportCaption($this->deathDate);
				if ($this->Createdby->Exportable) $Doc->ExportCaption($this->Createdby);
				if ($this->CreatedDate->Exportable) $Doc->ExportCaption($this->CreatedDate);
				if ($this->UpdatedBy->Exportable) $Doc->ExportCaption($this->UpdatedBy);
				if ($this->UpdatedDate->Exportable) $Doc->ExportCaption($this->UpdatedDate);
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
					if ($this->Remarks->Exportable) $Doc->ExportField($this->Remarks);
					if ($this->approved->Exportable) $Doc->ExportField($this->approved);
					if ($this->dateUpdated->Exportable) $Doc->ExportField($this->dateUpdated);
					if ($this->_field->Exportable) $Doc->ExportField($this->_field);
					if ($this->new_value->Exportable) $Doc->ExportField($this->new_value);
					if ($this->old_value->Exportable) $Doc->ExportField($this->old_value);
					if ($this->paymentmodeID->Exportable) $Doc->ExportField($this->paymentmodeID);
					if ($this->deathDate->Exportable) $Doc->ExportField($this->deathDate);
					if ($this->Createdby->Exportable) $Doc->ExportField($this->Createdby);
					if ($this->CreatedDate->Exportable) $Doc->ExportField($this->CreatedDate);
					if ($this->UpdatedBy->Exportable) $Doc->ExportField($this->UpdatedBy);
					if ($this->UpdatedDate->Exportable) $Doc->ExportField($this->UpdatedDate);
				} else {
					if ($this->updatesID->Exportable) $Doc->ExportField($this->updatesID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->status->Exportable) $Doc->ExportField($this->status);
					if ($this->approved->Exportable) $Doc->ExportField($this->approved);
					if ($this->dateUpdated->Exportable) $Doc->ExportField($this->dateUpdated);
					if ($this->_field->Exportable) $Doc->ExportField($this->_field);
					if ($this->paymentmodeID->Exportable) $Doc->ExportField($this->paymentmodeID);
					if ($this->deathDate->Exportable) $Doc->ExportField($this->deathDate);
					if ($this->Createdby->Exportable) $Doc->ExportField($this->Createdby);
					if ($this->CreatedDate->Exportable) $Doc->ExportField($this->CreatedDate);
					if ($this->UpdatedBy->Exportable) $Doc->ExportField($this->UpdatedBy);
					if ($this->UpdatedDate->Exportable) $Doc->ExportField($this->UpdatedDate);
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
