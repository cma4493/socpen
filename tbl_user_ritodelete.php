<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_user_ritoinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_user_rito_delete = NULL; // Initialize page object first

class ctbl_user_rito_delete extends ctbl_user_rito {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_user_rito';

	// Page object name
	var $PageObjName = 'tbl_user_rito_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}
	var $AuditTrailOnDelete = TRUE;

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-error ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<table class=\"ewStdTable\"><tr><td><div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div></td></tr></table>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language, $UserAgent;

		// User agent
		$UserAgent = ew_UserAgent();
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (tbl_user_rito)
		if (!isset($GLOBALS["tbl_user_rito"])) {
			$GLOBALS["tbl_user_rito"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_user_rito"];
		}

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_user_rito', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// User profile
		$UserProfile = new cUserProfile();
		$UserProfile->LoadProfile(@$_SESSION[EW_SESSION_USER_PROFILE]);

		// Security
		$Security = new cAdvancedSecurity();
		if (IsPasswordExpired())
			$this->Page_Terminate("changepwd.php");
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		$Security->TablePermission_Loaded();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("tbl_user_ritolist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Update last accessed time
		if ($UserProfile->IsValidUser(session_id())) {
			if (!$Security->IsSysAdmin())
				$UserProfile->SaveProfileToDatabase(CurrentUserName()); // Update last accessed time to user profile
		} else {
			echo $Language->Phrase("UserProfileCorrupted");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action
		$this->uid->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("tbl_user_ritolist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in tbl_user_rito class, tbl_user_ritoinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "D"; // Delete record directly
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
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

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->uid->DbValue = $row['uid'];
		$this->username->DbValue = $row['username'];
		$this->password->DbValue = $row['password'];
		$this->_email->DbValue = $row['email'];
		$this->firstname->DbValue = $row['firstname'];
		$this->middlename->DbValue = $row['middlename'];
		$this->surname->DbValue = $row['surname'];
		$this->extensionname->DbValue = $row['extensionname'];
		$this->position->DbValue = $row['position'];
		$this->designation->DbValue = $row['designation'];
		$this->region_code->DbValue = $row['region_code'];
		$this->user_level->DbValue = $row['user_level'];
		$this->contact_no->DbValue = $row['contact_no'];
		$this->activated->DbValue = $row['activated'];
		$this->profile->DbValue = $row['profile'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
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

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

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
			$sSqlWrk .= " ORDER BY `region_name` ASC";
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
			$sSqlWrk .= " ORDER BY `userlevelname` ASC";
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
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();
		if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteBegin")); // Batch delete begin

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['uid'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
			if ($DeleteRows) {
				foreach ($rsold as $row)
					$this->WriteAuditTrailOnDelete($row);
			}
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
		} else {
			$conn->RollbackTrans(); // Rollback changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteRollback")); // Batch delete rollback
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_user_ritolist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_user_rito';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'tbl_user_rito';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['uid'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $curUser = CurrentUserID();
		foreach (array_keys($rs) as $fldname) {
			if (array_key_exists($fldname, $this->fields) && $this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$oldvalue = $rs[$fldname];
					else
						$oldvalue = "[MEMO]"; // Memo field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$oldvalue = "[XML]"; // XML field
				} else {
					$oldvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $curUser, "D", $table, $fldname, $key, $oldvalue, "");
			}
		}
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($tbl_user_rito_delete)) $tbl_user_rito_delete = new ctbl_user_rito_delete();

// Page init
$tbl_user_rito_delete->Page_Init();

// Page main
$tbl_user_rito_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_user_rito_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_user_rito_delete = new ew_Page("tbl_user_rito_delete");
tbl_user_rito_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = tbl_user_rito_delete.PageID; // For backward compatibility

// Form object
var ftbl_user_ritodelete = new ew_Form("ftbl_user_ritodelete");

// Form_CustomValidate event
ftbl_user_ritodelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_user_ritodelete.ValidateRequired = true;
<?php } else { ?>
ftbl_user_ritodelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_user_ritodelete.Lists["x_region_code"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_user_ritodelete.Lists["x_user_level"] = {"LinkField":"x_userlevelid","Ajax":true,"AutoFill":false,"DisplayFields":["x_userlevelname","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($tbl_user_rito_delete->Recordset = $tbl_user_rito_delete->LoadRecordset())
	$tbl_user_rito_deleteTotalRecs = $tbl_user_rito_delete->Recordset->RecordCount(); // Get record count
if ($tbl_user_rito_deleteTotalRecs <= 0) { // No record found, exit
	if ($tbl_user_rito_delete->Recordset)
		$tbl_user_rito_delete->Recordset->Close();
	$tbl_user_rito_delete->Page_Terminate("tbl_user_ritolist.php"); // Return to list
}
?>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_user_rito_delete->ShowPageHeader(); ?>
<?php
$tbl_user_rito_delete->ShowMessage();
?>
<form name="ftbl_user_ritodelete" id="ftbl_user_ritodelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_user_rito">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($tbl_user_rito_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_tbl_user_ritodelete" class="ewTable ewTableSeparate">
<?php echo $tbl_user_rito->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($tbl_user_rito->uid->Visible) { // uid ?>
		<td><span id="elh_tbl_user_rito_uid" class="tbl_user_rito_uid"><?php echo $tbl_user_rito->uid->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->username->Visible) { // username ?>
		<td><span id="elh_tbl_user_rito_username" class="tbl_user_rito_username"><?php echo $tbl_user_rito->username->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->password->Visible) { // password ?>
		<td><span id="elh_tbl_user_rito_password" class="tbl_user_rito_password"><?php echo $tbl_user_rito->password->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->_email->Visible) { // email ?>
		<td><span id="elh_tbl_user_rito__email" class="tbl_user_rito__email"><?php echo $tbl_user_rito->_email->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->firstname->Visible) { // firstname ?>
		<td><span id="elh_tbl_user_rito_firstname" class="tbl_user_rito_firstname"><?php echo $tbl_user_rito->firstname->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->middlename->Visible) { // middlename ?>
		<td><span id="elh_tbl_user_rito_middlename" class="tbl_user_rito_middlename"><?php echo $tbl_user_rito->middlename->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->surname->Visible) { // surname ?>
		<td><span id="elh_tbl_user_rito_surname" class="tbl_user_rito_surname"><?php echo $tbl_user_rito->surname->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->extensionname->Visible) { // extensionname ?>
		<td><span id="elh_tbl_user_rito_extensionname" class="tbl_user_rito_extensionname"><?php echo $tbl_user_rito->extensionname->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->position->Visible) { // position ?>
		<td><span id="elh_tbl_user_rito_position" class="tbl_user_rito_position"><?php echo $tbl_user_rito->position->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->designation->Visible) { // designation ?>
		<td><span id="elh_tbl_user_rito_designation" class="tbl_user_rito_designation"><?php echo $tbl_user_rito->designation->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->region_code->Visible) { // region_code ?>
		<td><span id="elh_tbl_user_rito_region_code" class="tbl_user_rito_region_code"><?php echo $tbl_user_rito->region_code->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->user_level->Visible) { // user_level ?>
		<td><span id="elh_tbl_user_rito_user_level" class="tbl_user_rito_user_level"><?php echo $tbl_user_rito->user_level->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->contact_no->Visible) { // contact_no ?>
		<td><span id="elh_tbl_user_rito_contact_no" class="tbl_user_rito_contact_no"><?php echo $tbl_user_rito->contact_no->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_user_rito->activated->Visible) { // activated ?>
		<td><span id="elh_tbl_user_rito_activated" class="tbl_user_rito_activated"><?php echo $tbl_user_rito->activated->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$tbl_user_rito_delete->RecCnt = 0;
$i = 0;
while (!$tbl_user_rito_delete->Recordset->EOF) {
	$tbl_user_rito_delete->RecCnt++;
	$tbl_user_rito_delete->RowCnt++;

	// Set row properties
	$tbl_user_rito->ResetAttrs();
	$tbl_user_rito->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$tbl_user_rito_delete->LoadRowValues($tbl_user_rito_delete->Recordset);

	// Render row
	$tbl_user_rito_delete->RenderRow();
?>
	<tr<?php echo $tbl_user_rito->RowAttributes() ?>>
<?php if ($tbl_user_rito->uid->Visible) { // uid ?>
		<td<?php echo $tbl_user_rito->uid->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_uid" class="control-group tbl_user_rito_uid">
<span<?php echo $tbl_user_rito->uid->ViewAttributes() ?>>
<?php echo $tbl_user_rito->uid->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->username->Visible) { // username ?>
		<td<?php echo $tbl_user_rito->username->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_username" class="control-group tbl_user_rito_username">
<span<?php echo $tbl_user_rito->username->ViewAttributes() ?>>
<?php echo $tbl_user_rito->username->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->password->Visible) { // password ?>
		<td<?php echo $tbl_user_rito->password->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_password" class="control-group tbl_user_rito_password">
<span<?php echo $tbl_user_rito->password->ViewAttributes() ?>>
<?php echo $tbl_user_rito->password->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->_email->Visible) { // email ?>
		<td<?php echo $tbl_user_rito->_email->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito__email" class="control-group tbl_user_rito__email">
<span<?php echo $tbl_user_rito->_email->ViewAttributes() ?>>
<?php echo $tbl_user_rito->_email->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->firstname->Visible) { // firstname ?>
		<td<?php echo $tbl_user_rito->firstname->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_firstname" class="control-group tbl_user_rito_firstname">
<span<?php echo $tbl_user_rito->firstname->ViewAttributes() ?>>
<?php echo $tbl_user_rito->firstname->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->middlename->Visible) { // middlename ?>
		<td<?php echo $tbl_user_rito->middlename->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_middlename" class="control-group tbl_user_rito_middlename">
<span<?php echo $tbl_user_rito->middlename->ViewAttributes() ?>>
<?php echo $tbl_user_rito->middlename->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->surname->Visible) { // surname ?>
		<td<?php echo $tbl_user_rito->surname->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_surname" class="control-group tbl_user_rito_surname">
<span<?php echo $tbl_user_rito->surname->ViewAttributes() ?>>
<?php echo $tbl_user_rito->surname->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->extensionname->Visible) { // extensionname ?>
		<td<?php echo $tbl_user_rito->extensionname->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_extensionname" class="control-group tbl_user_rito_extensionname">
<span<?php echo $tbl_user_rito->extensionname->ViewAttributes() ?>>
<?php echo $tbl_user_rito->extensionname->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->position->Visible) { // position ?>
		<td<?php echo $tbl_user_rito->position->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_position" class="control-group tbl_user_rito_position">
<span<?php echo $tbl_user_rito->position->ViewAttributes() ?>>
<?php echo $tbl_user_rito->position->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->designation->Visible) { // designation ?>
		<td<?php echo $tbl_user_rito->designation->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_designation" class="control-group tbl_user_rito_designation">
<span<?php echo $tbl_user_rito->designation->ViewAttributes() ?>>
<?php echo $tbl_user_rito->designation->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->region_code->Visible) { // region_code ?>
		<td<?php echo $tbl_user_rito->region_code->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_region_code" class="control-group tbl_user_rito_region_code">
<span<?php echo $tbl_user_rito->region_code->ViewAttributes() ?>>
<?php echo $tbl_user_rito->region_code->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->user_level->Visible) { // user_level ?>
		<td<?php echo $tbl_user_rito->user_level->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_user_level" class="control-group tbl_user_rito_user_level">
<span<?php echo $tbl_user_rito->user_level->ViewAttributes() ?>>
<?php echo $tbl_user_rito->user_level->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->contact_no->Visible) { // contact_no ?>
		<td<?php echo $tbl_user_rito->contact_no->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_contact_no" class="control-group tbl_user_rito_contact_no">
<span<?php echo $tbl_user_rito->contact_no->ViewAttributes() ?>>
<?php echo $tbl_user_rito->contact_no->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_user_rito->activated->Visible) { // activated ?>
		<td<?php echo $tbl_user_rito->activated->CellAttributes() ?>>
<span id="el<?php echo $tbl_user_rito_delete->RowCnt ?>_tbl_user_rito_activated" class="control-group tbl_user_rito_activated">
<span<?php echo $tbl_user_rito->activated->ViewAttributes() ?>>
<?php echo $tbl_user_rito->activated->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$tbl_user_rito_delete->Recordset->MoveNext();
}
$tbl_user_rito_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
ftbl_user_ritodelete.Init();
</script>
<?php
$tbl_user_rito_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_user_rito_delete->Page_Terminate();
?>
