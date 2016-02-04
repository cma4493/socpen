<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_pensionerinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "tbl_representativegridcls.php" ?>
<?php include_once "tbl_supportgridcls.php" ?>
<?php include_once "tbl_updatesgridcls.php" ?>
<?php include_once "userfn10.php" ?>
<?php include_once "model/DAO.php" ?>
<?php include_once "model/pensionerotherdetails.php" ?>
<?php include_once "model/customsupportadd.php" ?>
<?php include_once "model/psgcclass.php" ?>
<?php include_once "model/SummaryBeneficiary.php" ?>
<?php

//
// Page class
//

$tbl_pensioner_view = NULL; // Initialize page object first

class ctbl_pensioner_view extends ctbl_pensioner {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_pensioner';

	// Page object name
	var $PageObjName = 'tbl_pensioner_view';

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

		// Table object (tbl_pensioner)
		if (!isset($GLOBALS["tbl_pensioner"])) {
			$GLOBALS["tbl_pensioner"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_pensioner"];
		}
		$KeyUrl = "";
		if (@$_GET["SeniorID"] <> "") {
			$this->RecKey["SeniorID"] = $_GET["SeniorID"];
			$KeyUrl .= "&SeniorID=" . urlencode($this->RecKey["SeniorID"]);
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
			define("EW_TABLE_NAME", 'tbl_pensioner', TRUE);

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
			$this->Page_Terminate("tbl_pensionerlist.php");
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
		$this->SeniorID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			if (@$_GET["SeniorID"] <> "") {
				$this->SeniorID->setQueryStringValue($_GET["SeniorID"]);
				$this->RecKey["SeniorID"] = $this->SeniorID->QueryStringValue;
			} else {
				$sReturnUrl = "tbl_pensionerlist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "tbl_pensionerlist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "tbl_pensionerlist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();

		// Set up detail parameters
		$this->SetUpDetailParms();
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
		$item->Visible = FALSE AND ($this->CopyUrl <> "" && $Security->CanAdd());

		// Delete
		$item = &$option->Add("delete");
		$item->Body = "<a onclick=\"return ew_Confirm(ewLanguage.Phrase('DeleteConfirmMsg'));\" class=\"btn btn-pink btn-sm \" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" ." <i class='icon-trash align-top bigger-125'></i> ". $Language->Phrase("ViewPageDeleteLink") . "</a>";
		$item->Visible = ($this->DeleteUrl <> "" && $Security->CanDelete());
		$DetailTableLink = "";
		$option = &$options["detail"];

		// Detail table 'tbl_representative'
		$body = $Language->TablePhrase("tbl_representative", "TblCaption");
		$body = "<a class=\"btn btn-info btn-sm\" href=\"" . ew_HtmlEncode("tbl_representativelist.php?" . EW_TABLE_SHOW_MASTER . "=tbl_pensioner&PensionerID=" . strval($this->PensionerID->CurrentValue) . "") . "\">" . $body . "</a>";
		$item = &$option->Add("detail_tbl_representative");
		$item->Body = $body;
		$item->Visible = $Security->AllowList(CurrentProjectID() . 'tbl_representative');
		if ($item->Visible) {
			if ($DetailTableLink <> "") $DetailTableLink .= ",";
			$DetailTableLink .= "tbl_representative";
		}

		// Detail table 'tbl_support'
		$body = $Language->TablePhrase("tbl_support", "TblCaption");
		$body = "<a class=\"btn btn-info btn-sm\" href=\"" . ew_HtmlEncode("tbl_supportlist.php?" . EW_TABLE_SHOW_MASTER . "=tbl_pensioner&PensionerID=" . strval($this->PensionerID->CurrentValue) . "") . "\">" . $body . "</a>";
		$item = &$option->Add("detail_tbl_support");
		$item->Body = $body;
		$item->Visible = $Security->AllowList(CurrentProjectID() . 'tbl_support');
		if ($item->Visible) {
			if ($DetailTableLink <> "") $DetailTableLink .= ",";
			$DetailTableLink .= "tbl_support";
		}

		// Detail table 'tbl_updates'
		$body = $Language->TablePhrase("tbl_updates", "TblCaption");
		$body = "<a class=\"btn btn-info btn-sm\" href=\"" . ew_HtmlEncode("tbl_updateslist.php?" . EW_TABLE_SHOW_MASTER . "=tbl_pensioner&PensionerID=" . strval($this->PensionerID->CurrentValue) . "") . "\">" . $body . "</a>";
		$item = &$option->Add("detail_tbl_updates");
		$item->Body = $body;
		$item->Visible = FALSE AND $Security->AllowList(CurrentProjectID() . 'tbl_updates');
		if ($item->Visible) {
			if ($DetailTableLink <> "") $DetailTableLink .= ",";
			$DetailTableLink .= "tbl_updates";
		}

		// Multiple details
		if ($this->ShowMultipleDetails) {
			$body = $Language->Phrase("MultipleMasterDetails");
			$body = "<a class=\"btn btn-danger\" data-action=\"view\" href=\"" . ew_HtmlEncode($this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailTableLink)) . "\">" . $body . " <i class='icon-zoom-in align-top bigger-125'></i> " . "</a>";
			$item = &$option->Add("details");
			$item->Body = $body;
			$item->Visible = ($DetailTableLink <> "");

			// Hide single master/detail items
			$ar = explode(",", $DetailTableLink);
			$cnt = count($ar);
			for ($i = 0; $i < $cnt; $i++) {
				if ($item = &$option->GetItem("detail_" . $ar[$i]))
					$item->Visible = FALSE;
			}
		}

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
		$this->SeniorID->setDbValue($rs->fields('SeniorID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->InclusionDate->setDbValue($rs->fields('InclusionDate'));
		$this->hh_id->setDbValue($rs->fields('hh_id'));
		$this->osca_ID->setDbValue($rs->fields('osca_ID'));
		$this->PlaceIssued->setDbValue($rs->fields('PlaceIssued'));
		$this->DateIssued->setDbValue($rs->fields('DateIssued'));
		$this->firstname->setDbValue($rs->fields('firstname'));
		$this->middlename->setDbValue($rs->fields('middlename'));
		$this->lastname->setDbValue($rs->fields('lastname'));
		$this->extname->setDbValue($rs->fields('extname'));
		$this->Birthdate->setDbValue($rs->fields('Birthdate'));
		$this->sex->setDbValue($rs->fields('sex'));
		$this->MaritalID->setDbValue($rs->fields('MaritalID'));
		$this->affliationID->setDbValue($rs->fields('affliationID'));
		$this->psgc_region->setDbValue($rs->fields('psgc_region'));
		$this->psgc_province->setDbValue($rs->fields('psgc_province'));
		$this->psgc_municipality->setDbValue($rs->fields('psgc_municipality'));
		$this->psgc_brgy->setDbValue($rs->fields('psgc_brgy'));
		$this->given_add->setDbValue($rs->fields('given_add'));
		$this->Status->setDbValue($rs->fields('Status'));
		$this->paymentmodeID->setDbValue($rs->fields('paymentmodeID'));
		$this->approved->setDbValue($rs->fields('approved'));
		$this->approvedby->setDbValue($rs->fields('approvedby'));
		$this->DateApproved->setDbValue($rs->fields('DateApproved'));
		$this->ArrangementID->setDbValue($rs->fields('ArrangementID'));
		$this->is_4ps->setDbValue($rs->fields('is_4ps'));
		$this->abandoned->setDbValue($rs->fields('abandoned'));
		$this->Createdby->setDbValue($rs->fields('Createdby'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
		$this->UpdateRemarks->setDbValue($rs->fields('UpdateRemarks'));
		$this->codeGen->setDbValue($rs->fields('codeGen'));
		$this->picture->Upload->DbValue = $rs->fields('picture');
		$this->picturename->setDbValue($rs->fields('picturename'));
		$this->picturetype->setDbValue($rs->fields('picturetype'));
		$this->picturewidth->setDbValue($rs->fields('picturewidth'));
		$this->pictureheight->setDbValue($rs->fields('pictureheight'));
		$this->picturesize->setDbValue($rs->fields('picturesize'));
		$this->hyperlink->setDbValue($rs->fields('hyperlink'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->SeniorID->DbValue = $row['SeniorID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->InclusionDate->DbValue = $row['InclusionDate'];
		$this->hh_id->DbValue = $row['hh_id'];
		$this->osca_ID->DbValue = $row['osca_ID'];
		$this->PlaceIssued->DbValue = $row['PlaceIssued'];
		$this->DateIssued->DbValue = $row['DateIssued'];
		$this->firstname->DbValue = $row['firstname'];
		$this->middlename->DbValue = $row['middlename'];
		$this->lastname->DbValue = $row['lastname'];
		$this->extname->DbValue = $row['extname'];
		$this->Birthdate->DbValue = $row['Birthdate'];
		$this->sex->DbValue = $row['sex'];
		$this->MaritalID->DbValue = $row['MaritalID'];
		$this->affliationID->DbValue = $row['affliationID'];
		$this->psgc_region->DbValue = $row['psgc_region'];
		$this->psgc_province->DbValue = $row['psgc_province'];
		$this->psgc_municipality->DbValue = $row['psgc_municipality'];
		$this->psgc_brgy->DbValue = $row['psgc_brgy'];
		$this->given_add->DbValue = $row['given_add'];
		$this->Status->DbValue = $row['Status'];
		$this->paymentmodeID->DbValue = $row['paymentmodeID'];
		$this->approved->DbValue = $row['approved'];
		$this->approvedby->DbValue = $row['approvedby'];
		$this->DateApproved->DbValue = $row['DateApproved'];
		$this->ArrangementID->DbValue = $row['ArrangementID'];
		$this->is_4ps->DbValue = $row['is_4ps'];
		$this->abandoned->DbValue = $row['abandoned'];
		$this->Createdby->DbValue = $row['Createdby'];
		$this->CreatedDate->DbValue = $row['CreatedDate'];
		$this->UpdatedBy->DbValue = $row['UpdatedBy'];
		$this->UpdatedDate->DbValue = $row['UpdatedDate'];
		$this->UpdateRemarks->DbValue = $row['UpdateRemarks'];
		$this->codeGen->DbValue = $row['codeGen'];
		$this->picture->Upload->DbValue = $row['picture'];
		$this->picturename->DbValue = $row['picturename'];
		$this->picturetype->DbValue = $row['picturetype'];
		$this->picturewidth->DbValue = $row['picturewidth'];
		$this->pictureheight->DbValue = $row['pictureheight'];
		$this->picturesize->DbValue = $row['picturesize'];
		$this->hyperlink->DbValue = $row['hyperlink'];
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
		// SeniorID
		// PensionerID
		// InclusionDate
		// hh_id
		// osca_ID
		// PlaceIssued
		// DateIssued
		// firstname
		// middlename
		// lastname
		// extname
		// Birthdate
		// sex
		// MaritalID
		// affliationID
		// psgc_region
		// psgc_province
		// psgc_municipality
		// psgc_brgy
		// given_add
		// Status
		// paymentmodeID
		// approved
		// approvedby
		// DateApproved
		// ArrangementID
		// is_4ps
		// abandoned
		// Createdby
		// CreatedDate
		// UpdatedBy
		// UpdatedDate
		// UpdateRemarks
		// codeGen
		// picture
		// picturename
		// picturetype
		// picturewidth
		// pictureheight
		// picturesize
		// hyperlink

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// SeniorID
			$this->SeniorID->ViewValue = $this->SeniorID->CurrentValue;
			$this->SeniorID->ViewCustomAttributes = "";

			// PensionerID
			$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewCustomAttributes = "";

			// InclusionDate
			$this->InclusionDate->ViewValue = $this->InclusionDate->CurrentValue;
			$this->InclusionDate->ViewValue = ew_FormatDateTime($this->InclusionDate->ViewValue, 6);
			$this->InclusionDate->ViewCustomAttributes = "";

			// hh_id
			$this->hh_id->ViewValue = $this->hh_id->CurrentValue;
			$this->hh_id->ViewCustomAttributes = "";

			// osca_ID
			$this->osca_ID->ViewValue = $this->osca_ID->CurrentValue;
			$this->osca_ID->ViewCustomAttributes = "";

			// PlaceIssued
			$this->PlaceIssued->ViewValue = $this->PlaceIssued->CurrentValue;
			$this->PlaceIssued->ViewCustomAttributes = "";

			// DateIssued
			$this->DateIssued->ViewValue = $this->DateIssued->CurrentValue;
			$this->DateIssued->ViewValue = ew_FormatDateTime($this->DateIssued->ViewValue, 6);
			$this->DateIssued->ViewCustomAttributes = "";

			// firstname
			$this->firstname->ViewValue = $this->firstname->CurrentValue;
			$this->firstname->ViewCustomAttributes = "";

			// middlename
			$this->middlename->ViewValue = $this->middlename->CurrentValue;
			$this->middlename->ViewCustomAttributes = "";

			// lastname
			$this->lastname->ViewValue = $this->lastname->CurrentValue;
			$this->lastname->ViewCustomAttributes = "";

			// extname
			$this->extname->ViewValue = $this->extname->CurrentValue;
			$this->extname->ViewCustomAttributes = "";

			// Birthdate
			$this->Birthdate->ViewValue = $this->Birthdate->CurrentValue;
			$this->Birthdate->ViewValue = ew_FormatDateTime($this->Birthdate->ViewValue, 6);
			$this->Birthdate->ViewCustomAttributes = "";

			// sex
			if (strval($this->sex->CurrentValue) <> "") {
				switch ($this->sex->CurrentValue) {
					case $this->sex->FldTagValue(1):
						$this->sex->ViewValue = $this->sex->FldTagCaption(1) <> "" ? $this->sex->FldTagCaption(1) : $this->sex->CurrentValue;
						break;
					case $this->sex->FldTagValue(2):
						$this->sex->ViewValue = $this->sex->FldTagCaption(2) <> "" ? $this->sex->FldTagCaption(2) : $this->sex->CurrentValue;
						break;
					default:
						$this->sex->ViewValue = $this->sex->CurrentValue;
				}
			} else {
				$this->sex->ViewValue = NULL;
			}
			$this->sex->ViewCustomAttributes = "";

			// MaritalID
			if (strval($this->MaritalID->CurrentValue) <> "") {
				$sFilterWrk = "`MaritalID`" . ew_SearchString("=", $this->MaritalID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `MaritalID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_civilstatus`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->MaritalID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `MaritalID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->MaritalID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->MaritalID->ViewValue = $this->MaritalID->CurrentValue;
				}
			} else {
				$this->MaritalID->ViewValue = NULL;
			}
			$this->MaritalID->ViewCustomAttributes = "";

			// affliationID
			if (strval($this->affliationID->CurrentValue) <> "") {
				$sFilterWrk = "`affliationID`" . ew_SearchString("=", $this->affliationID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `affliationID`, `aff_description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_affliation`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->affliationID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `affliationID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->affliationID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->affliationID->ViewValue = $this->affliationID->CurrentValue;
				}
			} else {
				$this->affliationID->ViewValue = NULL;
			}
			$this->affliationID->ViewCustomAttributes = "";

			// psgc_region
			if (strval($this->psgc_region->CurrentValue) <> "") {
				$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->psgc_region->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_region, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `region_code` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->psgc_region->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->psgc_region->ViewValue = $this->psgc_region->CurrentValue;
				}
			} else {
				$this->psgc_region->ViewValue = NULL;
			}
			$this->psgc_region->ViewCustomAttributes = "";

			// psgc_province
			if (strval($this->psgc_province->CurrentValue) <> "") {
				$sFilterWrk = "`prov_code`" . ew_SearchString("=", $this->psgc_province->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_provinces`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_province, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `prov_name` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->psgc_province->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->psgc_province->ViewValue = $this->psgc_province->CurrentValue;
				}
			} else {
				$this->psgc_province->ViewValue = NULL;
			}
			$this->psgc_province->ViewCustomAttributes = "";

			// psgc_municipality
			if (strval($this->psgc_municipality->CurrentValue) <> "") {
				$sFilterWrk = "`city_code`" . ew_SearchString("=", $this->psgc_municipality->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_cities`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_municipality, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `city_name` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->psgc_municipality->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->psgc_municipality->ViewValue = $this->psgc_municipality->CurrentValue;
				}
			} else {
				$this->psgc_municipality->ViewValue = NULL;
			}
			$this->psgc_municipality->ViewCustomAttributes = "";

			// psgc_brgy
			if (strval($this->psgc_brgy->CurrentValue) <> "") {
				$sFilterWrk = "`brgy_code`" . ew_SearchString("=", $this->psgc_brgy->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_brgy`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_brgy, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `brgy_name` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->psgc_brgy->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->psgc_brgy->ViewValue = $this->psgc_brgy->CurrentValue;
				}
			} else {
				$this->psgc_brgy->ViewValue = NULL;
			}
			$this->psgc_brgy->ViewCustomAttributes = "";

			// given_add
			$this->given_add->ViewValue = $this->given_add->CurrentValue;
			$this->given_add->ViewCustomAttributes = "";

			// Status
			if (strval($this->Status->CurrentValue) <> "") {
				$sFilterWrk = "`statusID`" . ew_SearchString("=", $this->Status->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `statusID`, `status` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_status`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Status, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `statusID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Status->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Status->ViewValue = $this->Status->CurrentValue;
				}
			} else {
				$this->Status->ViewValue = NULL;
			}
			$this->Status->ViewCustomAttributes = "";

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

			// approved
			$this->approved->ViewValue = $this->approved->CurrentValue;
			$this->approved->ViewCustomAttributes = "";

			// approvedby
			$this->approvedby->ViewValue = $this->approvedby->CurrentValue;
			$this->approvedby->ViewCustomAttributes = "";

			// DateApproved
			$this->DateApproved->ViewValue = $this->DateApproved->CurrentValue;
			$this->DateApproved->ViewValue = ew_FormatDateTime($this->DateApproved->ViewValue, 6);
			$this->DateApproved->ViewCustomAttributes = "";

			// ArrangementID
			if (strval($this->ArrangementID->CurrentValue) <> "") {
				$sFilterWrk = "`ArrangementID`" . ew_SearchString("=", $this->ArrangementID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `ArrangementID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_arrangement`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->ArrangementID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `ArrangementID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->ArrangementID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->ArrangementID->ViewValue = $this->ArrangementID->CurrentValue;
				}
			} else {
				$this->ArrangementID->ViewValue = NULL;
			}
			$this->ArrangementID->ViewCustomAttributes = "";

			// is_4ps
			if (strval($this->is_4ps->CurrentValue) <> "") {
				switch ($this->is_4ps->CurrentValue) {
					case $this->is_4ps->FldTagValue(1):
						$this->is_4ps->ViewValue = $this->is_4ps->FldTagCaption(1) <> "" ? $this->is_4ps->FldTagCaption(1) : $this->is_4ps->CurrentValue;
						break;
					case $this->is_4ps->FldTagValue(2):
						$this->is_4ps->ViewValue = $this->is_4ps->FldTagCaption(2) <> "" ? $this->is_4ps->FldTagCaption(2) : $this->is_4ps->CurrentValue;
						break;
					default:
						$this->is_4ps->ViewValue = $this->is_4ps->CurrentValue;
				}
			} else {
				$this->is_4ps->ViewValue = NULL;
			}
			$this->is_4ps->ViewCustomAttributes = "";

			// abandoned
			if (strval($this->abandoned->CurrentValue) <> "") {
				switch ($this->abandoned->CurrentValue) {
					case $this->abandoned->FldTagValue(1):
						$this->abandoned->ViewValue = $this->abandoned->FldTagCaption(1) <> "" ? $this->abandoned->FldTagCaption(1) : $this->abandoned->CurrentValue;
						break;
					case $this->abandoned->FldTagValue(2):
						$this->abandoned->ViewValue = $this->abandoned->FldTagCaption(2) <> "" ? $this->abandoned->FldTagCaption(2) : $this->abandoned->CurrentValue;
						break;
					default:
						$this->abandoned->ViewValue = $this->abandoned->CurrentValue;
				}
			} else {
				$this->abandoned->ViewValue = NULL;
			}
			$this->abandoned->ViewCustomAttributes = "";

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

			// UpdateRemarks
			$this->UpdateRemarks->ViewValue = $this->UpdateRemarks->CurrentValue;
			$this->UpdateRemarks->ViewCustomAttributes = "";

			// codeGen
			$this->codeGen->ViewValue = $this->codeGen->CurrentValue;
			$this->codeGen->ViewCustomAttributes = "";

			// picture
			if (!ew_Empty($this->picture->Upload->DbValue)) {
				if (!is_null($this->picturewidth->CurrentValue)) {
					$this->picture->ImageWidth = 200;
					//$this->picture->ImageWidth = $this->picturewidth->CurrentValue; jfsbaldo
				} else {
					$this->picture->ImageWidth = 0;
				}
				if (!is_null($this->pictureheight->CurrentValue)) {
					$this->picture->ImageHeight = 200;
					//$this->picture->ImageHeight = $this->pictureheight->CurrentValue; jfsbaldo
				} else {
					$this->picture->ImageHeight = 0;
				}
				$this->picture->ImageAlt = $this->picture->FldAlt();
				$this->picture->ViewValue = "tbl_pensioner_picture_bv.php?" . "SeniorID=" . $this->SeniorID->CurrentValue;
			} else {
				$this->picture->ViewValue = "";
			}
			$this->picture->ViewCustomAttributes = "";

			// SeniorID
			$this->SeniorID->LinkCustomAttributes = "";
			$this->SeniorID->HrefValue = "";
			$this->SeniorID->TooltipValue = "";

			// PensionerID
			$this->PensionerID->LinkCustomAttributes = "";
			$this->PensionerID->HrefValue = "";
			$this->PensionerID->TooltipValue = "";

			// InclusionDate
			$this->InclusionDate->LinkCustomAttributes = "";
			$this->InclusionDate->HrefValue = "";
			$this->InclusionDate->TooltipValue = "";

			// hh_id
			$this->hh_id->LinkCustomAttributes = "";
			$this->hh_id->HrefValue = "";
			$this->hh_id->TooltipValue = "";

			// osca_ID
			$this->osca_ID->LinkCustomAttributes = "";
			$this->osca_ID->HrefValue = "";
			$this->osca_ID->TooltipValue = "";

			// PlaceIssued
			$this->PlaceIssued->LinkCustomAttributes = "";
			$this->PlaceIssued->HrefValue = "";
			$this->PlaceIssued->TooltipValue = "";

			// DateIssued
			$this->DateIssued->LinkCustomAttributes = "";
			$this->DateIssued->HrefValue = "";
			$this->DateIssued->TooltipValue = "";

			// firstname
			$this->firstname->LinkCustomAttributes = "";
			$this->firstname->HrefValue = "";
			$this->firstname->TooltipValue = "";

			// middlename
			$this->middlename->LinkCustomAttributes = "";
			$this->middlename->HrefValue = "";
			$this->middlename->TooltipValue = "";

			// lastname
			$this->lastname->LinkCustomAttributes = "";
			$this->lastname->HrefValue = "";
			$this->lastname->TooltipValue = "";

			// extname
			$this->extname->LinkCustomAttributes = "";
			$this->extname->HrefValue = "";
			$this->extname->TooltipValue = "";

			// Birthdate
			$this->Birthdate->LinkCustomAttributes = "";
			$this->Birthdate->HrefValue = "";
			$this->Birthdate->TooltipValue = "";

			// sex
			$this->sex->LinkCustomAttributes = "";
			$this->sex->HrefValue = "";
			$this->sex->TooltipValue = "";

			// MaritalID
			$this->MaritalID->LinkCustomAttributes = "";
			$this->MaritalID->HrefValue = "";
			$this->MaritalID->TooltipValue = "";

			// affliationID
			$this->affliationID->LinkCustomAttributes = "";
			$this->affliationID->HrefValue = "";
			$this->affliationID->TooltipValue = "";

			// psgc_region
			$this->psgc_region->LinkCustomAttributes = "";
			$this->psgc_region->HrefValue = "";
			$this->psgc_region->TooltipValue = "";

			// psgc_province
			$this->psgc_province->LinkCustomAttributes = "";
			$this->psgc_province->HrefValue = "";
			$this->psgc_province->TooltipValue = "";

			// psgc_municipality
			$this->psgc_municipality->LinkCustomAttributes = "";
			$this->psgc_municipality->HrefValue = "";
			$this->psgc_municipality->TooltipValue = "";

			// psgc_brgy
			$this->psgc_brgy->LinkCustomAttributes = "";
			$this->psgc_brgy->HrefValue = "";
			$this->psgc_brgy->TooltipValue = "";

			// given_add
			$this->given_add->LinkCustomAttributes = "";
			$this->given_add->HrefValue = "";
			$this->given_add->TooltipValue = "";

			// Status
			$this->Status->LinkCustomAttributes = "";
			$this->Status->HrefValue = "";
			$this->Status->TooltipValue = "";

			// paymentmodeID
			$this->paymentmodeID->LinkCustomAttributes = "";
			$this->paymentmodeID->HrefValue = "";
			$this->paymentmodeID->TooltipValue = "";

			// approved
			$this->approved->LinkCustomAttributes = "";
			$this->approved->HrefValue = "";
			$this->approved->TooltipValue = "";

			// approvedby
			$this->approvedby->LinkCustomAttributes = "";
			$this->approvedby->HrefValue = "";
			$this->approvedby->TooltipValue = "";

			// DateApproved
			$this->DateApproved->LinkCustomAttributes = "";
			$this->DateApproved->HrefValue = "";
			$this->DateApproved->TooltipValue = "";

			// ArrangementID
			$this->ArrangementID->LinkCustomAttributes = "";
			$this->ArrangementID->HrefValue = "";
			$this->ArrangementID->TooltipValue = "";

			// is_4ps
			$this->is_4ps->LinkCustomAttributes = "";
			$this->is_4ps->HrefValue = "";
			$this->is_4ps->TooltipValue = "";

			// abandoned
			$this->abandoned->LinkCustomAttributes = "";
			$this->abandoned->HrefValue = "";
			$this->abandoned->TooltipValue = "";

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

			// UpdateRemarks
			$this->UpdateRemarks->LinkCustomAttributes = "";
			$this->UpdateRemarks->HrefValue = "";
			$this->UpdateRemarks->TooltipValue = "";

			// codeGen
			$this->codeGen->LinkCustomAttributes = "";
			$this->codeGen->HrefValue = "";
			$this->codeGen->TooltipValue = "";

			// picture
			$this->picture->LinkCustomAttributes = "";
			if (!ew_Empty($this->hyperlink->CurrentValue)) {
				$this->picture->HrefValue = ((!empty($this->hyperlink->ViewValue)) ? $this->hyperlink->ViewValue : $this->hyperlink->CurrentValue); // Add prefix/suffix
				$this->picture->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->picture->HrefValue = ew_ConvertFullUrl($this->picture->HrefValue);
			} else {
				$this->picture->HrefValue = "";
			}
			$this->picture->HrefValue2 = "tbl_pensioner_picture_bv.php?SeniorID=" . $this->SeniorID->CurrentValue;
			$this->picture->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up detail parms based on QueryString
	function SetUpDetailParms() {

		// Get the keys for master table
		if (isset($_GET[EW_TABLE_SHOW_DETAIL])) {
			$sDetailTblVar = $_GET[EW_TABLE_SHOW_DETAIL];
			$this->setCurrentDetailTable($sDetailTblVar);
		} else {
			$sDetailTblVar = $this->getCurrentDetailTable();
		}
		if ($sDetailTblVar <> "") {
			$DetailTblVar = explode(",", $sDetailTblVar);
			if (in_array("tbl_representative", $DetailTblVar)) {
				if (!isset($GLOBALS["tbl_representative_grid"]))
					$GLOBALS["tbl_representative_grid"] = new ctbl_representative_grid;
				if ($GLOBALS["tbl_representative_grid"]->DetailView) {
					$GLOBALS["tbl_representative_grid"]->CurrentMode = "view";

					// Save current master table to detail table
					$GLOBALS["tbl_representative_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["tbl_representative_grid"]->setStartRecordNumber(1);
					$GLOBALS["tbl_representative_grid"]->PensionerID->FldIsDetailKey = TRUE;
					$GLOBALS["tbl_representative_grid"]->PensionerID->CurrentValue = $this->PensionerID->CurrentValue;
					$GLOBALS["tbl_representative_grid"]->PensionerID->setSessionValue($GLOBALS["tbl_representative_grid"]->PensionerID->CurrentValue);
				}
			}
			if (in_array("tbl_support", $DetailTblVar)) {
				if (!isset($GLOBALS["tbl_support_grid"]))
					$GLOBALS["tbl_support_grid"] = new ctbl_support_grid;
				if ($GLOBALS["tbl_support_grid"]->DetailView) {
					$GLOBALS["tbl_support_grid"]->CurrentMode = "view";

					// Save current master table to detail table
					$GLOBALS["tbl_support_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["tbl_support_grid"]->setStartRecordNumber(1);
					$GLOBALS["tbl_support_grid"]->PensionerID->FldIsDetailKey = TRUE;
					$GLOBALS["tbl_support_grid"]->PensionerID->CurrentValue = $this->PensionerID->CurrentValue;
					$GLOBALS["tbl_support_grid"]->PensionerID->setSessionValue($GLOBALS["tbl_support_grid"]->PensionerID->CurrentValue);
				}
			}
			if (in_array("tbl_updates", $DetailTblVar)) {
				if (!isset($GLOBALS["tbl_updates_grid"]))
					$GLOBALS["tbl_updates_grid"] = new ctbl_updates_grid;
				if ($GLOBALS["tbl_updates_grid"]->DetailView) {
					$GLOBALS["tbl_updates_grid"]->CurrentMode = "view";

					// Save current master table to detail table
					$GLOBALS["tbl_updates_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["tbl_updates_grid"]->setStartRecordNumber(1);
					$GLOBALS["tbl_updates_grid"]->PensionerID->FldIsDetailKey = TRUE;
					$GLOBALS["tbl_updates_grid"]->PensionerID->CurrentValue = $this->PensionerID->CurrentValue;
					$GLOBALS["tbl_updates_grid"]->PensionerID->setSessionValue($GLOBALS["tbl_updates_grid"]->PensionerID->CurrentValue);
				}
			}
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_pensionerlist.php", $this->TableVar);
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
if (!isset($tbl_pensioner_view)) $tbl_pensioner_view = new ctbl_pensioner_view();

// Page init
$tbl_pensioner_view->Page_Init();

// Page main
$tbl_pensioner_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_pensioner_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_pensioner_view = new ew_Page("tbl_pensioner_view");
tbl_pensioner_view.PageID = "view"; // Page ID
var EW_PAGE_ID = tbl_pensioner_view.PageID; // For backward compatibility

// Form object
var ftbl_pensionerview = new ew_Form("ftbl_pensionerview");

// Form_CustomValidate event
ftbl_pensionerview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_pensionerview.ValidateRequired = true;
<?php } else { ?>
ftbl_pensionerview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_pensionerview.Lists["x_MaritalID"] = {"LinkField":"x_MaritalID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerview.Lists["x_affliationID"] = {"LinkField":"x_affliationID","Ajax":true,"AutoFill":false,"DisplayFields":["x_aff_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerview.Lists["x_psgc_region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerview.Lists["x_psgc_province"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerview.Lists["x_psgc_municipality"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerview.Lists["x_psgc_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerview.Lists["x_Status"] = {"LinkField":"x_statusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_status","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerview.Lists["x_paymentmodeID"] = {"LinkField":"x_paymentmodeID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerview.Lists["x_ArrangementID"] = {"LinkField":"x_ArrangementID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
function loadDoc()
{
	document.getElementById("jfsbaldo").style.display = "block";
}

function loadDoc2()
{
	document.getElementById("jfsbaldo2").style.display = "block";
}
function getProv(str)
{
	document.getElementById("jb_region_code").value = str;
	if (str=="")
	{
		document.getElementById("div_prov").innerHTML="";
		return;
	}
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById("div_prov").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","ajax_prov.php?q="+str,true);
	xmlhttp.send();
}

function getCity(str)
{
	document.getElementById("jb_prov_code").value = str;
	if (str=="")
	{
		document.getElementById("div_city").innerHTML="";
		return;
	}
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById("div_city").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","ajax_city.php?q="+str,true);
	xmlhttp.send();
}

function getBrgy(str)
{
	document.getElementById("jb_city_code").value = str;
	if (str=="")
	{
		document.getElementById("div_brgy").innerHTML="";
		return;
	}
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById("div_brgy").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","ajax_brgy.php?q="+str,true);
	xmlhttp.send();
}

function showkindsupport(str){
	if(str == "1"){
		document.getElementById("selectoptionKindSupport").style.display = "block";
	} else {
		document.getElementById("jb_kindsupport").value="";
		document.getElementById("selectoptionKindSupport").style.display = "none";
	}
}

function showkinddisability(str){
	if(str == "1"){
		document.getElementById("selectoptionlibdisability").style.display = "block";
	} else {
		document.getElementById("jb_disability").value="";
		document.getElementById("selectoptionlibdisability").style.display = "none";
	}
}

function showkindassistiveDevice(str){
	if(str == "1"){
		document.getElementById("selectoptionlibassistive").style.display = "block";
	} else {
		document.getElementById("jb_assistive").value="";
		document.getElementById("selectoptionlibassistive").style.display = "none";
	}
}

function showkindillness(str){
	if(str == "1"){
		document.getElementById("selectoptionlibillness").style.display = "block";
	} else {
		document.getElementById("jb_illness").value="";
		document.getElementById("selectoptionlibillness").style.display = "none";
	}
}
</script>
<?php //$Breadcrumb->Render(); ?>
<div class="ewViewExportOptions">
<?php $tbl_pensioner_view->ExportOptions->Render("body") ?>
<?php if (!$tbl_pensioner_view->ExportOptions->UseDropDownButton) { ?>
</div>
<div class="ewViewOtherOptions">
<?php } ?>
<?php
	/*foreach ($tbl_pensioner_view->OtherOptions as &$option)
		$option->Render("body");*/
?>
</div>
<?php $tbl_pensioner_view->ShowPageHeader(); ?>
<?php
$tbl_pensioner_view->ShowMessage();
?>
<div id="user-profile-1" class="user-profile row">
	<div class="col-xs-12 col-sm-3 center">
		<div>
			<span class="profile-picture">
				<!--<img id="avatar" class="editable img-responsive editable-click editable-empty" alt="Alex's Avatar" src="assets/avatars/profile-pic.jpg"></img>-->
				<?php if ($tbl_pensioner->picture->LinkAttributes() <> "") { ?>
					<?php if (!empty($tbl_pensioner->picture->Upload->DbValue)) { ?>
						<a<?php echo $tbl_pensioner->picture->LinkAttributes() ?>><img src="<?php echo $tbl_pensioner->picture->ViewValue ?>" alt="" style="border: 0;"<?php echo $tbl_pensioner->picture->ViewAttributes() ?>></a>
					<?php } elseif (!in_array($tbl_pensioner->CurrentAction, array("I", "edit", "gridedit"))) { ?>
						&nbsp;
					<?php } ?>
				<?php } else { ?>
					<?php if (!empty($tbl_pensioner->picture->Upload->DbValue)) { ?>
						<img src="<?php echo $tbl_pensioner->picture->ViewValue ?>" alt="" style="border: 0;"<?php echo $tbl_pensioner->picture->ViewAttributes() ?>>
					<?php } elseif (!in_array($tbl_pensioner->CurrentAction, array("I", "edit", "gridedit"))) { ?>
						&nbsp;
					<?php } ?>
				<?php } ?>
			</span>

			<div class="space-4"></div>

			<div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
				<div class="inline position-relative">
					<?php if ($tbl_pensioner->picture->Visible) { // picture ?>
						<span class="white"><?php echo $tbl_pensioner->firstname->ViewValue . ' ' . $tbl_pensioner->middlename->ViewValue . ' ' . $tbl_pensioner->lastname->ViewValue . ' ' . $tbl_pensioner->extname->ViewValue ?></span>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="space-6"></div>

		<div class="profile-contact-info">
			<div class="profile-contact-links align-left">
				<a class="btn btn-link" href="tbl_pensioneradd.php?showdetail=">
					<i class="icon-plus-sign bigger-120 green"></i>
					Add Pensioner
				</a>

				<a class="btn btn-link" href="tbl_pensioneredit.php?showdetail=&SeniorID=<?php echo $tbl_pensioner->SeniorID->ViewValue ?>">
					<i class="icon-edit bigger-120 pink"></i>
					Edit Profile
				</a>

				<a onclick="return confirm('Are you sure you want to delete?')" class="btn btn-link" href="tbl_pensionerdelete.php?SeniorID=<?php echo $tbl_pensioner->SeniorID->ViewValue ?>">
					<i class="icon-trash bigger-125 red"></i>
					Delete Profile
				</a>
			</div>

			<div class="space-6"></div>

			 <!-- <div class="profile-social-links center">
				<a href="#" class="tooltip-info" title="" data-original-title="Visit my Facebook">
					<i class="middle icon-facebook-sign icon-2x blue"></i>
				</a>

				<a href="#" class="tooltip-info" title="" data-original-title="Visit my Twitter">
					<i class="middle icon-twitter-sign icon-2x light-blue"></i>
				</a>

				<a href="#" class="tooltip-error" title="" data-original-title="Visit my Pinterest">
					<i class="middle icon-pinterest-sign icon-2x red"></i>
				</a>
			</div> -->
		</div>

		<div class="hr hr12 dotted"></div>
		<?php
		$SummaryBeneficiary = new SummaryBeneficiary();
		?>

		<div class="clearfix">
			<div class="grid2">
				<span class="bigger-175 blue"><?php echo $SummaryBeneficiary->getTotalBenefitsReceived($tbl_pensioner->PensionerID->CurrentValue) ?></span>

				<br>
				Total Benefits Received
			</div>

			<div class="grid2">
				<span class="bigger-175 blue"><?php echo $SummaryBeneficiary->getTotalBenefitsToBeReceived($tbl_pensioner->PensionerID->CurrentValue) ?></span>

				<br>
				Benefits to be Received
			</div>
		</div>

		<div class="hr hr16 dotted"></div>
	</div>

	<div class="col-xs-12 col-sm-9">
		<div class="table-header">
			Profile
		</div>
		<!--<div class="center">
			<span class="btn btn-app btn-sm btn-light no-hover">
				<span class="line-height-1 bigger-170 blue"> 1,411 </span>

				<br>
				<span class="line-height-1 smaller-90"> Views </span>
			</span>

			<span class="btn btn-app btn-sm btn-yellow no-hover">
				<span class="line-height-1 bigger-170"> 32 </span>

				<br>
				<span class="line-height-1 smaller-90"> Followers </span>
			</span>

			<span class="btn btn-app btn-sm btn-pink no-hover">
				<span class="line-height-1 bigger-170"> 4 </span>

				<br>
				<span class="line-height-1 smaller-90"> Projects </span>
			</span>

			<span class="btn btn-app btn-sm btn-grey no-hover">
				<span class="line-height-1 bigger-170"> 23 </span>

				<br>
				<span class="line-height-1 smaller-90"> Reviews </span>
			</span>

			<span class="btn btn-app btn-sm btn-success no-hover">
				<span class="line-height-1 bigger-170"> 7 </span>

				<br>
				<span class="line-height-1 smaller-90"> Albums </span>
			</span>

			<span class="btn btn-app btn-sm btn-primary no-hover">
				<span class="line-height-1 bigger-170"> 55 </span>

				<br>
				<span class="line-height-1 smaller-90"> Contacts </span>
			</span>
		</div> -->

		<form name="ftbl_pensionerview" id="ftbl_pensionerview" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
			<input type="hidden" name="t" value="tbl_pensioner">
			<div class="profile-user-info profile-user-info-striped">
				<?php if ($tbl_pensioner->PensionerID->Visible) { // PensionerID ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->PensionerID->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php echo $tbl_pensioner->PensionerID->ViewValue ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->InclusionDate->Visible) { // InclusionDate ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->InclusionDate->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->InclusionDate->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->InclusionDate->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->is_4ps->Visible) { // is_4ps ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->is_4ps->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->is_4ps->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->is_4ps->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->hh_id->Visible) { // hh_id ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->hh_id->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->hh_id->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->hh_id->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->osca_ID->Visible) { // osca_ID ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->osca_ID->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->osca_ID->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->osca_ID->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->PlaceIssued->Visible) { // PlaceIssued?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->PlaceIssued->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->PlaceIssued->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->PlaceIssued->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->DateIssued->Visible) { // DateIssued?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->DateIssued->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->DateIssued->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->DateIssued->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->Birthdate->Visible) { // Birthdate ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->Birthdate->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->Birthdate->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->Birthdate->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->sex->Visible) { // sex ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->sex->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->sex->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->sex->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->MaritalID->Visible) { // MaritalID ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->MaritalID->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->MaritalID->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->MaritalID->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->affliationID->Visible) { // affliationID ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->affliationID->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->affliationID->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->affliationID->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->psgc_region->Visible) { // psgc_region ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->psgc_region->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->psgc_region->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->psgc_region->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->psgc_province->Visible) { // psgc_province ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->psgc_province->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->psgc_province->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->psgc_province->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->psgc_municipality->Visible) { // psgc_municipality ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->psgc_municipality->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->psgc_municipality->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->psgc_municipality->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->psgc_brgy->Visible) { // psgc_brgy ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->psgc_brgy->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->psgc_brgy->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->psgc_brgy->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->given_add->Visible) { // given_add ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->given_add->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->given_add->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->given_add->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->Status->Visible) { // Status ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->Status->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->Status->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->Status->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if ($tbl_pensioner->paymentmodeID->Visible) { // paymentmodeID ?>
					<div class="profile-info-row">
						<div class="profile-info-name"> <?php echo $tbl_pensioner->paymentmodeID->FldCaption() ?> </div>

						<div class="profile-info-value">
							<?php if ($tbl_pensioner->paymentmodeID->ViewValue <> ''){ ?>
								<?php echo $tbl_pensioner->paymentmodeID->ViewValue ?>
							<?php } else { ?>
								N/A
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			</div>
<?php
	if (in_array("tbl_representative", explode(",", $tbl_pensioner->getCurrentDetailTable())) && $tbl_representative->DetailView) {
?>
<?php include_once "tbl_representativegrid.php" ?>
<?php } ?>
<?php
	if (in_array("tbl_support", explode(",", $tbl_pensioner->getCurrentDetailTable())) && $tbl_support->DetailView) {
?>
<?php include_once "tbl_supportgrid.php" ?>
<?php } ?>
<?php
	if (in_array("tbl_updates", explode(",", $tbl_pensioner->getCurrentDetailTable())) && $tbl_updates->DetailView) {
?>
<?php include_once "tbl_updatesgrid.php" ?>
<?php } ?>
</form>

		<div class="space-20"></div>
		<?php
		/**
		 * JFSBALDO: Type and Kinds of Support Received
		 */
		$pensionerotherdetails = new pensionerotherdetails($tbl_pensioner->PensionerID->CurrentValue);

		$customsupportadd = new customsupportadd();
		$customsupportadd->paramGetter('jb_kindsupport','jb_disability','jb_assistive','jb_illness','jb_physicalcondition','jb_lib_relationship');
		?>
		<div class="widget-box transparent">
			<div class="table-header">
				Type and Kinds of Support Received
			</div>
			<div class="table-responsive">
				<?php if ($pensionerotherdetails->checkSupportData() > 0){ ?>
				<table class="table table-striped table-bordered table-hover">
					<thead>
					<tr>
						<th class="center">Family Support?</th>
						<th class="center">Type of Support</th>
						<th class="center">Meals a Day</th>
						<th class="center">Disabled?</th>
						<th class="center">Type of Disability</th>
						<th class="center">Immobile?</th>
						<th class="center">Type of Assistive Device</th>
						<th class="center">Currently Ill?</th>
						<th class="center">Type of Illness</th>
						<th class="center">Physical Condition</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($pensionerotherdetails->getSupport() as $supportData): ?>
						<tr>
							<td class="center"><?php echo $supportData['family_support'] ?></td>
							<td class="center"><?php echo $supportData['KindSupID'] ?></td>
							<td class="center"><?php echo $supportData['meals'] ?></td>
							<td class="center"><?php echo $supportData['disability'] ?></td>
							<td class="center"><?php echo $supportData['disabilityID'] ?></td>
							<td class="center"><?php echo $supportData['immobile'] ?></td>
							<td class="center"><?php echo $supportData['assistiveID'] ?></td>
							<td class="center"><?php echo $supportData['preEx_illness'] ?></td>
							<td class="center"><?php echo $supportData['illnessID'] ?></td>
							<td class="center"><?php echo $supportData['physconditionID'] ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
				<?php } else { ?>
					<a class="btn btn-sm btn-success btn-block" onclick="loadDoc()">Add Support Details</a>
					<div id="jfsbaldo" style="display: none">
						<form class="ewForm form-horizontal" name="addsupport" id="addsupport" method="get" action="tbl_pensionerview_add.php">
							<div class="profile-user-info profile-user-info-striped">
								<div class="profile-info-row"> <!-- PensionerID -->
									<div class="profile-info-name">PensionerID</div>
									<div class="profile-info-value">
										<?php echo $tbl_pensioner->PensionerID->CurrentValue ?>
										<input type="hidden" name="jb_PensionerID" value="<?php echo $tbl_pensioner->PensionerID->CurrentValue ?>">
										<input type="hidden" name="jb_SeniorID" value="<?php echo $tbl_pensioner->SeniorID->CurrentValue ?>">
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name">Family Support</div>
									<div class="profile-info-value">
										<select name="jb_family_support" onchange="showkindsupport(this.value)">
											<option value="">Please Select</option>
											<option value="0" selected>0 - No</option>
											<option value="1">1 - Yes</option>
										</select>
									</div>
								</div>
								<div id="selectoptionKindSupport" class="profile-info-row" style="display: none;">
									<div class="profile-info-name">Kind of Support</div>
									<div class="profile-info-value">
										<?php echo $customsupportadd->inputoptions()['lib_support'] ?>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name">Meals a Day</div>
									<div class="profile-info-value">
										<input type="text" name="jb_mealsaday">
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name">Disabled?</div>
									<div class="profile-info-value">
										<select id="jb_is_disabled" name="jb_is_disabled" onchange="showkinddisability(this.value)">
											<option value="">Please Select</option>
											<option value="0" selected>0 - No</option>
											<option value="1">1 - Yes</option>
										</select>
									</div>
								</div>
								<div id="selectoptionlibdisability" class="profile-info-row" style="display: none">
									<div class="profile-info-name">Type of Disability</div>
									<div class="profile-info-value">
										<?php echo $customsupportadd->inputoptions()['lib_disability'] ?>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name">Immobile?</div>
									<div class="profile-info-value">
										<select id="jb_is_immobile" name="jb_is_immobile" onchange="showkindassistiveDevice(this.value)">
											<option value="">Please Select</option>
											<option value="0" selected>0 - No</option>
											<option value="1">1 - Yes</option>
										</select>
									</div>
								</div>
								<div id="selectoptionlibassistive" class="profile-info-row" style="display: none">
									<div class="profile-info-name">Type of Assistive Device</div>
									<div class="profile-info-value">
										<?php echo $customsupportadd->inputoptions()['lib_assistive'] ?>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name">Pre Exisint Illness?</div>
									<div class="profile-info-value">
										<select name="jb_is_ill" onchange="showkindillness(this.value)">
											<option value="">Please Select</option>
											<option value="0" selected>0 - No</option>
											<option value="1">1 - Yes</option>
										</select>
									</div>
								</div>
								<div id="selectoptionlibillness" class="profile-info-row" style="display: none">
									<div class="profile-info-name">Type of Illnes</div>
									<div class="profile-info-value">
										<?php echo $customsupportadd->inputoptions()['lib_illness'] ?>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name">Physical Condition</div>
									<div class="profile-info-value">
										<?php echo $customsupportadd->inputoptions()['lib_physical_condition'] ?>
									</div>
								</div>
								<div class="btn-group">
									<button name="addsupportbtn" value="1" type="submit" class="btn btn-sm btn-success"><i class="icon-save"></i> Add Support Details</button>
								</div>
							</div>
						</form>
					</div>
					<div class="space-12"></div>
				<?php } ?>
			</div>
		</div>
		<div class="widget-box transparent">
			<?php
			/**
			 * JFSBALDO: Authorized Representative/s
			 */
			?>
			<div class="table-header">
				Authorized Representative(s)
			</div>
			<div class="table-responsive">
				<?php if ($pensionerotherdetails->checkReps() > 0){ ?>
				<table class="table table-striped table-bordered table-hover">
					<thead>
					<tr>
						<th class="center">Name</th>
						<th class="center">Relationship to Pensioner</th>
						<th class="center">Contact No.</th>
						<th class="center">Location</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($pensionerotherdetails->getRepresentativeData() as $repData): ?>
						<tr>
							<td class="center"><?php echo $repData['lname'] . ' ' . $repData['fname'] . ' ' . $repData['mname'] . ' ' . $repData['lname'] ?></td>
							<td class="center"><?php echo $repData['relToPensioner'] ?></td>
							<td class="center"><?php echo $repData['ContactNo'] ?></td>
							<td class="center"><?php echo $repData['auth_Region'] . ', ' . $repData['auth_prov'] . ', ' . $repData['auth_city'] . ', ' . $repData['auth_brgy'] ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
				<?php } else { ?>
					<a class="btn btn-sm btn-success btn-block" onclick="loadDoc2()">Add Authorize Representative Details</a>
					<div id="jfsbaldo2" style="display: none">
						<form name="addrep" id="addrep" method="get" action="tbl_pensionerview_add.php">
							<div class="profile-user-info profile-user-info-striped">

								<div class="profile-info-row">
									<div class="profile-info-name">PensionerID</div>
									<div class="profile-info-value">
										<?php echo $tbl_pensioner->PensionerID->CurrentValue ?>
										<input type="hidden" name="jb_PensionerID" value="<?php echo $tbl_pensioner->PensionerID->CurrentValue ?>">
										<input type="hidden" name="jb_SeniorID" value="<?php echo $tbl_pensioner->SeniorID->CurrentValue ?>">
									</div>
								</div>

								<div class="profile-info-row">
									<div class="profile-info-name">First Name</div>
									<div class="profile-info-value">
										<input type="text" name="jb_firstname">
									</div>
								</div>

								<div class="profile-info-row">
									<div class="profile-info-name">Middle Name</div>
									<div class="profile-info-value">
										<input type="text" name="jb_middlename">
									</div>
								</div>

								<div class="profile-info-row">
									<div class="profile-info-name">Last Name</div>
									<div class="profile-info-value">
										<input type="text" name="jb_lastname">
									</div>
								</div>

								<div class="profile-info-row">
									<div class="profile-info-name">Relationship</div>
									<div class="profile-info-value">
										<?php echo $customsupportadd->inputoptions()['lib_relationship'] ?>
									</div>
								</div>

								<div class="profile-info-row">
									<div class="profile-info-name">Contact No.</div>
									<div class="profile-info-value">
										<input type="text" name="jb_contactno" maxlength="11">
									</div>
								</div>

								<?php $psgcclass = new psgcclass(); ?>

								<div class="profile-info-row">
									<div class="profile-info-name">Region</div>
									<div class="profile-info-value">
										<?php echo $psgcclass->regionoption('jb_region_code','getProv(this.value)') ?>
									</div>
								</div>

								<div class="profile-info-row">
									<div class="profile-info-name">Province</div>
									<div class="profile-info-value">
										<div id="div_prov">
											<select id="jb_prov_code" name="jb_prov_code">
												<option value="" selected>Please Select</option>
											</select>
										</div>
									</div>
								</div>

								<div class="profile-info-row">
									<div class="profile-info-name">City</div>
									<div class="profile-info-value">
										<div id="div_city">
											<select id="jb_city_code" name="jb_city_code">
												<option value="" selected>Please Select</option>
											</select>
										</div>
									</div>
								</div>

								<div class="profile-info-row">
									<div class="profile-info-name">Barangay</div>
									<div class="profile-info-value">
										<div id="div_brgy">
											<select id="jb_brgy_code" name="jb_brgy_code">
												<option value="" selected>Please Select</option>
											</select>
										</div>
									</div>
								</div>

								<div class="profile-info-row">
									<div class="profile-info-name">House No.</div>
									<div class="profile-info-value">
										<input type="text" name="jb_houseno">
									</div>
								</div>

								<div class="btn-group">
									<button name="addrepbtn" value="1" type="submit" class="btn btn-sm btn-success"><i class="icon-save"></i> Add Rep</button>
								</div>

							</div>
						</form>
					</div>
					<div class="space-12"></div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="space-6"></div>
<script type="text/javascript">
ftbl_pensionerview.Init();
</script>
<?php
$tbl_pensioner_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_pensioner_view->Page_Terminate();
?>
