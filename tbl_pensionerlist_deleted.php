<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_pensionerinfo_deleted.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "tbl_representativegridcls.php" ?>
<?php include_once "tbl_supportgridcls.php" ?>
<?php include_once "tbl_updatesgridcls.php" ?>
<?php include_once "userfn10.php" ?>
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
		$item->Visible = FALSE AND $Security->CanView();
		$item->OnLeft = TRUE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = FALSE AND $Security->CanEdit();
		$item->OnLeft = TRUE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = FALSE AND $Security->CanAdd();
		$item->OnLeft = TRUE;

		// "detail_tbl_representative"
		$item = &$this->ListOptions->Add("detail_tbl_representative");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = FALSE AND $Security->AllowList(CurrentProjectID() . 'tbl_representative') && !$this->ShowMultipleDetails;
		$item->OnLeft = TRUE;
		$item->ShowInButtonGroup = FALSE;
		if (!isset($GLOBALS["tbl_representative_grid"])) $GLOBALS["tbl_representative_grid"] = new ctbl_representative_grid;

		// "detail_tbl_support"
		$item = &$this->ListOptions->Add("detail_tbl_support");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = FALSE AND $Security->AllowList(CurrentProjectID() . 'tbl_support') && !$this->ShowMultipleDetails;
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
		$item->Visible = FALSE AND $Security->CanDelete();
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
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\" data-rel=\"tooltip-ace\" title=\"New Pensioner Profile\">" . "<i class='icon-file align-middle bigger-125'></i> ". $Language->Phrase("AddLink")."</a>";
		$item->Visible = FALSE AND ($this->AddUrl <> "" && $Security->CanAdd());
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
		$item = &$option->Add("multiuploader");
		$item->Body = "<a class=\"btn btn-primary btn-sm\" href=\"tbl_pensioneruploader.php\">" . "Upload Pensioners" . "</a>";
		$item->Visible = FALSE AND ($Security->CanDelete());

		// Add multi delete
		$item = &$option->Add("multidelete");
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_pensionerlist, '" . $this->MultiDeleteUrl . "', ewLanguage.Phrase('DeleteMultiConfirmMsg'));return false;\">" . $Language->Phrase("DeleteSelectedLink") . "</a>";
		$item->Visible = FALSE AND ($Security->CanDelete());

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

// Page object
var tbl_pensioner_list = new ew_Page("tbl_pensioner_list");
tbl_pensioner_list.PageID = "list"; // Page ID
var EW_PAGE_ID = tbl_pensioner_list.PageID; // For backward compatibility

// Form object
var ftbl_pensionerlist = new ew_Form("ftbl_pensionerlist");
ftbl_pensionerlist.FormKeyCountName = '<?php echo $tbl_pensioner_list->FormKeyCountName ?>';

