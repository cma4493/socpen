<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "lib_regionsinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$lib_regions_view = NULL; // Initialize page object first

class clib_regions_view extends clib_regions {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'lib_regions';

	// Page object name
	var $PageObjName = 'lib_regions_view';

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

		// Table object (lib_regions)
		if (!isset($GLOBALS["lib_regions"])) {
			$GLOBALS["lib_regions"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lib_regions"];
		}
		$KeyUrl = "";
		if (@$_GET["region_code"] <> "") {
			$this->RecKey["region_code"] = $_GET["region_code"];
			$KeyUrl .= "&region_code=" . urlencode($this->RecKey["region_code"]);
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
			define("EW_TABLE_NAME", 'lib_regions', TRUE);

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
			$this->Page_Terminate("lib_regionslist.php");
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
		$this->region_code->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			if (@$_GET["region_code"] <> "") {
				$this->region_code->setQueryStringValue($_GET["region_code"]);
				$this->RecKey["region_code"] = $this->region_code->QueryStringValue;
			} else {
				$sReturnUrl = "lib_regionslist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "lib_regionslist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "lib_regionslist.php"; // Not page request, return to list
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
		$this->region_code->setDbValue($rs->fields('region_code'));
		$this->region_name->setDbValue($rs->fields('region_name'));
		$this->region_nick->setDbValue($rs->fields('region_nick'));
		$this->region_director->setDbValue($rs->fields('region_director'));
		$this->philpost_director->setDbValue($rs->fields('philpost_director'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->region_code->DbValue = $row['region_code'];
		$this->region_name->DbValue = $row['region_name'];
		$this->region_nick->DbValue = $row['region_nick'];
		$this->region_director->DbValue = $row['region_director'];
		$this->philpost_director->DbValue = $row['philpost_director'];
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
		// region_code
		// region_name
		// region_nick
		// region_director
		// philpost_director

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// region_code
			$this->region_code->ViewValue = $this->region_code->CurrentValue;
			$this->region_code->ViewCustomAttributes = "";

			// region_name
			$this->region_name->ViewValue = $this->region_name->CurrentValue;
			$this->region_name->ViewCustomAttributes = "";

			// region_nick
			$this->region_nick->ViewValue = $this->region_nick->CurrentValue;
			$this->region_nick->ViewCustomAttributes = "";

			// region_director
			$this->region_director->ViewValue = $this->region_director->CurrentValue;
			$this->region_director->ViewCustomAttributes = "";

			// philpost_director
			$this->philpost_director->ViewValue = $this->philpost_director->CurrentValue;
			$this->philpost_director->ViewCustomAttributes = "";

			// region_code
			$this->region_code->LinkCustomAttributes = "";
			$this->region_code->HrefValue = "";
			$this->region_code->TooltipValue = "";

			// region_name
			$this->region_name->LinkCustomAttributes = "";
			$this->region_name->HrefValue = "";
			$this->region_name->TooltipValue = "";

			// region_nick
			$this->region_nick->LinkCustomAttributes = "";
			$this->region_nick->HrefValue = "";
			$this->region_nick->TooltipValue = "";

			// region_director
			$this->region_director->LinkCustomAttributes = "";
			$this->region_director->HrefValue = "";
			$this->region_director->TooltipValue = "";

			// philpost_director
			$this->philpost_director->LinkCustomAttributes = "";
			$this->philpost_director->HrefValue = "";
			$this->philpost_director->TooltipValue = "";
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "lib_regionslist.php", $this->TableVar);
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
if (!isset($lib_regions_view)) $lib_regions_view = new clib_regions_view();

// Page init
$lib_regions_view->Page_Init();

// Page main
$lib_regions_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$lib_regions_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lib_regions_view = new ew_Page("lib_regions_view");
lib_regions_view.PageID = "view"; // Page ID
var EW_PAGE_ID = lib_regions_view.PageID; // For backward compatibility

// Form object
var flib_regionsview = new ew_Form("flib_regionsview");

// Form_CustomValidate event
flib_regionsview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flib_regionsview.ValidateRequired = true;
<?php } else { ?>
flib_regionsview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<div class="ewViewExportOptions">
<?php $lib_regions_view->ExportOptions->Render("body") ?>
<?php if (!$lib_regions_view->ExportOptions->UseDropDownButton) { ?>
</div>
<div class="ewViewOtherOptions">
<?php } ?>
<?php
	foreach ($lib_regions_view->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php $lib_regions_view->ShowPageHeader(); ?>
<?php
$lib_regions_view->ShowMessage();
?>
<form name="flib_regionsview" id="flib_regionsview" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="lib_regions">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_lib_regionsview" class="table table-bordered table-striped">
<?php if ($lib_regions->region_code->Visible) { // region_code ?>
	<tr id="r_region_code">
		<td><span id="elh_lib_regions_region_code"><?php echo $lib_regions->region_code->FldCaption() ?></span></td>
		<td<?php echo $lib_regions->region_code->CellAttributes() ?>>
<span id="el_lib_regions_region_code" class="control-group">
<span<?php echo $lib_regions->region_code->ViewAttributes() ?>>
<?php echo $lib_regions->region_code->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($lib_regions->region_name->Visible) { // region_name ?>
	<tr id="r_region_name">
		<td><span id="elh_lib_regions_region_name"><?php echo $lib_regions->region_name->FldCaption() ?></span></td>
		<td<?php echo $lib_regions->region_name->CellAttributes() ?>>
<span id="el_lib_regions_region_name" class="control-group">
<span<?php echo $lib_regions->region_name->ViewAttributes() ?>>
<?php echo $lib_regions->region_name->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($lib_regions->region_nick->Visible) { // region_nick ?>
	<tr id="r_region_nick">
		<td><span id="elh_lib_regions_region_nick"><?php echo $lib_regions->region_nick->FldCaption() ?></span></td>
		<td<?php echo $lib_regions->region_nick->CellAttributes() ?>>
<span id="el_lib_regions_region_nick" class="control-group">
<span<?php echo $lib_regions->region_nick->ViewAttributes() ?>>
<?php echo $lib_regions->region_nick->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($lib_regions->region_director->Visible) { // region_director ?>
	<tr id="r_region_director">
		<td><span id="elh_lib_regions_region_director"><?php echo $lib_regions->region_director->FldCaption() ?></span></td>
		<td<?php echo $lib_regions->region_director->CellAttributes() ?>>
<span id="el_lib_regions_region_director" class="control-group">
<span<?php echo $lib_regions->region_director->ViewAttributes() ?>>
<?php echo $lib_regions->region_director->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($lib_regions->philpost_director->Visible) { // philpost_director ?>
	<tr id="r_philpost_director">
		<td><span id="elh_lib_regions_philpost_director"><?php echo $lib_regions->philpost_director->FldCaption() ?></span></td>
		<td<?php echo $lib_regions->philpost_director->CellAttributes() ?>>
<span id="el_lib_regions_philpost_director" class="control-group">
<span<?php echo $lib_regions->philpost_director->ViewAttributes() ?>>
<?php echo $lib_regions->philpost_director->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
</form>
<script type="text/javascript">
flib_regionsview.Init();
</script>
<?php
$lib_regions_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lib_regions_view->Page_Terminate();
?>