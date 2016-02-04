<?php

// Global variable for table object
$tbl_support = NULL;

//
// Table class for tbl_support
//
class ctbl_support extends cTable {
	var $supportID;
	var $PensionerID;
	var $family_support;
	var $KindSupID;
	var $meals;
	var $disability;
	var $disabilityID;
	var $immobile;
	var $assistiveID;
	var $preEx_illness;
	var $illnessID;
	var $physconditionID;
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
		$this->TableVar = 'tbl_support';
		$this->TableName = 'tbl_support';
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

		// supportID
		$this->supportID = new cField('tbl_support', 'tbl_support', 'x_supportID', 'supportID', '`supportID`', '`supportID`', 3, -1, FALSE, '`supportID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->supportID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['supportID'] = &$this->supportID;

		// PensionerID
		$this->PensionerID = new cField('tbl_support', 'tbl_support', 'x_PensionerID', 'PensionerID', '`PensionerID`', '`PensionerID`', 200, -1, FALSE, '`PensionerID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['PensionerID'] = &$this->PensionerID;

		// family_support
		$this->family_support = new cField('tbl_support', 'tbl_support', 'x_family_support', 'family_support', '`family_support`', '`family_support`', 3, -1, FALSE, '`family_support`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->family_support->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['family_support'] = &$this->family_support;

		// KindSupID
		$this->KindSupID = new cField('tbl_support', 'tbl_support', 'x_KindSupID', 'KindSupID', '`KindSupID`', '`KindSupID`', 3, -1, FALSE, '`KindSupID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->KindSupID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['KindSupID'] = &$this->KindSupID;

		// meals
		$this->meals = new cField('tbl_support', 'tbl_support', 'x_meals', 'meals', '`meals`', '`meals`', 16, -1, FALSE, '`meals`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->meals->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['meals'] = &$this->meals;

		// disability
		$this->disability = new cField('tbl_support', 'tbl_support', 'x_disability', 'disability', '`disability`', '`disability`', 16, -1, FALSE, '`disability`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->disability->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['disability'] = &$this->disability;

		// disabilityID
		$this->disabilityID = new cField('tbl_support', 'tbl_support', 'x_disabilityID', 'disabilityID', '`disabilityID`', '`disabilityID`', 3, -1, FALSE, '`disabilityID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->disabilityID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['disabilityID'] = &$this->disabilityID;

		// immobile
		$this->immobile = new cField('tbl_support', 'tbl_support', 'x_immobile', 'immobile', '`immobile`', '`immobile`', 16, -1, FALSE, '`immobile`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->immobile->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['immobile'] = &$this->immobile;

		// assistiveID
		$this->assistiveID = new cField('tbl_support', 'tbl_support', 'x_assistiveID', 'assistiveID', '`assistiveID`', '`assistiveID`', 3, -1, FALSE, '`assistiveID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->assistiveID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['assistiveID'] = &$this->assistiveID;

		// preEx_illness
		$this->preEx_illness = new cField('tbl_support', 'tbl_support', 'x_preEx_illness', 'preEx_illness', '`preEx_illness`', '`preEx_illness`', 16, -1, FALSE, '`preEx_illness`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->preEx_illness->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['preEx_illness'] = &$this->preEx_illness;

		// illnessID
		$this->illnessID = new cField('tbl_support', 'tbl_support', 'x_illnessID', 'illnessID', '`illnessID`', '`illnessID`', 3, -1, FALSE, '`illnessID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->illnessID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['illnessID'] = &$this->illnessID;

		// physconditionID
		$this->physconditionID = new cField('tbl_support', 'tbl_support', 'x_physconditionID', 'physconditionID', '`physconditionID`', '`physconditionID`', 3, -1, FALSE, '`physconditionID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->physconditionID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['physconditionID'] = &$this->physconditionID;

		// CreatedBy
		$this->CreatedBy = new cField('tbl_support', 'tbl_support', 'x_CreatedBy', 'CreatedBy', '`CreatedBy`', '`CreatedBy`', 3, -1, FALSE, '`CreatedBy`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->CreatedBy->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['CreatedBy'] = &$this->CreatedBy;

		// CreatedDate
		$this->CreatedDate = new cField('tbl_support', 'tbl_support', 'x_CreatedDate', 'CreatedDate', '`CreatedDate`', 'DATE_FORMAT(`CreatedDate`, \'%m/%d/%Y\')', 135, 6, FALSE, '`CreatedDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->CreatedDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['CreatedDate'] = &$this->CreatedDate;

		// UpdatedBy
		$this->UpdatedBy = new cField('tbl_support', 'tbl_support', 'x_UpdatedBy', 'UpdatedBy', '`UpdatedBy`', '`UpdatedBy`', 3, -1, FALSE, '`UpdatedBy`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->UpdatedBy->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['UpdatedBy'] = &$this->UpdatedBy;

		// UpdatedDate
		$this->UpdatedDate = new cField('tbl_support', 'tbl_support', 'x_UpdatedDate', 'UpdatedDate', '`UpdatedDate`', 'DATE_FORMAT(`UpdatedDate`, \'%m/%d/%Y\')', 135, 6, FALSE, '`UpdatedDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
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
		return "`tbl_support`";
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
	var $UpdateTable = "`tbl_support`";

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
			if (array_key_exists('supportID', $rs))
				ew_AddFilter($where, ew_QuotedName('supportID') . '=' . ew_QuotedValue($rs['supportID'], $this->supportID->FldDataType));
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
		return "`supportID` = @supportID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->supportID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@supportID@", ew_AdjustSql($this->supportID->CurrentValue), $sKeyFilter); // Replace key value
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
			return "tbl_supportlist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "tbl_supportlist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("tbl_supportview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("tbl_supportview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "tbl_supportadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("tbl_supportedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("tbl_supportadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("tbl_supportdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->supportID->CurrentValue)) {
			$sUrl .= "supportID=" . urlencode($this->supportID->CurrentValue);
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
			$arKeys[] = @$_GET["supportID"]; // supportID

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
			$this->supportID->CurrentValue = $key;
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
		$this->supportID->setDbValue($rs->fields('supportID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->family_support->setDbValue($rs->fields('family_support'));
		$this->KindSupID->setDbValue($rs->fields('KindSupID'));
		$this->meals->setDbValue($rs->fields('meals'));
		$this->disability->setDbValue($rs->fields('disability'));
		$this->disabilityID->setDbValue($rs->fields('disabilityID'));
		$this->immobile->setDbValue($rs->fields('immobile'));
		$this->assistiveID->setDbValue($rs->fields('assistiveID'));
		$this->preEx_illness->setDbValue($rs->fields('preEx_illness'));
		$this->illnessID->setDbValue($rs->fields('illnessID'));
		$this->physconditionID->setDbValue($rs->fields('physconditionID'));
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
		// supportID
		// PensionerID
		// family_support
		// KindSupID
		// meals
		// disability
		// disabilityID
		// immobile
		// assistiveID
		// preEx_illness
		// illnessID
		// physconditionID
		// CreatedBy
		// CreatedDate
		// UpdatedBy
		// UpdatedDate
		// supportID

		$this->supportID->ViewValue = $this->supportID->CurrentValue;
		$this->supportID->ViewCustomAttributes = "";

		// PensionerID
		$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
		$this->PensionerID->ViewCustomAttributes = "";

		// family_support
		if (strval($this->family_support->CurrentValue) <> "") {
			switch ($this->family_support->CurrentValue) {
				case $this->family_support->FldTagValue(1):
					$this->family_support->ViewValue = $this->family_support->FldTagCaption(1) <> "" ? $this->family_support->FldTagCaption(1) : $this->family_support->CurrentValue;
					break;
				case $this->family_support->FldTagValue(2):
					$this->family_support->ViewValue = $this->family_support->FldTagCaption(2) <> "" ? $this->family_support->FldTagCaption(2) : $this->family_support->CurrentValue;
					break;
				default:
					$this->family_support->ViewValue = $this->family_support->CurrentValue;
			}
		} else {
			$this->family_support->ViewValue = NULL;
		}
		$this->family_support->ViewCustomAttributes = "";

		// KindSupID
		if (strval($this->KindSupID->CurrentValue) <> "") {
			$sFilterWrk = "`SupportID`" . ew_SearchString("=", $this->KindSupID->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_support`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->KindSupID, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `SupportID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->KindSupID->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->KindSupID->ViewValue = $this->KindSupID->CurrentValue;
			}
		} else {
			$this->KindSupID->ViewValue = NULL;
		}
		$this->KindSupID->ViewCustomAttributes = "";

		// meals
		$this->meals->ViewValue = $this->meals->CurrentValue;
		$this->meals->ViewCustomAttributes = "";

		// disability
		if (strval($this->disability->CurrentValue) <> "") {
			switch ($this->disability->CurrentValue) {
				case $this->disability->FldTagValue(1):
					$this->disability->ViewValue = $this->disability->FldTagCaption(1) <> "" ? $this->disability->FldTagCaption(1) : $this->disability->CurrentValue;
					break;
				case $this->disability->FldTagValue(2):
					$this->disability->ViewValue = $this->disability->FldTagCaption(2) <> "" ? $this->disability->FldTagCaption(2) : $this->disability->CurrentValue;
					break;
				default:
					$this->disability->ViewValue = $this->disability->CurrentValue;
			}
		} else {
			$this->disability->ViewValue = NULL;
		}
		$this->disability->ViewCustomAttributes = "";

		// disabilityID
		if (strval($this->disabilityID->CurrentValue) <> "") {
			$sFilterWrk = "`disabilityID`" . ew_SearchString("=", $this->disabilityID->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_disability`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->disabilityID, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `disabilityID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->disabilityID->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->disabilityID->ViewValue = $this->disabilityID->CurrentValue;
			}
		} else {
			$this->disabilityID->ViewValue = NULL;
		}
		$this->disabilityID->ViewCustomAttributes = "";

		// immobile
		if (strval($this->immobile->CurrentValue) <> "") {
			switch ($this->immobile->CurrentValue) {
				case $this->immobile->FldTagValue(1):
					$this->immobile->ViewValue = $this->immobile->FldTagCaption(1) <> "" ? $this->immobile->FldTagCaption(1) : $this->immobile->CurrentValue;
					break;
				case $this->immobile->FldTagValue(2):
					$this->immobile->ViewValue = $this->immobile->FldTagCaption(2) <> "" ? $this->immobile->FldTagCaption(2) : $this->immobile->CurrentValue;
					break;
				default:
					$this->immobile->ViewValue = $this->immobile->CurrentValue;
			}
		} else {
			$this->immobile->ViewValue = NULL;
		}
		$this->immobile->ViewCustomAttributes = "";

		// assistiveID
		if (strval($this->assistiveID->CurrentValue) <> "") {
			$sFilterWrk = "`assistiveID`" . ew_SearchString("=", $this->assistiveID->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_assistive`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->assistiveID, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `assistiveID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->assistiveID->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->assistiveID->ViewValue = $this->assistiveID->CurrentValue;
			}
		} else {
			$this->assistiveID->ViewValue = NULL;
		}
		$this->assistiveID->ViewCustomAttributes = "";

		// preEx_illness
		if (strval($this->preEx_illness->CurrentValue) <> "") {
			switch ($this->preEx_illness->CurrentValue) {
				case $this->preEx_illness->FldTagValue(1):
					$this->preEx_illness->ViewValue = $this->preEx_illness->FldTagCaption(1) <> "" ? $this->preEx_illness->FldTagCaption(1) : $this->preEx_illness->CurrentValue;
					break;
				case $this->preEx_illness->FldTagValue(2):
					$this->preEx_illness->ViewValue = $this->preEx_illness->FldTagCaption(2) <> "" ? $this->preEx_illness->FldTagCaption(2) : $this->preEx_illness->CurrentValue;
					break;
				default:
					$this->preEx_illness->ViewValue = $this->preEx_illness->CurrentValue;
			}
		} else {
			$this->preEx_illness->ViewValue = NULL;
		}
		$this->preEx_illness->ViewCustomAttributes = "";

		// illnessID
		if (strval($this->illnessID->CurrentValue) <> "") {
			$sFilterWrk = "`illnessID`" . ew_SearchString("=", $this->illnessID->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_illness`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->illnessID, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `illnessID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->illnessID->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->illnessID->ViewValue = $this->illnessID->CurrentValue;
			}
		} else {
			$this->illnessID->ViewValue = NULL;
		}
		$this->illnessID->ViewCustomAttributes = "";

		// physconditionID
		if (strval($this->physconditionID->CurrentValue) <> "") {
			$sFilterWrk = "`physconditionID`" . ew_SearchString("=", $this->physconditionID->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_physical_condition`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->physconditionID, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `physconditionID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->physconditionID->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->physconditionID->ViewValue = $this->physconditionID->CurrentValue;
			}
		} else {
			$this->physconditionID->ViewValue = NULL;
		}
		$this->physconditionID->ViewCustomAttributes = "";

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

		// supportID
		$this->supportID->LinkCustomAttributes = "";
		$this->supportID->HrefValue = "";
		$this->supportID->TooltipValue = "";

		// PensionerID
		$this->PensionerID->LinkCustomAttributes = "";
		$this->PensionerID->HrefValue = "";
		$this->PensionerID->TooltipValue = "";

		// family_support
		$this->family_support->LinkCustomAttributes = "";
		$this->family_support->HrefValue = "";
		$this->family_support->TooltipValue = "";

		// KindSupID
		$this->KindSupID->LinkCustomAttributes = "";
		$this->KindSupID->HrefValue = "";
		$this->KindSupID->TooltipValue = "";

		// meals
		$this->meals->LinkCustomAttributes = "";
		$this->meals->HrefValue = "";
		$this->meals->TooltipValue = "";

		// disability
		$this->disability->LinkCustomAttributes = "";
		$this->disability->HrefValue = "";
		$this->disability->TooltipValue = "";

		// disabilityID
		$this->disabilityID->LinkCustomAttributes = "";
		$this->disabilityID->HrefValue = "";
		$this->disabilityID->TooltipValue = "";

		// immobile
		$this->immobile->LinkCustomAttributes = "";
		$this->immobile->HrefValue = "";
		$this->immobile->TooltipValue = "";

		// assistiveID
		$this->assistiveID->LinkCustomAttributes = "";
		$this->assistiveID->HrefValue = "";
		$this->assistiveID->TooltipValue = "";

		// preEx_illness
		$this->preEx_illness->LinkCustomAttributes = "";
		$this->preEx_illness->HrefValue = "";
		$this->preEx_illness->TooltipValue = "";

		// illnessID
		$this->illnessID->LinkCustomAttributes = "";
		$this->illnessID->HrefValue = "";
		$this->illnessID->TooltipValue = "";

		// physconditionID
		$this->physconditionID->LinkCustomAttributes = "";
		$this->physconditionID->HrefValue = "";
		$this->physconditionID->TooltipValue = "";

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
				if ($this->supportID->Exportable) $Doc->ExportCaption($this->supportID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->family_support->Exportable) $Doc->ExportCaption($this->family_support);
				if ($this->KindSupID->Exportable) $Doc->ExportCaption($this->KindSupID);
				if ($this->meals->Exportable) $Doc->ExportCaption($this->meals);
				if ($this->disability->Exportable) $Doc->ExportCaption($this->disability);
				if ($this->disabilityID->Exportable) $Doc->ExportCaption($this->disabilityID);
				if ($this->immobile->Exportable) $Doc->ExportCaption($this->immobile);
				if ($this->assistiveID->Exportable) $Doc->ExportCaption($this->assistiveID);
				if ($this->preEx_illness->Exportable) $Doc->ExportCaption($this->preEx_illness);
				if ($this->illnessID->Exportable) $Doc->ExportCaption($this->illnessID);
				if ($this->physconditionID->Exportable) $Doc->ExportCaption($this->physconditionID);
				if ($this->CreatedBy->Exportable) $Doc->ExportCaption($this->CreatedBy);
				if ($this->CreatedDate->Exportable) $Doc->ExportCaption($this->CreatedDate);
				if ($this->UpdatedBy->Exportable) $Doc->ExportCaption($this->UpdatedBy);
				if ($this->UpdatedDate->Exportable) $Doc->ExportCaption($this->UpdatedDate);
			} else {
				if ($this->supportID->Exportable) $Doc->ExportCaption($this->supportID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->family_support->Exportable) $Doc->ExportCaption($this->family_support);
				if ($this->KindSupID->Exportable) $Doc->ExportCaption($this->KindSupID);
				if ($this->meals->Exportable) $Doc->ExportCaption($this->meals);
				if ($this->disability->Exportable) $Doc->ExportCaption($this->disability);
				if ($this->disabilityID->Exportable) $Doc->ExportCaption($this->disabilityID);
				if ($this->immobile->Exportable) $Doc->ExportCaption($this->immobile);
				if ($this->assistiveID->Exportable) $Doc->ExportCaption($this->assistiveID);
				if ($this->preEx_illness->Exportable) $Doc->ExportCaption($this->preEx_illness);
				if ($this->illnessID->Exportable) $Doc->ExportCaption($this->illnessID);
				if ($this->physconditionID->Exportable) $Doc->ExportCaption($this->physconditionID);
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
					if ($this->supportID->Exportable) $Doc->ExportField($this->supportID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->family_support->Exportable) $Doc->ExportField($this->family_support);
					if ($this->KindSupID->Exportable) $Doc->ExportField($this->KindSupID);
					if ($this->meals->Exportable) $Doc->ExportField($this->meals);
					if ($this->disability->Exportable) $Doc->ExportField($this->disability);
					if ($this->disabilityID->Exportable) $Doc->ExportField($this->disabilityID);
					if ($this->immobile->Exportable) $Doc->ExportField($this->immobile);
					if ($this->assistiveID->Exportable) $Doc->ExportField($this->assistiveID);
					if ($this->preEx_illness->Exportable) $Doc->ExportField($this->preEx_illness);
					if ($this->illnessID->Exportable) $Doc->ExportField($this->illnessID);
					if ($this->physconditionID->Exportable) $Doc->ExportField($this->physconditionID);
					if ($this->CreatedBy->Exportable) $Doc->ExportField($this->CreatedBy);
					if ($this->CreatedDate->Exportable) $Doc->ExportField($this->CreatedDate);
					if ($this->UpdatedBy->Exportable) $Doc->ExportField($this->UpdatedBy);
					if ($this->UpdatedDate->Exportable) $Doc->ExportField($this->UpdatedDate);
				} else {
					if ($this->supportID->Exportable) $Doc->ExportField($this->supportID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->family_support->Exportable) $Doc->ExportField($this->family_support);
					if ($this->KindSupID->Exportable) $Doc->ExportField($this->KindSupID);
					if ($this->meals->Exportable) $Doc->ExportField($this->meals);
					if ($this->disability->Exportable) $Doc->ExportField($this->disability);
					if ($this->disabilityID->Exportable) $Doc->ExportField($this->disabilityID);
					if ($this->immobile->Exportable) $Doc->ExportField($this->immobile);
					if ($this->assistiveID->Exportable) $Doc->ExportField($this->assistiveID);
					if ($this->preEx_illness->Exportable) $Doc->ExportField($this->preEx_illness);
					if ($this->illnessID->Exportable) $Doc->ExportField($this->illnessID);
					if ($this->physconditionID->Exportable) $Doc->ExportField($this->physconditionID);
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
