<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "Pensioner_Updatesinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$Pensioner_Updates_delete = NULL; // Initialize page object first

class cPensioner_Updates_delete extends cPensioner_Updates {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'Pensioner Updates';

	// Page object name
	var $PageObjName = 'Pensioner_Updates_delete';

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

		// Table object (Pensioner_Updates)
		if (!isset($GLOBALS["Pensioner_Updates"])) {
			$GLOBALS["Pensioner_Updates"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Pensioner_Updates"];
		}

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'Pensioner Updates', TRUE);

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
			$this->Page_Terminate("Pensioner_Updateslist.php");
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
		$this->updatesID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->Page_Terminate("Pensioner_Updateslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in Pensioner_Updates class, Pensioner_Updatesinfo.php

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

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->updatesID->DbValue = $row['updatesID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->status->DbValue = $row['status'];
		$this->dateUpdated->DbValue = $row['dateUpdated'];
		$this->approved->DbValue = $row['approved'];
		$this->deathDate->DbValue = $row['deathDate'];
		$this->paymentmodeID->DbValue = $row['paymentmodeID'];
		$this->Createdby->DbValue = $row['Createdby'];
		$this->CreatedDate->DbValue = $row['CreatedDate'];
		$this->UpdatedBy->DbValue = $row['UpdatedBy'];
		$this->UpdatedDate->DbValue = $row['UpdatedDate'];
		$this->Remarks->DbValue = $row['Remarks'];
		$this->_field->DbValue = $row['field'];
		$this->new_value->DbValue = $row['new_value'];
		$this->old_value->DbValue = $row['old_value'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
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

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

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

			// field
			$this->_field->ViewValue = $this->_field->CurrentValue;
			$this->_field->ViewCustomAttributes = "";

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

			// field
			$this->_field->LinkCustomAttributes = "";
			$this->_field->HrefValue = "";
			$this->_field->TooltipValue = "";
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
				$sThisKey .= $row['updatesID'];
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
		} else {
			$conn->RollbackTrans(); // Rollback changes
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "Pensioner_Updateslist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
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
if (!isset($Pensioner_Updates_delete)) $Pensioner_Updates_delete = new cPensioner_Updates_delete();

// Page init
$Pensioner_Updates_delete->Page_Init();

// Page main
$Pensioner_Updates_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Pensioner_Updates_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var Pensioner_Updates_delete = new ew_Page("Pensioner_Updates_delete");
Pensioner_Updates_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = Pensioner_Updates_delete.PageID; // For backward compatibility

// Form object
var fPensioner_Updatesdelete = new ew_Form("fPensioner_Updatesdelete");

// Form_CustomValidate event
fPensioner_Updatesdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fPensioner_Updatesdelete.ValidateRequired = true;
<?php } else { ?>
fPensioner_Updatesdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fPensioner_Updatesdelete.Lists["x_Createdby"] = {"LinkField":"x_uid","Ajax":true,"AutoFill":false,"DisplayFields":["x_surname","x_firstname","x_middlename","x_extensionname"],"ParentFields":[],"FilterFields":[],"Options":[]};
fPensioner_Updatesdelete.Lists["x_UpdatedBy"] = {"LinkField":"x_uid","Ajax":true,"AutoFill":false,"DisplayFields":["x_surname","x_firstname","x_middlename","x_extensionname"],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($Pensioner_Updates_delete->Recordset = $Pensioner_Updates_delete->LoadRecordset())
	$Pensioner_Updates_deleteTotalRecs = $Pensioner_Updates_delete->Recordset->RecordCount(); // Get record count
if ($Pensioner_Updates_deleteTotalRecs <= 0) { // No record found, exit
	if ($Pensioner_Updates_delete->Recordset)
		$Pensioner_Updates_delete->Recordset->Close();
	$Pensioner_Updates_delete->Page_Terminate("Pensioner_Updateslist.php"); // Return to list
}
?>
<?php //$Breadcrumb->Render(); ?>
<?php $Pensioner_Updates_delete->ShowPageHeader(); ?>
<?php
$Pensioner_Updates_delete->ShowMessage();
?>
<form name="fPensioner_Updatesdelete" id="fPensioner_Updatesdelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="Pensioner_Updates">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($Pensioner_Updates_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_Pensioner_Updatesdelete" class="ewTable ewTableSeparate">
<?php echo $Pensioner_Updates->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($Pensioner_Updates->updatesID->Visible) { // updatesID ?>
		<td><span id="elh_Pensioner_Updates_updatesID" class="Pensioner_Updates_updatesID"><?php echo $Pensioner_Updates->updatesID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->PensionerID->Visible) { // PensionerID ?>
		<td><span id="elh_Pensioner_Updates_PensionerID" class="Pensioner_Updates_PensionerID"><?php echo $Pensioner_Updates->PensionerID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->status->Visible) { // status ?>
		<td><span id="elh_Pensioner_Updates_status" class="Pensioner_Updates_status"><?php echo $Pensioner_Updates->status->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->dateUpdated->Visible) { // dateUpdated ?>
		<td><span id="elh_Pensioner_Updates_dateUpdated" class="Pensioner_Updates_dateUpdated"><?php echo $Pensioner_Updates->dateUpdated->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->approved->Visible) { // approved ?>
		<td><span id="elh_Pensioner_Updates_approved" class="Pensioner_Updates_approved"><?php echo $Pensioner_Updates->approved->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->deathDate->Visible) { // deathDate ?>
		<td><span id="elh_Pensioner_Updates_deathDate" class="Pensioner_Updates_deathDate"><?php echo $Pensioner_Updates->deathDate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->paymentmodeID->Visible) { // paymentmodeID ?>
		<td><span id="elh_Pensioner_Updates_paymentmodeID" class="Pensioner_Updates_paymentmodeID"><?php echo $Pensioner_Updates->paymentmodeID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->Createdby->Visible) { // Createdby ?>
		<td><span id="elh_Pensioner_Updates_Createdby" class="Pensioner_Updates_Createdby"><?php echo $Pensioner_Updates->Createdby->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->CreatedDate->Visible) { // CreatedDate ?>
		<td><span id="elh_Pensioner_Updates_CreatedDate" class="Pensioner_Updates_CreatedDate"><?php echo $Pensioner_Updates->CreatedDate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->UpdatedBy->Visible) { // UpdatedBy ?>
		<td><span id="elh_Pensioner_Updates_UpdatedBy" class="Pensioner_Updates_UpdatedBy"><?php echo $Pensioner_Updates->UpdatedBy->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->UpdatedDate->Visible) { // UpdatedDate ?>
		<td><span id="elh_Pensioner_Updates_UpdatedDate" class="Pensioner_Updates_UpdatedDate"><?php echo $Pensioner_Updates->UpdatedDate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->_field->Visible) { // field ?>
		<td><span id="elh_Pensioner_Updates__field" class="Pensioner_Updates__field"><?php echo $Pensioner_Updates->_field->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$Pensioner_Updates_delete->RecCnt = 0;
$i = 0;
while (!$Pensioner_Updates_delete->Recordset->EOF) {
	$Pensioner_Updates_delete->RecCnt++;
	$Pensioner_Updates_delete->RowCnt++;

	// Set row properties
	$Pensioner_Updates->ResetAttrs();
	$Pensioner_Updates->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$Pensioner_Updates_delete->LoadRowValues($Pensioner_Updates_delete->Recordset);

	// Render row
	$Pensioner_Updates_delete->RenderRow();
?>
	<tr<?php echo $Pensioner_Updates->RowAttributes() ?>>
<?php if ($Pensioner_Updates->updatesID->Visible) { // updatesID ?>
		<td<?php echo $Pensioner_Updates->updatesID->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_updatesID" class="control-group Pensioner_Updates_updatesID">
<span<?php echo $Pensioner_Updates->updatesID->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->updatesID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $Pensioner_Updates->PensionerID->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_PensionerID" class="control-group Pensioner_Updates_PensionerID">
<span<?php echo $Pensioner_Updates->PensionerID->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->PensionerID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->status->Visible) { // status ?>
		<td<?php echo $Pensioner_Updates->status->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_status" class="control-group Pensioner_Updates_status">
<span<?php echo $Pensioner_Updates->status->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->dateUpdated->Visible) { // dateUpdated ?>
		<td<?php echo $Pensioner_Updates->dateUpdated->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_dateUpdated" class="control-group Pensioner_Updates_dateUpdated">
<span<?php echo $Pensioner_Updates->dateUpdated->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->dateUpdated->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->approved->Visible) { // approved ?>
		<td<?php echo $Pensioner_Updates->approved->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_approved" class="control-group Pensioner_Updates_approved">
<span<?php echo $Pensioner_Updates->approved->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->approved->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->deathDate->Visible) { // deathDate ?>
		<td<?php echo $Pensioner_Updates->deathDate->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_deathDate" class="control-group Pensioner_Updates_deathDate">
<span<?php echo $Pensioner_Updates->deathDate->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->deathDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->paymentmodeID->Visible) { // paymentmodeID ?>
		<td<?php echo $Pensioner_Updates->paymentmodeID->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_paymentmodeID" class="control-group Pensioner_Updates_paymentmodeID">
<span<?php echo $Pensioner_Updates->paymentmodeID->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->paymentmodeID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->Createdby->Visible) { // Createdby ?>
		<td<?php echo $Pensioner_Updates->Createdby->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_Createdby" class="control-group Pensioner_Updates_Createdby">
<span<?php echo $Pensioner_Updates->Createdby->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->Createdby->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $Pensioner_Updates->CreatedDate->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_CreatedDate" class="control-group Pensioner_Updates_CreatedDate">
<span<?php echo $Pensioner_Updates->CreatedDate->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->CreatedDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $Pensioner_Updates->UpdatedBy->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_UpdatedBy" class="control-group Pensioner_Updates_UpdatedBy">
<span<?php echo $Pensioner_Updates->UpdatedBy->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->UpdatedBy->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $Pensioner_Updates->UpdatedDate->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_UpdatedDate" class="control-group Pensioner_Updates_UpdatedDate">
<span<?php echo $Pensioner_Updates->UpdatedDate->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->UpdatedDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->_field->Visible) { // field ?>
		<td<?php echo $Pensioner_Updates->_field->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates__field" class="control-group Pensioner_Updates__field">
<span<?php echo $Pensioner_Updates->_field->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->_field->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$Pensioner_Updates_delete->Recordset->MoveNext();
}
$Pensioner_Updates_delete->Recordset->Close();
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
fPensioner_Updatesdelete.Init();
</script>
<?php
$Pensioner_Updates_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$Pensioner_Updates_delete->Page_Terminate();
?>
