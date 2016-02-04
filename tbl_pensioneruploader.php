<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
set_time_limit (900000); // jfsbaldo
/*header("Content-Type: text/html; charset=UTF-8");*/
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
<?php include_once "excel/insertparse.excel.php" ?>
<?php include_once "excel/simplexlsx.class.php" ?>
<?php include_once "excel/reader.php" ?>
<?php

//
// Page class
//

$tbl_pensioner_list = NULL; // Initialize page object first

class ctbl_pensioner_list extends ctbl_pensioner {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_pensioner';

	// Page object name
	var $PageObjName = 'tbl_pensioner_list';

	// Grid form hidden field names
	var $FormName = 'ftbl_pensionerlist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "tbl_pensioneradd.php?" . EW_TABLE_SHOW_DETAIL . "=";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "tbl_pensionerdelete.php";
		$this->MultiUpdateUrl = "tbl_pensionerupdate.php";

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_pensioner', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "span";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "span";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "span";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "span";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("login.php");
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

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();
		$this->SeniorID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->Createdby->Visible = !$this->IsAddOrEdit();
		$this->CreatedDate->Visible = !$this->IsAddOrEdit();
		$this->UpdatedBy->Visible = !$this->IsAddOrEdit();
		$this->UpdatedDate->Visible = !$this->IsAddOrEdit();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Setup other options
		$this->SetupOtherOptions();

		// Set "checkbox" visible
		if (count($this->CustomActions) > 0)
			$this->ListOptions->Items["checkbox"]->Visible = TRUE;
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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

			// Set up records per page
			$this->SetUpDisplayRecs();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide export options
			if ($this->Export <> "" || $this->CurrentAction <> "")
				$this->ExportOptions->HideAllOptions();

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Get and validate search values for advanced search
			$this->LoadSearchValues(); // Get search values
			if (!$this->ValidateSearch())
				$this->setFailureMessage($gsSearchError);

			// Restore search parms from Session if not searching / reset
			if ($this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall" && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Load advanced search from default
			if ($this->LoadAdvancedSearchDefault()) {
				$sSrchAdvanced = $this->AdvancedSearchWhere();
			}
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";
	}

