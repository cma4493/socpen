<?php

// Global variable for table object
$tbl_representative = NULL;

//
// Table class for tbl_representative
//
class ctbl_representative extends cTable {
	var $authID;
	var $PensionerID;
	var $fname;
	var $mname;
	var $lname;
	var $relToPensioner;
	var $ContactNo;
	var $auth_Region;
	var $auth_prov;
	var $auth_city;
	var $auth_brgy;
	var $houseNo;
	var $CreatedBy;
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
		$this->TableVar = 'tbl_representative';
		$this->TableName = 'tbl_representative';
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

		// authID
		$this->authID = new cField('tbl_representative', 'tbl_representative', 'x_authID', 'authID', '`authID`', '`authID`', 3, -1, FALSE, '`authID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->authID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['authID'] = &$this->authID;

		// PensionerID
		$this->PensionerID = new cField('tbl_representative', 'tbl_representative', 'x_PensionerID', 'PensionerID', '`PensionerID`', '`PensionerID`', 200, -1, FALSE, '`PensionerID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['PensionerID'] = &$this->PensionerID;

		// fname
		$this->fname = new cField('tbl_representative', 'tbl_representative', 'x_fname', 'fname', '`fname`', '`fname`', 200, -1, FALSE, '`fname`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['fname'] = &$this->fname;

		// mname
		$this->mname = new cField('tbl_representative', 'tbl_representative', 'x_mname', 'mname', '`mname`', '`mname`', 200, -1, FALSE, '`mname`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['mname'] = &$this->mname;

		// lname
		$this->lname = new cField('tbl_representative', 'tbl_representative', 'x_lname', 'lname', '`lname`', '`lname`', 200, -1, FALSE, '`lname`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['lname'] = &$this->lname;

		// relToPensioner
		$this->relToPensioner = new cField('tbl_representative', 'tbl_representative', 'x_relToPensioner', 'relToPensioner', '`relToPensioner`', '`relToPensioner`', 3, -1, FALSE, '`relToPensioner`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->relToPensioner->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['relToPensioner'] = &$this->relToPensioner;

		// ContactNo
		$this->ContactNo = new cField('tbl_representative', 'tbl_representative', 'x_ContactNo', 'ContactNo', '`ContactNo`', '`ContactNo`', 200, -1, FALSE, '`ContactNo`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['ContactNo'] = &$this->ContactNo;

		// auth_Region
		$this->auth_Region = new cField('tbl_representative', 'tbl_representative', 'x_auth_Region', 'auth_Region', '`auth_Region`', '`auth_Region`', 21, -1, FALSE, '`auth_Region`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->auth_Region->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['auth_Region'] = &$this->auth_Region;

		// auth_prov
		$this->auth_prov = new cField('tbl_representative', 'tbl_representative', 'x_auth_prov', 'auth_prov', '`auth_prov`', '`auth_prov`', 21, -1, FALSE, '`auth_prov`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->auth_prov->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['auth_prov'] = &$this->auth_prov;

		// auth_city
		$this->auth_city = new cField('tbl_representative', 'tbl_representative', 'x_auth_city', 'auth_city', '`auth_city`', '`auth_city`', 21, -1, FALSE, '`auth_city`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->auth_city->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['auth_city'] = &$this->auth_city;

		// auth_brgy
		$this->auth_brgy = new cField('tbl_representative', 'tbl_representative', 'x_auth_brgy', 'auth_brgy', '`auth_brgy`', '`auth_brgy`', 21, -1, FALSE, '`auth_brgy`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->auth_brgy->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['auth_brgy'] = &$this->auth_brgy;

		// houseNo
		$this->houseNo = new cField('tbl_representative', 'tbl_representative', 'x_houseNo', 'houseNo', '`houseNo`', '`houseNo`', 200, -1, FALSE, '`houseNo`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['houseNo'] = &$this->houseNo;

		// CreatedBy
		$this->CreatedBy = new cField('tbl_representative', 'tbl_representative', 'x_CreatedBy', 'CreatedBy', '`CreatedBy`', '`CreatedBy`', 3, -1, FALSE, '`CreatedBy`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->CreatedBy->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['CreatedBy'] = &$this->CreatedBy;

		// CreatedDate
		$this->CreatedDate = new cField('tbl_representative', 'tbl_representative', 'x_CreatedDate', 'CreatedDate', '`CreatedDate`', 'DATE_FORMAT(`CreatedDate`, \'%m/%d/%Y\')', 135, 6, FALSE, '`CreatedDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->CreatedDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['CreatedDate'] = &$this->CreatedDate;

		// UpdatedBy
		$this->UpdatedBy = new cField('tbl_representative', 'tbl_representative', 'x_UpdatedBy', 'UpdatedBy', '`UpdatedBy`', '`UpdatedBy`', 3, -1, FALSE, '`UpdatedBy`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->UpdatedBy->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['UpdatedBy'] = &$this->UpdatedBy;

		// UpdatedDate
		$this->UpdatedDate = new cField('tbl_representative', 'tbl_representative', 'x_UpdatedDate', 'UpdatedDate', '`UpdatedDate`', 'DATE_FORMAT(`UpdatedDate`, \'%m/%d/%Y\')', 135, 6, FALSE, '`UpdatedDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
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
		return "`tbl_representative`";
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
	var $UpdateTable = "`tbl_representative`";

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
			if (array_key_exists('authID', $rs))
				ew_AddFilter($where, ew_QuotedName('authID') . '=' . ew_QuotedValue($rs['authID'], $this->authID->FldDataType));
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
		return "`authID` = @authID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->authID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@authID@", ew_AdjustSql($this->authID->CurrentValue), $sKeyFilter); // Replace key value
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
			return "tbl_representativelist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "tbl_representativelist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("tbl_representativeview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("tbl_representativeview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "tbl_representativeadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("tbl_representativeedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("tbl_representativeadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("tbl_representativedelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->authID->CurrentValue)) {
			$sUrl .= "authID=" . urlencode($this->authID->CurrentValue);
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
			$arKeys[] = @$_GET["authID"]; // authID

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
			$this->authID->CurrentValue = $key;
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
		$this->authID->setDbValue($rs->fields('authID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->fname->setDbValue($rs->fields('fname'));
		$this->mname->setDbValue($rs->fields('mname'));
		$this->lname->setDbValue($rs->fields('lname'));
		$this->relToPensioner->setDbValue($rs->fields('relToPensioner'));
		$this->ContactNo->setDbValue($rs->fields('ContactNo'));
		$this->auth_Region->setDbValue($rs->fields('auth_Region'));
		$this->auth_prov->setDbValue($rs->fields('auth_prov'));
		$this->auth_city->setDbValue($rs->fields('auth_city'));
		$this->auth_brgy->setDbValue($rs->fields('auth_brgy'));
		$this->houseNo->setDbValue($rs->fields('houseNo'));
		$this->CreatedBy->setDbValue($rs->fields('CreatedBy'));
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
		// authID
		// PensionerID
		// fname
		// mname
		// lname
		// relToPensioner
		// ContactNo
		// auth_Region
		// auth_prov
		// auth_city
		// auth_brgy
		// houseNo
		// CreatedBy
		// CreatedDate
		// UpdatedBy
		// UpdatedDate
		// authID

		$this->authID->ViewValue = $this->authID->CurrentValue;
		$this->authID->ViewCustomAttributes = "";

		// PensionerID
		$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
		$this->PensionerID->ViewCustomAttributes = "";

		// fname
		$this->fname->ViewValue = $this->fname->CurrentValue;
		$this->fname->ViewCustomAttributes = "";

		// mname
		$this->mname->ViewValue = $this->mname->CurrentValue;
		$this->mname->ViewCustomAttributes = "";

		// lname
		$this->lname->ViewValue = $this->lname->CurrentValue;
		$this->lname->ViewCustomAttributes = "";

		// relToPensioner
		if (strval($this->relToPensioner->CurrentValue) <> "") {
			$sFilterWrk = "`RelationID`" . ew_SearchString("=", $this->relToPensioner->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `RelationID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_relationship`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->relToPensioner, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `Description` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->relToPensioner->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->relToPensioner->ViewValue = $this->relToPensioner->CurrentValue;
			}
		} else {
			$this->relToPensioner->ViewValue = NULL;
		}
		$this->relToPensioner->ViewCustomAttributes = "";

		// ContactNo
		$this->ContactNo->ViewValue = $this->ContactNo->CurrentValue;
		$this->ContactNo->ViewCustomAttributes = "";

		// auth_Region
		if (strval($this->auth_Region->CurrentValue) <> "") {
			$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->auth_Region->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->auth_Region, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `region_code` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->auth_Region->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->auth_Region->ViewValue = $this->auth_Region->CurrentValue;
			}
		} else {
			$this->auth_Region->ViewValue = NULL;
		}
		$this->auth_Region->ViewCustomAttributes = "";

		// auth_prov
		if (strval($this->auth_prov->CurrentValue) <> "") {
			$sFilterWrk = "`prov_code`" . ew_SearchString("=", $this->auth_prov->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_provinces`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->auth_prov, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `prov_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->auth_prov->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->auth_prov->ViewValue = $this->auth_prov->CurrentValue;
			}
		} else {
			$this->auth_prov->ViewValue = NULL;
		}
		$this->auth_prov->ViewCustomAttributes = "";

		// auth_city
		if (strval($this->auth_city->CurrentValue) <> "") {
			$sFilterWrk = "`city_code`" . ew_SearchString("=", $this->auth_city->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_cities`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->auth_city, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `city_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->auth_city->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->auth_city->ViewValue = $this->auth_city->CurrentValue;
			}
		} else {
			$this->auth_city->ViewValue = NULL;
		}
		$this->auth_city->ViewCustomAttributes = "";

		// auth_brgy
		if (strval($this->auth_brgy->CurrentValue) <> "") {
			$sFilterWrk = "`brgy_code`" . ew_SearchString("=", $this->auth_brgy->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_brgy`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->auth_brgy, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `brgy_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->auth_brgy->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->auth_brgy->ViewValue = $this->auth_brgy->CurrentValue;
			}
		} else {
			$this->auth_brgy->ViewValue = NULL;
		}
		$this->auth_brgy->ViewCustomAttributes = "";

		// houseNo
		$this->houseNo->ViewValue = $this->houseNo->CurrentValue;
		$this->houseNo->ViewCustomAttributes = "";

		// CreatedBy
		$this->CreatedBy->ViewValue = $this->CreatedBy->CurrentValue;
		$this->CreatedBy->ViewCustomAttributes = "";

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

		// authID
		$this->authID->LinkCustomAttributes = "";
		$this->authID->HrefValue = "";
		$this->authID->TooltipValue = "";

		// PensionerID
		$this->PensionerID->LinkCustomAttributes = "";
		$this->PensionerID->HrefValue = "";
		$this->PensionerID->TooltipValue = "";

		// fname
		$this->fname->LinkCustomAttributes = "";
		$this->fname->HrefValue = "";
		$this->fname->TooltipValue = "";

		// mname
		$this->mname->LinkCustomAttributes = "";
		$this->mname->HrefValue = "";
		$this->mname->TooltipValue = "";

		// lname
		$this->lname->LinkCustomAttributes = "";
		$this->lname->HrefValue = "";
		$this->lname->TooltipValue = "";

		// relToPensioner
		$this->relToPensioner->LinkCustomAttributes = "";
		$this->relToPensioner->HrefValue = "";
		$this->relToPensioner->TooltipValue = "";

		// ContactNo
		$this->ContactNo->LinkCustomAttributes = "";
		$this->ContactNo->HrefValue = "";
		$this->ContactNo->TooltipValue = "";

		// auth_Region
		$this->auth_Region->LinkCustomAttributes = "";
		$this->auth_Region->HrefValue = "";
		$this->auth_Region->TooltipValue = "";

		// auth_prov
		$this->auth_prov->LinkCustomAttributes = "";
		$this->auth_prov->HrefValue = "";
		$this->auth_prov->TooltipValue = "";

		// auth_city
		$this->auth_city->LinkCustomAttributes = "";
		$this->auth_city->HrefValue = "";
		$this->auth_city->TooltipValue = "";

		// auth_brgy
		$this->auth_brgy->LinkCustomAttributes = "";
		$this->auth_brgy->HrefValue = "";
		$this->auth_brgy->TooltipValue = "";

		// houseNo
		$this->houseNo->LinkCustomAttributes = "";
		$this->houseNo->HrefValue = "";
		$this->houseNo->TooltipValue = "";

		// CreatedBy
		$this->CreatedBy->LinkCustomAttributes = "";
		$this->CreatedBy->HrefValue = "";
		$this->CreatedBy->TooltipValue = "";

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
				if ($this->authID->Exportable) $Doc->ExportCaption($this->authID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->fname->Exportable) $Doc->ExportCaption($this->fname);
				if ($this->mname->Exportable) $Doc->ExportCaption($this->mname);
				if ($this->lname->Exportable) $Doc->ExportCaption($this->lname);
				if ($this->relToPensioner->Exportable) $Doc->ExportCaption($this->relToPensioner);
				if ($this->ContactNo->Exportable) $Doc->ExportCaption($this->ContactNo);
				if ($this->auth_Region->Exportable) $Doc->ExportCaption($this->auth_Region);
				if ($this->auth_prov->Exportable) $Doc->ExportCaption($this->auth_prov);
				if ($this->auth_city->Exportable) $Doc->ExportCaption($this->auth_city);
				if ($this->auth_brgy->Exportable) $Doc->ExportCaption($this->auth_brgy);
				if ($this->houseNo->Exportable) $Doc->ExportCaption($this->houseNo);
				if ($this->CreatedBy->Exportable) $Doc->ExportCaption($this->CreatedBy);
				if ($this->CreatedDate->Exportable) $Doc->ExportCaption($this->CreatedDate);
				if ($this->UpdatedBy->Exportable) $Doc->ExportCaption($this->UpdatedBy);
				if ($this->UpdatedDate->Exportable) $Doc->ExportCaption($this->UpdatedDate);
			} else {
				if ($this->authID->Exportable) $Doc->ExportCaption($this->authID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->fname->Exportable) $Doc->ExportCaption($this->fname);
				if ($this->mname->Exportable) $Doc->ExportCaption($this->mname);
				if ($this->lname->Exportable) $Doc->ExportCaption($this->lname);
				if ($this->relToPensioner->Exportable) $Doc->ExportCaption($this->relToPensioner);
				if ($this->ContactNo->Exportable) $Doc->ExportCaption($this->ContactNo);
				if ($this->auth_Region->Exportable) $Doc->ExportCaption($this->auth_Region);
				if ($this->auth_prov->Exportable) $Doc->ExportCaption($this->auth_prov);
				if ($this->auth_city->Exportable) $Doc->ExportCaption($this->auth_city);
				if ($this->auth_brgy->Exportable) $Doc->ExportCaption($this->auth_brgy);
				if ($this->houseNo->Exportable) $Doc->ExportCaption($this->houseNo);
				if ($this->CreatedBy->Exportable) $Doc->ExportCaption($this->CreatedBy);
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
					if ($this->authID->Exportable) $Doc->ExportField($this->authID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->fname->Exportable) $Doc->ExportField($this->fname);
					if ($this->mname->Exportable) $Doc->ExportField($this->mname);
					if ($this->lname->Exportable) $Doc->ExportField($this->lname);
					if ($this->relToPensioner->Exportable) $Doc->ExportField($this->relToPensioner);
					if ($this->ContactNo->Exportable) $Doc->ExportField($this->ContactNo);
					if ($this->auth_Region->Exportable) $Doc->ExportField($this->auth_Region);
					if ($this->auth_prov->Exportable) $Doc->ExportField($this->auth_prov);
					if ($this->auth_city->Exportable) $Doc->ExportField($this->auth_city);
					if ($this->auth_brgy->Exportable) $Doc->ExportField($this->auth_brgy);
					if ($this->houseNo->Exportable) $Doc->ExportField($this->houseNo);
					if ($this->CreatedBy->Exportable) $Doc->ExportField($this->CreatedBy);
					if ($this->CreatedDate->Exportable) $Doc->ExportField($this->CreatedDate);
					if ($this->UpdatedBy->Exportable) $Doc->ExportField($this->UpdatedBy);
					if ($this->UpdatedDate->Exportable) $Doc->ExportField($this->UpdatedDate);
				} else {
					if ($this->authID->Exportable) $Doc->ExportField($this->authID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->fname->Exportable) $Doc->ExportField($this->fname);
					if ($this->mname->Exportable) $Doc->ExportField($this->mname);
					if ($this->lname->Exportable) $Doc->ExportField($this->lname);
					if ($this->relToPensioner->Exportable) $Doc->ExportField($this->relToPensioner);
					if ($this->ContactNo->Exportable) $Doc->ExportField($this->ContactNo);
					if ($this->auth_Region->Exportable) $Doc->ExportField($this->auth_Region);
					if ($this->auth_prov->Exportable) $Doc->ExportField($this->auth_prov);
					if ($this->auth_city->Exportable) $Doc->ExportField($this->auth_city);
					if ($this->auth_brgy->Exportable) $Doc->ExportField($this->auth_brgy);
					if ($this->houseNo->Exportable) $Doc->ExportField($this->houseNo);
					if ($this->CreatedBy->Exportable) $Doc->ExportField($this->CreatedBy);
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
