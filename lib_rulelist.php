<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "lib_ruleinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$lib_rule_list = NULL; // Initialize page object first

class clib_rule_list extends clib_rule {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'lib_rule';

	// Page object name
	var $PageObjName = 'lib_rule_list';

	// Grid form hidden field names
	var $FormName = 'flib_rulelist';
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

		// Table object (lib_rule)
		if (!isset($GLOBALS["lib_rule"])) {
			$GLOBALS["lib_rule"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lib_rule"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "lib_ruleadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "lib_ruledelete.php";
		$this->MultiUpdateUrl = "lib_ruleupdate.php";

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lib_rule', TRUE);

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
		$this->ruleID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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

			// Set up sorting order
			$this->SetUpSortOrder();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

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
			$this->ruleID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->ruleID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->ruleID); // ruleID
			$this->UpdateSort($this->rule_age); // rule_age
			$this->UpdateSort($this->rule_affiliation); // rule_affiliation
			$this->UpdateSort($this->rule_active); // rule_active
			$this->UpdateSort($this->created_by); // created_by
			$this->UpdateSort($this->date_created); // date_created
			$this->UpdateSort($this->modified_by); // modified_by
			$this->UpdateSort($this->date_modified); // date_modified
			$this->UpdateSort($this->DELETED); // DELETED
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

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->ruleID->setSort("");
				$this->rule_age->setSort("");
				$this->rule_affiliation->setSort("");
				$this->rule_active->setSort("");
				$this->created_by->setSort("");
				$this->date_created->setSort("");
				$this->modified_by->setSort("");
				$this->date_modified->setSort("");
				$this->DELETED->setSort("");
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
		$item->Visible = $Security->CanAdd();
		$item->OnLeft = TRUE;

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

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->ruleID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
		$option = $options["action"];

		// Add multi delete
		$item = &$option->Add("multidelete");
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.flib_rulelist, '" . $this->MultiDeleteUrl . "', ewLanguage.Phrase('DeleteMultiConfirmMsg'));return false;\">" . $Language->Phrase("DeleteSelectedLink") . "</a>";
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
				$item->Body = "<a class=\"btn btn-warning btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.flib_rulelist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->ruleID->DbValue = $row['ruleID'];
		$this->rule_age->DbValue = $row['rule_age'];
		$this->rule_affiliation->DbValue = $row['rule_affiliation'];
		$this->rule_active->DbValue = $row['rule_active'];
		$this->created_by->DbValue = $row['created_by'];
		$this->date_created->DbValue = $row['date_created'];
		$this->modified_by->DbValue = $row['modified_by'];
		$this->date_modified->DbValue = $row['date_modified'];
		$this->DELETED->DbValue = $row['DELETED'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("ruleID")) <> "")
			$this->ruleID->CurrentValue = $this->getKey("ruleID"); // ruleID
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
		// ruleID
		// rule_age
		// rule_affiliation
		// rule_active
		// created_by
		// date_created
		// modified_by
		// date_modified
		// DELETED

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

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
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", $url, $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'lib_rule';
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
if (!isset($lib_rule_list)) $lib_rule_list = new clib_rule_list();

// Page init
$lib_rule_list->Page_Init();

// Page main
$lib_rule_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$lib_rule_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lib_rule_list = new ew_Page("lib_rule_list");
lib_rule_list.PageID = "list"; // Page ID
var EW_PAGE_ID = lib_rule_list.PageID; // For backward compatibility

// Form object
var flib_rulelist = new ew_Form("flib_rulelist");
flib_rulelist.FormKeyCountName = '<?php echo $lib_rule_list->FormKeyCountName ?>';