	// Set up number of records displayed per page
	function SetUpDisplayRecs() {
		$sWrk = @$_GET[EW_TABLE_REC_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayRecs = intval($sWrk);
			} else {
				if (strtolower($sWrk) == "all") { // Display all records
					$this->DisplayRecs = -1;
				} else {
					$this->DisplayRecs = 20; // Non-numeric, load default
				}
			}
			$this->setRecordsPerPage($this->DisplayRecs); // Save to Session

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue("k_key"));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue("k_key"));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->SeniorID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->SeniorID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere() {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->SeniorID, FALSE); // SeniorID
		$this->BuildSearchSql($sWhere, $this->PensionerID, FALSE); // PensionerID
		$this->BuildSearchSql($sWhere, $this->InclusionDate, FALSE); // InclusionDate
		$this->BuildSearchSql($sWhere, $this->hh_id, FALSE); // hh_id
		$this->BuildSearchSql($sWhere, $this->osca_ID, FALSE); // osca_ID
		$this->BuildSearchSql($sWhere, $this->PlaceIssued, FALSE); // PlaceIssued
		$this->BuildSearchSql($sWhere, $this->DateIssued, FALSE); // DateIssued
		$this->BuildSearchSql($sWhere, $this->firstname, FALSE); // firstname
		$this->BuildSearchSql($sWhere, $this->middlename, FALSE); // middlename
		$this->BuildSearchSql($sWhere, $this->lastname, FALSE); // lastname
		$this->BuildSearchSql($sWhere, $this->extname, FALSE); // extname
		$this->BuildSearchSql($sWhere, $this->Birthdate, FALSE); // Birthdate
		$this->BuildSearchSql($sWhere, $this->sex, FALSE); // sex
		$this->BuildSearchSql($sWhere, $this->MaritalID, FALSE); // MaritalID
		$this->BuildSearchSql($sWhere, $this->affliationID, FALSE); // affliationID
		$this->BuildSearchSql($sWhere, $this->psgc_region, FALSE); // psgc_region
		$this->BuildSearchSql($sWhere, $this->psgc_province, FALSE); // psgc_province
		$this->BuildSearchSql($sWhere, $this->psgc_municipality, FALSE); // psgc_municipality
		$this->BuildSearchSql($sWhere, $this->psgc_brgy, FALSE); // psgc_brgy
		$this->BuildSearchSql($sWhere, $this->given_add, FALSE); // given_add
		$this->BuildSearchSql($sWhere, $this->Status, FALSE); // Status
		$this->BuildSearchSql($sWhere, $this->paymentmodeID, FALSE); // paymentmodeID
		$this->BuildSearchSql($sWhere, $this->approved, FALSE); // approved
		$this->BuildSearchSql($sWhere, $this->approvedby, FALSE); // approvedby
		$this->BuildSearchSql($sWhere, $this->DateApproved, FALSE); // DateApproved
		$this->BuildSearchSql($sWhere, $this->ArrangementID, FALSE); // ArrangementID
		$this->BuildSearchSql($sWhere, $this->is_4ps, FALSE); // is_4ps
		$this->BuildSearchSql($sWhere, $this->abandoned, FALSE); // abandoned
		$this->BuildSearchSql($sWhere, $this->Createdby, FALSE); // Createdby
		$this->BuildSearchSql($sWhere, $this->CreatedDate, FALSE); // CreatedDate
		$this->BuildSearchSql($sWhere, $this->UpdatedBy, FALSE); // UpdatedBy
		$this->BuildSearchSql($sWhere, $this->UpdatedDate, FALSE); // UpdatedDate
		$this->BuildSearchSql($sWhere, $this->UpdateRemarks, FALSE); // UpdateRemarks
		$this->BuildSearchSql($sWhere, $this->codeGen, FALSE); // codeGen
		$this->BuildSearchSql($sWhere, $this->picturename, FALSE); // picturename
		$this->BuildSearchSql($sWhere, $this->picturetype, FALSE); // picturetype
		$this->BuildSearchSql($sWhere, $this->picturewidth, FALSE); // picturewidth
		$this->BuildSearchSql($sWhere, $this->pictureheight, FALSE); // pictureheight
		$this->BuildSearchSql($sWhere, $this->picturesize, FALSE); // picturesize
		$this->BuildSearchSql($sWhere, $this->hyperlink, FALSE); // hyperlink

		// Set up search parm
		if ($sWhere <> "") {
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->SeniorID->AdvancedSearch->Save(); // SeniorID
			$this->PensionerID->AdvancedSearch->Save(); // PensionerID
			$this->InclusionDate->AdvancedSearch->Save(); // InclusionDate
			$this->hh_id->AdvancedSearch->Save(); // hh_id
			$this->osca_ID->AdvancedSearch->Save(); // osca_ID
			$this->PlaceIssued->AdvancedSearch->Save(); // PlaceIssued
			$this->DateIssued->AdvancedSearch->Save(); // DateIssued
			$this->firstname->AdvancedSearch->Save(); // firstname
			$this->middlename->AdvancedSearch->Save(); // middlename
			$this->lastname->AdvancedSearch->Save(); // lastname
			$this->extname->AdvancedSearch->Save(); // extname
			$this->Birthdate->AdvancedSearch->Save(); // Birthdate
			$this->sex->AdvancedSearch->Save(); // sex
			$this->MaritalID->AdvancedSearch->Save(); // MaritalID
			$this->affliationID->AdvancedSearch->Save(); // affliationID
			$this->psgc_region->AdvancedSearch->Save(); // psgc_region
			$this->psgc_province->AdvancedSearch->Save(); // psgc_province
			$this->psgc_municipality->AdvancedSearch->Save(); // psgc_municipality
			$this->psgc_brgy->AdvancedSearch->Save(); // psgc_brgy
			$this->given_add->AdvancedSearch->Save(); // given_add
			$this->Status->AdvancedSearch->Save(); // Status
			$this->paymentmodeID->AdvancedSearch->Save(); // paymentmodeID
			$this->approved->AdvancedSearch->Save(); // approved
			$this->approvedby->AdvancedSearch->Save(); // approvedby
			$this->DateApproved->AdvancedSearch->Save(); // DateApproved
			$this->ArrangementID->AdvancedSearch->Save(); // ArrangementID
			$this->is_4ps->AdvancedSearch->Save(); // is_4ps
			$this->abandoned->AdvancedSearch->Save(); // abandoned
			$this->Createdby->AdvancedSearch->Save(); // Createdby
			$this->CreatedDate->AdvancedSearch->Save(); // CreatedDate
			$this->UpdatedBy->AdvancedSearch->Save(); // UpdatedBy
			$this->UpdatedDate->AdvancedSearch->Save(); // UpdatedDate
			$this->UpdateRemarks->AdvancedSearch->Save(); // UpdateRemarks
			$this->codeGen->AdvancedSearch->Save(); // codeGen
			$this->picturename->AdvancedSearch->Save(); // picturename
			$this->picturetype->AdvancedSearch->Save(); // picturetype
			$this->picturewidth->AdvancedSearch->Save(); // picturewidth
			$this->pictureheight->AdvancedSearch->Save(); // pictureheight
			$this->picturesize->AdvancedSearch->Save(); // picturesize
			$this->hyperlink->AdvancedSearch->Save(); // hyperlink
		}
		return $sWhere;
	}

	// Build search SQL
	function BuildSearchSql(&$Where, &$Fld, $MultiValue) {
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $Fld->AdvancedSearch->SearchValue; // @$_GET["x_$FldParm"]
		$FldOpr = $Fld->AdvancedSearch->SearchOperator; // @$_GET["z_$FldParm"]
		$FldCond = $Fld->AdvancedSearch->SearchCondition; // @$_GET["v_$FldParm"]
		$FldVal2 = $Fld->AdvancedSearch->SearchValue2; // @$_GET["y_$FldParm"]
		$FldOpr2 = $Fld->AdvancedSearch->SearchOperator2; // @$_GET["w_$FldParm"]
		$sWrk = "";

		//$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);

		//$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		if ($FldOpr == "") $FldOpr = "=";
		$FldOpr2 = strtoupper(trim($FldOpr2));
		if ($FldOpr2 == "") $FldOpr2 = "=";
		if (EW_SEARCH_MULTI_VALUE_OPTION == 1 || $FldOpr <> "LIKE" ||
			($FldOpr2 <> "LIKE" && $FldVal2 <> ""))
			$MultiValue = FALSE;
		if ($MultiValue) {
			$sWrk1 = ($FldVal <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr, $FldVal) : ""; // Field value 1
			$sWrk2 = ($FldVal2 <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr2, $FldVal2) : ""; // Field value 2
			$sWrk = $sWrk1; // Build final SQL
			if ($sWrk2 <> "")
				$sWrk = ($sWrk <> "") ? "($sWrk) $FldCond ($sWrk2)" : $sWrk2;
		} else {
			$FldVal = $this->ConvertSearchValue($Fld, $FldVal);
			$FldVal2 = $this->ConvertSearchValue($Fld, $FldVal2);
			$sWrk = ew_GetSearchSql($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2);
		}
		ew_AddFilter($Where, $sWrk);
	}

	// Convert search value
	function ConvertSearchValue(&$Fld, $FldVal) {
		if ($FldVal == EW_NULL_VALUE || $FldVal == EW_NOT_NULL_VALUE)
			return $FldVal;
		$Value = $FldVal;
		if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
			if ($FldVal <> "") $Value = ($FldVal == "1" || strtolower(strval($FldVal)) == "y" || strtolower(strval($FldVal)) == "t") ? $Fld->TrueValue : $Fld->FalseValue;
		} elseif ($Fld->FldDataType == EW_DATATYPE_DATE) {
			if ($FldVal <> "") $Value = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		}
		return $Value;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->PensionerID, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->hh_id, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->osca_ID, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->PlaceIssued, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->firstname, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->middlename, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->lastname, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->extname, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->given_add, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->UpdateRemarks, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->codeGen, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->picturename, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->picturetype, $Keyword);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $Keyword) {
		if ($Keyword == EW_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NULL";
		} elseif ($Keyword == EW_NOT_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NOT NULL";
		} else {
			$sFldExpression = ($Fld->FldVirtualExpression <> $Fld->FldExpression) ? $Fld->FldVirtualExpression : $Fld->FldBasicSearchExpression;
			$sWrk = $sFldExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING));
		}
		if ($Where <> "") $Where .= " OR ";
		$Where .= $sWrk;
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere() {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = $this->BasicSearch->Keyword;
		$sSearchType = $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				while (strpos($sSearch, "  ") !== FALSE)
					$sSearch = str_replace("  ", " ", $sSearch);
				$arKeyword = explode(" ", trim($sSearch));
				foreach ($arKeyword as $sKeyword) {
					if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
					$sSearchStr .= "(" . $this->BasicSearchSQL($sKeyword) . ")";
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL($sSearch);
			}
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		if ($this->SeniorID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PensionerID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->InclusionDate->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->hh_id->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->osca_ID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PlaceIssued->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->DateIssued->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->firstname->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->middlename->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->lastname->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->extname->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Birthdate->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->sex->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->MaritalID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->affliationID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->psgc_region->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->psgc_province->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->psgc_municipality->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->psgc_brgy->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->given_add->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Status->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->paymentmodeID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->approved->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->approvedby->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->DateApproved->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->ArrangementID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->is_4ps->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->abandoned->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Createdby->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->CreatedDate->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->UpdatedBy->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->UpdatedDate->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->UpdateRemarks->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->codeGen->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->picturename->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->picturetype->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->picturewidth->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->pictureheight->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->picturesize->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->hyperlink->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->SeniorID->AdvancedSearch->UnsetSession();
		$this->PensionerID->AdvancedSearch->UnsetSession();
		$this->InclusionDate->AdvancedSearch->UnsetSession();
		$this->hh_id->AdvancedSearch->UnsetSession();
		$this->osca_ID->AdvancedSearch->UnsetSession();
		$this->PlaceIssued->AdvancedSearch->UnsetSession();
		$this->DateIssued->AdvancedSearch->UnsetSession();
		$this->firstname->AdvancedSearch->UnsetSession();
		$this->middlename->AdvancedSearch->UnsetSession();
		$this->lastname->AdvancedSearch->UnsetSession();
		$this->extname->AdvancedSearch->UnsetSession();
		$this->Birthdate->AdvancedSearch->UnsetSession();
		$this->sex->AdvancedSearch->UnsetSession();
		$this->MaritalID->AdvancedSearch->UnsetSession();
		$this->affliationID->AdvancedSearch->UnsetSession();
		$this->psgc_region->AdvancedSearch->UnsetSession();
		$this->psgc_province->AdvancedSearch->UnsetSession();
		$this->psgc_municipality->AdvancedSearch->UnsetSession();
		$this->psgc_brgy->AdvancedSearch->UnsetSession();
		$this->given_add->AdvancedSearch->UnsetSession();
		$this->Status->AdvancedSearch->UnsetSession();
		$this->paymentmodeID->AdvancedSearch->UnsetSession();
		$this->approved->AdvancedSearch->UnsetSession();
		$this->approvedby->AdvancedSearch->UnsetSession();
		$this->DateApproved->AdvancedSearch->UnsetSession();
		$this->ArrangementID->AdvancedSearch->UnsetSession();
		$this->is_4ps->AdvancedSearch->UnsetSession();
		$this->abandoned->AdvancedSearch->UnsetSession();
		$this->Createdby->AdvancedSearch->UnsetSession();
		$this->CreatedDate->AdvancedSearch->UnsetSession();
		$this->UpdatedBy->AdvancedSearch->UnsetSession();
		$this->UpdatedDate->AdvancedSearch->UnsetSession();
		$this->UpdateRemarks->AdvancedSearch->UnsetSession();
		$this->codeGen->AdvancedSearch->UnsetSession();
		$this->picturename->AdvancedSearch->UnsetSession();
		$this->picturetype->AdvancedSearch->UnsetSession();
		$this->picturewidth->AdvancedSearch->UnsetSession();
		$this->pictureheight->AdvancedSearch->UnsetSession();
		$this->picturesize->AdvancedSearch->UnsetSession();
		$this->hyperlink->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->SeniorID->AdvancedSearch->Load();
		$this->PensionerID->AdvancedSearch->Load();
		$this->InclusionDate->AdvancedSearch->Load();
		$this->hh_id->AdvancedSearch->Load();
		$this->osca_ID->AdvancedSearch->Load();
		$this->PlaceIssued->AdvancedSearch->Load();
		$this->DateIssued->AdvancedSearch->Load();
		$this->firstname->AdvancedSearch->Load();
		$this->middlename->AdvancedSearch->Load();
		$this->lastname->AdvancedSearch->Load();
		$this->extname->AdvancedSearch->Load();
		$this->Birthdate->AdvancedSearch->Load();
		$this->sex->AdvancedSearch->Load();
		$this->MaritalID->AdvancedSearch->Load();
		$this->affliationID->AdvancedSearch->Load();
		$this->psgc_region->AdvancedSearch->Load();
		$this->psgc_province->AdvancedSearch->Load();
		$this->psgc_municipality->AdvancedSearch->Load();
		$this->psgc_brgy->AdvancedSearch->Load();
		$this->given_add->AdvancedSearch->Load();
		$this->Status->AdvancedSearch->Load();
		$this->paymentmodeID->AdvancedSearch->Load();
		$this->approved->AdvancedSearch->Load();
		$this->approvedby->AdvancedSearch->Load();
		$this->DateApproved->AdvancedSearch->Load();
		$this->ArrangementID->AdvancedSearch->Load();
		$this->is_4ps->AdvancedSearch->Load();
		$this->abandoned->AdvancedSearch->Load();
		$this->Createdby->AdvancedSearch->Load();
		$this->CreatedDate->AdvancedSearch->Load();
		$this->UpdatedBy->AdvancedSearch->Load();
		$this->UpdatedDate->AdvancedSearch->Load();
		$this->UpdateRemarks->AdvancedSearch->Load();
		$this->codeGen->AdvancedSearch->Load();
		$this->picturename->AdvancedSearch->Load();
		$this->picturetype->AdvancedSearch->Load();
		$this->picturewidth->AdvancedSearch->Load();
		$this->pictureheight->AdvancedSearch->Load();
		$this->picturesize->AdvancedSearch->Load();
		$this->hyperlink->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->SeniorID); // SeniorID
			$this->UpdateSort($this->PensionerID); // PensionerID
			$this->UpdateSort($this->InclusionDate); // InclusionDate
			$this->UpdateSort($this->hh_id); // hh_id
			$this->UpdateSort($this->osca_ID); // osca_ID
			$this->UpdateSort($this->PlaceIssued); // PlaceIssued
			$this->UpdateSort($this->DateIssued); // DateIssued
			$this->UpdateSort($this->firstname); // firstname
			$this->UpdateSort($this->middlename); // middlename
			$this->UpdateSort($this->lastname); // lastname
			$this->UpdateSort($this->extname); // extname
			$this->UpdateSort($this->Birthdate); // Birthdate
			$this->UpdateSort($this->sex); // sex
			$this->UpdateSort($this->MaritalID); // MaritalID
			$this->UpdateSort($this->affliationID); // affliationID
			$this->UpdateSort($this->psgc_region); // psgc_region
			$this->UpdateSort($this->psgc_province); // psgc_province
			$this->UpdateSort($this->psgc_municipality); // psgc_municipality
			$this->UpdateSort($this->psgc_brgy); // psgc_brgy
			$this->UpdateSort($this->given_add); // given_add
			$this->UpdateSort($this->Status); // Status
			$this->UpdateSort($this->paymentmodeID); // paymentmodeID
			$this->UpdateSort($this->approved); // approved
			$this->UpdateSort($this->approvedby); // approvedby
			$this->UpdateSort($this->DateApproved); // DateApproved
			$this->UpdateSort($this->ArrangementID); // ArrangementID
			$this->UpdateSort($this->is_4ps); // is_4ps
			$this->UpdateSort($this->abandoned); // abandoned
			$this->UpdateSort($this->Createdby); // Createdby
			$this->UpdateSort($this->CreatedDate); // CreatedDate
			$this->UpdateSort($this->UpdatedBy); // UpdatedBy
			$this->UpdateSort($this->UpdatedDate); // UpdatedDate
			$this->UpdateSort($this->UpdateRemarks); // UpdateRemarks
			$this->UpdateSort($this->codeGen); // codeGen
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->SqlOrderBy() <> "") {
				$sOrderBy = $this->SqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->SeniorID->setSort("");
				$this->PensionerID->setSort("");
				$this->InclusionDate->setSort("");
				$this->hh_id->setSort("");
				$this->osca_ID->setSort("");
				$this->PlaceIssued->setSort("");
				$this->DateIssued->setSort("");
				$this->firstname->setSort("");
				$this->middlename->setSort("");
				$this->lastname->setSort("");
				$this->extname->setSort("");
				$this->Birthdate->setSort("");
				$this->sex->setSort("");
				$this->MaritalID->setSort("");
				$this->affliationID->setSort("");
				$this->psgc_region->setSort("");
				$this->psgc_province->setSort("");
				$this->psgc_municipality->setSort("");
				$this->psgc_brgy->setSort("");
				$this->given_add->setSort("");
				$this->Status->setSort("");
				$this->paymentmodeID->setSort("");
				$this->approved->setSort("");
				$this->approvedby->setSort("");
				$this->DateApproved->setSort("");
				$this->ArrangementID->setSort("");
				$this->is_4ps->setSort("");
				$this->abandoned->setSort("");
				$this->Createdby->setSort("");
				$this->CreatedDate->setSort("");
				$this->UpdatedBy->setSort("");
				$this->UpdatedDate->setSort("");
				$this->UpdateRemarks->setSort("");
				$this->codeGen->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanView();
		$item->OnLeft = TRUE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = TRUE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = FALSE AND $Security->CanAdd();
		$item->OnLeft = TRUE;

		// "detail_tbl_representative"
		$item = &$this->ListOptions->Add("detail_tbl_representative");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->AllowList(CurrentProjectID() . 'tbl_representative') && !$this->ShowMultipleDetails;
		$item->OnLeft = TRUE;
		$item->ShowInButtonGroup = FALSE;
		if (!isset($GLOBALS["tbl_representative_grid"])) $GLOBALS["tbl_representative_grid"] = new ctbl_representative_grid;

		// "detail_tbl_support"
		$item = &$this->ListOptions->Add("detail_tbl_support");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->AllowList(CurrentProjectID() . 'tbl_support') && !$this->ShowMultipleDetails;
		$item->OnLeft = TRUE;
		$item->ShowInButtonGroup = FALSE;
		if (!isset($GLOBALS["tbl_support_grid"])) $GLOBALS["tbl_support_grid"] = new ctbl_support_grid;

		// "detail_tbl_updates"
		$item = &$this->ListOptions->Add("detail_tbl_updates");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = FALSE AND $Security->AllowList(CurrentProjectID() . 'tbl_updates') && !$this->ShowMultipleDetails;
		$item->OnLeft = TRUE;
		$item->ShowInButtonGroup = FALSE;
		if (!isset($GLOBALS["tbl_updates_grid"])) $GLOBALS["tbl_updates_grid"] = new ctbl_updates_grid;

		// Multiple details
		if ($this->ShowMultipleDetails) {
			$item = &$this->ListOptions->Add("details");
			$item->CssStyle = "white-space: nowrap;";
			$item->Visible = $this->ShowMultipleDetails;
			$item->OnLeft = TRUE;
			$item->ShowInButtonGroup = FALSE;
		}

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = TRUE;
		$item->Header = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\"></label>";
		$item->MoveTo(0);
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group tolits "btn-small"

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->CanView())
			$oListOpt->Body = "<a class=\"blue\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "<i class=\"icon-zoom-in bigger-130\"></i></a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"green\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "<i class=\"icon-pencil bigger-130\"></i></a>";
		} else {
			$oListOpt->Body = "";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->CanAdd()) {
			$oListOpt->Body = "<a class=\"blue\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CopyLink")) . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "<i class=\"icon-copy bigger-130\"></i></a>";
		} else {
			$oListOpt->Body = "";
		}
		$DetailViewTblVar = "";
		$DetailCopyTblVar = "";
		$DetailEditTblVar = "";

		// "detail_tbl_representative"
		$oListOpt = &$this->ListOptions->Items["detail_tbl_representative"];
		if ($Security->AllowList(CurrentProjectID() . 'tbl_representative')) {
			$body = $Language->Phrase("DetailLink") . $Language->TablePhrase("tbl_representative", "TblCaption");
			$body = "<a class=\"btn btn-success btn-sm\" data-action-ace=\"list\" href=\"" . ew_HtmlEncode("tbl_representativelist.php?" . EW_TABLE_SHOW_MASTER . "=tbl_pensioner&PensionerID=" . strval($this->PensionerID->CurrentValue) . "") . "\">" . $body . "</a>";
			$links = "";
			if ($GLOBALS["tbl_representative_grid"]->DetailView && $Security->CanView() && $Security->AllowView(CurrentProjectID() . 'tbl_representative')) {
				$links .= "<li><a class=\"ewView\" data-action-ace=\"view\" href=\"" . ew_HtmlEncode($this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=tbl_representative")) . "\">" . $Language->Phrase("MasterDetailViewLink") . "</a></li>";
				if ($DetailViewTblVar <> "") $DetailViewTblVar .= ",";
				$DetailViewTblVar .= "tbl_representative";
			}
			if ($GLOBALS["tbl_representative_grid"]->DetailEdit && $Security->CanEdit() && $Security->AllowEdit(CurrentProjectID() . 'tbl_representative')) {
				$links .= "<li><a class=\"ewEdit\" data-action-ace=\"edit\" href=\"" . ew_HtmlEncode($this->GetEditUrl(EW_TABLE_SHOW_DETAIL . "=tbl_representative")) . "\">" . $Language->Phrase("MasterDetailEditLink") . "</a></li>";
				if ($DetailEditTblVar <> "") $DetailEditTblVar .= ",";
				$DetailEditTblVar .= "tbl_representative";
			}
			if ($GLOBALS["tbl_representative_grid"]->DetailAdd && $Security->CanAdd() && $Security->AllowAdd(CurrentProjectID() . 'tbl_representative')) {
				$links .= "<li><a class=\"btn-success\" data-action-ace=\"add\" href=\"" . ew_HtmlEncode($this->GetCopyUrl(EW_TABLE_SHOW_DETAIL . "=tbl_representative")) . "\">" . $Language->Phrase("MasterDetailCopyLink") . "</a></li>";
				if ($DetailCopyTblVar <> "") $DetailCopyTblVar .= ",";
				$DetailCopyTblVar .= "tbl_representative";
			}
			if ($links <> "") {
				$body .= "<button class=\"btn btn-success btn-sm dropdown-toggle\" data-toggle=\"dropdown\"><span class=\"icon-caret-down icon-only\"></span></button>";
				$body .= "<ul class=\"dropdown-menu dropdown-success\">". $links . "</ul>";
			}
			$body = "<div class=\"btn-group\">" . $body . "</div>";
			$oListOpt->Body = $body;
			if ($this->ShowMultipleDetails) $oListOpt->Visible = FALSE;
		}

		// "detail_tbl_support"
		$oListOpt = &$this->ListOptions->Items["detail_tbl_support"];
		if ($Security->AllowList(CurrentProjectID() . 'tbl_support')) {
			$body = $Language->Phrase("DetailLink") . $Language->TablePhrase("tbl_support", "TblCaption");
			$body = "<a class=\"btn btn-success btn-sm\" data-action-ace=\"list\" href=\"" . ew_HtmlEncode("tbl_supportlist.php?" . EW_TABLE_SHOW_MASTER . "=tbl_pensioner&PensionerID=" . strval($this->PensionerID->CurrentValue) . "") . "\">" . $body . "</a>";
			$links = "";
			if ($GLOBALS["tbl_support_grid"]->DetailView && $Security->CanView() && $Security->AllowView(CurrentProjectID() . 'tbl_support')) {
				$links .= "<li><a class=\"ewView\" data-action-ace=\"view\" href=\"" . ew_HtmlEncode($this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=tbl_support")) . "\">" . $Language->Phrase("MasterDetailViewLink") . "</a></li>";
				if ($DetailViewTblVar <> "") $DetailViewTblVar .= ",";
				$DetailViewTblVar .= "tbl_support";
			}
			if ($GLOBALS["tbl_support_grid"]->DetailEdit && $Security->CanEdit() && $Security->AllowEdit(CurrentProjectID() . 'tbl_support')) {
				$links .= "<li><a class=\"ewEdit\" data-action-ace=\"edit\" href=\"" . ew_HtmlEncode($this->GetEditUrl(EW_TABLE_SHOW_DETAIL . "=tbl_support")) . "\">" . $Language->Phrase("MasterDetailEditLink") . "</a></li>";
				if ($DetailEditTblVar <> "") $DetailEditTblVar .= ",";
				$DetailEditTblVar .= "tbl_support";
			}
			if ($GLOBALS["tbl_support_grid"]->DetailAdd && $Security->CanAdd() && $Security->AllowAdd(CurrentProjectID() . 'tbl_support')) {
				$links .= "<li><a class=\"btn-success\" data-action-ace=\"add\" href=\"" . ew_HtmlEncode($this->GetCopyUrl(EW_TABLE_SHOW_DETAIL . "=tbl_support")) . "\">" . $Language->Phrase("MasterDetailCopyLink") . "</a></li>";
				if ($DetailCopyTblVar <> "") $DetailCopyTblVar .= ",";
				$DetailCopyTblVar .= "tbl_support";
			}
			if ($links <> "") {
				$body .= "<button class=\"btn btn-success btn-sm dropdown-toggle\" data-toggle=\"dropdown\"><span class=\"icon-caret-down icon-only\"></span></button>";
				$body .= "<ul class=\"dropdown-menu dropdown-success\">". $links . "</ul>";
			}
			$body = "<div class=\"btn-group\">" . $body . "</div>";
			$oListOpt->Body = $body;
			if ($this->ShowMultipleDetails) $oListOpt->Visible = FALSE;
		}

		// "detail_tbl_updates"
		$oListOpt = &$this->ListOptions->Items["detail_tbl_updates"];
		if ($Security->AllowList(CurrentProjectID() . 'tbl_updates')) {
			$body = $Language->Phrase("DetailLink") . $Language->TablePhrase("tbl_updates", "TblCaption");
			$body = "<a class=\"btn btn-success btn-sm\" data-action-ace=\"list\" href=\"" . ew_HtmlEncode("tbl_updateslist.php?" . EW_TABLE_SHOW_MASTER . "=tbl_pensioner&PensionerID=" . strval($this->PensionerID->CurrentValue) . "") . "\">" . $body . "</a>";
			$links = "";
			if ($GLOBALS["tbl_updates_grid"]->DetailView && $Security->CanView() && $Security->AllowView(CurrentProjectID() . 'tbl_updates')) {
				$links .= "<li><a class=\"ewView\" data-action-ace=\"view\" href=\"" . ew_HtmlEncode($this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=tbl_updates")) . "\">" . $Language->Phrase("MasterDetailViewLink") . "</a></li>";
				if ($DetailViewTblVar <> "") $DetailViewTblVar .= ",";
				$DetailViewTblVar .= "tbl_updates";
			}
			if ($GLOBALS["tbl_updates_grid"]->DetailEdit && $Security->CanEdit() && $Security->AllowEdit(CurrentProjectID() . 'tbl_updates')) {
				$links .= "<li><a class=\"ewEdit\" data-action-ace=\"edit\" href=\"" . ew_HtmlEncode($this->GetEditUrl(EW_TABLE_SHOW_DETAIL . "=tbl_updates")) . "\">" . $Language->Phrase("MasterDetailEditLink") . "</a></li>";
				if ($DetailEditTblVar <> "") $DetailEditTblVar .= ",";
				$DetailEditTblVar .= "tbl_updates";
			}
			if ($GLOBALS["tbl_updates_grid"]->DetailAdd && $Security->CanAdd() && $Security->AllowAdd(CurrentProjectID() . 'tbl_updates')) {
				$links .= "<li><a class=\"btn-success\" data-action-ace=\"add\" href=\"" . ew_HtmlEncode($this->GetCopyUrl(EW_TABLE_SHOW_DETAIL . "=tbl_updates")) . "\">" . $Language->Phrase("MasterDetailCopyLink") . "</a></li>";
				if ($DetailCopyTblVar <> "") $DetailCopyTblVar .= ",";
				$DetailCopyTblVar .= "tbl_updates";
			}
			if ($links <> "") {
				$body .= "<button class=\"btn btn-success btn-sm dropdown-toggle\" data-toggle=\"dropdown\"><span class=\"icon-caret-down icon-only\"></span></button>";
				$body .= "<ul class=\"dropdown-menu dropdown-success\">". $links . "</ul>";
			}
			$body = "<div class=\"btn-group\">" . $body . "</div>";
			$oListOpt->Body = $body;
			if ($this->ShowMultipleDetails) $oListOpt->Visible = FALSE;
		}
		if ($this->ShowMultipleDetails) {
			$body = $Language->Phrase("MultipleMasterDetails");
			$body = "<div class=\"btn-group\">" .
				"<a class=\"btn-success btn-sm\"  href=\"" . ew_HtmlEncode($this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailViewTblVar)) . "\">" . $body . "</a>";
			$links = "";
			if ($DetailViewTblVar <> "") {
				$links .= "<li><a class=\"btn-success btn-sm\"  href=\"" . ew_HtmlEncode($this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailViewTblVar)) . "\">" . $Language->Phrase("MasterDetailViewLink") . "</a></li>";
			}
			if ($DetailEditTblVar <> "") {
				$links .= "<li><a class=\"btn-success btn-sm\"  href=\"" . ew_HtmlEncode($this->GetEditUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailEditTblVar)) . "\">" . $Language->Phrase("MasterDetailEditLink") . "</a></li>";
			}
			if ($DetailCopyTblVar <> "") {
				$links .= "<li><a class=\"btn-success btn-sm\"  href=\"" . ew_HtmlEncode($this->GetCopyUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailCopyTblVar)) . "\">" . $Language->Phrase("MasterDetailCopyLink") . "</a></li>";
			}
			if ($links <> "") {
				$body .= "<button class=\"btn btn-info btn-sm dropdown-toggle\" data-toggle=\"dropdown\">&nbsp;<span class=\"icon-caret-down icon-only\"></span></button>";
				$body .= "<ul class=\"dropdown-menu dropdown-info\">". $links . "</ul>";
			}
			$body .= "</div>";

			// Multiple details
			$oListOpt = &$this->ListOptions->Items["details"];
			$oListOpt->Body = $body;
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->SeniorID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . "<i class='icon-file align-middle bigger-125'></i> ". $Language->Phrase("AddLink")."</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());
		$option = $options["detail"];
		$DetailTableLink = "";
		$item = &$option->Add("detailadd_tbl_representative");
		$item->Body = "<a class=\"btn btn-pink btn-sm\" href=\"" . ew_HtmlEncode($this->GetAddUrl() . "?" . EW_TABLE_SHOW_DETAIL . "=tbl_representative") . "\">" . $Language->Phrase("AddLink") . "&nbsp;" . $this->TableCaption() . "/" . $GLOBALS["tbl_representative"]->TableCaption() . "</a>";
		$item->Visible = ($GLOBALS["tbl_representative"]->DetailAdd && $Security->AllowAdd(CurrentProjectID() . 'tbl_representative') && $Security->CanAdd());
		if ($item->Visible) {
			if ($DetailTableLink <> "") $DetailTableLink .= ",";
			$DetailTableLink .= "tbl_representative";
		}
		$item = &$option->Add("detailadd_tbl_support");
		$item->Body = "<a class=\"btn btn-pink btn-sm\" href=\"" . ew_HtmlEncode($this->GetAddUrl() . "?" . EW_TABLE_SHOW_DETAIL . "=tbl_support") . "\">" . $Language->Phrase("AddLink") . "&nbsp;" . $this->TableCaption() . "/" . $GLOBALS["tbl_support"]->TableCaption() . "</a>";
		$item->Visible = ($GLOBALS["tbl_support"]->DetailAdd && $Security->AllowAdd(CurrentProjectID() . 'tbl_support') && $Security->CanAdd());
		if ($item->Visible) {
			if ($DetailTableLink <> "") $DetailTableLink .= ",";
			$DetailTableLink .= "tbl_support";
		}
		$item = &$option->Add("detailadd_tbl_updates");
		$item->Body = "<a class=\"btn btn-pink btn-sm\" href=\"" . ew_HtmlEncode($this->GetAddUrl() . "?" . EW_TABLE_SHOW_DETAIL . "=tbl_updates") . "\">" . $Language->Phrase("AddLink") . "&nbsp;" . $this->TableCaption() . "/" . $GLOBALS["tbl_updates"]->TableCaption() . "</a>";
		$item->Visible = ($GLOBALS["tbl_updates"]->DetailAdd && $Security->AllowAdd(CurrentProjectID() . 'tbl_updates') && $Security->CanAdd());
		if ($item->Visible) {
			if ($DetailTableLink <> "") $DetailTableLink .= ",";
			$DetailTableLink .= "tbl_updates";
		}

		// Add multiple details
		if ($this->ShowMultipleDetails) {
			$item = &$option->Add("detailsadd");
			$item->Body = "<a class=\"btn btn-info btn-sm\" href=\"" . ew_HtmlEncode($this->GetAddUrl() . "?" . EW_TABLE_SHOW_DETAIL . "=" . $DetailTableLink) . "\">" . $Language->Phrase("AddMasterDetailLink") . "</a>";
			$item->Visible = ($DetailTableLink <> "" && $Security->CanAdd());

			// Hide single master/detail items
			$ar = explode(",", $DetailTableLink);
			$cnt = count($ar);
			for ($i = 0; $i < $cnt; $i++) {
				if ($item = &$option->GetItem("detailadd_" . $ar[$i]))
					$item->Visible = FALSE;
			}
		}
		$option = $options["action"];

		// Add multi delete
		$item = &$option->Add("multidelete");
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_pensionerlist, '" . $this->MultiDeleteUrl . "', ewLanguage.Phrase('DeleteMultiConfirmMsg'));return false;\">" . $Language->Phrase("DeleteSelectedLink") . "</a>";
		$item->Visible = ($Security->CanDelete());

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-sm"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];
			foreach ($this->CustomActions as $action => $name) {

				// Add custom action
				$item = &$option->Add("custom_" . $action);
				$item->Body = "<a class=\"btn btn-warning btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_pensionerlist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
			}

			// Hide grid edit, multi-delete and multi-update
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$item = &$option->GetItem("multidelete");
				if ($item) $item->Visible = FALSE;
				$item = &$option->GetItem("multiupdate");
				if ($item) $item->Visible = FALSE;
			}
	}

	// Process custom action
	function ProcessCustomAction() {
		global $conn, $Language, $Security;
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$rsuser = ($rs) ? $rs->GetRows() : array();
			if ($rs)
				$rs->Close();

			// Call row custom action event
			if (count($rsuser) > 0) {
				$conn->BeginTrans();
				foreach ($rsuser as $row) {
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCancelled")));
					}
				}
			}
		}
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// SeniorID

		$this->SeniorID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_SeniorID"]);
		if ($this->SeniorID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->SeniorID->AdvancedSearch->SearchOperator = @$_GET["z_SeniorID"];

		// PensionerID
		$this->PensionerID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PensionerID"]);
		if ($this->PensionerID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PensionerID->AdvancedSearch->SearchOperator = @$_GET["z_PensionerID"];

		// InclusionDate
		$this->InclusionDate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_InclusionDate"]);
		if ($this->InclusionDate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->InclusionDate->AdvancedSearch->SearchOperator = @$_GET["z_InclusionDate"];

		// hh_id
		$this->hh_id->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_hh_id"]);
		if ($this->hh_id->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->hh_id->AdvancedSearch->SearchOperator = @$_GET["z_hh_id"];

		// osca_ID
		$this->osca_ID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_osca_ID"]);
		if ($this->osca_ID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->osca_ID->AdvancedSearch->SearchOperator = @$_GET["z_osca_ID"];

		// PlaceIssued
		$this->PlaceIssued->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PlaceIssued"]);
		if ($this->PlaceIssued->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PlaceIssued->AdvancedSearch->SearchOperator = @$_GET["z_PlaceIssued"];

		// DateIssued
		$this->DateIssued->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_DateIssued"]);
		if ($this->DateIssued->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->DateIssued->AdvancedSearch->SearchOperator = @$_GET["z_DateIssued"];

		// firstname
		$this->firstname->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_firstname"]);
		if ($this->firstname->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->firstname->AdvancedSearch->SearchOperator = @$_GET["z_firstname"];

		// middlename
		$this->middlename->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_middlename"]);
		if ($this->middlename->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->middlename->AdvancedSearch->SearchOperator = @$_GET["z_middlename"];

		// lastname
		$this->lastname->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_lastname"]);
		if ($this->lastname->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->lastname->AdvancedSearch->SearchOperator = @$_GET["z_lastname"];

		// extname
		$this->extname->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_extname"]);
		if ($this->extname->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->extname->AdvancedSearch->SearchOperator = @$_GET["z_extname"];

		// Birthdate
		$this->Birthdate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Birthdate"]);
		if ($this->Birthdate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Birthdate->AdvancedSearch->SearchOperator = @$_GET["z_Birthdate"];

		// sex
		$this->sex->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_sex"]);
		if ($this->sex->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->sex->AdvancedSearch->SearchOperator = @$_GET["z_sex"];

		// MaritalID
		$this->MaritalID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_MaritalID"]);
		if ($this->MaritalID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->MaritalID->AdvancedSearch->SearchOperator = @$_GET["z_MaritalID"];

		// affliationID
		$this->affliationID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_affliationID"]);
		if ($this->affliationID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->affliationID->AdvancedSearch->SearchOperator = @$_GET["z_affliationID"];

		// psgc_region
		$this->psgc_region->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_psgc_region"]);
		if ($this->psgc_region->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->psgc_region->AdvancedSearch->SearchOperator = @$_GET["z_psgc_region"];

		// psgc_province
		$this->psgc_province->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_psgc_province"]);
		if ($this->psgc_province->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->psgc_province->AdvancedSearch->SearchOperator = @$_GET["z_psgc_province"];

		// psgc_municipality
		$this->psgc_municipality->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_psgc_municipality"]);
		if ($this->psgc_municipality->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->psgc_municipality->AdvancedSearch->SearchOperator = @$_GET["z_psgc_municipality"];

		// psgc_brgy
		$this->psgc_brgy->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_psgc_brgy"]);
		if ($this->psgc_brgy->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->psgc_brgy->AdvancedSearch->SearchOperator = @$_GET["z_psgc_brgy"];

		// given_add
		$this->given_add->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_given_add"]);
		if ($this->given_add->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->given_add->AdvancedSearch->SearchOperator = @$_GET["z_given_add"];

		// Status
		$this->Status->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Status"]);
		if ($this->Status->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Status->AdvancedSearch->SearchOperator = @$_GET["z_Status"];

		// paymentmodeID
		$this->paymentmodeID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_paymentmodeID"]);
		if ($this->paymentmodeID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->paymentmodeID->AdvancedSearch->SearchOperator = @$_GET["z_paymentmodeID"];

		// approved
		$this->approved->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_approved"]);
		if ($this->approved->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->approved->AdvancedSearch->SearchOperator = @$_GET["z_approved"];

		// approvedby
		$this->approvedby->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_approvedby"]);
		if ($this->approvedby->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->approvedby->AdvancedSearch->SearchOperator = @$_GET["z_approvedby"];

		// DateApproved
		$this->DateApproved->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_DateApproved"]);
		if ($this->DateApproved->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->DateApproved->AdvancedSearch->SearchOperator = @$_GET["z_DateApproved"];

		// ArrangementID
		$this->ArrangementID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_ArrangementID"]);
		if ($this->ArrangementID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->ArrangementID->AdvancedSearch->SearchOperator = @$_GET["z_ArrangementID"];

		// is_4ps
		$this->is_4ps->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_is_4ps"]);
		if ($this->is_4ps->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->is_4ps->AdvancedSearch->SearchOperator = @$_GET["z_is_4ps"];

		// abandoned
		$this->abandoned->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_abandoned"]);
		if ($this->abandoned->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->abandoned->AdvancedSearch->SearchOperator = @$_GET["z_abandoned"];

		// Createdby
		$this->Createdby->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Createdby"]);
		if ($this->Createdby->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Createdby->AdvancedSearch->SearchOperator = @$_GET["z_Createdby"];

		// CreatedDate
		$this->CreatedDate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_CreatedDate"]);
		if ($this->CreatedDate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->CreatedDate->AdvancedSearch->SearchOperator = @$_GET["z_CreatedDate"];

		// UpdatedBy
		$this->UpdatedBy->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_UpdatedBy"]);
		if ($this->UpdatedBy->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->UpdatedBy->AdvancedSearch->SearchOperator = @$_GET["z_UpdatedBy"];

		// UpdatedDate
		$this->UpdatedDate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_UpdatedDate"]);
		if ($this->UpdatedDate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->UpdatedDate->AdvancedSearch->SearchOperator = @$_GET["z_UpdatedDate"];

		// UpdateRemarks
		$this->UpdateRemarks->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_UpdateRemarks"]);
		if ($this->UpdateRemarks->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->UpdateRemarks->AdvancedSearch->SearchOperator = @$_GET["z_UpdateRemarks"];

		// codeGen
		$this->codeGen->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_codeGen"]);
		if ($this->codeGen->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->codeGen->AdvancedSearch->SearchOperator = @$_GET["z_codeGen"];

		// picturename
		$this->picturename->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_picturename"]);
		if ($this->picturename->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->picturename->AdvancedSearch->SearchOperator = @$_GET["z_picturename"];

		// picturetype
		$this->picturetype->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_picturetype"]);
		if ($this->picturetype->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->picturetype->AdvancedSearch->SearchOperator = @$_GET["z_picturetype"];

		// picturewidth
		$this->picturewidth->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_picturewidth"]);
		if ($this->picturewidth->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->picturewidth->AdvancedSearch->SearchOperator = @$_GET["z_picturewidth"];

		// pictureheight
		$this->pictureheight->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_pictureheight"]);
		if ($this->pictureheight->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->pictureheight->AdvancedSearch->SearchOperator = @$_GET["z_pictureheight"];

		// picturesize
		$this->picturesize->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_picturesize"]);
		if ($this->picturesize->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->picturesize->AdvancedSearch->SearchOperator = @$_GET["z_picturesize"];

		// hyperlink
		$this->hyperlink->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_hyperlink"]);
		if ($this->hyperlink->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->hyperlink->AdvancedSearch->SearchOperator = @$_GET["z_hyperlink"];
	}

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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("SeniorID")) <> "")
			$this->SeniorID->CurrentValue = $this->getKey("SeniorID"); // SeniorID
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// SeniorID
			$this->SeniorID->EditCustomAttributes = "";
			$this->SeniorID->EditValue = ew_HtmlEncode($this->SeniorID->AdvancedSearch->SearchValue);
			$this->SeniorID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->SeniorID->FldCaption()));

			// PensionerID
			$this->PensionerID->EditCustomAttributes = "";
			$this->PensionerID->EditValue = ew_HtmlEncode($this->PensionerID->AdvancedSearch->SearchValue);
			$this->PensionerID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->PensionerID->FldCaption()));

			// InclusionDate
			$this->InclusionDate->EditCustomAttributes = "";
			$this->InclusionDate->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->InclusionDate->AdvancedSearch->SearchValue, 6), 6));
			$this->InclusionDate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->InclusionDate->FldCaption()));

			// hh_id
			$this->hh_id->EditCustomAttributes = "";
			$this->hh_id->EditValue = ew_HtmlEncode($this->hh_id->AdvancedSearch->SearchValue);
			$this->hh_id->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->hh_id->FldCaption()));

			// osca_ID
			$this->osca_ID->EditCustomAttributes = "";
			$this->osca_ID->EditValue = ew_HtmlEncode($this->osca_ID->AdvancedSearch->SearchValue);
			$this->osca_ID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->osca_ID->FldCaption()));

			// PlaceIssued
			$this->PlaceIssued->EditCustomAttributes = "";
			$this->PlaceIssued->EditValue = ew_HtmlEncode($this->PlaceIssued->AdvancedSearch->SearchValue);
			$this->PlaceIssued->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->PlaceIssued->FldCaption()));

			// DateIssued
			$this->DateIssued->EditCustomAttributes = "";
			$this->DateIssued->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->DateIssued->AdvancedSearch->SearchValue, 6), 6));
			$this->DateIssued->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->DateIssued->FldCaption()));

			// firstname
			$this->firstname->EditCustomAttributes = "";
			$this->firstname->EditValue = ew_HtmlEncode($this->firstname->AdvancedSearch->SearchValue);
			$this->firstname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->firstname->FldCaption()));

			// middlename
			$this->middlename->EditCustomAttributes = "";
			$this->middlename->EditValue = ew_HtmlEncode($this->middlename->AdvancedSearch->SearchValue);
			$this->middlename->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->middlename->FldCaption()));

			// lastname
			$this->lastname->EditCustomAttributes = "";
			$this->lastname->EditValue = ew_HtmlEncode($this->lastname->AdvancedSearch->SearchValue);
			$this->lastname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->lastname->FldCaption()));

			// extname
			$this->extname->EditCustomAttributes = "";
			$this->extname->EditValue = ew_HtmlEncode($this->extname->AdvancedSearch->SearchValue);
			$this->extname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->extname->FldCaption()));

			// Birthdate
			$this->Birthdate->EditCustomAttributes = "";
			$this->Birthdate->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->Birthdate->AdvancedSearch->SearchValue, 6), 6));
			$this->Birthdate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->Birthdate->FldCaption()));

			// sex
			$this->sex->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->sex->FldTagValue(1), $this->sex->FldTagCaption(1) <> "" ? $this->sex->FldTagCaption(1) : $this->sex->FldTagValue(1));
			$arwrk[] = array($this->sex->FldTagValue(2), $this->sex->FldTagCaption(2) <> "" ? $this->sex->FldTagCaption(2) : $this->sex->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->sex->EditValue = $arwrk;

			// MaritalID
			$this->MaritalID->EditCustomAttributes = "";
			if (trim(strval($this->MaritalID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`MaritalID`" . ew_SearchString("=", $this->MaritalID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `MaritalID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_civilstatus`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->MaritalID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `MaritalID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->MaritalID->EditValue = $arwrk;

			// affliationID
			$this->affliationID->EditCustomAttributes = "";
			if (trim(strval($this->affliationID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`affliationID`" . ew_SearchString("=", $this->affliationID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `affliationID`, `aff_description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_affliation`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->affliationID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `affliationID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->affliationID->EditValue = $arwrk;

			// psgc_region
			$this->psgc_region->EditCustomAttributes = "";
			if (trim(strval($this->psgc_region->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->psgc_region->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_regions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_region, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `region_code` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->psgc_region->EditValue = $arwrk;

			// psgc_province
			$this->psgc_province->EditCustomAttributes = "";
			if (trim(strval($this->psgc_province->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`prov_code`" . ew_SearchString("=", $this->psgc_province->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `region_code` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_provinces`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_province, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `prov_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->psgc_province->EditValue = $arwrk;

			// psgc_municipality
			$this->psgc_municipality->EditCustomAttributes = "";
			if (trim(strval($this->psgc_municipality->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`city_code`" . ew_SearchString("=", $this->psgc_municipality->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `prov_code` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_cities`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_municipality, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `city_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->psgc_municipality->EditValue = $arwrk;

			// psgc_brgy
			$this->psgc_brgy->EditCustomAttributes = "";
			if (trim(strval($this->psgc_brgy->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`brgy_code`" . ew_SearchString("=", $this->psgc_brgy->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `city_code` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_brgy`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_brgy, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `brgy_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->psgc_brgy->EditValue = $arwrk;

			// given_add
			$this->given_add->EditCustomAttributes = "";
			$this->given_add->EditValue = ew_HtmlEncode($this->given_add->AdvancedSearch->SearchValue);
			$this->given_add->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->given_add->FldCaption()));

			// Status
			$this->Status->EditCustomAttributes = "";
			if (trim(strval($this->Status->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`statusID`" . ew_SearchString("=", $this->Status->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `statusID`, `status` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_status`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Status, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `statusID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Status->EditValue = $arwrk;

			// paymentmodeID
			$this->paymentmodeID->EditCustomAttributes = "";
			if (trim(strval($this->paymentmodeID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`paymentmodeID`" . ew_SearchString("=", $this->paymentmodeID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `paymentmodeID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_paymentmode`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->paymentmodeID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `paymentmodeID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->paymentmodeID->EditValue = $arwrk;

			// approved
			$this->approved->EditCustomAttributes = "";
			$this->approved->EditValue = ew_HtmlEncode($this->approved->AdvancedSearch->SearchValue);
			$this->approved->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->approved->FldCaption()));

			// approvedby
			$this->approvedby->EditCustomAttributes = "";
			$this->approvedby->EditValue = ew_HtmlEncode($this->approvedby->AdvancedSearch->SearchValue);
			$this->approvedby->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->approvedby->FldCaption()));

			// DateApproved
			$this->DateApproved->EditCustomAttributes = "";
			$this->DateApproved->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->DateApproved->AdvancedSearch->SearchValue, 6), 6));
			$this->DateApproved->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->DateApproved->FldCaption()));

			// ArrangementID
			$this->ArrangementID->EditCustomAttributes = "";

			// is_4ps
			$this->is_4ps->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->is_4ps->FldTagValue(1), $this->is_4ps->FldTagCaption(1) <> "" ? $this->is_4ps->FldTagCaption(1) : $this->is_4ps->FldTagValue(1));
			$arwrk[] = array($this->is_4ps->FldTagValue(2), $this->is_4ps->FldTagCaption(2) <> "" ? $this->is_4ps->FldTagCaption(2) : $this->is_4ps->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->is_4ps->EditValue = $arwrk;

			// abandoned
			$this->abandoned->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->abandoned->FldTagValue(1), $this->abandoned->FldTagCaption(1) <> "" ? $this->abandoned->FldTagCaption(1) : $this->abandoned->FldTagValue(1));
			$arwrk[] = array($this->abandoned->FldTagValue(2), $this->abandoned->FldTagCaption(2) <> "" ? $this->abandoned->FldTagCaption(2) : $this->abandoned->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->abandoned->EditValue = $arwrk;

			// Createdby
			$this->Createdby->EditCustomAttributes = "";
			$this->Createdby->EditValue = ew_HtmlEncode($this->Createdby->AdvancedSearch->SearchValue);
			$this->Createdby->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->Createdby->FldCaption()));

			// CreatedDate
			$this->CreatedDate->EditCustomAttributes = "";
			$this->CreatedDate->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->CreatedDate->AdvancedSearch->SearchValue, 6), 6));
			$this->CreatedDate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->CreatedDate->FldCaption()));

			// UpdatedBy
			$this->UpdatedBy->EditCustomAttributes = "";
			$this->UpdatedBy->EditValue = ew_HtmlEncode($this->UpdatedBy->AdvancedSearch->SearchValue);
			$this->UpdatedBy->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->UpdatedBy->FldCaption()));

			// UpdatedDate
			$this->UpdatedDate->EditCustomAttributes = "";
			$this->UpdatedDate->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->UpdatedDate->AdvancedSearch->SearchValue, 6), 6));
			$this->UpdatedDate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->UpdatedDate->FldCaption()));

			// UpdateRemarks
			$this->UpdateRemarks->EditCustomAttributes = "";
			$this->UpdateRemarks->EditValue = ew_HtmlEncode($this->UpdateRemarks->AdvancedSearch->SearchValue);
			$this->UpdateRemarks->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->UpdateRemarks->FldCaption()));

			// codeGen
			$this->codeGen->EditCustomAttributes = "";
			$this->codeGen->EditValue = ew_HtmlEncode($this->codeGen->AdvancedSearch->SearchValue);
			$this->codeGen->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->codeGen->FldCaption()));
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->SeniorID->AdvancedSearch->Load();
		$this->PensionerID->AdvancedSearch->Load();
		$this->InclusionDate->AdvancedSearch->Load();
		$this->hh_id->AdvancedSearch->Load();
		$this->osca_ID->AdvancedSearch->Load();
		$this->PlaceIssued->AdvancedSearch->Load();
		$this->DateIssued->AdvancedSearch->Load();
		$this->firstname->AdvancedSearch->Load();
		$this->middlename->AdvancedSearch->Load();
		$this->lastname->AdvancedSearch->Load();
		$this->extname->AdvancedSearch->Load();
		$this->Birthdate->AdvancedSearch->Load();
		$this->sex->AdvancedSearch->Load();
		$this->MaritalID->AdvancedSearch->Load();
		$this->affliationID->AdvancedSearch->Load();
		$this->psgc_region->AdvancedSearch->Load();
		$this->psgc_province->AdvancedSearch->Load();
		$this->psgc_municipality->AdvancedSearch->Load();
		$this->psgc_brgy->AdvancedSearch->Load();
		$this->given_add->AdvancedSearch->Load();
		$this->Status->AdvancedSearch->Load();
		$this->paymentmodeID->AdvancedSearch->Load();
		$this->approved->AdvancedSearch->Load();
		$this->approvedby->AdvancedSearch->Load();
		$this->DateApproved->AdvancedSearch->Load();
		$this->ArrangementID->AdvancedSearch->Load();
		$this->is_4ps->AdvancedSearch->Load();
		$this->abandoned->AdvancedSearch->Load();
		$this->Createdby->AdvancedSearch->Load();
		$this->CreatedDate->AdvancedSearch->Load();
		$this->UpdatedBy->AdvancedSearch->Load();
		$this->UpdatedDate->AdvancedSearch->Load();
		$this->UpdateRemarks->AdvancedSearch->Load();
		$this->codeGen->AdvancedSearch->Load();
		$this->picturename->AdvancedSearch->Load();
		$this->picturetype->AdvancedSearch->Load();
		$this->picturewidth->AdvancedSearch->Load();
		$this->pictureheight->AdvancedSearch->Load();
		$this->picturesize->AdvancedSearch->Load();
		$this->hyperlink->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", $url, $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_pensioner';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($tbl_pensioner_list)) $tbl_pensioner_list = new ctbl_pensioner_list();