// Form_CustomValidate event
ftbl_pensionerlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_pensionerlist.ValidateRequired = true;
<?php } else { ?>
ftbl_pensionerlist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_pensionerlist.Lists["x_MaritalID"] = {"LinkField":"x_MaritalID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlist.Lists["x_affliationID"] = {"LinkField":"x_affliationID","Ajax":true,"AutoFill":false,"DisplayFields":["x_aff_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlist.Lists["x_psgc_region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlist.Lists["x_psgc_province"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlist.Lists["x_psgc_municipality"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlist.Lists["x_psgc_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlist.Lists["x_Status"] = {"LinkField":"x_statusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_status","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlist.Lists["x_paymentmodeID"] = {"LinkField":"x_paymentmodeID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlist.Lists["x_ArrangementID"] = {"LinkField":"x_ArrangementID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var ftbl_pensionerlistsrch = new ew_Form("ftbl_pensionerlistsrch");

// Validate function for search
ftbl_pensionerlistsrch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";

	// Set up row object
	ew_ElementsToRow(fobj);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
ftbl_pensionerlistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_pensionerlistsrch.ValidateRequired = true; // Use JavaScript validation
<?php } else { ?>
ftbl_pensionerlistsrch.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Dynamic selection lists
ftbl_pensionerlistsrch.Lists["x_MaritalID"] = {"LinkField":"x_MaritalID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlistsrch.Lists["x_affliationID"] = {"LinkField":"x_affliationID","Ajax":true,"AutoFill":false,"DisplayFields":["x_aff_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlistsrch.Lists["x_psgc_region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlistsrch.Lists["x_psgc_province"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":["x_psgc_region"],"FilterFields":["x_region_code"],"Options":[]};
ftbl_pensionerlistsrch.Lists["x_psgc_municipality"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":["x_psgc_province"],"FilterFields":["x_prov_code"],"Options":[]};
ftbl_pensionerlistsrch.Lists["x_psgc_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":["x_psgc_municipality"],"FilterFields":["x_city_code"],"Options":[]};
ftbl_pensionerlistsrch.Lists["x_Status"] = {"LinkField":"x_statusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_status","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerlistsrch.Lists["x_paymentmodeID"] = {"LinkField":"x_paymentmodeID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php if ($tbl_pensioner_list->ExportOptions->Visible()) { ?>
	<!-- Tolits expot removed original script Export -->
		<div class="row">
			<div class="col-xs-12">
				<p>
					<a class="btn btn-sm" href="<?php echo ew_CurrentPage();?>?export=pdf">
						<i class="icon-file-text align-top bigger-125"></i>
							PDF
					</a>		
					<a class="btn btn-primary btn-sm" href="<?php echo ew_CurrentPage();?>?export=csv">
						<i class="icon-table align-top bigger-125"></i>
							CSV
					</a>
					<a class="btn btn-info btn-sm" href="<?php echo ew_CurrentPage();?>?export=excel">
						<i class="icon-list align-top bigger-125"></i>
							Excel
					</a>
					<a class="btn btn-success btn-sm" href="<?php echo ew_CurrentPage();?>?export=print">
						<i class="icon-print align-top bigger-125"></i>
							Print
					</a>
					<a class="btn btn-warning btn-sm" href="<?php echo ew_CurrentPage();?>?export=html">
						<i class="icon-th-list align-top bigger-125"></i>
							HTML
					</a>
				</p>
			</div>
		</div>	
	<!-- End -->
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$tbl_pensioner_list->TotalRecs = $tbl_pensioner->SelectRecordCount();
	} else {
		if ($tbl_pensioner_list->Recordset = $tbl_pensioner_list->LoadRecordset())
			$tbl_pensioner_list->TotalRecs = $tbl_pensioner_list->Recordset->RecordCount();
	}
	$tbl_pensioner_list->StartRec = 1;
	if ($tbl_pensioner_list->DisplayRecs <= 0 || ($tbl_pensioner->Export <> "" && $tbl_pensioner->ExportAll)) // Display all records
		$tbl_pensioner_list->DisplayRecs = $tbl_pensioner_list->TotalRecs;
	if (!($tbl_pensioner->Export <> "" && $tbl_pensioner->ExportAll))
		$tbl_pensioner_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$tbl_pensioner_list->Recordset = $tbl_pensioner_list->LoadRecordset($tbl_pensioner_list->StartRec-1, $tbl_pensioner_list->DisplayRecs);
$tbl_pensioner_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($tbl_pensioner->Export == "" && $tbl_pensioner->CurrentAction == "") { ?>
<form name="ftbl_pensionerlistsrch" id="ftbl_pensionerlistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<!--<table class="ewSearchTable"><tr><td>-->
<div class="row">
	<div class="col-xs-12">
		<div class="widget-box collapsed">
			<div class="widget-header header-color-pink">
				<h5 class="lighter">Advanced Search</h5>
				<div class="widget-toolbar">
					<a href="#" data-action="collapse">
					<i class="1 icon-chevron-down bigger-125"></i>
					</a>
					<a href="#" data-action="reload">
					<i class="1 icon-refresh bigger-125"></i>
					</a>
				</div>
			</div>
		<div class="widget-body">
	<div class="widget-main">
	<div id="ftbl_pensionerlistsrch_SearchPanel">
		<input type="hidden" name="cmd" value="search">
		<input type="hidden" name="t" value="tbl_pensioner">
		<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$tbl_pensioner_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$tbl_pensioner->RowType = EW_ROWTYPE_SEARCH;

// Render row
$tbl_pensioner->ResetAttrs();
$tbl_pensioner_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($tbl_pensioner->sex->Visible) { // sex ?>
	<span id="xsc_sex" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pensioner->sex->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_sex" id="z_sex" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_sex" id="x_sex" name="x_sex"<?php echo $tbl_pensioner->sex->EditAttributes() ?>>
<?php
if (is_array($tbl_pensioner->sex->EditValue)) {
	$arwrk = $tbl_pensioner->sex->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pensioner->sex->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
<?php if ($tbl_pensioner->MaritalID->Visible) { // MaritalID ?>
	<span id="xsc_MaritalID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pensioner->MaritalID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_MaritalID" id="z_MaritalID" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_MaritalID" id="x_MaritalID" name="x_MaritalID"<?php echo $tbl_pensioner->MaritalID->EditAttributes() ?>>
<?php
if (is_array($tbl_pensioner->MaritalID->EditValue)) {
	$arwrk = $tbl_pensioner->MaritalID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pensioner->MaritalID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `MaritalID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_civilstatus`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_pensioner->Lookup_Selecting($tbl_pensioner->MaritalID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `MaritalID` ASC";
?>
<input type="hidden" name="s_x_MaritalID" id="s_x_MaritalID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`MaritalID` = {filter_value}"); ?>&t0=3">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_3" class="ewRow">
<?php if ($tbl_pensioner->affliationID->Visible) { // affliationID ?>
	<span id="xsc_affliationID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pensioner->affliationID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_affliationID" id="z_affliationID" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_affliationID" id="x_affliationID" name="x_affliationID"<?php echo $tbl_pensioner->affliationID->EditAttributes() ?>>
<?php
if (is_array($tbl_pensioner->affliationID->EditValue)) {
	$arwrk = $tbl_pensioner->affliationID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pensioner->affliationID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `affliationID`, `aff_description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_affliation`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_pensioner->Lookup_Selecting($tbl_pensioner->affliationID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `affliationID` ASC";
?>
<input type="hidden" name="s_x_affliationID" id="s_x_affliationID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`affliationID` = {filter_value}"); ?>&t0=3">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_4" class="ewRow">
<?php if ($tbl_pensioner->psgc_region->Visible) { // psgc_region ?>
	<span id="xsc_psgc_region" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pensioner->psgc_region->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_psgc_region" id="z_psgc_region" value="="></span>
		<span class="control-group ewSearchField">
<?php $tbl_pensioner->psgc_region->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_psgc_province']); " . @$tbl_pensioner->psgc_region->EditAttrs["onchange"]; ?>
<select data-field="x_psgc_region" id="x_psgc_region" name="x_psgc_region"<?php echo $tbl_pensioner->psgc_region->EditAttributes() ?>>
<?php
if (is_array($tbl_pensioner->psgc_region->EditValue)) {
	$arwrk = $tbl_pensioner->psgc_region->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pensioner->psgc_region->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_pensioner->Lookup_Selecting($tbl_pensioner->psgc_region, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `region_code` ASC";
?>
<input type="hidden" name="s_x_psgc_region" id="s_x_psgc_region" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`region_code` = {filter_value}"); ?>&t0=21">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_5" class="ewRow">
<?php if ($tbl_pensioner->psgc_province->Visible) { // psgc_province ?>
	<span id="xsc_psgc_province" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pensioner->psgc_province->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_psgc_province" id="z_psgc_province" value="="></span>
		<span class="control-group ewSearchField">
<?php $tbl_pensioner->psgc_province->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_psgc_municipality']); " . @$tbl_pensioner->psgc_province->EditAttrs["onchange"]; ?>
<select data-field="x_psgc_province" id="x_psgc_province" name="x_psgc_province"<?php echo $tbl_pensioner->psgc_province->EditAttributes() ?>>
<?php
if (is_array($tbl_pensioner->psgc_province->EditValue)) {
	$arwrk = $tbl_pensioner->psgc_province->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pensioner->psgc_province->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_provinces`";
$sWhereWrk = "{filter}";

// Call Lookup selecting
$tbl_pensioner->Lookup_Selecting($tbl_pensioner->psgc_province, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `prov_name` ASC";
?>
<input type="hidden" name="s_x_psgc_province" id="s_x_psgc_province" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`prov_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`region_code` IN ({filter_value})"); ?>&t1=21">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_6" class="ewRow">
<?php if ($tbl_pensioner->psgc_municipality->Visible) { // psgc_municipality ?>
	<span id="xsc_psgc_municipality" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pensioner->psgc_municipality->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_psgc_municipality" id="z_psgc_municipality" value="="></span>
		<span class="control-group ewSearchField">
<?php $tbl_pensioner->psgc_municipality->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_psgc_brgy']); " . @$tbl_pensioner->psgc_municipality->EditAttrs["onchange"]; ?>
<select data-field="x_psgc_municipality" id="x_psgc_municipality" name="x_psgc_municipality"<?php echo $tbl_pensioner->psgc_municipality->EditAttributes() ?>>
<?php
if (is_array($tbl_pensioner->psgc_municipality->EditValue)) {
	$arwrk = $tbl_pensioner->psgc_municipality->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pensioner->psgc_municipality->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_cities`";
$sWhereWrk = "{filter}";

// Call Lookup selecting
$tbl_pensioner->Lookup_Selecting($tbl_pensioner->psgc_municipality, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `city_name` ASC";
?>
<input type="hidden" name="s_x_psgc_municipality" id="s_x_psgc_municipality" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`city_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`prov_code` IN ({filter_value})"); ?>&t1=21">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_7" class="ewRow">
<?php if ($tbl_pensioner->psgc_brgy->Visible) { // psgc_brgy ?>
	<span id="xsc_psgc_brgy" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pensioner->psgc_brgy->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_psgc_brgy" id="z_psgc_brgy" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_psgc_brgy" id="x_psgc_brgy" name="x_psgc_brgy"<?php echo $tbl_pensioner->psgc_brgy->EditAttributes() ?>>
<?php
if (is_array($tbl_pensioner->psgc_brgy->EditValue)) {
	$arwrk = $tbl_pensioner->psgc_brgy->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pensioner->psgc_brgy->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_brgy`";
$sWhereWrk = "{filter}";

// Call Lookup selecting
$tbl_pensioner->Lookup_Selecting($tbl_pensioner->psgc_brgy, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `brgy_name` ASC";
?>
<input type="hidden" name="s_x_psgc_brgy" id="s_x_psgc_brgy" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`brgy_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`city_code` IN ({filter_value})"); ?>&t1=21">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_8" class="ewRow">
<?php if ($tbl_pensioner->Status->Visible) { // Status ?>
	<span id="xsc_Status" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pensioner->Status->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Status" id="z_Status" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_Status" id="x_Status" name="x_Status"<?php echo $tbl_pensioner->Status->EditAttributes() ?>>
<?php
if (is_array($tbl_pensioner->Status->EditValue)) {
	$arwrk = $tbl_pensioner->Status->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pensioner->Status->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `statusID`, `status` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_status`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_pensioner->Lookup_Selecting($tbl_pensioner->Status, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `statusID` ASC";
?>
<input type="hidden" name="s_x_Status" id="s_x_Status" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`statusID` = {filter_value}"); ?>&t0=3">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_9" class="ewRow">
<?php if ($tbl_pensioner->paymentmodeID->Visible) { // paymentmodeID ?>
	<span id="xsc_paymentmodeID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pensioner->paymentmodeID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_paymentmodeID" id="z_paymentmodeID" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_paymentmodeID" id="x_paymentmodeID" name="x_paymentmodeID"<?php echo $tbl_pensioner->paymentmodeID->EditAttributes() ?>>
<?php
if (is_array($tbl_pensioner->paymentmodeID->EditValue)) {
	$arwrk = $tbl_pensioner->paymentmodeID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pensioner->paymentmodeID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `paymentmodeID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_paymentmode`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_pensioner->Lookup_Selecting($tbl_pensioner->paymentmodeID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `paymentmodeID` ASC";
?>
<input type="hidden" name="s_x_paymentmodeID" id="s_x_paymentmodeID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`paymentmodeID` = {filter_value}"); ?>&t0=3">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_10" class="ewRow">
<?php if ($tbl_pensioner->is_4ps->Visible) { // is_4ps ?>
	<span id="xsc_is_4ps" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pensioner->is_4ps->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_is_4ps" id="z_is_4ps" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_is_4ps" id="x_is_4ps" name="x_is_4ps"<?php echo $tbl_pensioner->is_4ps->EditAttributes() ?>>
<?php
if (is_array($tbl_pensioner->is_4ps->EditValue)) {
	$arwrk = $tbl_pensioner->is_4ps->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pensioner->is_4ps->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_11" class="row">
	<div class="col-xs-12 col-sm-4">
	<div class="input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control search-query" value="<?php echo ew_HtmlEncode($tbl_pensioner_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo $Language->Phrase("Search") ?>">
	<span class="input-group-btn">
	<button class="btn btn-purple btn-sm" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?> <i class="icon-search icon-on-right bigger-110"></i></button>&nbsp;
	<a type="button" class="btn btn-success btn-sm" href="<?php echo $tbl_pensioner_list->PageUrl() ?>cmd=reset">ShowAll <i class="icon-refresh icon-on-right bigger-110"></i></a>
	</span>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<!--<a class="btn ewShowAll" href="<?php echo $tbl_pensioner_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a> -->
</div>
<div id="xsr_12" class="radio">
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($tbl_pensioner_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("ExactPhrase") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($tbl_pensioner_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AllWord") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($tbl_pensioner_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AnyWord") ?></span></label>
</div>
</div>
</div>
			</div>
		</div>
	</div>
</div>
<!--</td></tr></table>-->
</form>
<?php } ?>
<?php } ?>
<?php $tbl_pensioner_list->ShowPageHeader(); ?>
<?php
$tbl_pensioner_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($tbl_pensioner->CurrentAction <> "gridadd" && $tbl_pensioner->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_pensioner_list->Pager)) $tbl_pensioner_list->Pager = new cNumericPager($tbl_pensioner_list->StartRec, $tbl_pensioner_list->DisplayRecs, $tbl_pensioner_list->TotalRecs, $tbl_pensioner_list->RecRange) ?>
<?php if ($tbl_pensioner_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_pensioner_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pensioner_list->PageUrl() ?>start=<?php echo $tbl_pensioner_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_pensioner_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pensioner_list->PageUrl() ?>start=<?php echo $tbl_pensioner_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_pensioner_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_pensioner_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_pensioner_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pensioner_list->PageUrl() ?>start=<?php echo $tbl_pensioner_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_pensioner_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pensioner_list->PageUrl() ?>start=<?php echo $tbl_pensioner_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_pensioner_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_pensioner_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_pensioner_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_pensioner_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_pensioner_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_pensioner_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_pensioner">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_pensioner_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_pensioner_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_pensioner_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_pensioner->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_pensioner_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="ftbl_pensionerlist" id="ftbl_pensionerlist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_pensioner">
<div id="gmp_tbl_pensioner" class="ewGridMiddlePanel">
<?php if ($tbl_pensioner_list->TotalRecs > 0) { ?>
<table id="tbl_tbl_pensionerlist" class="ewTable ewTableSeparate">
<?php echo $tbl_pensioner->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$tbl_pensioner_list->RenderListOptions();

// Render list options (header, left)
$tbl_pensioner_list->ListOptions->Render("header", "left");
?>
<?php /* if ($tbl_pensioner->SeniorID->Visible) { // SeniorID ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->SeniorID) == "") { ?>
		<td><div id="elh_tbl_pensioner_SeniorID" class="tbl_pensioner_SeniorID"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->SeniorID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->SeniorID) ?>',1);"><div id="elh_tbl_pensioner_SeniorID" class="tbl_pensioner_SeniorID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->SeniorID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->SeniorID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->SeniorID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } */ ?>
<?php if ($tbl_pensioner->PensionerID->Visible) { // PensionerID ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->PensionerID) == "") { ?>
		<td><div id="elh_tbl_pensioner_PensionerID" class="tbl_pensioner_PensionerID"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->PensionerID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->PensionerID) ?>',1);"><div id="elh_tbl_pensioner_PensionerID" class="tbl_pensioner_PensionerID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->PensionerID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->PensionerID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->PensionerID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php /* if ($tbl_pensioner->InclusionDate->Visible) { // InclusionDate ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->InclusionDate) == "") { ?>
		<td><div id="elh_tbl_pensioner_InclusionDate" class="tbl_pensioner_InclusionDate"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->InclusionDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->InclusionDate) ?>',1);"><div id="elh_tbl_pensioner_InclusionDate" class="tbl_pensioner_InclusionDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->InclusionDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->InclusionDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->InclusionDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->hh_id->Visible) { // hh_id ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->hh_id) == "") { ?>
		<td><div id="elh_tbl_pensioner_hh_id" class="tbl_pensioner_hh_id"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->hh_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->hh_id) ?>',1);"><div id="elh_tbl_pensioner_hh_id" class="tbl_pensioner_hh_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->hh_id->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->hh_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->hh_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->osca_ID->Visible) { // osca_ID ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->osca_ID) == "") { ?>
		<td><div id="elh_tbl_pensioner_osca_ID" class="tbl_pensioner_osca_ID"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->osca_ID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->osca_ID) ?>',1);"><div id="elh_tbl_pensioner_osca_ID" class="tbl_pensioner_osca_ID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->osca_ID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->osca_ID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->osca_ID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->PlaceIssued->Visible) { // PlaceIssued ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->PlaceIssued) == "") { ?>
		<td><div id="elh_tbl_pensioner_PlaceIssued" class="tbl_pensioner_PlaceIssued"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->PlaceIssued->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->PlaceIssued) ?>',1);"><div id="elh_tbl_pensioner_PlaceIssued" class="tbl_pensioner_PlaceIssued">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->PlaceIssued->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->PlaceIssued->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->PlaceIssued->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->DateIssued->Visible) { // DateIssued ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->DateIssued) == "") { ?>
		<td><div id="elh_tbl_pensioner_DateIssued" class="tbl_pensioner_DateIssued"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->DateIssued->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->DateIssued) ?>',1);"><div id="elh_tbl_pensioner_DateIssued" class="tbl_pensioner_DateIssued">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->DateIssued->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->DateIssued->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->DateIssued->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } */ ?>
<?php if ($tbl_pensioner->lastname->Visible) { // lastname ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->lastname) == "") { ?>
		<td><div id="elh_tbl_pensioner_lastname" class="tbl_pensioner_lastname"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->lastname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->lastname) ?>',1);"><div id="elh_tbl_pensioner_lastname" class="tbl_pensioner_lastname">
					<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->lastname->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->lastname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->lastname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
				</div></div></td>
	<?php } ?>
<?php } ?>
<?php if ($tbl_pensioner->firstname->Visible) { // firstname ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->firstname) == "") { ?>
		<td><div id="elh_tbl_pensioner_firstname" class="tbl_pensioner_firstname"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->firstname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->firstname) ?>',1);"><div id="elh_tbl_pensioner_firstname" class="tbl_pensioner_firstname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->firstname->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->firstname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->firstname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->middlename->Visible) { // middlename ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->middlename) == "") { ?>
		<td><div id="elh_tbl_pensioner_middlename" class="tbl_pensioner_middlename"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->middlename->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->middlename) ?>',1);"><div id="elh_tbl_pensioner_middlename" class="tbl_pensioner_middlename">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->middlename->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->middlename->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->middlename->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>
<?php if ($tbl_pensioner->extname->Visible) { // extname ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->extname) == "") { ?>
		<td><div id="elh_tbl_pensioner_extname" class="tbl_pensioner_extname"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->extname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->extname) ?>',1);"><div id="elh_tbl_pensioner_extname" class="tbl_pensioner_extname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->extname->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->extname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->extname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php /* if ($tbl_pensioner->Birthdate->Visible) { // Birthdate ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->Birthdate) == "") { ?>
		<td><div id="elh_tbl_pensioner_Birthdate" class="tbl_pensioner_Birthdate"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->Birthdate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->Birthdate) ?>',1);"><div id="elh_tbl_pensioner_Birthdate" class="tbl_pensioner_Birthdate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->Birthdate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->Birthdate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->Birthdate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->sex->Visible) { // sex ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->sex) == "") { ?>
		<td><div id="elh_tbl_pensioner_sex" class="tbl_pensioner_sex"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->sex->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->sex) ?>',1);"><div id="elh_tbl_pensioner_sex" class="tbl_pensioner_sex">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->sex->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->sex->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->sex->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->MaritalID->Visible) { // MaritalID ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->MaritalID) == "") { ?>
		<td><div id="elh_tbl_pensioner_MaritalID" class="tbl_pensioner_MaritalID"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->MaritalID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->MaritalID) ?>',1);"><div id="elh_tbl_pensioner_MaritalID" class="tbl_pensioner_MaritalID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->MaritalID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->MaritalID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->MaritalID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->affliationID->Visible) { // affliationID ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->affliationID) == "") { ?>
		<td><div id="elh_tbl_pensioner_affliationID" class="tbl_pensioner_affliationID"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->affliationID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->affliationID) ?>',1);"><div id="elh_tbl_pensioner_affliationID" class="tbl_pensioner_affliationID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->affliationID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->affliationID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->affliationID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } */ ?>
<?php if ($tbl_pensioner->psgc_region->Visible) { // psgc_region ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->psgc_region) == "") { ?>
		<td><div id="elh_tbl_pensioner_psgc_region" class="tbl_pensioner_psgc_region"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->psgc_region->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->psgc_region) ?>',1);"><div id="elh_tbl_pensioner_psgc_region" class="tbl_pensioner_psgc_region">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->psgc_region->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->psgc_region->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->psgc_region->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->psgc_province->Visible) { // psgc_province ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->psgc_province) == "") { ?>
		<td><div id="elh_tbl_pensioner_psgc_province" class="tbl_pensioner_psgc_province"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->psgc_province->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->psgc_province) ?>',1);"><div id="elh_tbl_pensioner_psgc_province" class="tbl_pensioner_psgc_province">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->psgc_province->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->psgc_province->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->psgc_province->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->psgc_municipality->Visible) { // psgc_municipality ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->psgc_municipality) == "") { ?>
		<td><div id="elh_tbl_pensioner_psgc_municipality" class="tbl_pensioner_psgc_municipality"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->psgc_municipality->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->psgc_municipality) ?>',1);"><div id="elh_tbl_pensioner_psgc_municipality" class="tbl_pensioner_psgc_municipality">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->psgc_municipality->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->psgc_municipality->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->psgc_municipality->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->psgc_brgy->Visible) { // psgc_brgy ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->psgc_brgy) == "") { ?>
		<td><div id="elh_tbl_pensioner_psgc_brgy" class="tbl_pensioner_psgc_brgy"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->psgc_brgy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->psgc_brgy) ?>',1);"><div id="elh_tbl_pensioner_psgc_brgy" class="tbl_pensioner_psgc_brgy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->psgc_brgy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->psgc_brgy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->psgc_brgy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php /* if ($tbl_pensioner->given_add->Visible) { // given_add ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->given_add) == "") { ?>
		<td><div id="elh_tbl_pensioner_given_add" class="tbl_pensioner_given_add"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->given_add->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->given_add) ?>',1);"><div id="elh_tbl_pensioner_given_add" class="tbl_pensioner_given_add">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->given_add->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->given_add->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->given_add->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } */ ?>
<?php if ($tbl_pensioner->Status->Visible) { // Status ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->Status) == "") { ?>
		<td><div id="elh_tbl_pensioner_Status" class="tbl_pensioner_Status"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->Status->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->Status) ?>',1);"><div id="elh_tbl_pensioner_Status" class="tbl_pensioner_Status">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->Status->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->Status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->Status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php /* if ($tbl_pensioner->paymentmodeID->Visible) { // paymentmodeID ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->paymentmodeID) == "") { ?>
		<td><div id="elh_tbl_pensioner_paymentmodeID" class="tbl_pensioner_paymentmodeID"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->paymentmodeID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->paymentmodeID) ?>',1);"><div id="elh_tbl_pensioner_paymentmodeID" class="tbl_pensioner_paymentmodeID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->paymentmodeID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->paymentmodeID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->paymentmodeID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->approved->Visible) { // approved ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->approved) == "") { ?>
		<td><div id="elh_tbl_pensioner_approved" class="tbl_pensioner_approved"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->approved->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->approved) ?>',1);"><div id="elh_tbl_pensioner_approved" class="tbl_pensioner_approved">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->approved->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->approved->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->approved->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->approvedby->Visible) { // approvedby ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->approvedby) == "") { ?>
		<td><div id="elh_tbl_pensioner_approvedby" class="tbl_pensioner_approvedby"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->approvedby->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->approvedby) ?>',1);"><div id="elh_tbl_pensioner_approvedby" class="tbl_pensioner_approvedby">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->approvedby->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->approvedby->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->approvedby->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->DateApproved->Visible) { // DateApproved ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->DateApproved) == "") { ?>
		<td><div id="elh_tbl_pensioner_DateApproved" class="tbl_pensioner_DateApproved"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->DateApproved->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->DateApproved) ?>',1);"><div id="elh_tbl_pensioner_DateApproved" class="tbl_pensioner_DateApproved">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->DateApproved->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->DateApproved->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->DateApproved->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->ArrangementID->Visible) { // ArrangementID ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->ArrangementID) == "") { ?>
		<td><div id="elh_tbl_pensioner_ArrangementID" class="tbl_pensioner_ArrangementID"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->ArrangementID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->ArrangementID) ?>',1);"><div id="elh_tbl_pensioner_ArrangementID" class="tbl_pensioner_ArrangementID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->ArrangementID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->ArrangementID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->ArrangementID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->is_4ps->Visible) { // is_4ps ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->is_4ps) == "") { ?>
		<td><div id="elh_tbl_pensioner_is_4ps" class="tbl_pensioner_is_4ps"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->is_4ps->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->is_4ps) ?>',1);"><div id="elh_tbl_pensioner_is_4ps" class="tbl_pensioner_is_4ps">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->is_4ps->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->is_4ps->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->is_4ps->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->abandoned->Visible) { // abandoned ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->abandoned) == "") { ?>
		<td><div id="elh_tbl_pensioner_abandoned" class="tbl_pensioner_abandoned"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->abandoned->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->abandoned) ?>',1);"><div id="elh_tbl_pensioner_abandoned" class="tbl_pensioner_abandoned">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->abandoned->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->abandoned->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->abandoned->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->Createdby->Visible) { // Createdby ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->Createdby) == "") { ?>
		<td><div id="elh_tbl_pensioner_Createdby" class="tbl_pensioner_Createdby"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->Createdby->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->Createdby) ?>',1);"><div id="elh_tbl_pensioner_Createdby" class="tbl_pensioner_Createdby">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->Createdby->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->Createdby->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->Createdby->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->CreatedDate->Visible) { // CreatedDate ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->CreatedDate) == "") { ?>
		<td><div id="elh_tbl_pensioner_CreatedDate" class="tbl_pensioner_CreatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->CreatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->CreatedDate) ?>',1);"><div id="elh_tbl_pensioner_CreatedDate" class="tbl_pensioner_CreatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->CreatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->CreatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->CreatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->UpdatedBy->Visible) { // UpdatedBy ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->UpdatedBy) == "") { ?>
		<td><div id="elh_tbl_pensioner_UpdatedBy" class="tbl_pensioner_UpdatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->UpdatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->UpdatedBy) ?>',1);"><div id="elh_tbl_pensioner_UpdatedBy" class="tbl_pensioner_UpdatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->UpdatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->UpdatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->UpdatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->UpdatedDate->Visible) { // UpdatedDate ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->UpdatedDate) == "") { ?>
		<td><div id="elh_tbl_pensioner_UpdatedDate" class="tbl_pensioner_UpdatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->UpdatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->UpdatedDate) ?>',1);"><div id="elh_tbl_pensioner_UpdatedDate" class="tbl_pensioner_UpdatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->UpdatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->UpdatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->UpdatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->UpdateRemarks->Visible) { // UpdateRemarks ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->UpdateRemarks) == "") { ?>
		<td><div id="elh_tbl_pensioner_UpdateRemarks" class="tbl_pensioner_UpdateRemarks"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->UpdateRemarks->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->UpdateRemarks) ?>',1);"><div id="elh_tbl_pensioner_UpdateRemarks" class="tbl_pensioner_UpdateRemarks">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->UpdateRemarks->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->UpdateRemarks->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->UpdateRemarks->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pensioner->codeGen->Visible) { // codeGen ?>
	<?php if ($tbl_pensioner->SortUrl($tbl_pensioner->codeGen) == "") { ?>
		<td><div id="elh_tbl_pensioner_codeGen" class="tbl_pensioner_codeGen"><div class="ewTableHeaderCaption"><?php echo $tbl_pensioner->codeGen->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pensioner->SortUrl($tbl_pensioner->codeGen) ?>',1);"><div id="elh_tbl_pensioner_codeGen" class="tbl_pensioner_codeGen">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pensioner->codeGen->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pensioner->codeGen->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pensioner->codeGen->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } */ ?>
<?php

// Render list options (header, right)
$tbl_pensioner_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($tbl_pensioner->ExportAll && $tbl_pensioner->Export <> "") {
	$tbl_pensioner_list->StopRec = $tbl_pensioner_list->TotalRecs;
} else {

	// Set the last record to display
	if ($tbl_pensioner_list->TotalRecs > $tbl_pensioner_list->StartRec + $tbl_pensioner_list->DisplayRecs - 1)
		$tbl_pensioner_list->StopRec = $tbl_pensioner_list->StartRec + $tbl_pensioner_list->DisplayRecs - 1;
	else
		$tbl_pensioner_list->StopRec = $tbl_pensioner_list->TotalRecs;
}
$tbl_pensioner_list->RecCnt = $tbl_pensioner_list->StartRec - 1;
if ($tbl_pensioner_list->Recordset && !$tbl_pensioner_list->Recordset->EOF) {
	$tbl_pensioner_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $tbl_pensioner_list->StartRec > 1)
		$tbl_pensioner_list->Recordset->Move($tbl_pensioner_list->StartRec - 1);
} elseif (!$tbl_pensioner->AllowAddDeleteRow && $tbl_pensioner_list->StopRec == 0) {
	$tbl_pensioner_list->StopRec = $tbl_pensioner->GridAddRowCount;
}

// Initialize aggregate
$tbl_pensioner->RowType = EW_ROWTYPE_AGGREGATEINIT;
$tbl_pensioner->ResetAttrs();
$tbl_pensioner_list->RenderRow();
while ($tbl_pensioner_list->RecCnt < $tbl_pensioner_list->StopRec) {
	$tbl_pensioner_list->RecCnt++;
	if (intval($tbl_pensioner_list->RecCnt) >= intval($tbl_pensioner_list->StartRec)) {
		$tbl_pensioner_list->RowCnt++;

		// Set up key count
		$tbl_pensioner_list->KeyCount = $tbl_pensioner_list->RowIndex;

		// Init row class and style
		$tbl_pensioner->ResetAttrs();
		$tbl_pensioner->CssClass = "";
		if ($tbl_pensioner->CurrentAction == "gridadd") {
		} else {
			$tbl_pensioner_list->LoadRowValues($tbl_pensioner_list->Recordset); // Load row values
		}
		$tbl_pensioner->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$tbl_pensioner->RowAttrs = array_merge($tbl_pensioner->RowAttrs, array('data-rowindex'=>$tbl_pensioner_list->RowCnt, 'id'=>'r' . $tbl_pensioner_list->RowCnt . '_tbl_pensioner', 'data-rowtype'=>$tbl_pensioner->RowType));

		// Render row
		$tbl_pensioner_list->RenderRow();

		// Render list options
		$tbl_pensioner_list->RenderListOptions();
?>
	<tr<?php echo $tbl_pensioner->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_pensioner_list->ListOptions->Render("body", "left", $tbl_pensioner_list->RowCnt);
?>
	<?php /* if ($tbl_pensioner->SeniorID->Visible) { // SeniorID ?>
		<td<?php echo $tbl_pensioner->SeniorID->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->SeniorID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->SeniorID->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } */ ?>
	<?php if ($tbl_pensioner->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_pensioner->PensionerID->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->PensionerID->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php /* if ($tbl_pensioner->InclusionDate->Visible) { // InclusionDate ?>
		<td<?php echo $tbl_pensioner->InclusionDate->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->InclusionDate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->InclusionDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->hh_id->Visible) { // hh_id ?>
		<td<?php echo $tbl_pensioner->hh_id->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->hh_id->ViewAttributes() ?>>
<?php echo $tbl_pensioner->hh_id->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->osca_ID->Visible) { // osca_ID ?>
		<td<?php echo $tbl_pensioner->osca_ID->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->osca_ID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->osca_ID->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->PlaceIssued->Visible) { // PlaceIssued ?>
		<td<?php echo $tbl_pensioner->PlaceIssued->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->PlaceIssued->ViewAttributes() ?>>
<?php echo $tbl_pensioner->PlaceIssued->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->DateIssued->Visible) { // DateIssued ?>
		<td<?php echo $tbl_pensioner->DateIssued->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->DateIssued->ViewAttributes() ?>>
<?php echo $tbl_pensioner->DateIssued->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } */ ?>
	<?php if ($tbl_pensioner->lastname->Visible) { // lastname ?>
		<td<?php echo $tbl_pensioner->lastname->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->lastname->ViewAttributes() ?>>
<?php echo $tbl_pensioner->lastname->ListViewValue() ?></span>
			<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->firstname->Visible) { // firstname ?>
		<td<?php echo $tbl_pensioner->firstname->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->firstname->ViewAttributes() ?>>
<?php echo $tbl_pensioner->firstname->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->middlename->Visible) { // middlename ?>
		<td<?php echo $tbl_pensioner->middlename->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->middlename->ViewAttributes() ?>>
<?php echo $tbl_pensioner->middlename->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->extname->Visible) { // extname ?>
		<td<?php echo $tbl_pensioner->extname->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->extname->ViewAttributes() ?>>
<?php echo $tbl_pensioner->extname->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php /* if ($tbl_pensioner->Birthdate->Visible) { // Birthdate ?>
		<td<?php echo $tbl_pensioner->Birthdate->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->Birthdate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->Birthdate->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->sex->Visible) { // sex ?>
		<td<?php echo $tbl_pensioner->sex->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->sex->ViewAttributes() ?>>
<?php echo $tbl_pensioner->sex->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->MaritalID->Visible) { // MaritalID ?>
		<td<?php echo $tbl_pensioner->MaritalID->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->MaritalID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->MaritalID->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->affliationID->Visible) { // affliationID ?>
		<td<?php echo $tbl_pensioner->affliationID->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->affliationID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->affliationID->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } */ ?>
	<?php if ($tbl_pensioner->psgc_region->Visible) { // psgc_region ?>
		<td<?php echo $tbl_pensioner->psgc_region->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->psgc_region->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_region->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->psgc_province->Visible) { // psgc_province ?>
		<td<?php echo $tbl_pensioner->psgc_province->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->psgc_province->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_province->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->psgc_municipality->Visible) { // psgc_municipality ?>
		<td<?php echo $tbl_pensioner->psgc_municipality->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->psgc_municipality->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_municipality->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->psgc_brgy->Visible) { // psgc_brgy ?>
		<td<?php echo $tbl_pensioner->psgc_brgy->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->psgc_brgy->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_brgy->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php /* if ($tbl_pensioner->given_add->Visible) { // given_add ?>
		<td<?php echo $tbl_pensioner->given_add->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->given_add->ViewAttributes() ?>>
<?php echo $tbl_pensioner->given_add->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } */ ?>
	<?php if ($tbl_pensioner->Status->Visible) { // Status ?>
		<td<?php echo $tbl_pensioner->Status->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->Status->ViewAttributes() ?>>
<?php echo $tbl_pensioner->Status->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php /* if ($tbl_pensioner->paymentmodeID->Visible) { // paymentmodeID ?>
		<td<?php echo $tbl_pensioner->paymentmodeID->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->paymentmodeID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->paymentmodeID->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->approved->Visible) { // approved ?>
		<td<?php echo $tbl_pensioner->approved->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->approved->ViewAttributes() ?>>
<?php echo $tbl_pensioner->approved->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->approvedby->Visible) { // approvedby ?>
		<td<?php echo $tbl_pensioner->approvedby->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->approvedby->ViewAttributes() ?>>
<?php echo $tbl_pensioner->approvedby->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->DateApproved->Visible) { // DateApproved ?>
		<td<?php echo $tbl_pensioner->DateApproved->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->DateApproved->ViewAttributes() ?>>
<?php echo $tbl_pensioner->DateApproved->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->ArrangementID->Visible) { // ArrangementID ?>
		<td<?php echo $tbl_pensioner->ArrangementID->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->ArrangementID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->ArrangementID->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->is_4ps->Visible) { // is_4ps ?>
		<td<?php echo $tbl_pensioner->is_4ps->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->is_4ps->ViewAttributes() ?>>
<?php echo $tbl_pensioner->is_4ps->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->abandoned->Visible) { // abandoned ?>
		<td<?php echo $tbl_pensioner->abandoned->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->abandoned->ViewAttributes() ?>>
<?php echo $tbl_pensioner->abandoned->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->Createdby->Visible) { // Createdby ?>
		<td<?php echo $tbl_pensioner->Createdby->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->Createdby->ViewAttributes() ?>>
<?php echo $tbl_pensioner->Createdby->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_pensioner->CreatedDate->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->CreatedDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_pensioner->UpdatedBy->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_pensioner->UpdatedBy->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_pensioner->UpdatedDate->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->UpdatedDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->UpdateRemarks->Visible) { // UpdateRemarks ?>
		<td<?php echo $tbl_pensioner->UpdateRemarks->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->UpdateRemarks->ViewAttributes() ?>>
<?php echo $tbl_pensioner->UpdateRemarks->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pensioner->codeGen->Visible) { // codeGen ?>
		<td<?php echo $tbl_pensioner->codeGen->CellAttributes() ?>>
<span<?php echo $tbl_pensioner->codeGen->ViewAttributes() ?>>
<?php echo $tbl_pensioner->codeGen->ListViewValue() ?></span>
<a id="<?php echo $tbl_pensioner_list->PageObjName . "_row_" . $tbl_pensioner_list->RowCnt ?>"></a></td>
	<?php } */ ?>
<?php

// Render list options (body, right)
$tbl_pensioner_list->ListOptions->Render("body", "right", $tbl_pensioner_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($tbl_pensioner->CurrentAction <> "gridadd")
		$tbl_pensioner_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($tbl_pensioner->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($tbl_pensioner_list->Recordset)
	$tbl_pensioner_list->Recordset->Close();
?>
<?php if ($tbl_pensioner_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($tbl_pensioner->CurrentAction <> "gridadd" && $tbl_pensioner->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_pensioner_list->Pager)) $tbl_pensioner_list->Pager = new cNumericPager($tbl_pensioner_list->StartRec, $tbl_pensioner_list->DisplayRecs, $tbl_pensioner_list->TotalRecs, $tbl_pensioner_list->RecRange) ?>
<?php if ($tbl_pensioner_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_pensioner_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pensioner_list->PageUrl() ?>start=<?php echo $tbl_pensioner_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_pensioner_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pensioner_list->PageUrl() ?>start=<?php echo $tbl_pensioner_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_pensioner_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_pensioner_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_pensioner_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pensioner_list->PageUrl() ?>start=<?php echo $tbl_pensioner_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_pensioner_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pensioner_list->PageUrl() ?>start=<?php echo $tbl_pensioner_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_pensioner_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_pensioner_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_pensioner_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_pensioner_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_pensioner_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_pensioner_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_pensioner">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_pensioner_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_pensioner_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_pensioner_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_pensioner->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_pensioner_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
ftbl_pensionerlistsrch.Init();
ftbl_pensionerlist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_pensioner_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_pensioner_list->Page_Terminate();
?>
