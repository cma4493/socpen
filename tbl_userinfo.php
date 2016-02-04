<?php

// Global variable for table object
$tbl_user = NULL;

//
// Table class for tbl_user
//
class ctbl_user extends cTable {
	var $uid;
	var $username;
	var $password;
	var $_email;
	var $firstname;
	var $middlename;
	var $surname;
	var $extensionname;
	var $position;
	var $designation;
	var $region_code;
	var $user_level;
	var $contact_no;
	var $activated;
	var $profile;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'tbl_user';
		$this->TableName = 'tbl_user';
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

		// uid
		$this->uid = new cField('tbl_user', 'tbl_user', 'x_uid', 'uid', '`uid`', '`uid`', 21, -1, FALSE, '`uid`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->uid->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['uid'] = &$this->uid;

		// username
		$this->username = new cField('tbl_user', 'tbl_user', 'x_username', 'username', '`username`', '`username`', 200, -1, FALSE, '`username`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['username'] = &$this->username;

		// password
		$this->password = new cField('tbl_user', 'tbl_user', 'x_password', 'password', '`password`', '`password`', 200, -1, FALSE, '`password`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['password'] = &$this->password;

		// email
		$this->_email = new cField('tbl_user', 'tbl_user', 'x__email', 'email', '`email`', '`email`', 200, -1, FALSE, '`email`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['email'] = &$this->_email;

		// firstname
		$this->firstname = new cField('tbl_user', 'tbl_user', 'x_firstname', 'firstname', '`firstname`', '`firstname`', 200, -1, FALSE, '`firstname`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['firstname'] = &$this->firstname;

		// middlename
		$this->middlename = new cField('tbl_user', 'tbl_user', 'x_middlename', 'middlename', '`middlename`', '`middlename`', 200, -1, FALSE, '`middlename`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['middlename'] = &$this->middlename;

		// surname
		$this->surname = new cField('tbl_user', 'tbl_user', 'x_surname', 'surname', '`surname`', '`surname`', 200, -1, FALSE, '`surname`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['surname'] = &$this->surname;

		// extensionname
		$this->extensionname = new cField('tbl_user', 'tbl_user', 'x_extensionname', 'extensionname', '`extensionname`', '`extensionname`', 200, -1, FALSE, '`extensionname`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['extensionname'] = &$this->extensionname;

		// position
		$this->position = new cField('tbl_user', 'tbl_user', 'x_position', 'position', '`position`', '`position`', 200, -1, FALSE, '`position`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['position'] = &$this->position;

		// designation
		$this->designation = new cField('tbl_user', 'tbl_user', 'x_designation', 'designation', '`designation`', '`designation`', 200, -1, FALSE, '`designation`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['designation'] = &$this->designation;

		// region_code
		$this->region_code = new cField('tbl_user', 'tbl_user', 'x_region_code', 'region_code', '`region_code`', '`region_code`', 21, -1, FALSE, '`region_code`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->region_code->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['region_code'] = &$this->region_code;

		// user_level
		$this->user_level = new cField('tbl_user', 'tbl_user', 'x_user_level', 'user_level', '`user_level`', '`user_level`', 3, -1, FALSE, '`user_level`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->user_level->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['user_level'] = &$this->user_level;

		// contact_no
		$this->contact_no = new cField('tbl_user', 'tbl_user', 'x_contact_no', 'contact_no', '`contact_no`', '`contact_no`', 200, -1, FALSE, '`contact_no`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['contact_no'] = &$this->contact_no;

		// activated
		$this->activated = new cField('tbl_user', 'tbl_user', 'x_activated', 'activated', '`activated`', '`activated`', 16, -1, FALSE, '`activated`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->activated->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['activated'] = &$this->activated;

		// profile
		$this->profile = new cField('tbl_user', 'tbl_user', 'x_profile', 'profile', '`profile`', '`profile`', 201, -1, FALSE, '`profile`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['profile'] = &$this->profile;
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
		return "`tbl_user`";
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
		global $Security;

		// Add User ID filter
		if (!$this->AllowAnonymousUser() && $Security->CurrentUserID() <> "" && !$Security->IsAdmin()) { // Non system admin
			$sFilter = $this->AddUserIDFilter($sFilter);
		}
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = $this->UserIDAllowSecurity;
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
	var $UpdateTable = "`tbl_user`";

	// INSERT statement
	function InsertSQL(&$rs) {
		global $conn;
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			if (EW_ENCRYPTED_PASSWORD && $name == 'password')
				$value = (EW_CASE_SENSITIVE_PASSWORD) ? ew_EncryptPassword($value) : ew_EncryptPassword(strtolower($value));
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
			if (EW_ENCRYPTED_PASSWORD && $name == 'password') {
				$value = (EW_CASE_SENSITIVE_PASSWORD) ? ew_EncryptPassword($value) : ew_EncryptPassword(strtolower($value));
			}
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
			if (array_key_exists('uid', $rs))
				ew_AddFilter($where, ew_QuotedName('uid') . '=' . ew_QuotedValue($rs['uid'], $this->uid->FldDataType));
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
		return "`uid` = @uid@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->uid->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@uid@", ew_AdjustSql($this->uid->CurrentValue), $sKeyFilter); // Replace key value
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
			return "tbl_userlist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "tbl_userlist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("tbl_userview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("tbl_userview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "tbl_useradd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("tbl_useredit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("tbl_useradd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("tbl_userdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->uid->CurrentValue)) {
			$sUrl .= "uid=" . urlencode($this->uid->CurrentValue);
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
			$arKeys[] = @$_GET["uid"]; // uid

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
			$this->uid->CurrentValue = $key;
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
		$this->uid->setDbValue($rs->fields('uid'));
		$this->username->setDbValue($rs->fields('username'));
		$this->password->setDbValue($rs->fields('password'));
		$this->_email->setDbValue($rs->fields('email'));
		$this->firstname->setDbValue($rs->fields('firstname'));
		$this->middlename->setDbValue($rs->fields('middlename'));
		$this->surname->setDbValue($rs->fields('surname'));
		$this->extensionname->setDbValue($rs->fields('extensionname'));
		$this->position->setDbValue($rs->fields('position'));
		$this->designation->setDbValue($rs->fields('designation'));
		$this->region_code->setDbValue($rs->fields('region_code'));
		$this->user_level->setDbValue($rs->fields('user_level'));
		$this->contact_no->setDbValue($rs->fields('contact_no'));
		$this->activated->setDbValue($rs->fields('activated'));
		$this->profile->setDbValue($rs->fields('profile'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// uid
		// username
		// password
		// email
		// firstname
		// middlename
		// surname
		// extensionname
		// position
		// designation
		// region_code
		// user_level
		// contact_no
		// activated
		// profile
		// uid

		$this->uid->ViewValue = $this->uid->CurrentValue;
		$this->uid->ViewCustomAttributes = "";

		// username
		$this->username->ViewValue = $this->username->CurrentValue;
		$this->username->ViewCustomAttributes = "";

		// password
		$this->password->ViewValue = "********";
		$this->password->ViewCustomAttributes = "";

		// email
		$this->_email->ViewValue = $this->_email->CurrentValue;
		$this->_email->ViewCustomAttributes = "";

		// firstname
		$this->firstname->ViewValue = $this->firstname->CurrentValue;
		$this->firstname->ViewCustomAttributes = "";

		// middlename
		$this->middlename->ViewValue = $this->middlename->CurrentValue;
		$this->middlename->ViewCustomAttributes = "";

		// surname
		$this->surname->ViewValue = $this->surname->CurrentValue;
		$this->surname->ViewCustomAttributes = "";

		// extensionname
		$this->extensionname->ViewValue = $this->extensionname->CurrentValue;
		$this->extensionname->ViewCustomAttributes = "";

		// position
		$this->position->ViewValue = $this->position->CurrentValue;
		$this->position->ViewCustomAttributes = "";

		// designation
		$this->designation->ViewValue = $this->designation->CurrentValue;
		$this->designation->ViewCustomAttributes = "";

		// region_code
		if (strval($this->region_code->CurrentValue) <> "") {
			$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->region_code->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->region_code, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `region_code` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->region_code->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->region_code->ViewValue = $this->region_code->CurrentValue;
			}
		} else {
			$this->region_code->ViewValue = NULL;
		}
		$this->region_code->ViewCustomAttributes = "";

		// user_level
		if ($Security->CanAdmin()) { // System admin
		if (strval($this->user_level->CurrentValue) <> "") {
			$sFilterWrk = "`userlevelid`" . ew_SearchString("=", $this->user_level->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `userlevelid`, `userlevelname` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `userlevels`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->user_level, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->user_level->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->user_level->ViewValue = $this->user_level->CurrentValue;
			}
		} else {
			$this->user_level->ViewValue = NULL;
		}
		} else {
			$this->user_level->ViewValue = "********";
		}
		$this->user_level->ViewCustomAttributes = "";

		// contact_no
		$this->contact_no->ViewValue = $this->contact_no->CurrentValue;
		$this->contact_no->ViewCustomAttributes = "";

		// activated
		if (strval($this->activated->CurrentValue) <> "") {
			switch ($this->activated->CurrentValue) {
				case $this->activated->FldTagValue(1):
					$this->activated->ViewValue = $this->activated->FldTagCaption(1) <> "" ? $this->activated->FldTagCaption(1) : $this->activated->CurrentValue;
					break;
				case $this->activated->FldTagValue(2):
					$this->activated->ViewValue = $this->activated->FldTagCaption(2) <> "" ? $this->activated->FldTagCaption(2) : $this->activated->CurrentValue;
					break;
				default:
					$this->activated->ViewValue = $this->activated->CurrentValue;
			}
		} else {
			$this->activated->ViewValue = NULL;
		}
		$this->activated->ViewCustomAttributes = "";

		// profile
		$this->profile->ViewValue = $this->profile->CurrentValue;
		$this->profile->ViewCustomAttributes = "";

		// uid
		$this->uid->LinkCustomAttributes = "";
		$this->uid->HrefValue = "";
		$this->uid->TooltipValue = "";

		// username
		$this->username->LinkCustomAttributes = "";
		$this->username->HrefValue = "";
		$this->username->TooltipValue = "";

		// password
		$this->password->LinkCustomAttributes = "";
		$this->password->HrefValue = "";
		$this->password->TooltipValue = "";

		// email
		$this->_email->LinkCustomAttributes = "";
		$this->_email->HrefValue = "";
		$this->_email->TooltipValue = "";

		// firstname
		$this->firstname->LinkCustomAttributes = "";
		$this->firstname->HrefValue = "";
		$this->firstname->TooltipValue = "";

		// middlename
		$this->middlename->LinkCustomAttributes = "";
		$this->middlename->HrefValue = "";
		$this->middlename->TooltipValue = "";

		// surname
		$this->surname->LinkCustomAttributes = "";
		$this->surname->HrefValue = "";
		$this->surname->TooltipValue = "";

		// extensionname
		$this->extensionname->LinkCustomAttributes = "";
		$this->extensionname->HrefValue = "";
		$this->extensionname->TooltipValue = "";

		// position
		$this->position->LinkCustomAttributes = "";
		$this->position->HrefValue = "";
		$this->position->TooltipValue = "";

		// designation
		$this->designation->LinkCustomAttributes = "";
		$this->designation->HrefValue = "";
		$this->designation->TooltipValue = "";

		// region_code
		$this->region_code->LinkCustomAttributes = "";
		$this->region_code->HrefValue = "";
		$this->region_code->TooltipValue = "";

		// user_level
		$this->user_level->LinkCustomAttributes = "";
		$this->user_level->HrefValue = "";
		$this->user_level->TooltipValue = "";

		// contact_no
		$this->contact_no->LinkCustomAttributes = "";
		$this->contact_no->HrefValue = "";
		$this->contact_no->TooltipValue = "";

		// activated
		$this->activated->LinkCustomAttributes = "";
		$this->activated->HrefValue = "";
		$this->activated->TooltipValue = "";

		// profile
		$this->profile->LinkCustomAttributes = "";
		$this->profile->HrefValue = "";
		$this->profile->TooltipValue = "";

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
				if ($this->uid->Exportable) $Doc->ExportCaption($this->uid);
				if ($this->username->Exportable) $Doc->ExportCaption($this->username);
				if ($this->password->Exportable) $Doc->ExportCaption($this->password);
				if ($this->_email->Exportable) $Doc->ExportCaption($this->_email);
				if ($this->firstname->Exportable) $Doc->ExportCaption($this->firstname);
				if ($this->middlename->Exportable) $Doc->ExportCaption($this->middlename);
				if ($this->surname->Exportable) $Doc->ExportCaption($this->surname);
				if ($this->extensionname->Exportable) $Doc->ExportCaption($this->extensionname);
				if ($this->position->Exportable) $Doc->ExportCaption($this->position);
				if ($this->designation->Exportable) $Doc->ExportCaption($this->designation);
				if ($this->region_code->Exportable) $Doc->ExportCaption($this->region_code);
				if ($this->user_level->Exportable) $Doc->ExportCaption($this->user_level);
				if ($this->contact_no->Exportable) $Doc->ExportCaption($this->contact_no);
				if ($this->activated->Exportable) $Doc->ExportCaption($this->activated);
				if ($this->profile->Exportable) $Doc->ExportCaption($this->profile);
			} else {
				if ($this->uid->Exportable) $Doc->ExportCaption($this->uid);
				if ($this->username->Exportable) $Doc->ExportCaption($this->username);
				if ($this->password->Exportable) $Doc->ExportCaption($this->password);
				if ($this->_email->Exportable) $Doc->ExportCaption($this->_email);
				if ($this->firstname->Exportable) $Doc->ExportCaption($this->firstname);
				if ($this->middlename->Exportable) $Doc->ExportCaption($this->middlename);
				if ($this->surname->Exportable) $Doc->ExportCaption($this->surname);
				if ($this->extensionname->Exportable) $Doc->ExportCaption($this->extensionname);
				if ($this->position->Exportable) $Doc->ExportCaption($this->position);
				if ($this->designation->Exportable) $Doc->ExportCaption($this->designation);
				if ($this->region_code->Exportable) $Doc->ExportCaption($this->region_code);
				if ($this->user_level->Exportable) $Doc->ExportCaption($this->user_level);
				if ($this->contact_no->Exportable) $Doc->ExportCaption($this->contact_no);
				if ($this->activated->Exportable) $Doc->ExportCaption($this->activated);
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
					if ($this->uid->Exportable) $Doc->ExportField($this->uid);
					if ($this->username->Exportable) $Doc->ExportField($this->username);
					if ($this->password->Exportable) $Doc->ExportField($this->password);
					if ($this->_email->Exportable) $Doc->ExportField($this->_email);
					if ($this->firstname->Exportable) $Doc->ExportField($this->firstname);
					if ($this->middlename->Exportable) $Doc->ExportField($this->middlename);
					if ($this->surname->Exportable) $Doc->ExportField($this->surname);
					if ($this->extensionname->Exportable) $Doc->ExportField($this->extensionname);
					if ($this->position->Exportable) $Doc->ExportField($this->position);
					if ($this->designation->Exportable) $Doc->ExportField($this->designation);
					if ($this->region_code->Exportable) $Doc->ExportField($this->region_code);
					if ($this->user_level->Exportable) $Doc->ExportField($this->user_level);
					if ($this->contact_no->Exportable) $Doc->ExportField($this->contact_no);
					if ($this->activated->Exportable) $Doc->ExportField($this->activated);
					if ($this->profile->Exportable) $Doc->ExportField($this->profile);
				} else {
					if ($this->uid->Exportable) $Doc->ExportField($this->uid);
					if ($this->username->Exportable) $Doc->ExportField($this->username);
					if ($this->password->Exportable) $Doc->ExportField($this->password);
					if ($this->_email->Exportable) $Doc->ExportField($this->_email);
					if ($this->firstname->Exportable) $Doc->ExportField($this->firstname);
					if ($this->middlename->Exportable) $Doc->ExportField($this->middlename);
					if ($this->surname->Exportable) $Doc->ExportField($this->surname);
					if ($this->extensionname->Exportable) $Doc->ExportField($this->extensionname);
					if ($this->position->Exportable) $Doc->ExportField($this->position);
					if ($this->designation->Exportable) $Doc->ExportField($this->designation);
					if ($this->region_code->Exportable) $Doc->ExportField($this->region_code);
					if ($this->user_level->Exportable) $Doc->ExportField($this->user_level);
					if ($this->contact_no->Exportable) $Doc->ExportField($this->contact_no);
					if ($this->activated->Exportable) $Doc->ExportField($this->activated);
				}
				$Doc->EndExportRow();
			}
			$Recordset->MoveNext();
		}
		$Doc->ExportTableFooter();
	}

	// User ID filter
	function UserIDFilter($userid) {
		$sUserIDFilter = '`uid` = ' . ew_QuotedValue($userid, EW_DATATYPE_NUMBER);
		$sParentUserIDFilter = '`uid` IN (SELECT `uid` FROM ' . "`tbl_user`" . ' WHERE `uid` = ' . ew_QuotedValue($userid, EW_DATATYPE_NUMBER) . ')';
		$sUserIDFilter = "($sUserIDFilter) OR ($sParentUserIDFilter)";
		return $sUserIDFilter;
	}

	// Add User ID filter
	function AddUserIDFilter($sFilter) {
		global $Security;
		$sFilterWrk = "";
		$id = (CurrentPageID() == "list") ? $this->CurrentAction : CurrentPageID();
		if (!$this->UserIDAllow($id) && !$Security->IsAdmin()) {
			$sFilterWrk = $Security->UserIDList();
			if ($sFilterWrk <> "")
				$sFilterWrk = '`uid` IN (' . $sFilterWrk . ')';
		}

		// Call Row Rendered event
		$this->UserID_Filtering($sFilterWrk);
		ew_AddFilter($sFilter, $sFilterWrk);
		return $sFilter;
	}

	// Add Parent User ID filter
	function AddParentUserIDFilter($sFilter, $userid) {
		global $Security;
		if (!$Security->IsAdmin()) {
			$result = $Security->ParentUserIDList($userid);
			if ($result <> "")
				$result = '`uid` IN (' . $result . ')';
			ew_AddFilter($result, $sFilter);
			return $result;
		} else {
			return $sFilter;
		}
	}

	// User ID subquery
	function GetUserIDSubquery(&$fld, &$masterfld) {
		global $conn;
		$sWrk = "";
		$sSql = "SELECT " . $masterfld->FldExpression . " FROM `tbl_user`";
		$sFilter = $this->AddUserIDFilter("");
		if ($sFilter <> "") $sSql .= " WHERE " . $sFilter;

		// Use subquery
		if (EW_USE_SUBQUERY_FOR_MASTER_USER_ID) {
			$sWrk = $sSql;
		} else {

			// List all values
			if ($rs = $conn->Execute($sSql)) {
				while (!$rs->EOF) {
					if ($sWrk <> "") $sWrk .= ",";
					$sWrk .= ew_QuotedValue($rs->fields[0], $masterfld->FldDataType);
					$rs->MoveNext();
				}
				$rs->Close();
			}
		}
		if ($sWrk <> "") {
			$sWrk = $fld->FldExpression . " IN (" . $sWrk . ")";
		}
		return $sWrk;
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