// Page init
$tbl_pensioner_list->Page_Init();

// Page main
$tbl_pensioner_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_pensioner_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">
	function checkHerbyCerti(){
		var herby = document.getElementById("herby_certi").value;
		if (herby == null || herby == ''){
			alert("You should certify the list first before you can upload!");
			document.getElementById("herby_certi").focus();
			return false;
		} else {
			return true;
		}
	}
</script>
<?php // custom page start jfsbaldo ?>

<?php $InsertParseDAO = new InsertParseDAO('','','','','','','','','','','','',''); ?>
<div class="well well-lg">
	<b>NOTE:</b><br>
	This module uses the SPIS Merger Template File <a href="merger_template/merger_templatexls.xls" target="_blank">XLS format</a> or <a href="merger_template/merger_template.xlsx" target="_blank">XLSX format</a>. Please download the Merger File for you to upload the Pensioners.
	Arrange your data set following the merger file columns and make sure that it is located on the first worksheet.
	Upload your accomplished merger file on the form below and wait for the summary report. the larger your file the longer it takes to process and validate your entries.
</div>
<div class="well well-lg">
<form method="post" enctype="multipart/form-data" onsubmit="return checkHerbyCerti(this.value)">
	Target File:<input type="file" name="file">
	<label>
		<input value="1" type="checkbox" id="herby_certi" name="herby_certi"> I hereby certify that the uploaded information is true and correct.
	</label><br>
	<button type="submit" class="btn btn-sm btn-success"><i class="icon-gears"></i> Start Processing</button>
