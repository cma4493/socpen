<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_pension_payrollinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_pension_payroll_view = NULL; // Initialize page object first

class ctbl_pension_payroll_view extends ctbl_pension_payroll {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_pension_payroll';

	// Page object name
	var $PageObjName = 'tbl_pension_payroll_view';

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

		// Table object (tbl_pension_payroll)
		if (!isset($GLOBALS["tbl_pension_payroll"])) {
			$GLOBALS["tbl_pension_payroll"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_pension_payroll"];
		}
		$KeyUrl = "";
		if (@$_GET["PayrollID"] <> "") {
			$this->RecKey["PayrollID"] = $_GET["PayrollID"];
			$KeyUrl .= "&PayrollID=" . urlencode($this->RecKey["PayrollID"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_pension_payroll', TRUE);

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
			$this->Page_Terminate("tbl_pension_payrolllist.php");
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
		$this->PayrollID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			if (@$_GET["PayrollID"] <> "") {
				$this->PayrollID->setQueryStringValue($_GET["PayrollID"]);
				$this->RecKey["PayrollID"] = $this->PayrollID->QueryStringValue;
			} else {
				$sReturnUrl = "tbl_pension_payrolllist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "tbl_pension_payrolllist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "tbl_pension_payrolllist.php"; // Not page request, return to list
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

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->PayrollID->DbValue = $row['PayrollID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->PayrollYear->DbValue = $row['PayrollYear'];
		$this->cMonth->DbValue = $row['cMonth'];
		$this->amount->DbValue = $row['amount'];
		$this->paymentmodeID->DbValue = $row['paymentmodeID'];
		$this->Approved->DbValue = $row['Approved'];
		$this->Claimed->DbValue = $row['Claimed'];
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

		// Convert decimal values if posted back
		if ($this->amount->FormValue == $this->amount->CurrentValue && is_numeric(ew_StrToFloat($this->amount->CurrentValue)))
			$this->amount->CurrentValue = ew_StrToFloat($this->amount->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
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

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_pension_payrolllist.php", $this->TableVar);
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
if (!isset($tbl_pension_payroll_view)) $tbl_pension_payroll_view = new ctbl_pension_payroll_view();

// Page init
$tbl_pension_payroll_view->Page_Init();

// Page main
$tbl_pension_payroll_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_pension_payroll_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_pension_payroll_view = new ew_Page("tbl_pension_payroll_view");
tbl_pension_payroll_view.PageID = "view"; // Page ID
var EW_PAGE_ID = tbl_pension_payroll_view.PageID; // For backward compatibility

// Form object
var ftbl_pension_payrollview = new ew_Form("ftbl_pension_payrollview");

// Form_CustomValidate event
ftbl_pension_payrollview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_pension_payrollview.ValidateRequired = true;
<?php } else { ?>
ftbl_pension_payrollview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_pension_payrollview.Lists["x_PensionerID"] = {"LinkField":"x_PensionerID","Ajax":true,"AutoFill":false,"DisplayFields":["x_lastname","x_firstname","x_middlename","x_extname"],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrollview.Lists["x_PayrollYear"] = {"LinkField":"x_Year","Ajax":true,"AutoFill":false,"DisplayFields":["x_Year","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrollview.Lists["x_cMonth"] = {"LinkField":"x_MonthID","Ajax":true,"AutoFill":false,"DisplayFields":["x_desc","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrollview.Lists["x_paymentmodeID"] = {"LinkField":"x_paymentmodeID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<div class="ewViewExportOptions">
<?php $tbl_pension_payroll_view->ExportOptions->Render("body") ?>
<?php if (!$tbl_pension_payroll_view->ExportOptions->UseDropDownButton) { ?>
</div>
<div class="ewViewOtherOptions">
<?php } ?>
<?php
	foreach ($tbl_pension_payroll_view->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php $tbl_pension_payroll_view->ShowPageHeader(); ?>
<?php
$tbl_pension_payroll_view->ShowMessage();
?>
<form name="ftbl_pension_payrollview" id="ftbl_pension_payrollview" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_pension_payroll">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_tbl_pension_payrollview" class="table table-bordered table-striped">
<?php if ($tbl_pension_payroll->PayrollID->Visible) { // PayrollID ?>
	<tr id="r_PayrollID">
		<td><span id="elh_tbl_pension_payroll_PayrollID"><?php echo $tbl_pension_payroll->PayrollID->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->PayrollID->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_PayrollID" class="control-group">
<span<?php echo $tbl_pension_payroll->PayrollID->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->PayrollID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->PensionerID->Visible) { // PensionerID ?>
	<tr id="r_PensionerID">
		<td><span id="elh_tbl_pension_payroll_PensionerID"><?php echo $tbl_pension_payroll->PensionerID->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->PensionerID->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_PensionerID" class="control-group">
<span<?php echo $tbl_pension_payroll->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->PensionerID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->PayrollYear->Visible) { // PayrollYear ?>
	<tr id="r_PayrollYear">
		<td><span id="elh_tbl_pension_payroll_PayrollYear"><?php echo $tbl_pension_payroll->PayrollYear->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->PayrollYear->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_PayrollYear" class="control-group">
<span<?php echo $tbl_pension_payroll->PayrollYear->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->PayrollYear->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->cMonth->Visible) { // cMonth ?>
	<tr id="r_cMonth">
		<td><span id="elh_tbl_pension_payroll_cMonth"><?php echo $tbl_pension_payroll->cMonth->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->cMonth->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_cMonth" class="control-group">
<span<?php echo $tbl_pension_payroll->cMonth->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->cMonth->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->amount->Visible) { // amount ?>
	<tr id="r_amount">
		<td><span id="elh_tbl_pension_payroll_amount"><?php echo $tbl_pension_payroll->amount->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->amount->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_amount" class="control-group">
<span<?php echo $tbl_pension_payroll->amount->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->amount->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->paymentmodeID->Visible) { // paymentmodeID ?>
	<tr id="r_paymentmodeID">
		<td><span id="elh_tbl_pension_payroll_paymentmodeID"><?php echo $tbl_pension_payroll->paymentmodeID->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->paymentmodeID->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_paymentmodeID" class="control-group">
<span<?php echo $tbl_pension_payroll->paymentmodeID->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->paymentmodeID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->Approved->Visible) { // Approved ?>
	<tr id="r_Approved">
		<td><span id="elh_tbl_pension_payroll_Approved"><?php echo $tbl_pension_payroll->Approved->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->Approved->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_Approved" class="control-group">
<span<?php echo $tbl_pension_payroll->Approved->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->Approved->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->Claimed->Visible) { // Claimed ?>
	<tr id="r_Claimed">
		<td><span id="elh_tbl_pension_payroll_Claimed"><?php echo $tbl_pension_payroll->Claimed->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->Claimed->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_Claimed" class="control-group">
<span<?php echo $tbl_pension_payroll->Claimed->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->Claimed->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->Createdby->Visible) { // Createdby ?>
	<tr id="r_Createdby">
		<td><span id="elh_tbl_pension_payroll_Createdby"><?php echo $tbl_pension_payroll->Createdby->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->Createdby->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_Createdby" class="control-group">
<span<?php echo $tbl_pension_payroll->Createdby->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->Createdby->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->CreatedDate->Visible) { // CreatedDate ?>
	<tr id="r_CreatedDate">
		<td><span id="elh_tbl_pension_payroll_CreatedDate"><?php echo $tbl_pension_payroll->CreatedDate->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->CreatedDate->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_CreatedDate" class="control-group">
<span<?php echo $tbl_pension_payroll->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->CreatedDate->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->UpdatedBy->Visible) { // UpdatedBy ?>
	<tr id="r_UpdatedBy">
		<td><span id="elh_tbl_pension_payroll_UpdatedBy"><?php echo $tbl_pension_payroll->UpdatedBy->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->UpdatedBy->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_UpdatedBy" class="control-group">
<span<?php echo $tbl_pension_payroll->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->UpdatedBy->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->UpdatedDate->Visible) { // UpdatedDate ?>
	<tr id="r_UpdatedDate">
		<td><span id="elh_tbl_pension_payroll_UpdatedDate"><?php echo $tbl_pension_payroll->UpdatedDate->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->UpdatedDate->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_UpdatedDate" class="control-group">
<span<?php echo $tbl_pension_payroll->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->UpdatedDate->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
</form>
<script type="text/javascript">
ftbl_pension_payrollview.Init();
</script>
<?php
$tbl_pension_payroll_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_pension_payroll_view->Page_Terminate();
?>
