<?php

// Global variable for table object
$lib_cities = NULL;

//
// Table class for lib_cities
//
class clib_cities extends cTable {
	var $city_code;
	var $city_name;
	var $prov_code;
	var $district_no;
	var $district_name;
	var $is_Urban;
	var $locked;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'lib_cities';
		$this->TableName = 'lib_cities';
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

		// city_code
		$this->city_code = new cField('lib_cities', 'lib_cities', 'x_city_code', 'city_code', '`city_code`', '`city_code`', 21, -1, FALSE, '`city_code`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->city_code->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['city_code'] = &$this->city_code;

		// city_name
		$this->city_name = new cField('lib_cities', 'lib_cities', 'x_city_name', 'city_name', '`city_name`', '`city_name`', 200, -1, FALSE, '`city_name`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['city_name'] = &$this->city_name;

		// prov_code
		$this->prov_code = new cField('lib_cities', 'lib_cities', 'x_prov_code', 'prov_code', '`prov_code`', '`prov_code`', 21, -1, FALSE, '`prov_code`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->prov_code->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['prov_code'] = &$this->prov_code;

		// district_no
		$this->district_no = new cField('lib_cities', 'lib_cities', 'x_district_no', 'district_no', '`district_no`', '`district_no`', 19, -1, FALSE, '`district_no`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->district_no->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['district_no'] = &$this->district_no;

		// district_name
		$this->district_name = new cField('lib_cities', 'lib_cities', 'x_district_name', 'district_name', '`district_name`', '`district_name`', 200, -1, FALSE, '`district_name`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['district_name'] = &$this->district_name;

		// is_Urban
		$this->is_Urban = new cField('lib_cities', 'lib_cities', 'x_is_Urban', 'is_Urban', '`is_Urban`', '`is_Urban`', 16, -1, FALSE, '`is_Urban`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->is_Urban->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['is_Urban'] = &$this->is_Urban;

		// locked
		$this->locked = new cField('lib_cities', 'lib_cities', 'x_locked', 'locked', '`locked`', '`locked`', 16, -1, FALSE, '`locked`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->locked->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['locked'] = &$this->locked;
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
		return "`lib_cities`";
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
	var $UpdateTable = "`lib_cities`";

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
			if (array_key_exists('city_code', $rs))
				ew_AddFilter($where, ew_QuotedName('city_code') . '=' . ew_QuotedValue($rs['city_code'], $this->city_code->FldDataType));
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
		return "`city_code` = @city_code@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->city_code->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@city_code@", ew_AdjustSql($this->city_code->CurrentValue), $sKeyFilter); // Replace key value
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
			return "lib_citieslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "lib_citieslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("lib_citiesview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("lib_citiesview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "lib_citiesadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("lib_citiesedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("lib_citiesadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("lib_citiesdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->city_code->CurrentValue)) {
			$sUrl .= "city_code=" . urlencode($this->city_code->CurrentValue);
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
			$arKeys[] = @$_GET["city_code"]; // city_code

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
			$this->city_code->CurrentValue = $key;
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
		$this->city_code->setDbValue($rs->fields('city_code'));
		$this->city_name->setDbValue($rs->fields('city_name'));
		$this->prov_code->setDbValue($rs->fields('prov_code'));
		$this->district_no->setDbValue($rs->fields('district_no'));
		$this->district_name->setDbValue($rs->fields('district_name'));
		$this->is_Urban->setDbValue($rs->fields('is_Urban'));
		$this->locked->setDbValue($rs->fields('locked'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// city_code
		// city_name
		// prov_code
		// district_no
		// district_name
		// is_Urban
		// locked
		// city_code

		$this->city_code->ViewValue = $this->city_code->CurrentValue;
		$this->city_code->ViewCustomAttributes = "";

		// city_name
		$this->city_name->ViewValue = $this->city_name->CurrentValue;
		$this->city_name->ViewCustomAttributes = "";

		// prov_code
		$this->prov_code->ViewValue = $this->prov_code->CurrentValue;
		$this->prov_code->ViewCustomAttributes = "";

		// district_no
		$this->district_no->ViewValue = $this->district_no->CurrentValue;
		$this->district_no->ViewCustomAttributes = "";

		// district_name
		$this->district_name->ViewValue = $this->district_name->CurrentValue;
		$this->district_name->ViewCustomAttributes = "";

		// is_Urban
		$this->is_Urban->ViewValue = $this->is_Urban->CurrentValue;
		$this->is_Urban->ViewCustomAttributes = "";

		// locked
		$this->locked->ViewValue = $this->locked->CurrentValue;
		$this->locked->ViewCustomAttributes = "";

		// city_code
		$this->city_code->LinkCustomAttributes = "";
		$this->city_code->HrefValue = "";
		$this->city_code->TooltipValue = "";

		// city_name
		$this->city_name->LinkCustomAttributes = "";
		$this->city_name->HrefValue = "";
		$this->city_name->TooltipValue = "";

		// prov_code
		$this->prov_code->LinkCustomAttributes = "";
		$this->prov_code->HrefValue = "";
		$this->prov_code->TooltipValue = "";

		// district_no
		$this->district_no->LinkCustomAttributes = "";
		$this->district_no->HrefValue = "";
		$this->district_no->TooltipValue = "";

		// district_name
		$this->district_name->LinkCustomAttributes = "";
		$this->district_name->HrefValue = "";
		$this->district_name->TooltipValue = "";

		// is_Urban
		$this->is_Urban->LinkCustomAttributes = "";
		$this->is_Urban->HrefValue = "";
		$this->is_Urban->TooltipValue = "";

		// locked
		$this->locked->LinkCustomAttributes = "";
		$this->locked->HrefValue = "";
		$this->locked->TooltipValue = "";

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
				if ($this->city_code->Exportable) $Doc->ExportCaption($this->city_code);
				if ($this->city_name->Exportable) $Doc->ExportCaption($this->city_name);
				if ($this->prov_code->Exportable) $Doc->ExportCaption($this->prov_code);
				if ($this->district_no->Exportable) $Doc->ExportCaption($this->district_no);
				if ($this->district_name->Exportable) $Doc->ExportCaption($this->district_name);
				if ($this->is_Urban->Exportable) $Doc->ExportCaption($this->is_Urban);
				if ($this->locked->Exportable) $Doc->ExportCaption($this->locked);
			} else {
				if ($this->city_code->Exportable) $Doc->ExportCaption($this->city_code);
				if ($this->city_name->Exportable) $Doc->ExportCaption($this->city_name);
				if ($this->prov_code->Exportable) $Doc->ExportCaption($this->prov_code);
				if ($this->district_no->Exportable) $Doc->ExportCaption($this->district_no);
				if ($this->district_name->Exportable) $Doc->ExportCaption($this->district_name);
				if ($this->is_Urban->Exportable) $Doc->ExportCaption($this->is_Urban);
				if ($this->locked->Exportable) $Doc->ExportCaption($this->locked);
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
					if ($this->city_code->Exportable) $Doc->ExportField($this->city_code);
					if ($this->city_name->Exportable) $Doc->ExportField($this->city_name);
					if ($this->prov_code->Exportable) $Doc->ExportField($this->prov_code);
					if ($this->district_no->Exportable) $Doc->ExportField($this->district_no);
					if ($this->district_name->Exportable) $Doc->ExportField($this->district_name);
					if ($this->is_Urban->Exportable) $Doc->ExportField($this->is_Urban);
					if ($this->locked->Exportable) $Doc->ExportField($this->locked);
				} else {
					if ($this->city_code->Exportable) $Doc->ExportField($this->city_code);
					if ($this->city_name->Exportable) $Doc->ExportField($this->city_name);
					if ($this->prov_code->Exportable) $Doc->ExportField($this->prov_code);
					if ($this->district_no->Exportable) $Doc->ExportField($this->district_no);
					if ($this->district_name->Exportable) $Doc->ExportField($this->district_name);
					if ($this->is_Urban->Exportable) $Doc->ExportField($this->is_Urban);
					if ($this->locked->Exportable) $Doc->ExportField($this->locked);
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