</form>
	<?php //echo CurrentUserID(); ?>
</div>
<?php
$uploaderlog="";
if((!empty($_FILES["file"]))) { // && ($_FILES['file']['error'] == 0)

	$limitSize	= 20000000; //(20 Mb) - Maximum size of uploaded file, change it to any size you want
	$fileName	= basename($_FILES['file']['name']);
	$fileSize	= $_FILES["file"]["size"];
	$fileExt	= substr($fileName, strrpos($fileName, '.') + 1);
	$codeGen 	= date("His");

	if(($fileExt == "xlsx" ) && ($fileSize < $limitSize) && $_REQUEST['herby_certi'] <> ''){ //for xlsx files


		//====begin for xlsx files
		// require_once "simplexlsx.class.php"; // class files for xlsx
		$getWorksheetName = array();
		$xlsx = new SimpleXLSX( $_FILES['file']['tmp_name'] );
		$getWorksheetName = $xlsx->getWorksheetName();
		//====end for xlsx files


		//display file information
		echo '<hr><div id="datacontent">';
		echo '<h4>File Info:</h1><ul><li><b>File Name:  </b>'.$fileName.'</li>';
		echo '<li><b>File Size:</b> '.($fileSize/1000).' kb</li></li></ul><hr>';
		echo '<div id="datacontent">';

		for($j=1;$j <= 1 ;$j++){ //process first sheet only
			echo '<h4>Worksheet Name: '.$getWorksheetName[$j-1].'</h1>';
			$htmltable = '<table border="1" id="xlsxTable">';
			list($cols,) = $xlsx->dimension($j);
			$cols = 14; //force checking to 14 columns only
			//Prepare table
			//process column headers
			$ch[] = array();
			$total_rows=0;
			$total_valid =0;
			$total_existing =0;
			$total_null =0;
			$total_saved =0;
			$total_savingerror =0;
			$total_incomplete =0;
			$total_witherrors =0;
			foreach( $xlsx->rows($j) as $k => $r) { //process first row
				if ($k == 0){
					$htmltable .= "<thead><tr>";
					$failedfields = 0;
					for( $i = 0; $i < $cols; $i++){
						//Display column headers

						// $current_column_value = trim(strtoupper($r[$i]));
						$current_column_value = trim($r[$i]);
						//begin checking column titles

						if($i == 0 && $current_column_value != "hh_id"){ $failedfields = $failedfields + 1 ; }
						if($i == 1 && $current_column_value != "first_name"){ $failedfields = $failedfields + 1 ; }
						if($i == 2 && $current_column_value != "middle_name"){ $failedfields = $failedfields + 1 ; }
						if($i == 3 && $current_column_value != "last_name"){ $failedfields = $failedfields + 1 ; }
						if($i == 4 && $current_column_value != "ext_name"){ $failedfields = $failedfields + 1 ; }
						if($i == 5 && $current_column_value != "birthdate"){ $failedfields = $failedfields + 1 ; }
						if($i == 6 && $current_column_value != "sex"){ $failedfields = $failedfields + 1 ; }
						if($i == 7 && $current_column_value != "region_psgc"){ $failedfields = $failedfields + 1 ; }
						if($i == 8 && $current_column_value != "province_psgc"){ $failedfields = $failedfields + 1 ; }
						if($i == 9 && $current_column_value != "municipality_psgc"){ $failedfields = $failedfields + 1 ; }
						if($i == 10 && $current_column_value != "brgy_psgc"){ $failedfields = $failedfields + 1 ; }
						if($i == 11 && $current_column_value != "street_address"){ $failedfields = $failedfields + 1 ; }
						if($i == 12 && $current_column_value != "status"){ $failedfields = $failedfields + 1 ; }
						if($i == 13 && $current_column_value != "payment_mode"){ $failedfields = $failedfields + 1 ; }

						/* ======================== removed fields ========================
							if($i == 0 && $current_column_value != "inclusion_date"){ $failedfields = $failedfields + 1 ; }
							if($i == 2 && $current_column_value != "osca_id"){ $failedfields = $failedfields + 1 ; }
							if($i == 3 && $current_column_value != "osca_place"){ $failedfields = $failedfields + 1 ; }
							if($i == 4 && $current_column_value != "osca_date"){ $failedfields = $failedfields + 1 ; }
							if($i == 11 && $current_column_value != "marital_status"){ $failedfields = $failedfields + 1 ; }
						*/

						//end checking column titles
						$ch[$i] = $current_column_value; //save current column name for user in error messages
						$htmltable .=  '<td><b>' . $current_column_value . '</b></td>';
					}
					$htmltable .=  "<th><b>Remarks</b></th><th><b>Excel Row No.</b></th></tr></thead>";
				}

			}
			// echo $failedfields . "<br>";
			if($failedfields ==0){
				//echo $htmltable;
				$uploaderlog = $htmltable;
				//process each record
				foreach( $xlsx->rows($j) as $l => $m) {
					flush();
					sleep(1);

					if ($l >= 1){
						$field_error = ""; //contains remarks on field errors
						// $swi_ind_total=0;
						$counterRight=0;
						$tablerow =  '<tr>';
						$swivars[] = array();
						for( $n = 0; $n < $cols; $n++){
							//Display data
							//====================begin validating values=========================
							$celldata = trim(strtoupper($m[$n]));

							if($n ==1){ // first name
								if($celldata == '' || $celldata == NULL){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
								}elseif(is_numeric($celldata)){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
								}else{
									$celldatanew = $celldata;
									$counterRight = $counterRight + 1;
								}
							}

							/*if($n ==2){ // middle name
								if($celldata == '' || $celldata == NULL){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
								}elseif(is_numeric($celldata)){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
								}else{
									$celldatanew = $celldata;
									$counterRight = $counterRight + 1;
								}
							}*/

							if($n ==3){ // last name
								if($celldata == '' || $celldata == NULL){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
								}elseif(is_numeric($celldata)){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
								}else{
									$celldatanew = $celldata;
									$counterRight = $counterRight + 1;
								}
							}

							/*if($n ==4){ // extension name
								if($celldata == '' || $celldata == NULL){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
								}elseif(is_numeric($celldata)){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
								}else{
									$celldatanew = $celldata;
									$counterRight = $counterRight + 1;
								}
							}*/

							if($n ==5) { //date checking

								//$celldatanew = date("Y-m-d",ExcelToPHP($celldata));
								$celldatanew = date("Y-m-d",strtotime($celldata)); //for normal date only
								if($celldatanew  == "1970-01-01"){

									$celldatanew2 = date("Y-m-d",ExcelToPHP($celldata));
									if(!is_numeric($celldata)){
										$celldatanew = $celldata;
										$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid/no_value</font>,";
									}else{
										$celldatanew = $celldatanew2;
										$counterRight = $counterRight + 1;
									}

								}
								$celldata = $celldatanew;

							}

							if($n ==6) { //SEX checking

								if($celldata == '' || $celldata == NULL){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
								}elseif($celldata == "MALE"){
									$celldatanew = 0;
									$counterRight = $counterRight + 1;
								}elseif($celldata == "FEMALE"){
									$celldatanew = 1;
									$counterRight = $counterRight + 1;
								}else{
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
								}
								$celldata = $celldatanew;
							}

							if($n == 7){ // region
								if($celldata == '' || $celldata == NULL){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
								}else{
									$celldatanew = $celldata;
									$counterRight = $counterRight + 1;
								}
							}

							if($n == 8){ // province
								if($celldata == '' || $celldata == NULL){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
								}else{
									$celldatanew = $celldata;
									$counterRight = $counterRight + 1;
								}
							}

							if($n == 9){ // municipality
								if($celldata == '' || $celldata == NULL){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
								}else{
									$celldatanew = $celldata;
									$counterRight = $counterRight + 1;
								}
							}

							if($n == 10){ // barangay
								if($celldata == '' || $celldata == NULL){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
								}else{
									$celldatanew = $celldata;
									$counterRight = $counterRight + 1;
								}
							}

							if($n == 12){ // status
								if(!is_numeric($celldata) || $celldata == ''){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid/no_value</font>,";
								}else{
									$celldatanew = $celldata;
									$counterRight = $counterRight + 1;
								}
								$celldata = $celldatanew;
							}

							if($n == 13){ // payment mode
								if(!is_numeric($celldata) || $celldata == ''){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid/no_value</font>,";
								}else{
									$celldatanew = $celldata;
									$counterRight = $counterRight + 1;
								}
								$celldata = $celldatanew;
							}

							//====================end validating values========================
							$swivars[$n] = $celldata;
							$tablerow .=  '<td>'. $celldata . '</td>';

						}
						$bindkeys = "";
						// echo $field_error . "<br>";
						// echo $counterRight . "<br>";
						if($field_error == "" && $counterRight != 0){ // if no error on all fields
							$field_error = "<font color='green'>VALID ENTRY</font>,";
							$total_valid = $total_valid + 1;
							// $bindkeys = "<tr bgcolor='green'>";
							// $swidup = new BeneficiaryDAO();

							foreach($swivars as $varid => $varvalue){ //swi vars enumeration
								$bindkeys .= "<td>" . $varvalue . "</td>";
							}
							// echo "<font color='red'>(existing" . $swivars[5] . "," . $swivars[6] . "," . $swivars[8] . "," . $swivars[9] . "," . $swivars[10] . ")</font>";
							if($InsertParseDAO->entryChecker($swivars[1],$swivars[2],$swivars[3],$swivars[4],$swivars[5],$swivars[6])){ //duplicate checker
								$field_error .= "<font color='red'>(existing)</font>";
								$total_existing = $total_existing + 1;
							}else{

								// $switrans = new BeneficiaryDAO();
								// $CurrentUser = CurrentUserID();
								$InsertParseDAO23 = new InsertParseDAO($swivars[0],$swivars[1],$swivars[2],$swivars[3],$swivars[4],$swivars[5],
									$swivars[6],$swivars[7],$swivars[8],$swivars[9],
									$swivars[10],$swivars[11],$codeGen,CurrentUserID(),$swivars[12],$swivars[13]);
								$swisaved = $InsertParseDAO23->_InsertParse();

								if($swisaved == true){
									$field_error .= "<font color='green'>(saved)</font>";
									$total_saved = $total_saved +1 ;
								}else{
									$field_error .= "<font color='red'>(error saving / duplicate)</font>";
									$total_savingerror = $total_savingerror +1 ;
								}
							}
							//$bindkeys .= "</tr>";

						}else{
							$field_error .= "<font color='red'>(incomplete data)</font>";
							$total_null = $total_null + 1;
						}
						// if($swi_ind_total ==0 || $swi_ind_total ==1){
						// $field_error = "<font color='red'>incomplete data</font>,";
						// $total_incomplete = $total_incomplete + 1;
						// }

						$curent_row = $l + 1;
						$tablerow .=  '<td>'. $field_error . '</td><td>' . $curent_row . '</td></tr>';

						//echo $tablerow ;
						$uploaderlog .= $tablerow;
						//echo $bindkeys;
						$total_rows=$total_rows + 1; //row counting
					}

				}
			}else{
				echo "<font color='red'>Invalid Merger File. Please make sure you followed the <br>
					downloadable <a href='merger_file/merger_templatexls.xls'>Merger Template</a> and <br>
					had a all contents on the first worksheet.</font>,";
			}

			//echo '</table>';
			$uploaderlog .= '</table>';
			$mylogfile = "merger_files/" . time() . "-" . CurrentUserID() . "-" . $fileName . ".xls"; // change 999 to CurrentUserID()
			CreateLog($mylogfile,$uploaderlog);
			$paramArray = array($mylogfile,$codeGen);
			$paramImplode = implode("::",$paramArray);
			echo "<br>Total rows = " . $total_rows;
			echo "<br>Total valid rows = " . $total_valid ;
			echo "<br>Total existing rows = " . $total_existing ;
			echo "<br>Total incomplete/invalid rows = " . $total_null ;
			echo "<br>Total saved rows = " . $total_saved ;
			echo "<br>Total rows with saving error = " . $total_savingerror ;

			// echo "<br>Total incomplete rows = " . $total_incomplete ;
			// echo "<br>Total rows with invalid entries = " . ($total_rows - ($total_valid )) ; // + $total_incomplete
			if ($total_existing > 0 || $total_rows == $total_savingerror) {
				echo '<br><br><strong>Unable to upload, there are error on the entries, please recheck on the logs</strong>';
				echo '<br>Done [<a href="' . $mylogfile . '" target="_blank">Download Log</a>]...';
			} else {
				echo "<form id=\"generator\" method=\"post\" action=\"generatepensionerid.php\">";
				echo "<input type=\"hidden\" name=\"codegen\" value=\"".$paramImplode."\">";
				echo "<input type=\"submit\" value=\"Generate IDs\">";
				echo "</form>";
			}

		}
		echo '</div>';
//=====================================================================================
//=====================================================================================
//=====================================================================================
//=====================================================================================
//=====================================================================================
//=====================================================================================

	}elseif(($fileExt == "xls" ) && ($fileSize < $limitSize) && $_REQUEST['herby_certi'] <> ''){ //for xls files

		// require_once 'reader.php';
		$data = new Spreadsheet_Excel_Reader();
		// Set output Encoding.
		$data->setOutputEncoding('CP1251');
		$data->read($_FILES['file']['tmp_name']);
		$cols = 14; //force checking to 14 columns only

		//display file information
		echo '<hr><div id="datacontent">';
		echo '<h4>File Info:</h1><ul><li><b>File Name:  </b>'.$fileName.'</li>';
		echo '<li><b>File Size:</b> '.($fileSize/1000).' kb</li></li></ul><hr>';
		echo '<div id="datacontent">';
		echo '<h4>Worksheet Name: Sheet1</h1>';

		$htmltable = "<table border='1'>"; //used for xls value matrix contruction

		//loop on all check column headers
		$ch[] = array();
		$total_rows=0;
		$total_valid =0;
		$total_existing =0;
		$total_null =0;
		$total_saved =0;
		$total_savingerror =0;
		$total_incomplete =0;
		$total_witherrors =0;

		for ($i = 1; $i <= 1 ; $i++) {
			$failedfields = 0;
			$htmltable .= "<thead><tr>";
			for ($j = 1; $j <= $cols; $j++) { //$data->sheets[0]['numCols']

				$current_column_value = $data->sheets[0]['cells'][$i][$j];
				//begin checking column titles
				if($j == 1 && $current_column_value != "hh_id"){ $failedfields = $failedfields + 1 ; }
				if($j == 2 && $current_column_value != "first_name"){ $failedfields = $failedfields + 1 ; }
				if($j == 3 && $current_column_value != "middle_name"){ $failedfields = $failedfields + 1 ; }
				if($j == 4 && $current_column_value != "last_name"){ $failedfields = $failedfields + 1 ; }
				if($j == 5 && $current_column_value != "ext_name"){ $failedfields = $failedfields + 1 ; }
				if($j == 6 && $current_column_value != "birthdate"){ $failedfields = $failedfields + 1 ; }
				if($j == 7 && $current_column_value != "sex"){ $failedfields = $failedfields + 1 ; }
				if($j == 8 && $current_column_value != "region_psgc"){ $failedfields = $failedfields + 1 ; }
				if($j == 9 && $current_column_value != "province_psgc"){ $failedfields = $failedfields + 1 ; }
				if($j == 10 && $current_column_value != "municipality_psgc"){ $failedfields = $failedfields + 1 ; }
				if($j == 11 && $current_column_value != "brgy_psgc"){ $failedfields = $failedfields + 1 ; }
				if($j == 12 && $current_column_value != "street_address"){ $failedfields = $failedfields + 1 ; }
				if($j == 13 && $current_column_value != "status"){ $failedfields = $failedfields + 1 ; }
				if($j == 14 && $current_column_value != "payment_mode"){ $failedfields = $failedfields + 1 ; }
				//end checking column titles
				$ch[$j] = $current_column_value; //save current column name for user in error messages
				$htmltable .= '<td><b>' . $current_column_value . '</b></td>';

			}
			$htmltable .= "<th><b>Remarks</b></th><th><b>Excel Row No.</b></th></tr></thead>";
		}
		// echo $failedfields . "<br>";
		if($failedfields ==0){
			//echo $htmltable;
			$uploaderlog .= $htmltable;
			//loop on all records
			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
				flush();
				sleep(1);
				$field_error = ""; //contains remarks on field errors
				$counterRight=0;

				$tablerow = "<tr>";
				$swivars[] = array();
				for ($n = 1; $n <= $cols; $n++) {

					//Display data
					//====================begin validating values=========================
					$celldata = trim(strtoupper($data->sheets[0]['cells'][$i][$n]));

					if($n ==2){ // first name
						if($celldata == '' || $celldata == NULL){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
						}elseif(is_numeric($celldata)){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
						}else{
							$celldatanew = $celldata;
							$counterRight = $counterRight + 1;
						}
					}

					/*if($n ==3){ // middle name
						if($celldata == '' || $celldata == NULL){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
						}elseif(is_numeric($celldata)){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
						}else{
							$celldatanew = $celldata;
							$counterRight = $counterRight + 1;
						}
					}*/

					if($n ==4){ // last name
						if($celldata == '' || $celldata == NULL){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
						}elseif(is_numeric($celldata)){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
						}else{
							$celldatanew = $celldata;
							$counterRight = $counterRight + 1;
						}
					}

					/*if($n ==5){ // extension name
						if($celldata == '' || $celldata == NULL){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
						}elseif(is_numeric($celldata)){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
						}else{
							$celldatanew = $celldata;
							$counterRight = $counterRight + 1;
						}
					}*/

					if($n ==6) { //date checking

						//$celldatanew = date("Y-m-d",ExcelToPHP($celldata));
						$celldatanew = date("Y-m-d",strtotime($celldata)); //for normal date only
						if($celldatanew  == "1970-01-01"){

							$celldatanew2 = date("Y-m-d",ExcelToPHP($celldata));
							if(!is_numeric($celldata)){
								$celldatanew = $celldata;
								$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid/no_value</font>,";
							}else{
								$celldatanew = $celldatanew2;
								$counterRight = $counterRight + 1;
							}

						}
						$celldata = $celldatanew;

					}

					if($n ==7) { //SEX checking

						if($celldata == '' || $celldata == NULL){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
						}elseif($celldata == "MALE"){
							$celldatanew = 0;
							$counterRight = $counterRight + 1;
						}elseif($celldata == "FEMALE"){
							$celldatanew = 1;
							$counterRight = $counterRight + 1;
						}else{
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
						}
						$celldata = $celldatanew;
					}

					if($n ==8){ // region
						if($celldata == '' || $celldata == NULL){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
						}else{
							$celldatanew = $celldata;
							$counterRight = $counterRight + 1;
						}
					}

					if($n ==9){ // province
						if($celldata == '' || $celldata == NULL){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
						}else{
							$celldatanew = $celldata;
							$counterRight = $counterRight + 1;
						}
					}

					if($n ==10){ // municipality
						if($celldata == '' || $celldata == NULL){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
						}else{
							$celldatanew = $celldata;
							$counterRight = $counterRight + 1;
						}
					}

					if($n ==11){ // barangay
						if($celldata == '' || $celldata == NULL){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]no_value</font>,";
						}else{
							$celldatanew = $celldata;
							$counterRight = $counterRight + 1;
						}
					}

					if($n == 13){ // status
						if(!is_numeric($celldata)){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid/no_value</font>,";
						}else{
							$celldatanew = $celldata;
							$counterRight = $counterRight + 1;
						}
						$celldata = $celldatanew;
					}

					if($n == 14){ // payment mode
						if(!is_numeric($celldata)){
							$celldatanew = $celldata;
							$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid/no_value</font>,";
						}else{
							$celldatanew = $celldata;
							$counterRight = $counterRight + 1;
						}
						$celldata = $celldatanew;
					}

					//====================end validating values========================
					$swivars[$n] = $celldata;
					$tablerow .= '<td>'. $celldata . '</td>';

				}

				$bindkeys = "";
				if($field_error == "" && $counterRight != 0){ // if no error on all fields  // && $swi_ind_total !=0
					$field_error = "<font color='green'>VALID ENTRY</font>,";
					$total_valid = $total_valid + 1;

					//$bindkeys = "<tr bgcolor='green'>";
					// $swidup = new BeneficiaryDAO();

					foreach($swivars as $varid => $varvalue){ //swi vars enumeration
						$bindkeys .= "<td>" . $varvalue . "</td>";
					}
					// echo "<font color='red'>(existing" . $swivars[1] . "," . $swivars[31] . ")</font>";
					if($InsertParseDAO->entryChecker($swivars[6],$swivars[7],$swivars[8],$swivars[9],$swivars[10],$swivars[11])){ //duplicate swi checker
						$field_error .= "<font color='red'>(existing)</font>";
						$total_existing = $total_existing + 1;
					}else{

						// $switrans = new BeneficiaryDAO();
						// $CurrentUser = CurrentUserID();
						$InsertParseDAO23 = new InsertParseDAO($swivars[1],$swivars[2],$swivars[3],$swivars[4],$swivars[5],$swivars[6],
							$swivars[7],$swivars[8],$swivars[9],$swivars[10],
							$swivars[11],$swivars[12],$codeGen,CurrentUserID(),$swivars[13],$swivars[14]);
						$swisaved = $InsertParseDAO23->_InsertParse();

						if($swisaved == true){
							$field_error .= "<font color='green'>(saved)</font>";
							$total_saved = $total_saved +1 ;
						}else{
							$field_error .= "<font color='red'>(error saving / duplicate)</font>";
							$total_savingerror = $total_savingerror +1 ;
						}
					}
					//$bindkeys .= "</tr>";
				}else{
					$field_error .= "<font color='red'>(incomplete data)</font>";
					$total_null = $total_null + 1;
				}
				// if($swi_ind_total ==0 || $swi_ind_total ==1){
				// $field_error = "<font color='red'>incomplete swi</font>,";
				// $total_incomplete = $total_incomplete + 1;
				// }
				$curent_row = $i;
				$tablerow .= '<td>'. $field_error . '</td><td>' . $curent_row . '</td></tr>';
				$uploaderlog .= $tablerow;
				$total_rows=$total_rows + 1; //row counting
			}
			$uploaderlog .= "</table>";

			$mylogfile = "merger_files/" . time() . "-" . CurrentUserID() . "-" . $fileName;
			CreateLog($mylogfile,$uploaderlog);
			$paramArray = array($mylogfile,$codeGen);
			$paramImplode = implode("::",$paramArray);
			echo "<br>Total rows = " . $total_rows;
			echo "<br>Total valid rows = " . $total_valid ;
			echo "<br>Total existing rows = " . $total_existing ;
			echo "<br>Total incomplete/invalid rows = " . $total_null ;
			echo "<br>Total saved rows = " . $total_saved ;
			echo "<br>Total rows with saving error = " . $total_savingerror ;
			// echo "<br>Total incomplete rows = " . $total_incomplete ;
			// echo "<br>Total rows with invalid entries = " . ($total_rows - ($total_valid )) ; // + $total_incomplete
			if ($total_existing > 0 || $total_rows == $total_savingerror) {
				echo '<br><br><strong>Unable to upload, there are error on the entries, please recheck on the logs</strong>';
				echo '<br>Done [<a href="' . $mylogfile . '" target="_blank">Download Log</a>]...';
			} else {
				echo "<form id=\"generator\" method=\"post\" action=\"generatepensionerid.php\">";
				echo "<input type=\"hidden\" name=\"codegen\" value=\"".$paramImplode."\">";
				echo "<input type=\"submit\" value=\"Generate IDs\">";
				echo "</form>";
			}
		}else{
			echo "<font color='red'>Invalid Merger File. Please make sure you followed the <br>
					downloadable <a href='merger_file/merger_templatexls.xls'>Merger Template</a> and <br>
					had a all contents on the first worksheet.</font>,";
		}
	}else{
		if ($_REQUEST['herby_certi'] == ''){
			echo '<script>alert("You should certify the list first before you can upload!")</script>';
		} elseif($fileExt == '') {
			echo '<script>alert("Sorry, only [xls] and [xlsx] Merger Template files under '.($limitSize/1000000).' Mb are allowed!")</script>';
		}
	}
}
?>
<?php //function goes here
function checkDateFormat($date){
	//match the format of the date
	if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)){
		//check weather the date is valid of not
		if(checkdate($parts[2],$parts[3],$parts[1]))
			return true;
		else
			return false;
	}
	else
		return false;
}

