<?php

// Global variable for table object
$tbl_pension_payroll = NULL;

//
// Table class for tbl_pension_payroll
//
class ctbl_pension_payroll extends cTable {
	var $PayrollID;
	var $PensionerID;
	var $PayrollYear;
	var $cMonth;
	var $amount;
	var $paymentmodeID;
	var $Approved;
	var $Claimed;
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
		$this->TableVar = 'tbl_pension_payroll';
		$this->TableName = 'tbl_pension_payroll';
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

		// PayrollID
		$this->PayrollID = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_PayrollID', 'PayrollID', '`PayrollID`', '`PayrollID`', 3, -1, FALSE, '`PayrollID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->PayrollID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['PayrollID'] = &$this->PayrollID;

		// PensionerID
		$this->PensionerID = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_PensionerID', 'PensionerID', '`PensionerID`', '`PensionerID`', 200, -1, FALSE, '`PensionerID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['PensionerID'] = &$this->PensionerID;

		// PayrollYear
		$this->PayrollYear = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_PayrollYear', 'PayrollYear', '`PayrollYear`', '`PayrollYear`', 3, -1, FALSE, '`PayrollYear`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->PayrollYear->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['PayrollYear'] = &$this->PayrollYear;

		// cMonth
		$this->cMonth = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_cMonth', 'cMonth', '`cMonth`', '`cMonth`', 3, -1, FALSE, '`cMonth`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->cMonth->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['cMonth'] = &$this->cMonth;

		// amount
		$this->amount = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_amount', 'amount', '`amount`', '`amount`', 5, -1, FALSE, '`amount`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->amount->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['amount'] = &$this->amount;

		// paymentmodeID
		$this->paymentmodeID = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_paymentmodeID', 'paymentmodeID', '`paymentmodeID`', '`paymentmodeID`', 3, -1, FALSE, '`paymentmodeID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->paymentmodeID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['paymentmodeID'] = &$this->paymentmodeID;

		// Approved
		$this->Approved = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_Approved', 'Approved', '`Approved`', '`Approved`', 16, -1, FALSE, '`Approved`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Approved->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Approved'] = &$this->Approved;

		// Claimed
		$this->Claimed = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_Claimed', 'Claimed', '`Claimed`', '`Claimed`', 16, -1, FALSE, '`Claimed`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Claimed->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Claimed'] = &$this->Claimed;

		// Createdby
		$this->Createdby = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_Createdby', 'Createdby', '`Createdby`', '`Createdby`', 3, -1, FALSE, '`Createdby`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Createdby->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Createdby'] = &$this->Createdby;

		// CreatedDate
		$this->CreatedDate = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_CreatedDate', 'CreatedDate', '`CreatedDate`', 'DATE_FORMAT(`CreatedDate`, \'%m/%d/%Y\')', 135, 6, FALSE, '`CreatedDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->CreatedDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['CreatedDate'] = &$this->CreatedDate;

		// UpdatedBy
		$this->UpdatedBy = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_UpdatedBy', 'UpdatedBy', '`UpdatedBy`', '`UpdatedBy`', 3, -1, FALSE, '`UpdatedBy`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->UpdatedBy->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['UpdatedBy'] = &$this->UpdatedBy;

