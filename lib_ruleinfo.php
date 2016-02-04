<?php

// Global variable for table object
$lib_rule = NULL;

//
// Table class for lib_rule
//
class clib_rule extends cTable {
	var $ruleID;
	var $rule_age;
	var $rule_affiliation;
	var $rule_active;
	var $created_by;
	var $date_created;
	var $modified_by;
	var $date_modified;
	var $DELETED;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'lib_rule';
		$this->TableName = 'lib_rule';
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

		// ruleID
		$this->ruleID = new cField('lib_rule', 'lib_rule', 'x_ruleID', 'ruleID', '`ruleID`', '`ruleID`', 3, -1, FALSE, '`ruleID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->ruleID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['ruleID'] = &$this->ruleID;

		// rule_age
		$this->rule_age = new cField('lib_rule', 'lib_rule', 'x_rule_age', 'rule_age', '`rule_age`', '`rule_age`', 3, -1, FALSE, '`rule_age`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->rule_age->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['rule_age'] = &$this->rule_age;

		// rule_affiliation
		$this->rule_affiliation = new cField('lib_rule', 'lib_rule', 'x_rule_affiliation', 'rule_affiliation', '`rule_affiliation`', '`rule_affiliation`', 3, -1, FALSE, '`rule_affiliation`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->rule_affiliation->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['rule_affiliation'] = &$this->rule_affiliation;

		// rule_active
		$this->rule_active = new cField('lib_rule', 'lib_rule', 'x_rule_active', 'rule_active', '`rule_active`', '`rule_active`', 16, -1, FALSE, '`rule_active`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->rule_active->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['rule_active'] = &$this->rule_active;

		// created_by
		$this->created_by = new cField('lib_rule', 'lib_rule', 'x_created_by', 'created_by', '`created_by`', '`created_by`', 3, -1, FALSE, '`created_by`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->created_by->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['created_by'] = &$this->created_by;

		// date_created
		$this->date_created = new cField('lib_rule', 'lib_rule', 'x_date_created', 'date_created', '`date_created`', 'DATE_FORMAT(`date_created`, \'%m/%d/%Y\')', 135, 6, FALSE, '`date_created`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->date_created->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['date_created'] = &$this->date_created;

		// modified_by
		$this->modified_by = new cField('lib_rule', 'lib_rule', 'x_modified_by', 'modified_by', '`modified_by`', '`modified_by`', 3, -1, FALSE, '`modified_by`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->modified_by->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['modified_by'] = &$this->modified_by;

		// date_modified
		$this->date_modified = new cField('lib_rule', 'lib_rule', 'x_date_modified', 'date_modified', '`date_modified`', 'DATE_FORMAT(`date_modified`, \'%m/%d/%Y\')', 135, 6, FALSE, '`date_modified`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->date_modified->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['date_modified'] = &$this->date_modified;

		// DELETED
		$this->DELETED = new cField('lib_rule', 'lib_rule', 'x_DELETED', 'DELETED', '`DELETED`', '`DELETED`', 16, -1, FALSE, '`DELETED`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->DELETED->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['DELETED'] = &$this->DELETED;
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
		return "`lib_rule`";
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
	var $UpdateTable = "`lib_rule`";

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
			if (array_key_exists('ruleID', $rs))
				ew_AddFilter($where, ew_QuotedName('ruleID') . '=' . ew_QuotedValue($rs['ruleID'], $this->ruleID->FldDataType));
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
		return "`ruleID` = @ruleID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->ruleID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@ruleID@", ew_AdjustSql($this->ruleID->CurrentValue), $sKeyFilter); // Replace key value
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
			return "lib_rulelist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "lib_rulelist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("lib_ruleview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("lib_ruleview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "lib_ruleadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("lib_ruleedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("lib_ruleadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("lib_ruledelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->ruleID->CurrentValue)) {
			$sUrl .= "ruleID=" . urlencode($this->ruleID->CurrentValue);
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
			$arKeys[] = @$_GET["ruleID"]; // ruleID

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
			$this->ruleID->CurrentValue = $key;
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
		$this->ruleID->setDbValue($rs->fields('ruleID'));
		$this->rule_age->setDbValue($rs->fields('rule_age'));
		$this->rule_affiliation->setDbValue($rs->fields('rule_affiliation'));
		$this->rule_active->setDbValue($rs->fields('rule_active'));
		$this->created_by->setDbValue($rs->fields('created_by'));
		$this->date_created->setDbValue($rs->fields('date_created'));
		$this->modified_by->setDbValue($rs->fields('modified_by'));
		$this->date_modified->setDbValue($rs->fields('date_modified'));
		$this->DELETED->setDbValue($rs->fields('DELETED'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// ruleID
		// rule_age
		// rule_affiliation
		// rule_active
		// created_by
		// date_created
		// modified_by
		// date_modified
		// DELETED
		// ruleID

		$this->ruleID->ViewValue = $this->ruleID->CurrentValue;
		$this->ruleID->ViewCustomAttributes = "";

		// rule_age
		$this->rule_age->ViewValue = $this->rule_age->CurrentValue;
		$this->rule_age->ViewCustomAttributes = "";

		// rule_affiliation
		$this->rule_affiliation->ViewValue = $this->rule_affiliation->CurrentValue;
		$this->rule_affiliation->ViewCustomAttributes = "";

		// rule_active
		$this->rule_active->ViewValue = $this->rule_active->CurrentValue;
		$this->rule_active->ViewCustomAttributes = "";

		// created_by
		$this->created_by->ViewValue = $this->created_by->CurrentValue;
		$this->created_by->ViewCustomAttributes = "";

		// date_created
		$this->date_created->ViewValue = $this->date_created->CurrentValue;
		$this->date_created->ViewValue = ew_FormatDateTime($this->date_created->ViewValue, 6);
		$this->date_created->ViewCustomAttributes = "";

		// modified_by
		$this->modified_by->ViewValue = $this->modified_by->CurrentValue;
		$this->modified_by->ViewCustomAttributes = "";

		// date_modified
		$this->date_modified->ViewValue = $this->date_modified->CurrentValue;
		$this->date_modified->ViewValue = ew_FormatDateTime($this->date_modified->ViewValue, 6);
		$this->date_modified->ViewCustomAttributes = "";

		// DELETED
		$this->DELETED->ViewValue = $this->DELETED->CurrentValue;
		$this->DELETED->ViewCustomAttributes = "";

		// ruleID
		$this->ruleID->LinkCustomAttributes = "";
		$this->ruleID->HrefValue = "";
		$this->ruleID->TooltipValue = "";

		// rule_age
		$this->rule_age->LinkCustomAttributes = "";
		$this->rule_age->HrefValue = "";
		$this->rule_age->TooltipValue = "";

		// rule_affiliation
		$this->rule_affiliation->LinkCustomAttributes = "";
		$this->rule_affiliation->HrefValue = "";
		$this->rule_affiliation->TooltipValue = "";

		// rule_active
		$this->rule_active->LinkCustomAttributes = "";
		$this->rule_active->HrefValue = "";
		$this->rule_active->TooltipValue = "";

		// created_by
		$this->created_by->LinkCustomAttributes = "";
		$this->created_by->HrefValue = "";
		$this->created_by->TooltipValue = "";

		// date_created
		$this->date_created->LinkCustomAttributes = "";
		$this->date_created->HrefValue = "";
		$this->date_created->TooltipValue = "";

		// modified_by
		$this->modified_by->LinkCustomAttributes = "";
		$this->modified_by->HrefValue = "";
		$this->modified_by->TooltipValue = "";

		// date_modified
		$this->date_modified->LinkCustomAttributes = "";
		$this->date_modified->HrefValue = "";
		$this->date_modified->TooltipValue = "";

		// DELETED
		$this->DELETED->LinkCustomAttributes = "";
		$this->DELETED->HrefValue = "";
		$this->DELETED->TooltipValue = "";

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
				if ($this->ruleID->Exportable) $Doc->ExportCaption($this->ruleID);
				if ($this->rule_age->Exportable) $Doc->ExportCaption($this->rule_age);
				if ($this->rule_affiliation->Exportable) $Doc->ExportCaption($this->rule_affiliation);
				if ($this->rule_active->Exportable) $Doc->ExportCaption($this->rule_active);
				if ($this->created_by->Exportable) $Doc->ExportCaption($this->created_by);
				if ($this->date_created->Exportable) $Doc->ExportCaption($this->date_created);
				if ($this->modified_by->Exportable) $Doc->ExportCaption($this->modified_by);
				if ($this->date_modified->Exportable) $Doc->ExportCaption($this->date_modified);
				if ($this->DELETED->Exportable) $Doc->ExportCaption($this->DELETED);
			} else {
				if ($this->ruleID->Exportable) $Doc->ExportCaption($this->ruleID);
				if ($this->rule_age->Exportable) $Doc->ExportCaption($this->rule_age);
				if ($this->rule_affiliation->Exportable) $Doc->ExportCaption($this->rule_affiliation);
				if ($this->rule_active->Exportable) $Doc->ExportCaption($this->rule_active);
				if ($this->created_by->Exportable) $Doc->ExportCaption($this->created_by);
				if ($this->date_created->Exportable) $Doc->ExportCaption($this->date_created);
				if ($this->modified_by->Exportable) $Doc->ExportCaption($this->modified_by);
				if ($this->date_modified->Exportable) $Doc->ExportCaption($this->date_modified);
				if ($this->DELETED->Exportable) $Doc->ExportCaption($this->DELETED);
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
					if ($this->ruleID->Exportable) $Doc->ExportField($this->ruleID);
					if ($this->rule_age->Exportable) $Doc->ExportField($this->rule_age);
					if ($this->rule_affiliation->Exportable) $Doc->ExportField($this->rule_affiliation);
					if ($this->rule_active->Exportable) $Doc->ExportField($this->rule_active);
					if ($this->created_by->Exportable) $Doc->ExportField($this->created_by);
					if ($this->date_created->Exportable) $Doc->ExportField($this->date_created);
					if ($this->modified_by->Exportable) $Doc->ExportField($this->modified_by);
					if ($this->date_modified->Exportable) $Doc->ExportField($this->date_modified);
					if ($this->DELETED->Exportable) $Doc->ExportField($this->DELETED);
				} else {
					if ($this->ruleID->Exportable) $Doc->ExportField($this->ruleID);
					if ($this->rule_age->Exportable) $Doc->ExportField($this->rule_age);
					if ($this->rule_affiliation->Exportable) $Doc->ExportField($this->rule_affiliation);
					if ($this->rule_active->Exportable) $Doc->ExportField($this->rule_active);
					if ($this->created_by->Exportable) $Doc->ExportField($this->created_by);
					if ($this->date_created->Exportable) $Doc->ExportField($this->date_created);
					if ($this->modified_by->Exportable) $Doc->ExportField($this->modified_by);
					if ($this->date_modified->Exportable) $Doc->ExportField($this->date_modified);
					if ($this->DELETED->Exportable) $Doc->ExportField($this->DELETED);
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
