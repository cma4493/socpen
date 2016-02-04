<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_updatesinfo.php" ?>
<?php include_once "tbl_pensionerinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_updates_view = NULL; // Initialize page object first

class ctbl_updates_view extends ctbl_updates {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_updates';

	// Page object name
	var $PageObjName = 'tbl_updates_view';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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

		// Table object (tbl_updates)
		if (!isset($GLOBALS["tbl_updates"])) {
			$GLOBALS["tbl_updates"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_updates"];
		}
		$KeyUrl = "";
		if (@$_GET["updatesID"] <> "") {
			$this->RecKey["updatesID"] = $_GET["updatesID"];
			$KeyUrl .= "&updatesID=" . urlencode($this->RecKey["updatesID"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Table object (tbl_pensioner)
		if (!isset($GLOBALS['tbl_pensioner'])) $GLOBALS['tbl_pensioner'] = new ctbl_pensioner();

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_updates', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "span";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "span";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "span";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
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
		if (!$Security->CanView()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("tbl_updateslist.php");
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
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 1;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["updatesID"] <> "") {
				$this->updatesID->setQueryStringValue($_GET["updatesID"]);
				$this->RecKey["updatesID"] = $this->updatesID->QueryStringValue;
			} else {
				$sReturnUrl = "tbl_updateslist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "tbl_updateslist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "tbl_updateslist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = &$options["action"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"btn btn-success btn-sm\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" ." <i class='icon-file align-top bigger-125'></i> " . $Language->Phrase("ViewPageAddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());

		// Edit
		$item = &$option->Add("edit");
		$item->Body = "<a class=\"btn btn-warning btn-sm\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" ." <i class='icon-pencil align-top bigger-125'></i> " . $Language->Phrase("ViewPageEditLink") . "</a>";
		$item->Visible = ($this->EditUrl <> "" && $Security->CanEdit());

		// Copy
		$item = &$option->Add("copy");
		$item->Body = "<a class=\"btn-info btn-sm\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" ." <i class='icon-copy align-top bigger-125'></i> " . $Language->Phrase("ViewPageCopyLink") . "</a>";
		$item->Visible = ($this->CopyUrl <> "" && $Security->CanAdd());

		// Delete
		$item = &$option->Add("delete");
		$item->Body = "<a onclick=\"return ew_Confirm(ewLanguage.Phrase('DeleteConfirmMsg'));\" class=\"btn btn-pink btn-sm \" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" ." <i class='icon-trash align-top bigger-125'></i> ". $Language->Phrase("ViewPageDeleteLink") . "</a>";
		$item->Visible = ($this->DeleteUrl <> "" && $Security->CanDelete());

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
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

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->updatesID->DbValue = $row['updatesID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->status->DbValue = $row['status'];
		$this->Remarks->DbValue = $row['Remarks'];
		$this->approved->DbValue = $row['approved'];
		$this->dateUpdated->DbValue = $row['dateUpdated'];
		$this->_field->DbValue = $row['field'];
		$this->new_value->DbValue = $row['new_value'];
		$this->old_value->DbValue = $row['old_value'];
		$this->paymentmodeID->DbValue = $row['paymentmodeID'];
		$this->deathDate->DbValue = $row['deathDate'];
		$this->Createdby->DbValue = $row['Createdby'];
		$this->CreatedDate->DbValue = $row['CreatedDate'];
		$this->UpdatedBy->DbValue = $row['UpdatedBy'];
		$this->UpdatedDate->DbValue = $row['UpdatedDate'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();
		$this->SetupOtherOptions();

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
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
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_updateslist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("view");
		$Breadcrumb->Add("view", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
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
if (!isset($tbl_updates_view)) $tbl_updates_view = new ctbl_updates_view();

// Page init
$tbl_updates_view->Page_Init();

// Page main
$tbl_updates_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_updates_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_updates_view = new ew_Page("tbl_updates_view");
tbl_updates_view.PageID = "view"; // Page ID
var EW_PAGE_ID = tbl_updates_view.PageID; // For backward compatibility

// Form object
var ftbl_updatesview = new ew_Form("ftbl_updatesview");

// Form_CustomValidate event
ftbl_updatesview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_updatesview.ValidateRequired = true;
<?php } else { ?>
ftbl_updatesview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<div class="ewViewExportOptions">
<?php $tbl_updates_view->ExportOptions->Render("body") ?>
<?php if (!$tbl_updates_view->ExportOptions->UseDropDownButton) { ?>
</div>
<div class="ewViewOtherOptions">
<?php } ?>
<?php
	foreach ($tbl_updates_view->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php $tbl_updates_view->ShowPageHeader(); ?>
<?php
$tbl_updates_view->ShowMessage();
?>
<form name="ftbl_updatesview" id="ftbl_updatesview" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_updates">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_tbl_updatesview" class="table table-bordered table-striped">
<?php if ($tbl_updates->updatesID->Visible) { // updatesID ?>
	<tr id="r_updatesID">
		<td><span id="elh_tbl_updates_updatesID"><?php echo $tbl_updates->updatesID->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->updatesID->CellAttributes() ?>>
<span id="el_tbl_updates_updatesID" class="control-group">
<span<?php echo $tbl_updates->updatesID->ViewAttributes() ?>>
<?php echo $tbl_updates->updatesID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->PensionerID->Visible) { // PensionerID ?>
	<tr id="r_PensionerID">
		<td><span id="elh_tbl_updates_PensionerID"><?php echo $tbl_updates->PensionerID->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->PensionerID->CellAttributes() ?>>
<span id="el_tbl_updates_PensionerID" class="control-group">
<span<?php echo $tbl_updates->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_updates->PensionerID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->status->Visible) { // status ?>
	<tr id="r_status">
		<td><span id="elh_tbl_updates_status"><?php echo $tbl_updates->status->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->status->CellAttributes() ?>>
<span id="el_tbl_updates_status" class="control-group">
<span<?php echo $tbl_updates->status->ViewAttributes() ?>>
<?php echo $tbl_updates->status->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->Remarks->Visible) { // Remarks ?>
	<tr id="r_Remarks">
		<td><span id="elh_tbl_updates_Remarks"><?php echo $tbl_updates->Remarks->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->Remarks->CellAttributes() ?>>
<span id="el_tbl_updates_Remarks" class="control-group">
<span<?php echo $tbl_updates->Remarks->ViewAttributes() ?>>
<?php echo $tbl_updates->Remarks->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->approved->Visible) { // approved ?>
	<tr id="r_approved">
		<td><span id="elh_tbl_updates_approved"><?php echo $tbl_updates->approved->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->approved->CellAttributes() ?>>
<span id="el_tbl_updates_approved" class="control-group">
<span<?php echo $tbl_updates->approved->ViewAttributes() ?>>
<?php echo $tbl_updates->approved->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->dateUpdated->Visible) { // dateUpdated ?>
	<tr id="r_dateUpdated">
		<td><span id="elh_tbl_updates_dateUpdated"><?php echo $tbl_updates->dateUpdated->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->dateUpdated->CellAttributes() ?>>
<span id="el_tbl_updates_dateUpdated" class="control-group">
<span<?php echo $tbl_updates->dateUpdated->ViewAttributes() ?>>
<?php echo $tbl_updates->dateUpdated->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->_field->Visible) { // field ?>
	<tr id="r__field">
		<td><span id="elh_tbl_updates__field"><?php echo $tbl_updates->_field->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->_field->CellAttributes() ?>>
<span id="el_tbl_updates__field" class="control-group">
<span<?php echo $tbl_updates->_field->ViewAttributes() ?>>
<?php echo $tbl_updates->_field->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->new_value->Visible) { // new_value ?>
	<tr id="r_new_value">
		<td><span id="elh_tbl_updates_new_value"><?php echo $tbl_updates->new_value->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->new_value->CellAttributes() ?>>
<span id="el_tbl_updates_new_value" class="control-group">
<span<?php echo $tbl_updates->new_value->ViewAttributes() ?>>
<?php echo $tbl_updates->new_value->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->old_value->Visible) { // old_value ?>
	<tr id="r_old_value">
		<td><span id="elh_tbl_updates_old_value"><?php echo $tbl_updates->old_value->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->old_value->CellAttributes() ?>>
<span id="el_tbl_updates_old_value" class="control-group">
<span<?php echo $tbl_updates->old_value->ViewAttributes() ?>>
<?php echo $tbl_updates->old_value->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->paymentmodeID->Visible) { // paymentmodeID ?>
	<tr id="r_paymentmodeID">
		<td><span id="elh_tbl_updates_paymentmodeID"><?php echo $tbl_updates->paymentmodeID->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->paymentmodeID->CellAttributes() ?>>
<span id="el_tbl_updates_paymentmodeID" class="control-group">
<span<?php echo $tbl_updates->paymentmodeID->ViewAttributes() ?>>
<?php echo $tbl_updates->paymentmodeID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->deathDate->Visible) { // deathDate ?>
	<tr id="r_deathDate">
		<td><span id="elh_tbl_updates_deathDate"><?php echo $tbl_updates->deathDate->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->deathDate->CellAttributes() ?>>
<span id="el_tbl_updates_deathDate" class="control-group">
<span<?php echo $tbl_updates->deathDate->ViewAttributes() ?>>
<?php echo $tbl_updates->deathDate->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->Createdby->Visible) { // Createdby ?>
	<tr id="r_Createdby">
		<td><span id="elh_tbl_updates_Createdby"><?php echo $tbl_updates->Createdby->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->Createdby->CellAttributes() ?>>
<span id="el_tbl_updates_Createdby" class="control-group">
<span<?php echo $tbl_updates->Createdby->ViewAttributes() ?>>
<?php echo $tbl_updates->Createdby->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->CreatedDate->Visible) { // CreatedDate ?>
	<tr id="r_CreatedDate">
		<td><span id="elh_tbl_updates_CreatedDate"><?php echo $tbl_updates->CreatedDate->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->CreatedDate->CellAttributes() ?>>
<span id="el_tbl_updates_CreatedDate" class="control-group">
<span<?php echo $tbl_updates->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_updates->CreatedDate->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->UpdatedBy->Visible) { // UpdatedBy ?>
	<tr id="r_UpdatedBy">
		<td><span id="elh_tbl_updates_UpdatedBy"><?php echo $tbl_updates->UpdatedBy->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->UpdatedBy->CellAttributes() ?>>
<span id="el_tbl_updates_UpdatedBy" class="control-group">
<span<?php echo $tbl_updates->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_updates->UpdatedBy->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->UpdatedDate->Visible) { // UpdatedDate ?>
	<tr id="r_UpdatedDate">
		<td><span id="elh_tbl_updates_UpdatedDate"><?php echo $tbl_updates->UpdatedDate->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->UpdatedDate->CellAttributes() ?>>
<span id="el_tbl_updates_UpdatedDate" class="control-group">
<span<?php echo $tbl_updates->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_updates->UpdatedDate->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
</form>
<script type="text/javascript">
ftbl_updatesview.Init();
</script>
<?php
$tbl_updates_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_updates_view->Page_Terminate();
?>