function is_date( $str ) { //check if valid date
	try {
		$dt = new DateTime( trim($str) );
	}
	catch( Exception $e ) {
		return false;
	}
	$month = $dt->format('m');
	$day = $dt->format('d');
	$year = $dt->format('Y');
	if( checkdate($month, $day, $year) ) {
		return true;
	}
	else {
		return false;
	}
}

function inRange($number, $a, $b){ //check number within range
	$min = min($a, $b);
	$max = max($a, $b);
	if ($number < $min) return FALSE;
	if ($number > $max) return FALSE;
	return TRUE;
}

function ExcelToPHP($dateValue = 0, $ExcelBaseDate=0) {
	if ($ExcelBaseDate == 0) {
		$myExcelBaseDate = 25569;
		//  Adjust for the spurious 29-Feb-1900 (Day 60)
		if ($dateValue < 60) {
			--$myExcelBaseDate;
		}
	} else {
		$myExcelBaseDate = 24107;
	}

	// Perform conversion
	if ($dateValue >= 1) {
		$utcDays = $dateValue - $myExcelBaseDate;
		$returnValue = round($utcDays * 86400);
		if (($returnValue <= PHP_INT_MAX) && ($returnValue >= -PHP_INT_MAX)) {
			$returnValue = (integer) $returnValue;
		}
	} else {
		$hours = round($dateValue * 24);
		$mins = round($dateValue * 1440) - round($hours * 60);
		$secs = round($dateValue * 86400) - round($hours * 3600) - round($mins * 60);
		$returnValue = (integer) gmmktime($hours, $mins, $secs);
	}

	// Return
	return $returnValue;
}
function CreateLog($filename,$filecontent){
	$myFile = $filename;
	$fh = fopen($myFile, 'w') or die("can't open file");
	fwrite($fh, $filecontent);
	fclose($fh);
}
?>

<?php // custom page end jfsbaldo ?>
<?php include_once "footer.php" ?>
<?php
$tbl_pensioner_list->Page_Terminate();
?>