		// UpdatedDate
		$this->UpdatedDate = new cField('tbl_pension_payroll', 'tbl_pension_payroll', 'x_UpdatedDate', 'UpdatedDate', '`UpdatedDate`', 'DATE_FORMAT(`UpdatedDate`, \'%m/%d/%Y\')', 135, 6, FALSE, '`UpdatedDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
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

	// Table level SQL
	function SqlFrom() { // From
		return "`tbl_pension_payroll`";
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
	var $UpdateTable = "`tbl_pension_payroll`";

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
			if (array_key_exists('PayrollID', $rs))
				ew_AddFilter($where, ew_QuotedName('PayrollID') . '=' . ew_QuotedValue($rs['PayrollID'], $this->PayrollID->FldDataType));
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

	// SetClaim statement
	function SetClaimeSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET Claimed = '1' WHERE ";
		if ($rs) {
			if (array_key_exists('PayrollID', $rs))
				ew_AddFilter($where, ew_QuotedName('PayrollID') . '=' . ew_QuotedValue($rs['PayrollID'], $this->PayrollID->FldDataType));
		}
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// SETCLAIM
	function SetClaim(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->SetClaimeSQL($rs, $where));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`PayrollID` = @PayrollID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->PayrollID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@PayrollID@", ew_AdjustSql($this->PayrollID->CurrentValue), $sKeyFilter); // Replace key value
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
			return "tbl_pension_payrolllist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "tbl_pension_payrolllist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("tbl_pension_payrollview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("tbl_pension_payrollview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "tbl_pension_payrolladd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("tbl_pension_payrolledit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("tbl_pension_payrolladd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("tbl_pension_payrolldelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->PayrollID->CurrentValue)) {
			$sUrl .= "PayrollID=" . urlencode($this->PayrollID->CurrentValue);
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
			$arKeys[] = @$_GET["PayrollID"]; // PayrollID

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
			$this->PayrollID->CurrentValue = $key;
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
		$this->PayrollID->setDbValue($rs->fields('PayrollID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->PayrollYear->setDbValue($rs->fields('PayrollYear'));
		$this->cMonth->setDbValue($rs->fields('cMonth'));
		$this->amount->setDbValue($rs->fields('amount'));
		$this->paymentmodeID->setDbValue($rs->fields('paymentmodeID'));
		$this->Approved->setDbValue($rs->fields('Approved'));
		$this->Claimed->setDbValue($rs->fields('Claimed'));
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
		// PayrollID
		// PensionerID
		// PayrollYear
		// cMonth
		// amount
		// paymentmodeID
		// Approved
		// Claimed
		// Createdby
		// CreatedDate
		// UpdatedBy
		// UpdatedDate
		// PayrollID

		$this->PayrollID->ViewValue = $this->PayrollID->CurrentValue;
		$this->PayrollID->ViewCustomAttributes = "";

		// PensionerID
		if (strval($this->PensionerID->CurrentValue) <> "") {
			$sFilterWrk = "`PensionerID`" . ew_SearchString("=", $this->PensionerID->CurrentValue, EW_DATATYPE_STRING);
		$sSqlWrk = "SELECT `PensionerID`, `lastname` AS `DispFld`, `firstname` AS `Disp2Fld`, `middlename` AS `Disp3Fld`, `extname` AS `Disp4Fld` FROM `tbl_pensioner`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->PensionerID, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `lastname` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->PensionerID->ViewValue = $rswrk->fields('DispFld');
				$this->PensionerID->ViewValue .= ew_ValueSeparator(1,$this->PensionerID) . $rswrk->fields('Disp2Fld');
				$this->PensionerID->ViewValue .= ew_ValueSeparator(2,$this->PensionerID) . $rswrk->fields('Disp3Fld');
				$this->PensionerID->ViewValue .= ew_ValueSeparator(3,$this->PensionerID) . $rswrk->fields('Disp4Fld');
				$rswrk->Close();
			} else {
				$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			}
		} else {
			$this->PensionerID->ViewValue = NULL;
		}
		$this->PensionerID->ViewCustomAttributes = "";

		// PayrollYear
		if (strval($this->PayrollYear->CurrentValue) <> "") {
			$sFilterWrk = "`Year`" . ew_SearchString("=", $this->PayrollYear->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `Year`, `Year` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_year`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->PayrollYear, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `Year` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->PayrollYear->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->PayrollYear->ViewValue = $this->PayrollYear->CurrentValue;
			}
		} else {
			$this->PayrollYear->ViewValue = NULL;
		}
		$this->PayrollYear->ViewCustomAttributes = "";

		// cMonth
		if (strval($this->cMonth->CurrentValue) <> "") {
			$sFilterWrk = "`MonthID`" . ew_SearchString("=", $this->cMonth->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `MonthID`, `desc` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_month`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->cMonth, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `MonthID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->cMonth->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->cMonth->ViewValue = $this->cMonth->CurrentValue;
			}
		} else {
			$this->cMonth->ViewValue = NULL;
		}
		$this->cMonth->ViewCustomAttributes = "";

		// amount
		$this->amount->ViewValue = $this->amount->CurrentValue;
		$this->amount->ViewCustomAttributes = "";

		// paymentmodeID
		if (strval($this->paymentmodeID->CurrentValue) <> "") {
			$sFilterWrk = "`paymentmodeID`" . ew_SearchString("=", $this->paymentmodeID->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `paymentmodeID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_paymentmode`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->paymentmodeID, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `paymentmodeID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->paymentmodeID->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->paymentmodeID->ViewValue = $this->paymentmodeID->CurrentValue;
			}
		} else {
			$this->paymentmodeID->ViewValue = NULL;
		}
		$this->paymentmodeID->ViewCustomAttributes = "";

		// Approved
		if (strval($this->Approved->CurrentValue) <> "") {
			switch ($this->Approved->CurrentValue) {
				case $this->Approved->FldTagValue(1):
					$this->Approved->ViewValue = $this->Approved->FldTagCaption(1) <> "" ? $this->Approved->FldTagCaption(1) : $this->Approved->CurrentValue;
					break;
				case $this->Approved->FldTagValue(2):
					$this->Approved->ViewValue = $this->Approved->FldTagCaption(2) <> "" ? $this->Approved->FldTagCaption(2) : $this->Approved->CurrentValue;
					break;
				default:
					$this->Approved->ViewValue = $this->Approved->CurrentValue;
			}
		} else {
			$this->Approved->ViewValue = NULL;
		}
		$this->Approved->ViewCustomAttributes = "";

		// Claimed
		if (strval($this->Claimed->CurrentValue) <> "") {
			switch ($this->Claimed->CurrentValue) {
				case $this->Claimed->FldTagValue(1):
					$this->Claimed->ViewValue = $this->Claimed->FldTagCaption(1) <> "" ? $this->Claimed->FldTagCaption(1) : $this->Claimed->CurrentValue;
					break;
				case $this->Claimed->FldTagValue(2):
					$this->Claimed->ViewValue = $this->Claimed->FldTagCaption(2) <> "" ? $this->Claimed->FldTagCaption(2) : $this->Claimed->CurrentValue;
					break;
				default:
					$this->Claimed->ViewValue = $this->Claimed->CurrentValue;
			}
		} else {
			$this->Claimed->ViewValue = NULL;
		}
		$this->Claimed->ViewCustomAttributes = "";

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

		// PayrollID
		$this->PayrollID->LinkCustomAttributes = "";
		$this->PayrollID->HrefValue = "";
		$this->PayrollID->TooltipValue = "";

		// PensionerID
		$this->PensionerID->LinkCustomAttributes = "";
		$this->PensionerID->HrefValue = "";
		$this->PensionerID->TooltipValue = "";

		// PayrollYear
		$this->PayrollYear->LinkCustomAttributes = "";
		$this->PayrollYear->HrefValue = "";
		$this->PayrollYear->TooltipValue = "";

		// cMonth
		$this->cMonth->LinkCustomAttributes = "";
		$this->cMonth->HrefValue = "";
		$this->cMonth->TooltipValue = "";

		// amount
		$this->amount->LinkCustomAttributes = "";
		$this->amount->HrefValue = "";
		$this->amount->TooltipValue = "";

		// paymentmodeID
		$this->paymentmodeID->LinkCustomAttributes = "";
		$this->paymentmodeID->HrefValue = "";
		$this->paymentmodeID->TooltipValue = "";

		// Approved
		$this->Approved->LinkCustomAttributes = "";
		$this->Approved->HrefValue = "";
		$this->Approved->TooltipValue = "";

		// Claimed
		$this->Claimed->LinkCustomAttributes = "";
		$this->Claimed->HrefValue = "";
		$this->Claimed->TooltipValue = "";

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
				if ($this->PayrollID->Exportable) $Doc->ExportCaption($this->PayrollID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->PayrollYear->Exportable) $Doc->ExportCaption($this->PayrollYear);
				if ($this->cMonth->Exportable) $Doc->ExportCaption($this->cMonth);
				if ($this->amount->Exportable) $Doc->ExportCaption($this->amount);
				if ($this->paymentmodeID->Exportable) $Doc->ExportCaption($this->paymentmodeID);
				if ($this->Approved->Exportable) $Doc->ExportCaption($this->Approved);
				if ($this->Claimed->Exportable) $Doc->ExportCaption($this->Claimed);
				if ($this->Createdby->Exportable) $Doc->ExportCaption($this->Createdby);
				if ($this->CreatedDate->Exportable) $Doc->ExportCaption($this->CreatedDate);
				if ($this->UpdatedBy->Exportable) $Doc->ExportCaption($this->UpdatedBy);
				if ($this->UpdatedDate->Exportable) $Doc->ExportCaption($this->UpdatedDate);
			} else {
				if ($this->PayrollID->Exportable) $Doc->ExportCaption($this->PayrollID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->PayrollYear->Exportable) $Doc->ExportCaption($this->PayrollYear);
				if ($this->amount->Exportable) $Doc->ExportCaption($this->amount);
				if ($this->paymentmodeID->Exportable) $Doc->ExportCaption($this->paymentmodeID);
				if ($this->Approved->Exportable) $Doc->ExportCaption($this->Approved);
				if ($this->Claimed->Exportable) $Doc->ExportCaption($this->Claimed);
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
					if ($this->PayrollID->Exportable) $Doc->ExportField($this->PayrollID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->PayrollYear->Exportable) $Doc->ExportField($this->PayrollYear);
					if ($this->cMonth->Exportable) $Doc->ExportField($this->cMonth);
					if ($this->amount->Exportable) $Doc->ExportField($this->amount);
					if ($this->paymentmodeID->Exportable) $Doc->ExportField($this->paymentmodeID);
					if ($this->Approved->Exportable) $Doc->ExportField($this->Approved);
					if ($this->Claimed->Exportable) $Doc->ExportField($this->Claimed);
					if ($this->Createdby->Exportable) $Doc->ExportField($this->Createdby);
					if ($this->CreatedDate->Exportable) $Doc->ExportField($this->CreatedDate);
					if ($this->UpdatedBy->Exportable) $Doc->ExportField($this->UpdatedBy);
					if ($this->UpdatedDate->Exportable) $Doc->ExportField($this->UpdatedDate);
				} else {
					if ($this->PayrollID->Exportable) $Doc->ExportField($this->PayrollID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->PayrollYear->Exportable) $Doc->ExportField($this->PayrollYear);
					if ($this->amount->Exportable) $Doc->ExportField($this->amount);
					if ($this->paymentmodeID->Exportable) $Doc->ExportField($this->paymentmodeID);
					if ($this->Approved->Exportable) $Doc->ExportField($this->Approved);
					if ($this->Claimed->Exportable) $Doc->ExportField($this->Claimed);
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