// Form_CustomValidate event
flib_rulelist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flib_rulelist.ValidateRequired = true;
<?php } else { ?>
flib_rulelist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php if ($lib_rule_list->ExportOptions->Visible()) { ?>
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
		$lib_rule_list->TotalRecs = $lib_rule->SelectRecordCount();
	} else {
		if ($lib_rule_list->Recordset = $lib_rule_list->LoadRecordset())
			$lib_rule_list->TotalRecs = $lib_rule_list->Recordset->RecordCount();
	}
	$lib_rule_list->StartRec = 1;
	if ($lib_rule_list->DisplayRecs <= 0 || ($lib_rule->Export <> "" && $lib_rule->ExportAll)) // Display all records
		$lib_rule_list->DisplayRecs = $lib_rule_list->TotalRecs;
	if (!($lib_rule->Export <> "" && $lib_rule->ExportAll))
		$lib_rule_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$lib_rule_list->Recordset = $lib_rule_list->LoadRecordset($lib_rule_list->StartRec-1, $lib_rule_list->DisplayRecs);
$lib_rule_list->RenderOtherOptions();
?>
<?php $lib_rule_list->ShowPageHeader(); ?>
<?php
$lib_rule_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($lib_rule->CurrentAction <> "gridadd" && $lib_rule->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($lib_rule_list->Pager)) $lib_rule_list->Pager = new cNumericPager($lib_rule_list->StartRec, $lib_rule_list->DisplayRecs, $lib_rule_list->TotalRecs, $lib_rule_list->RecRange) ?>
<?php if ($lib_rule_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($lib_rule_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $lib_rule_list->PageUrl() ?>start=<?php echo $lib_rule_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($lib_rule_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $lib_rule_list->PageUrl() ?>start=<?php echo $lib_rule_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($lib_rule_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $lib_rule_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($lib_rule_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $lib_rule_list->PageUrl() ?>start=<?php echo $lib_rule_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($lib_rule_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $lib_rule_list->PageUrl() ?>start=<?php echo $lib_rule_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($lib_rule_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $lib_rule_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $lib_rule_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $lib_rule_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($lib_rule_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($lib_rule_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="lib_rule">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($lib_rule_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($lib_rule_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($lib_rule_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($lib_rule->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($lib_rule_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="flib_rulelist" id="flib_rulelist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="lib_rule">
<div id="gmp_lib_rule" class="ewGridMiddlePanel">
<?php if ($lib_rule_list->TotalRecs > 0) { ?>
<table id="tbl_lib_rulelist" class="ewTable ewTableSeparate">
<?php echo $lib_rule->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$lib_rule_list->RenderListOptions();

// Render list options (header, left)
$lib_rule_list->ListOptions->Render("header", "left");
?>
<?php if ($lib_rule->ruleID->Visible) { // ruleID ?>
	<?php if ($lib_rule->SortUrl($lib_rule->ruleID) == "") { ?>
		<td><div id="elh_lib_rule_ruleID" class="lib_rule_ruleID"><div class="ewTableHeaderCaption"><?php echo $lib_rule->ruleID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $lib_rule->SortUrl($lib_rule->ruleID) ?>',1);"><div id="elh_lib_rule_ruleID" class="lib_rule_ruleID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $lib_rule->ruleID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($lib_rule->ruleID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($lib_rule->ruleID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($lib_rule->rule_age->Visible) { // rule_age ?>
	<?php if ($lib_rule->SortUrl($lib_rule->rule_age) == "") { ?>
		<td><div id="elh_lib_rule_rule_age" class="lib_rule_rule_age"><div class="ewTableHeaderCaption"><?php echo $lib_rule->rule_age->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $lib_rule->SortUrl($lib_rule->rule_age) ?>',1);"><div id="elh_lib_rule_rule_age" class="lib_rule_rule_age">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $lib_rule->rule_age->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($lib_rule->rule_age->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($lib_rule->rule_age->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($lib_rule->rule_affiliation->Visible) { // rule_affiliation ?>
	<?php if ($lib_rule->SortUrl($lib_rule->rule_affiliation) == "") { ?>
		<td><div id="elh_lib_rule_rule_affiliation" class="lib_rule_rule_affiliation"><div class="ewTableHeaderCaption"><?php echo $lib_rule->rule_affiliation->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $lib_rule->SortUrl($lib_rule->rule_affiliation) ?>',1);"><div id="elh_lib_rule_rule_affiliation" class="lib_rule_rule_affiliation">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $lib_rule->rule_affiliation->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($lib_rule->rule_affiliation->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($lib_rule->rule_affiliation->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($lib_rule->rule_active->Visible) { // rule_active ?>
	<?php if ($lib_rule->SortUrl($lib_rule->rule_active) == "") { ?>
		<td><div id="elh_lib_rule_rule_active" class="lib_rule_rule_active"><div class="ewTableHeaderCaption"><?php echo $lib_rule->rule_active->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $lib_rule->SortUrl($lib_rule->rule_active) ?>',1);"><div id="elh_lib_rule_rule_active" class="lib_rule_rule_active">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $lib_rule->rule_active->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($lib_rule->rule_active->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($lib_rule->rule_active->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($lib_rule->created_by->Visible) { // created_by ?>
	<?php if ($lib_rule->SortUrl($lib_rule->created_by) == "") { ?>
		<td><div id="elh_lib_rule_created_by" class="lib_rule_created_by"><div class="ewTableHeaderCaption"><?php echo $lib_rule->created_by->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $lib_rule->SortUrl($lib_rule->created_by) ?>',1);"><div id="elh_lib_rule_created_by" class="lib_rule_created_by">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $lib_rule->created_by->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($lib_rule->created_by->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($lib_rule->created_by->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($lib_rule->date_created->Visible) { // date_created ?>
	<?php if ($lib_rule->SortUrl($lib_rule->date_created) == "") { ?>
		<td><div id="elh_lib_rule_date_created" class="lib_rule_date_created"><div class="ewTableHeaderCaption"><?php echo $lib_rule->date_created->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $lib_rule->SortUrl($lib_rule->date_created) ?>',1);"><div id="elh_lib_rule_date_created" class="lib_rule_date_created">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $lib_rule->date_created->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($lib_rule->date_created->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($lib_rule->date_created->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($lib_rule->modified_by->Visible) { // modified_by ?>
	<?php if ($lib_rule->SortUrl($lib_rule->modified_by) == "") { ?>
		<td><div id="elh_lib_rule_modified_by" class="lib_rule_modified_by"><div class="ewTableHeaderCaption"><?php echo $lib_rule->modified_by->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $lib_rule->SortUrl($lib_rule->modified_by) ?>',1);"><div id="elh_lib_rule_modified_by" class="lib_rule_modified_by">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $lib_rule->modified_by->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($lib_rule->modified_by->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($lib_rule->modified_by->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($lib_rule->date_modified->Visible) { // date_modified ?>
	<?php if ($lib_rule->SortUrl($lib_rule->date_modified) == "") { ?>
		<td><div id="elh_lib_rule_date_modified" class="lib_rule_date_modified"><div class="ewTableHeaderCaption"><?php echo $lib_rule->date_modified->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $lib_rule->SortUrl($lib_rule->date_modified) ?>',1);"><div id="elh_lib_rule_date_modified" class="lib_rule_date_modified">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $lib_rule->date_modified->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($lib_rule->date_modified->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($lib_rule->date_modified->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($lib_rule->DELETED->Visible) { // DELETED ?>
	<?php if ($lib_rule->SortUrl($lib_rule->DELETED) == "") { ?>
		<td><div id="elh_lib_rule_DELETED" class="lib_rule_DELETED"><div class="ewTableHeaderCaption"><?php echo $lib_rule->DELETED->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $lib_rule->SortUrl($lib_rule->DELETED) ?>',1);"><div id="elh_lib_rule_DELETED" class="lib_rule_DELETED">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $lib_rule->DELETED->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($lib_rule->DELETED->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($lib_rule->DELETED->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$lib_rule_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($lib_rule->ExportAll && $lib_rule->Export <> "") {
	$lib_rule_list->StopRec = $lib_rule_list->TotalRecs;
} else {

	// Set the last record to display
	if ($lib_rule_list->TotalRecs > $lib_rule_list->StartRec + $lib_rule_list->DisplayRecs - 1)
		$lib_rule_list->StopRec = $lib_rule_list->StartRec + $lib_rule_list->DisplayRecs - 1;
	else
		$lib_rule_list->StopRec = $lib_rule_list->TotalRecs;
}
$lib_rule_list->RecCnt = $lib_rule_list->StartRec - 1;
if ($lib_rule_list->Recordset && !$lib_rule_list->Recordset->EOF) {
	$lib_rule_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $lib_rule_list->StartRec > 1)
		$lib_rule_list->Recordset->Move($lib_rule_list->StartRec - 1);
} elseif (!$lib_rule->AllowAddDeleteRow && $lib_rule_list->StopRec == 0) {
	$lib_rule_list->StopRec = $lib_rule->GridAddRowCount;
}

// Initialize aggregate
$lib_rule->RowType = EW_ROWTYPE_AGGREGATEINIT;
$lib_rule->ResetAttrs();
$lib_rule_list->RenderRow();
while ($lib_rule_list->RecCnt < $lib_rule_list->StopRec) {
	$lib_rule_list->RecCnt++;
	if (intval($lib_rule_list->RecCnt) >= intval($lib_rule_list->StartRec)) {
		$lib_rule_list->RowCnt++;

		// Set up key count
		$lib_rule_list->KeyCount = $lib_rule_list->RowIndex;

		// Init row class and style
		$lib_rule->ResetAttrs();
		$lib_rule->CssClass = "";
		if ($lib_rule->CurrentAction == "gridadd") {
		} else {
			$lib_rule_list->LoadRowValues($lib_rule_list->Recordset); // Load row values
		}
		$lib_rule->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$lib_rule->RowAttrs = array_merge($lib_rule->RowAttrs, array('data-rowindex'=>$lib_rule_list->RowCnt, 'id'=>'r' . $lib_rule_list->RowCnt . '_lib_rule', 'data-rowtype'=>$lib_rule->RowType));

		// Render row
		$lib_rule_list->RenderRow();

		// Render list options
		$lib_rule_list->RenderListOptions();
?>
	<tr<?php echo $lib_rule->RowAttributes() ?>>
<?php

// Render list options (body, left)
$lib_rule_list->ListOptions->Render("body", "left", $lib_rule_list->RowCnt);
?>
	<?php if ($lib_rule->ruleID->Visible) { // ruleID ?>
		<td<?php echo $lib_rule->ruleID->CellAttributes() ?>>
<span<?php echo $lib_rule->ruleID->ViewAttributes() ?>>
<?php echo $lib_rule->ruleID->ListViewValue() ?></span>
<a id="<?php echo $lib_rule_list->PageObjName . "_row_" . $lib_rule_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lib_rule->rule_age->Visible) { // rule_age ?>
		<td<?php echo $lib_rule->rule_age->CellAttributes() ?>>
<span<?php echo $lib_rule->rule_age->ViewAttributes() ?>>
<?php echo $lib_rule->rule_age->ListViewValue() ?></span>
<a id="<?php echo $lib_rule_list->PageObjName . "_row_" . $lib_rule_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lib_rule->rule_affiliation->Visible) { // rule_affiliation ?>
		<td<?php echo $lib_rule->rule_affiliation->CellAttributes() ?>>
<span<?php echo $lib_rule->rule_affiliation->ViewAttributes() ?>>
<?php echo $lib_rule->rule_affiliation->ListViewValue() ?></span>
<a id="<?php echo $lib_rule_list->PageObjName . "_row_" . $lib_rule_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lib_rule->rule_active->Visible) { // rule_active ?>
		<td<?php echo $lib_rule->rule_active->CellAttributes() ?>>
<span<?php echo $lib_rule->rule_active->ViewAttributes() ?>>
<?php echo $lib_rule->rule_active->ListViewValue() ?></span>
<a id="<?php echo $lib_rule_list->PageObjName . "_row_" . $lib_rule_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lib_rule->created_by->Visible) { // created_by ?>
		<td<?php echo $lib_rule->created_by->CellAttributes() ?>>
<span<?php echo $lib_rule->created_by->ViewAttributes() ?>>
<?php echo $lib_rule->created_by->ListViewValue() ?></span>
<a id="<?php echo $lib_rule_list->PageObjName . "_row_" . $lib_rule_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lib_rule->date_created->Visible) { // date_created ?>
		<td<?php echo $lib_rule->date_created->CellAttributes() ?>>
<span<?php echo $lib_rule->date_created->ViewAttributes() ?>>
<?php echo $lib_rule->date_created->ListViewValue() ?></span>
<a id="<?php echo $lib_rule_list->PageObjName . "_row_" . $lib_rule_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lib_rule->modified_by->Visible) { // modified_by ?>
		<td<?php echo $lib_rule->modified_by->CellAttributes() ?>>
<span<?php echo $lib_rule->modified_by->ViewAttributes() ?>>
<?php echo $lib_rule->modified_by->ListViewValue() ?></span>
<a id="<?php echo $lib_rule_list->PageObjName . "_row_" . $lib_rule_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lib_rule->date_modified->Visible) { // date_modified ?>
		<td<?php echo $lib_rule->date_modified->CellAttributes() ?>>
<span<?php echo $lib_rule->date_modified->ViewAttributes() ?>>
<?php echo $lib_rule->date_modified->ListViewValue() ?></span>
<a id="<?php echo $lib_rule_list->PageObjName . "_row_" . $lib_rule_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lib_rule->DELETED->Visible) { // DELETED ?>
		<td<?php echo $lib_rule->DELETED->CellAttributes() ?>>
<span<?php echo $lib_rule->DELETED->ViewAttributes() ?>>
<?php echo $lib_rule->DELETED->ListViewValue() ?></span>
<a id="<?php echo $lib_rule_list->PageObjName . "_row_" . $lib_rule_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$lib_rule_list->ListOptions->Render("body", "right", $lib_rule_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($lib_rule->CurrentAction <> "gridadd")
		$lib_rule_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($lib_rule->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($lib_rule_list->Recordset)
	$lib_rule_list->Recordset->Close();
?>
<?php if ($lib_rule_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($lib_rule->CurrentAction <> "gridadd" && $lib_rule->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($lib_rule_list->Pager)) $lib_rule_list->Pager = new cNumericPager($lib_rule_list->StartRec, $lib_rule_list->DisplayRecs, $lib_rule_list->TotalRecs, $lib_rule_list->RecRange) ?>
<?php if ($lib_rule_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($lib_rule_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $lib_rule_list->PageUrl() ?>start=<?php echo $lib_rule_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($lib_rule_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $lib_rule_list->PageUrl() ?>start=<?php echo $lib_rule_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($lib_rule_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $lib_rule_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($lib_rule_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $lib_rule_list->PageUrl() ?>start=<?php echo $lib_rule_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($lib_rule_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $lib_rule_list->PageUrl() ?>start=<?php echo $lib_rule_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($lib_rule_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $lib_rule_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $lib_rule_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $lib_rule_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($lib_rule_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($lib_rule_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="lib_rule">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($lib_rule_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($lib_rule_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($lib_rule_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($lib_rule->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($lib_rule_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
flib_rulelist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$lib_rule_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lib_rule_list->Page_Terminate();
?>
