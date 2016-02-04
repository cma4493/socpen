<?php include_once "tbl_supportinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php

//
// Page class
//

$tbl_support_grid = NULL; // Initialize page object first

class ctbl_support_grid extends ctbl_support {

	// Page ID
	var $PageID = 'grid';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_support';

	// Page object name
	var $PageObjName = 'tbl_support_grid';

	// Grid form hidden field names
	var $FormName = 'ftbl_supportgrid';
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
	var $AuditTrailOnAdd = TRUE;
	var $AuditTrailOnEdit = TRUE;
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
		$this->FormActionName .= '_' . $this->FormName;
		$this->FormKeyName .= '_' . $this->FormName;
		$this->FormOldKeyName .= '_' . $this->FormName;
		$this->FormBlankRowName .= '_' . $this->FormName;
		$this->FormKeyCountName .= '_' . $this->FormName;
		$GLOBALS["Grid"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (tbl_support)
		if (!isset($GLOBALS["tbl_support"])) {
			$GLOBALS["tbl_support"] = &$this;

//			$GLOBALS["MasterTable"] = &$GLOBALS["Table"];
//			if (!isset($GLOBALS["Table"])) $GLOBALS["Table"] = &$GLOBALS["tbl_support"];

		}

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'grid', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_support', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "span";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
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

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();
		$this->supportID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->CreatedBy->Visible = !$this->IsAddOrEdit();
		$this->CreatedDate->Visible = !$this->IsAddOrEdit();
		$this->UpdatedBy->Visible = !$this->IsAddOrEdit();
		$this->UpdatedDate->Visible = !$this->IsAddOrEdit();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Setup other options
		$this->SetupOtherOptions();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

//		$GLOBALS["Table"] = &$GLOBALS["MasterTable"];
		unset($GLOBALS["Grid"]);
		if ($url == "")
			return;

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

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
	var $ShowOtherOptions = FALSE;
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

			// Set up records per page
			$this->SetUpDisplayRecs();

			// Handle reset command
			$this->ResetCmd();

			// Set up master detail parameters
			$this->SetUpMasterParms();

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

			// Show grid delete link for grid add / grid edit
			if ($this->AllowAddDeleteRow) {
				if ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
					$item = $this->ListOptions->GetItem("griddelete");
					if ($item) $item->Visible = TRUE;
				}
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

		// Restore master/detail filter
		$this->DbMasterFilter = $this->GetMasterFilter(); // Restore master filter
		$this->DbDetailFilter = $this->GetDetailFilter(); // Restore detail filter
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Load master record
		if ($this->CurrentMode <> "add" && $this->GetMasterFilter() <> "" && $this->getCurrentMasterTable() == "tbl_pensioner") {
			global $tbl_pensioner;
			$rsmaster = $tbl_pensioner->LoadRs($this->DbMasterFilter);
			$this->MasterRecordExists = ($rsmaster && !$rsmaster->EOF);
			if (!$this->MasterRecordExists) {
				$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record found
				$this->Page_Terminate("tbl_pensionerlist.php"); // Return to master page
			} else {
				$tbl_pensioner->LoadListRowValues($rsmaster);
				$tbl_pensioner->RowType = EW_ROWTYPE_MASTER; // Master row
				$tbl_pensioner->RenderListRow();
				$rsmaster->Close();
			}
		}

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

	//  Exit inline mode
	function ClearInlineMode() {
		$this->LastAction = $this->CurrentAction; // Save last action
		$this->CurrentAction = ""; // Clear action
		$_SESSION[EW_SESSION_INLINE_MODE] = ""; // Clear inline mode
	}

	// Switch to Grid Add mode
	function GridAddMode() {
		$_SESSION[EW_SESSION_INLINE_MODE] = "gridadd"; // Enabled grid add
	}

	// Switch to Grid Edit mode
	function GridEditMode() {
		$_SESSION[EW_SESSION_INLINE_MODE] = "gridedit"; // Enable grid edit
	}

	// Perform update to grid
	function GridUpdate() {
		global $conn, $Language, $objForm, $gsFormError;
		$bGridUpdate = TRUE;
		if ($this->AuditTrailOnEdit) $this->WriteAuditTrailDummy($Language->Phrase("BatchUpdateBegin")); // Batch update begin

		// Get old recordset
		$this->CurrentFilter = $this->BuildKeyFilter();
		$sSql = $this->SQL();
		if ($rs = $conn->Execute($sSql)) {
			$rsold = $rs->GetRows();
			$rs->Close();
		}
		$sKey = "";

		// Update row index and get row key
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;

		// Update all rows based on key
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {
			$objForm->Index = $rowindex;
			$rowkey = strval($objForm->GetValue($this->FormKeyName));
			$rowaction = strval($objForm->GetValue($this->FormActionName));

			// Load all values and keys
			if ($rowaction <> "insertdelete") { // Skip insert then deleted rows
				$this->LoadFormValues(); // Get form values
				if ($rowaction == "" || $rowaction == "edit" || $rowaction == "delete") {
					$bGridUpdate = $this->SetupKeyValues($rowkey); // Set up key values
				} else {
					$bGridUpdate = TRUE;
				}

				// Skip empty row
				if ($rowaction == "insert" && $this->EmptyRow()) {

					// No action required
				// Validate form and insert/update/delete record

				} elseif ($bGridUpdate) {
					if ($rowaction == "delete") {
						$this->CurrentFilter = $this->KeyFilter();
						$bGridUpdate = $this->DeleteRows(); // Delete this row
					} else if (!$this->ValidateForm()) {
						$bGridUpdate = FALSE; // Form error, reset action
						$this->setFailureMessage($gsFormError);
					} else {
						if ($rowaction == "insert") {
							$bGridUpdate = $this->AddRow(); // Insert this row
						} else {
							if ($rowkey <> "") {
								$this->SendEmail = FALSE; // Do not send email on update success
								$bGridUpdate = $this->EditRow(); // Update this row
							}
						} // End update
					}
				}
				if ($bGridUpdate) {
					if ($sKey <> "") $sKey .= ", ";
					$sKey .= $rowkey;
				} else {
					break;
				}
			}
		}
		if ($bGridUpdate) {

			// Get new recordset
			if ($rs = $conn->Execute($sSql)) {
				$rsnew = $rs->GetRows();
				$rs->Close();
			}
			if ($this->AuditTrailOnEdit) $this->WriteAuditTrailDummy($Language->Phrase("BatchUpdateSuccess")); // Batch update success
			$this->ClearInlineMode(); // Clear inline edit mode
		} else {
			if ($this->AuditTrailOnEdit) $this->WriteAuditTrailDummy($Language->Phrase("BatchUpdateRollback")); // Batch update rollback
			if ($this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("UpdateFailed")); // Set update failed message
			$this->EventCancelled = TRUE; // Set event cancelled
			$this->CurrentAction = "gridedit"; // Stay in Grid Edit mode
		}
		return $bGridUpdate;
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
			$this->supportID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->supportID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Perform Grid Add
	function GridInsert() {
		global $conn, $Language, $objForm, $gsFormError;
		$rowindex = 1;
		$bGridInsert = FALSE;

		// Init key filter
		$sWrkFilter = "";
		$addcnt = 0;
		if ($this->AuditTrailOnAdd) $this->WriteAuditTrailDummy($Language->Phrase("BatchInsertBegin")); // Batch insert begin
		$sKey = "";

		// Get row count
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;

		// Insert all rows
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {

			// Load current row values
			$objForm->Index = $rowindex;
			$rowaction = strval($objForm->GetValue($this->FormActionName));
			if ($rowaction <> "" && $rowaction <> "insert")
				continue; // Skip
			if ($rowaction == "insert") {
				$this->RowOldKey = strval($objForm->GetValue($this->FormOldKeyName));
				$this->LoadOldRecord(); // Load old recordset
			}
			$this->LoadFormValues(); // Get form values
			if (!$this->EmptyRow()) {
				$addcnt++;
				$this->SendEmail = FALSE; // Do not send email on insert success

				// Validate form
				if (!$this->ValidateForm()) {
					$bGridInsert = FALSE; // Form error, reset action
					$this->setFailureMessage($gsFormError);
				} else {
					$bGridInsert = $this->AddRow($this->OldRecordset); // Insert this row
				}
				if ($bGridInsert) {
					if ($sKey <> "") $sKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
					$sKey .= $this->supportID->CurrentValue;

					// Add filter for this record
					$sFilter = $this->KeyFilter();
					if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
					$sWrkFilter .= $sFilter;
				} else {
					break;
				}
			}
		}
		if ($addcnt == 0) { // No record inserted
			$this->ClearInlineMode(); // Clear grid add mode and return
			return TRUE;
		}
		if ($bGridInsert) {

			// Get new recordset
			$this->CurrentFilter = $sWrkFilter;
			$sSql = $this->SQL();
			if ($rs = $conn->Execute($sSql)) {
				$rsnew = $rs->GetRows();
				$rs->Close();
			}
			if ($this->AuditTrailOnAdd) $this->WriteAuditTrailDummy($Language->Phrase("BatchInsertSuccess")); // Batch insert success
			$this->ClearInlineMode(); // Clear grid add mode
		} else {
			if ($this->AuditTrailOnAdd) $this->WriteAuditTrailDummy($Language->Phrase("BatchInsertRollback")); // Batch insert rollback
			if ($this->getFailureMessage() == "") {
				$this->setFailureMessage($Language->Phrase("InsertFailed")); // Set insert failed message
			}
			$this->EventCancelled = TRUE; // Set event cancelled
			$this->CurrentAction = "gridadd"; // Stay in gridadd mode
		}
		return $bGridInsert;
	}

	// Check if empty row
	function EmptyRow() {
		global $objForm;
		if ($objForm->HasValue("x_PensionerID") && $objForm->HasValue("o_PensionerID") && $this->PensionerID->CurrentValue <> $this->PensionerID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_family_support") && $objForm->HasValue("o_family_support") && $this->family_support->CurrentValue <> $this->family_support->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_KindSupID") && $objForm->HasValue("o_KindSupID") && $this->KindSupID->CurrentValue <> $this->KindSupID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_meals") && $objForm->HasValue("o_meals") && $this->meals->CurrentValue <> $this->meals->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_disability") && $objForm->HasValue("o_disability") && $this->disability->CurrentValue <> $this->disability->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_disabilityID") && $objForm->HasValue("o_disabilityID") && $this->disabilityID->CurrentValue <> $this->disabilityID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_immobile") && $objForm->HasValue("o_immobile") && $this->immobile->CurrentValue <> $this->immobile->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_assistiveID") && $objForm->HasValue("o_assistiveID") && $this->assistiveID->CurrentValue <> $this->assistiveID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_preEx_illness") && $objForm->HasValue("o_preEx_illness") && $this->preEx_illness->CurrentValue <> $this->preEx_illness->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_illnessID") && $objForm->HasValue("o_illnessID") && $this->illnessID->CurrentValue <> $this->illnessID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_physconditionID") && $objForm->HasValue("o_physconditionID") && $this->physconditionID->CurrentValue <> $this->physconditionID->OldValue)
			return FALSE;
		return TRUE;
	}

	// Validate grid form
	function ValidateGridForm() {
		global $objForm;

		// Get row count
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;

		// Validate all records
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {

			// Load current row values
			$objForm->Index = $rowindex;
			$rowaction = strval($objForm->GetValue($this->FormActionName));
			if ($rowaction <> "delete" && $rowaction <> "insertdelete") {
				$this->LoadFormValues(); // Get form values
				if ($rowaction == "insert" && $this->EmptyRow()) {

					// Ignore
				} else if (!$this->ValidateForm()) {
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	// Restore form values for current row
	function RestoreCurrentRowFormValues($idx) {
		global $objForm;

		// Get row based on current index
		$objForm->Index = $idx;
		$this->LoadFormValues(); // Load form values
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
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

			// Reset master/detail keys
			if ($this->Command == "resetall") {
				$this->setCurrentMasterTable(""); // Clear master table
				$this->DbMasterFilter = "";
				$this->DbDetailFilter = "";
				$this->PensionerID->setSessionValue("");
			}

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// "griddelete"
		if ($this->AllowAddDeleteRow) {
			$item = &$this->ListOptions->Add("griddelete");
			$item->CssStyle = "white-space: nowrap;";
			$item->OnLeft = TRUE;
			$item->Visible = FALSE; // Default hidden
		}

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group tolits "btn-small"
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// Set up row action and key
		if (is_numeric($this->RowIndex) && $this->CurrentMode <> "view") {
			$objForm->Index = $this->RowIndex;
			$ActionName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormActionName);
			$OldKeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormOldKeyName);
			$KeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormKeyName);
			$BlankRowName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormBlankRowName);
			if ($this->RowAction <> "")
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $ActionName . "\" id=\"" . $ActionName . "\" value=\"" . $this->RowAction . "\">";
			if ($objForm->HasValue($this->FormOldKeyName))
				$this->RowOldKey = strval($objForm->GetValue($this->FormOldKeyName));
			if ($this->RowOldKey <> "")
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $OldKeyName . "\" id=\"" . $OldKeyName . "\" value=\"" . ew_HtmlEncode($this->RowOldKey) . "\">";
			if ($this->RowAction == "delete") {
				$rowkey = $objForm->GetValue($this->FormKeyName);
				$this->SetupKeyValues($rowkey);
			}
			if ($this->RowAction == "insert" && $this->CurrentAction == "F" && $this->EmptyRow())
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $BlankRowName . "\" id=\"" . $BlankRowName . "\" value=\"1\">";
		}

		// "delete"
		if ($this->AllowAddDeleteRow) {
			if ($this->CurrentMode == "add" || $this->CurrentMode == "copy" || $this->CurrentMode == "edit") {
				$option = &$this->ListOptions;
				$option->UseButtonGroup = TRUE; // Use button group for grid delete button
				$option->UseImageAndText = TRUE; // Use image and text for grid delete button
				$oListOpt = &$option->Items["griddelete"];
				if (!$Security->CanDelete() && is_numeric($this->RowIndex) && ($this->RowAction == "" || $this->RowAction == "edit")) { // Do not allow delete existing record
					$oListOpt->Body = "&nbsp;";
				} else {
					$oListOpt->Body = "<a class=\"btn btn-danger btn-sm\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"javascript:void(0);\" onclick=\"ew_DeleteGridRow(this, " . $this->RowIndex . ");\">" . $Language->Phrase("DeleteLink") . "Delete" . "</a>";
				}
			}
		}
		if ($this->CurrentMode == "edit" && is_numeric($this->RowIndex)) {
			$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $KeyName . "\" id=\"" . $KeyName . "\" value=\"" . $this->supportID->CurrentValue . "\">";
		}
		$this->RenderListOptionsExt();
	}

	// Set record key
	function SetRecordKey(&$key, $rs) {
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs->fields('supportID');
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$option = &$this->OtherOptions["addedit"];
		$option->UseDropDownButton = FALSE;
		$option->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$option->UseButtonGroup = TRUE;
		$option->ButtonClass = "btn-sm"; // Class for button group tolits "btn-small"
		$item = &$option->Add($option->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		if (($this->CurrentMode == "add" || $this->CurrentMode == "copy" || $this->CurrentMode == "edit") && $this->CurrentAction != "F") { // Check add/copy/edit mode
			if ($this->AllowAddDeleteRow) {
				$option = &$options["addedit"];
				$option->UseDropDownButton = FALSE;
				$option->UseImageAndText = TRUE;
				$item = &$option->Add("addblankrow");
				$item->Body = "<a class=\"btn btn-pink btn-sm\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("AddBlankRow")) . "\" href=\"javascript:void(0);\" onclick=\"ew_AddGridRow(this);\">" . $Language->Phrase("AddBlankRow") . "Add Blank Row". "</a>";
				$item->Visible = $Security->CanAdd();
				$this->ShowOtherOptions = $item->Visible;
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

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->supportID->CurrentValue = NULL;
		$this->supportID->OldValue = $this->supportID->CurrentValue;
		$this->PensionerID->CurrentValue = NULL;
		$this->PensionerID->OldValue = $this->PensionerID->CurrentValue;
		$this->family_support->CurrentValue = NULL;
		$this->family_support->OldValue = $this->family_support->CurrentValue;
		$this->KindSupID->CurrentValue = NULL;
		$this->KindSupID->OldValue = $this->KindSupID->CurrentValue;
		$this->meals->CurrentValue = NULL;
		$this->meals->OldValue = $this->meals->CurrentValue;
		$this->disability->CurrentValue = NULL;
		$this->disability->OldValue = $this->disability->CurrentValue;
		$this->disabilityID->CurrentValue = NULL;
		$this->disabilityID->OldValue = $this->disabilityID->CurrentValue;
		$this->immobile->CurrentValue = NULL;
		$this->immobile->OldValue = $this->immobile->CurrentValue;
		$this->assistiveID->CurrentValue = NULL;
		$this->assistiveID->OldValue = $this->assistiveID->CurrentValue;
		$this->preEx_illness->CurrentValue = NULL;
		$this->preEx_illness->OldValue = $this->preEx_illness->CurrentValue;
		$this->illnessID->CurrentValue = NULL;
		$this->illnessID->OldValue = $this->illnessID->CurrentValue;
		$this->physconditionID->CurrentValue = NULL;
		$this->physconditionID->OldValue = $this->physconditionID->CurrentValue;
		$this->CreatedBy->CurrentValue = NULL;
		$this->CreatedBy->OldValue = $this->CreatedBy->CurrentValue;
		$this->CreatedDate->CurrentValue = NULL;
		$this->CreatedDate->OldValue = $this->CreatedDate->CurrentValue;
		$this->UpdatedBy->CurrentValue = NULL;
		$this->UpdatedBy->OldValue = $this->UpdatedBy->CurrentValue;
		$this->UpdatedDate->CurrentValue = NULL;
		$this->UpdatedDate->OldValue = $this->UpdatedDate->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->supportID->FldIsDetailKey && $this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->supportID->setFormValue($objForm->GetValue("x_supportID"));
		if (!$this->PensionerID->FldIsDetailKey) {
			$this->PensionerID->setFormValue($objForm->GetValue("x_PensionerID"));
		}
		$this->PensionerID->setOldValue($objForm->GetValue("o_PensionerID"));
		if (!$this->family_support->FldIsDetailKey) {
			$this->family_support->setFormValue($objForm->GetValue("x_family_support"));
		}
		$this->family_support->setOldValue($objForm->GetValue("o_family_support"));
		if (!$this->KindSupID->FldIsDetailKey) {
			$this->KindSupID->setFormValue($objForm->GetValue("x_KindSupID"));
		}
		$this->KindSupID->setOldValue($objForm->GetValue("o_KindSupID"));
		if (!$this->meals->FldIsDetailKey) {
			$this->meals->setFormValue($objForm->GetValue("x_meals"));
		}
		$this->meals->setOldValue($objForm->GetValue("o_meals"));
		if (!$this->disability->FldIsDetailKey) {
			$this->disability->setFormValue($objForm->GetValue("x_disability"));
		}
		$this->disability->setOldValue($objForm->GetValue("o_disability"));
		if (!$this->disabilityID->FldIsDetailKey) {
			$this->disabilityID->setFormValue($objForm->GetValue("x_disabilityID"));
		}
		$this->disabilityID->setOldValue($objForm->GetValue("o_disabilityID"));
		if (!$this->immobile->FldIsDetailKey) {
			$this->immobile->setFormValue($objForm->GetValue("x_immobile"));
		}
		$this->immobile->setOldValue($objForm->GetValue("o_immobile"));
		if (!$this->assistiveID->FldIsDetailKey) {
			$this->assistiveID->setFormValue($objForm->GetValue("x_assistiveID"));
		}
		$this->assistiveID->setOldValue($objForm->GetValue("o_assistiveID"));
		if (!$this->preEx_illness->FldIsDetailKey) {
			$this->preEx_illness->setFormValue($objForm->GetValue("x_preEx_illness"));
		}
		$this->preEx_illness->setOldValue($objForm->GetValue("o_preEx_illness"));
		if (!$this->illnessID->FldIsDetailKey) {
			$this->illnessID->setFormValue($objForm->GetValue("x_illnessID"));
		}
		$this->illnessID->setOldValue($objForm->GetValue("o_illnessID"));
		if (!$this->physconditionID->FldIsDetailKey) {
			$this->physconditionID->setFormValue($objForm->GetValue("x_physconditionID"));
		}
		$this->physconditionID->setOldValue($objForm->GetValue("o_physconditionID"));
		if (!$this->CreatedBy->FldIsDetailKey) {
			$this->CreatedBy->setFormValue($objForm->GetValue("x_CreatedBy"));
		}
		$this->CreatedBy->setOldValue($objForm->GetValue("o_CreatedBy"));
		if (!$this->CreatedDate->FldIsDetailKey) {
			$this->CreatedDate->setFormValue($objForm->GetValue("x_CreatedDate"));
			$this->CreatedDate->CurrentValue = ew_UnFormatDateTime($this->CreatedDate->CurrentValue, 6);
		}
		$this->CreatedDate->setOldValue($objForm->GetValue("o_CreatedDate"));
		if (!$this->UpdatedBy->FldIsDetailKey) {
			$this->UpdatedBy->setFormValue($objForm->GetValue("x_UpdatedBy"));
		}
		$this->UpdatedBy->setOldValue($objForm->GetValue("o_UpdatedBy"));
		if (!$this->UpdatedDate->FldIsDetailKey) {
			$this->UpdatedDate->setFormValue($objForm->GetValue("x_UpdatedDate"));
			$this->UpdatedDate->CurrentValue = ew_UnFormatDateTime($this->UpdatedDate->CurrentValue, 6);
		}
		$this->UpdatedDate->setOldValue($objForm->GetValue("o_UpdatedDate"));
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		if ($this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->supportID->CurrentValue = $this->supportID->FormValue;
		$this->PensionerID->CurrentValue = $this->PensionerID->FormValue;
		$this->family_support->CurrentValue = $this->family_support->FormValue;
		$this->KindSupID->CurrentValue = $this->KindSupID->FormValue;
		$this->meals->CurrentValue = $this->meals->FormValue;
		$this->disability->CurrentValue = $this->disability->FormValue;
		$this->disabilityID->CurrentValue = $this->disabilityID->FormValue;
		$this->immobile->CurrentValue = $this->immobile->FormValue;
		$this->assistiveID->CurrentValue = $this->assistiveID->FormValue;
		$this->preEx_illness->CurrentValue = $this->preEx_illness->FormValue;
		$this->illnessID->CurrentValue = $this->illnessID->FormValue;
		$this->physconditionID->CurrentValue = $this->physconditionID->FormValue;
		$this->CreatedBy->CurrentValue = $this->CreatedBy->FormValue;
		$this->CreatedDate->CurrentValue = $this->CreatedDate->FormValue;
		$this->CreatedDate->CurrentValue = ew_UnFormatDateTime($this->CreatedDate->CurrentValue, 6);
		$this->UpdatedBy->CurrentValue = $this->UpdatedBy->FormValue;
		$this->UpdatedDate->CurrentValue = $this->UpdatedDate->FormValue;
		$this->UpdatedDate->CurrentValue = ew_UnFormatDateTime($this->UpdatedDate->CurrentValue, 6);
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
		$this->supportID->setDbValue($rs->fields('supportID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->family_support->setDbValue($rs->fields('family_support'));
		$this->KindSupID->setDbValue($rs->fields('KindSupID'));
		$this->meals->setDbValue($rs->fields('meals'));
		$this->disability->setDbValue($rs->fields('disability'));
		$this->disabilityID->setDbValue($rs->fields('disabilityID'));
		$this->immobile->setDbValue($rs->fields('immobile'));
		$this->assistiveID->setDbValue($rs->fields('assistiveID'));
		$this->preEx_illness->setDbValue($rs->fields('preEx_illness'));
		$this->illnessID->setDbValue($rs->fields('illnessID'));
		$this->physconditionID->setDbValue($rs->fields('physconditionID'));
		$this->CreatedBy->setDbValue($rs->fields('CreatedBy'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->supportID->DbValue = $row['supportID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->family_support->DbValue = $row['family_support'];
		$this->KindSupID->DbValue = $row['KindSupID'];
		$this->meals->DbValue = $row['meals'];
		$this->disability->DbValue = $row['disability'];
		$this->disabilityID->DbValue = $row['disabilityID'];
		$this->immobile->DbValue = $row['immobile'];
		$this->assistiveID->DbValue = $row['assistiveID'];
		$this->preEx_illness->DbValue = $row['preEx_illness'];
		$this->illnessID->DbValue = $row['illnessID'];
		$this->physconditionID->DbValue = $row['physconditionID'];
		$this->CreatedBy->DbValue = $row['CreatedBy'];
		$this->CreatedDate->DbValue = $row['CreatedDate'];
		$this->UpdatedBy->DbValue = $row['UpdatedBy'];
		$this->UpdatedDate->DbValue = $row['UpdatedDate'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		$arKeys[] = $this->RowOldKey;
		$cnt = count($arKeys);
		if ($cnt >= 1) {
			if (strval($arKeys[0]) <> "")
				$this->supportID->CurrentValue = strval($arKeys[0]); // supportID
			else
				$bValidKey = FALSE;
		} else {
			$bValidKey = FALSE;
		}

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
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// supportID
		// PensionerID
		// family_support
		// KindSupID
		// meals
		// disability
		// disabilityID
		// immobile
		// assistiveID
		// preEx_illness
		// illnessID
		// physconditionID
		// CreatedBy
		// CreatedDate
		// UpdatedBy
		// UpdatedDate

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// supportID
			$this->supportID->ViewValue = $this->supportID->CurrentValue;
			$this->supportID->ViewCustomAttributes = "";

			// PensionerID
			$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewCustomAttributes = "";

			// family_support
			if (strval($this->family_support->CurrentValue) <> "") {
				switch ($this->family_support->CurrentValue) {
					case $this->family_support->FldTagValue(1):
						$this->family_support->ViewValue = $this->family_support->FldTagCaption(1) <> "" ? $this->family_support->FldTagCaption(1) : $this->family_support->CurrentValue;
						break;
					case $this->family_support->FldTagValue(2):
						$this->family_support->ViewValue = $this->family_support->FldTagCaption(2) <> "" ? $this->family_support->FldTagCaption(2) : $this->family_support->CurrentValue;
						break;
					default:
						$this->family_support->ViewValue = $this->family_support->CurrentValue;
				}
			} else {
				$this->family_support->ViewValue = NULL;
			}
			$this->family_support->ViewCustomAttributes = "";

			// KindSupID
			if (strval($this->KindSupID->CurrentValue) <> "") {
				$sFilterWrk = "`SupportID`" . ew_SearchString("=", $this->KindSupID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_support`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->KindSupID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `SupportID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->KindSupID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->KindSupID->ViewValue = $this->KindSupID->CurrentValue;
				}
			} else {
				$this->KindSupID->ViewValue = NULL;
			}
			$this->KindSupID->ViewCustomAttributes = "";

			// meals
			$this->meals->ViewValue = $this->meals->CurrentValue;
			$this->meals->ViewCustomAttributes = "";

			// disability
			if (strval($this->disability->CurrentValue) <> "") {
				switch ($this->disability->CurrentValue) {
					case $this->disability->FldTagValue(1):
						$this->disability->ViewValue = $this->disability->FldTagCaption(1) <> "" ? $this->disability->FldTagCaption(1) : $this->disability->CurrentValue;
						break;
					case $this->disability->FldTagValue(2):
						$this->disability->ViewValue = $this->disability->FldTagCaption(2) <> "" ? $this->disability->FldTagCaption(2) : $this->disability->CurrentValue;
						break;
					default:
						$this->disability->ViewValue = $this->disability->CurrentValue;
				}
			} else {
				$this->disability->ViewValue = NULL;
			}
			$this->disability->ViewCustomAttributes = "";

			// disabilityID
			if (strval($this->disabilityID->CurrentValue) <> "") {
				$sFilterWrk = "`disabilityID`" . ew_SearchString("=", $this->disabilityID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_disability`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->disabilityID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `disabilityID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->disabilityID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->disabilityID->ViewValue = $this->disabilityID->CurrentValue;
				}
			} else {
				$this->disabilityID->ViewValue = NULL;
			}
			$this->disabilityID->ViewCustomAttributes = "";

			// immobile
			if (strval($this->immobile->CurrentValue) <> "") {
				switch ($this->immobile->CurrentValue) {
					case $this->immobile->FldTagValue(1):
						$this->immobile->ViewValue = $this->immobile->FldTagCaption(1) <> "" ? $this->immobile->FldTagCaption(1) : $this->immobile->CurrentValue;
						break;
					case $this->immobile->FldTagValue(2):
						$this->immobile->ViewValue = $this->immobile->FldTagCaption(2) <> "" ? $this->immobile->FldTagCaption(2) : $this->immobile->CurrentValue;
						break;
					default:
						$this->immobile->ViewValue = $this->immobile->CurrentValue;
				}
			} else {
				$this->immobile->ViewValue = NULL;
			}
			$this->immobile->ViewCustomAttributes = "";

			// assistiveID
			if (strval($this->assistiveID->CurrentValue) <> "") {
				$sFilterWrk = "`assistiveID`" . ew_SearchString("=", $this->assistiveID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_assistive`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->assistiveID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `assistiveID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->assistiveID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->assistiveID->ViewValue = $this->assistiveID->CurrentValue;
				}
			} else {
				$this->assistiveID->ViewValue = NULL;
			}
			$this->assistiveID->ViewCustomAttributes = "";

			// preEx_illness
			if (strval($this->preEx_illness->CurrentValue) <> "") {
				switch ($this->preEx_illness->CurrentValue) {
					case $this->preEx_illness->FldTagValue(1):
						$this->preEx_illness->ViewValue = $this->preEx_illness->FldTagCaption(1) <> "" ? $this->preEx_illness->FldTagCaption(1) : $this->preEx_illness->CurrentValue;
						break;
					case $this->preEx_illness->FldTagValue(2):
						$this->preEx_illness->ViewValue = $this->preEx_illness->FldTagCaption(2) <> "" ? $this->preEx_illness->FldTagCaption(2) : $this->preEx_illness->CurrentValue;
						break;
					default:
						$this->preEx_illness->ViewValue = $this->preEx_illness->CurrentValue;
				}
			} else {
				$this->preEx_illness->ViewValue = NULL;
			}
			$this->preEx_illness->ViewCustomAttributes = "";

			// illnessID
			if (strval($this->illnessID->CurrentValue) <> "") {
				$sFilterWrk = "`illnessID`" . ew_SearchString("=", $this->illnessID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_illness`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->illnessID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `illnessID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->illnessID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->illnessID->ViewValue = $this->illnessID->CurrentValue;
				}
			} else {
				$this->illnessID->ViewValue = NULL;
			}
			$this->illnessID->ViewCustomAttributes = "";

			// physconditionID
			if (strval($this->physconditionID->CurrentValue) <> "") {
				$sFilterWrk = "`physconditionID`" . ew_SearchString("=", $this->physconditionID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_physical_condition`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->physconditionID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `physconditionID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->physconditionID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->physconditionID->ViewValue = $this->physconditionID->CurrentValue;
				}
			} else {
				$this->physconditionID->ViewValue = NULL;
			}
			$this->physconditionID->ViewCustomAttributes = "";

			// CreatedBy
			$this->CreatedBy->ViewValue = $this->CreatedBy->CurrentValue;
			$this->CreatedBy->ViewCustomAttributes = "";

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

			// supportID
			$this->supportID->LinkCustomAttributes = "";
			$this->supportID->HrefValue = "";
			$this->supportID->TooltipValue = "";

			// PensionerID
			$this->PensionerID->LinkCustomAttributes = "";
			$this->PensionerID->HrefValue = "";
			$this->PensionerID->TooltipValue = "";

			// family_support
			$this->family_support->LinkCustomAttributes = "";
			$this->family_support->HrefValue = "";
			$this->family_support->TooltipValue = "";

			// KindSupID
			$this->KindSupID->LinkCustomAttributes = "";
			$this->KindSupID->HrefValue = "";
			$this->KindSupID->TooltipValue = "";

			// meals
			$this->meals->LinkCustomAttributes = "";
			$this->meals->HrefValue = "";
			$this->meals->TooltipValue = "";

			// disability
			$this->disability->LinkCustomAttributes = "";
			$this->disability->HrefValue = "";
			$this->disability->TooltipValue = "";

			// disabilityID
			$this->disabilityID->LinkCustomAttributes = "";
			$this->disabilityID->HrefValue = "";
			$this->disabilityID->TooltipValue = "";

			// immobile
			$this->immobile->LinkCustomAttributes = "";
			$this->immobile->HrefValue = "";
			$this->immobile->TooltipValue = "";

			// assistiveID
			$this->assistiveID->LinkCustomAttributes = "";
			$this->assistiveID->HrefValue = "";
			$this->assistiveID->TooltipValue = "";

			// preEx_illness
			$this->preEx_illness->LinkCustomAttributes = "";
			$this->preEx_illness->HrefValue = "";
			$this->preEx_illness->TooltipValue = "";

			// illnessID
			$this->illnessID->LinkCustomAttributes = "";
			$this->illnessID->HrefValue = "";
			$this->illnessID->TooltipValue = "";

			// physconditionID
			$this->physconditionID->LinkCustomAttributes = "";
			$this->physconditionID->HrefValue = "";
			$this->physconditionID->TooltipValue = "";

			// CreatedBy
			$this->CreatedBy->LinkCustomAttributes = "";
			$this->CreatedBy->HrefValue = "";
			$this->CreatedBy->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// supportID
			// PensionerID

			$this->PensionerID->EditCustomAttributes = "";
			if ($this->PensionerID->getSessionValue() <> "") {
				$this->PensionerID->CurrentValue = $this->PensionerID->getSessionValue();
				$this->PensionerID->OldValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewCustomAttributes = "";
			} else {
			$this->PensionerID->EditValue = ew_HtmlEncode($this->PensionerID->CurrentValue);
			$this->PensionerID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->PensionerID->FldCaption()));
			}

			// family_support
			$this->family_support->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->family_support->FldTagValue(1), $this->family_support->FldTagCaption(1) <> "" ? $this->family_support->FldTagCaption(1) : $this->family_support->FldTagValue(1));
			$arwrk[] = array($this->family_support->FldTagValue(2), $this->family_support->FldTagCaption(2) <> "" ? $this->family_support->FldTagCaption(2) : $this->family_support->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->family_support->EditValue = $arwrk;

			// KindSupID
			$this->KindSupID->EditCustomAttributes = "";
			if (trim(strval($this->KindSupID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`SupportID`" . ew_SearchString("=", $this->KindSupID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_support`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->KindSupID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `SupportID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->KindSupID->EditValue = $arwrk;

			// meals
			$this->meals->EditCustomAttributes = "";
			$this->meals->EditValue = ew_HtmlEncode($this->meals->CurrentValue);
			$this->meals->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->meals->FldCaption()));

			// disability
			$this->disability->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->disability->FldTagValue(1), $this->disability->FldTagCaption(1) <> "" ? $this->disability->FldTagCaption(1) : $this->disability->FldTagValue(1));
			$arwrk[] = array($this->disability->FldTagValue(2), $this->disability->FldTagCaption(2) <> "" ? $this->disability->FldTagCaption(2) : $this->disability->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->disability->EditValue = $arwrk;

			// disabilityID
			$this->disabilityID->EditCustomAttributes = "";
			if (trim(strval($this->disabilityID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`disabilityID`" . ew_SearchString("=", $this->disabilityID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_disability`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->disabilityID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `disabilityID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->disabilityID->EditValue = $arwrk;

			// immobile
			$this->immobile->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->immobile->FldTagValue(1), $this->immobile->FldTagCaption(1) <> "" ? $this->immobile->FldTagCaption(1) : $this->immobile->FldTagValue(1));
			$arwrk[] = array($this->immobile->FldTagValue(2), $this->immobile->FldTagCaption(2) <> "" ? $this->immobile->FldTagCaption(2) : $this->immobile->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->immobile->EditValue = $arwrk;

			// assistiveID
			$this->assistiveID->EditCustomAttributes = "";
			if (trim(strval($this->assistiveID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`assistiveID`" . ew_SearchString("=", $this->assistiveID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_assistive`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->assistiveID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `assistiveID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->assistiveID->EditValue = $arwrk;

			// preEx_illness
			$this->preEx_illness->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->preEx_illness->FldTagValue(1), $this->preEx_illness->FldTagCaption(1) <> "" ? $this->preEx_illness->FldTagCaption(1) : $this->preEx_illness->FldTagValue(1));
			$arwrk[] = array($this->preEx_illness->FldTagValue(2), $this->preEx_illness->FldTagCaption(2) <> "" ? $this->preEx_illness->FldTagCaption(2) : $this->preEx_illness->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->preEx_illness->EditValue = $arwrk;

			// illnessID
			$this->illnessID->EditCustomAttributes = "";
			if (trim(strval($this->illnessID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`illnessID`" . ew_SearchString("=", $this->illnessID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_illness`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->illnessID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `illnessID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->illnessID->EditValue = $arwrk;

			// physconditionID
			$this->physconditionID->EditCustomAttributes = "";
			if (trim(strval($this->physconditionID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`physconditionID`" . ew_SearchString("=", $this->physconditionID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_physical_condition`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->physconditionID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `physconditionID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->physconditionID->EditValue = $arwrk;

			// CreatedBy
			// CreatedDate
			// UpdatedBy
			// UpdatedDate
			// Edit refer script
			// supportID

			$this->supportID->HrefValue = "";

			// PensionerID
			$this->PensionerID->HrefValue = "";

			// family_support
			$this->family_support->HrefValue = "";

			// KindSupID
			$this->KindSupID->HrefValue = "";

			// meals
			$this->meals->HrefValue = "";

			// disability
			$this->disability->HrefValue = "";

			// disabilityID
			$this->disabilityID->HrefValue = "";

			// immobile
			$this->immobile->HrefValue = "";

			// assistiveID
			$this->assistiveID->HrefValue = "";

			// preEx_illness
			$this->preEx_illness->HrefValue = "";

			// illnessID
			$this->illnessID->HrefValue = "";

			// physconditionID
			$this->physconditionID->HrefValue = "";

			// CreatedBy
			$this->CreatedBy->HrefValue = "";

			// CreatedDate
			$this->CreatedDate->HrefValue = "";

			// UpdatedBy
			$this->UpdatedBy->HrefValue = "";

			// UpdatedDate
			$this->UpdatedDate->HrefValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// supportID
			$this->supportID->EditCustomAttributes = "";
			$this->supportID->EditValue = $this->supportID->CurrentValue;
			$this->supportID->ViewCustomAttributes = "";

			// PensionerID
			$this->PensionerID->EditCustomAttributes = "";
			if ($this->PensionerID->getSessionValue() <> "") {
				$this->PensionerID->CurrentValue = $this->PensionerID->getSessionValue();
				$this->PensionerID->OldValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewCustomAttributes = "";
			} else {
			$this->PensionerID->EditValue = ew_HtmlEncode($this->PensionerID->CurrentValue);
			$this->PensionerID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->PensionerID->FldCaption()));
			}

			// family_support
			$this->family_support->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->family_support->FldTagValue(1), $this->family_support->FldTagCaption(1) <> "" ? $this->family_support->FldTagCaption(1) : $this->family_support->FldTagValue(1));
			$arwrk[] = array($this->family_support->FldTagValue(2), $this->family_support->FldTagCaption(2) <> "" ? $this->family_support->FldTagCaption(2) : $this->family_support->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->family_support->EditValue = $arwrk;

			// KindSupID
			$this->KindSupID->EditCustomAttributes = "";
			if (trim(strval($this->KindSupID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`SupportID`" . ew_SearchString("=", $this->KindSupID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_support`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->KindSupID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `SupportID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->KindSupID->EditValue = $arwrk;

			// meals
			$this->meals->EditCustomAttributes = "";
			$this->meals->EditValue = ew_HtmlEncode($this->meals->CurrentValue);
			$this->meals->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->meals->FldCaption()));

			// disability
			$this->disability->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->disability->FldTagValue(1), $this->disability->FldTagCaption(1) <> "" ? $this->disability->FldTagCaption(1) : $this->disability->FldTagValue(1));
			$arwrk[] = array($this->disability->FldTagValue(2), $this->disability->FldTagCaption(2) <> "" ? $this->disability->FldTagCaption(2) : $this->disability->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->disability->EditValue = $arwrk;

			// disabilityID
			$this->disabilityID->EditCustomAttributes = "";
			if (trim(strval($this->disabilityID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`disabilityID`" . ew_SearchString("=", $this->disabilityID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_disability`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->disabilityID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `disabilityID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->disabilityID->EditValue = $arwrk;

			// immobile
			$this->immobile->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->immobile->FldTagValue(1), $this->immobile->FldTagCaption(1) <> "" ? $this->immobile->FldTagCaption(1) : $this->immobile->FldTagValue(1));
			$arwrk[] = array($this->immobile->FldTagValue(2), $this->immobile->FldTagCaption(2) <> "" ? $this->immobile->FldTagCaption(2) : $this->immobile->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->immobile->EditValue = $arwrk;

			// assistiveID
			$this->assistiveID->EditCustomAttributes = "";
			if (trim(strval($this->assistiveID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`assistiveID`" . ew_SearchString("=", $this->assistiveID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_assistive`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->assistiveID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `assistiveID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->assistiveID->EditValue = $arwrk;

			// preEx_illness
			$this->preEx_illness->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->preEx_illness->FldTagValue(1), $this->preEx_illness->FldTagCaption(1) <> "" ? $this->preEx_illness->FldTagCaption(1) : $this->preEx_illness->FldTagValue(1));
			$arwrk[] = array($this->preEx_illness->FldTagValue(2), $this->preEx_illness->FldTagCaption(2) <> "" ? $this->preEx_illness->FldTagCaption(2) : $this->preEx_illness->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->preEx_illness->EditValue = $arwrk;

			// illnessID
			$this->illnessID->EditCustomAttributes = "";
			if (trim(strval($this->illnessID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`illnessID`" . ew_SearchString("=", $this->illnessID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_illness`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->illnessID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `illnessID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->illnessID->EditValue = $arwrk;

			// physconditionID
			$this->physconditionID->EditCustomAttributes = "";
			if (trim(strval($this->physconditionID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`physconditionID`" . ew_SearchString("=", $this->physconditionID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_physical_condition`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->physconditionID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `physconditionID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->physconditionID->EditValue = $arwrk;

			// CreatedBy
			// CreatedDate
			// UpdatedBy
			// UpdatedDate
			// Edit refer script
			// supportID

			$this->supportID->HrefValue = "";

			// PensionerID
			$this->PensionerID->HrefValue = "";

			// family_support
			$this->family_support->HrefValue = "";

			// KindSupID
			$this->KindSupID->HrefValue = "";

			// meals
			$this->meals->HrefValue = "";

			// disability
			$this->disability->HrefValue = "";

			// disabilityID
			$this->disabilityID->HrefValue = "";

			// immobile
			$this->immobile->HrefValue = "";

			// assistiveID
			$this->assistiveID->HrefValue = "";

			// preEx_illness
			$this->preEx_illness->HrefValue = "";

			// illnessID
			$this->illnessID->HrefValue = "";

			// physconditionID
			$this->physconditionID->HrefValue = "";

			// CreatedBy
			$this->CreatedBy->HrefValue = "";

			// CreatedDate
			$this->CreatedDate->HrefValue = "";

			// UpdatedBy
			$this->UpdatedBy->HrefValue = "";

			// UpdatedDate
			$this->UpdatedDate->HrefValue = "";
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

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!ew_CheckInteger($this->meals->FormValue)) {
			ew_AddMessage($gsFormError, $this->meals->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
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
				$sThisKey .= $row['supportID'];
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
			if ($DeleteRows) {
				foreach ($rsold as $row)
					$this->WriteAuditTrailOnDelete($row);
			}
		} else {
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
			if ($this->PensionerID->CurrentValue <> "") { // Check field with unique index
			$sFilterChk = "(`PensionerID` = '" . ew_AdjustSql($this->PensionerID->CurrentValue) . "')";
			$sFilterChk .= " AND NOT (" . $sFilter . ")";
			$this->CurrentFilter = $sFilterChk;
			$sSqlChk = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rsChk = $conn->Execute($sSqlChk);
			$conn->raiseErrorFn = '';
			if ($rsChk === FALSE) {
				return FALSE;
			} elseif (!$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->PensionerID->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->PensionerID->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
			$rsChk->Close();
		}
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// PensionerID
			$this->PensionerID->SetDbValueDef($rsnew, $this->PensionerID->CurrentValue, NULL, $this->PensionerID->ReadOnly);

			// family_support
			$this->family_support->SetDbValueDef($rsnew, $this->family_support->CurrentValue, NULL, $this->family_support->ReadOnly);

			// KindSupID
			$this->KindSupID->SetDbValueDef($rsnew, $this->KindSupID->CurrentValue, NULL, $this->KindSupID->ReadOnly);

			// meals
			$this->meals->SetDbValueDef($rsnew, $this->meals->CurrentValue, NULL, $this->meals->ReadOnly);

			// disability
			$this->disability->SetDbValueDef($rsnew, $this->disability->CurrentValue, NULL, $this->disability->ReadOnly);

			// disabilityID
			$this->disabilityID->SetDbValueDef($rsnew, $this->disabilityID->CurrentValue, NULL, $this->disabilityID->ReadOnly);

			// immobile
			$this->immobile->SetDbValueDef($rsnew, $this->immobile->CurrentValue, NULL, $this->immobile->ReadOnly);

			// assistiveID
			$this->assistiveID->SetDbValueDef($rsnew, $this->assistiveID->CurrentValue, NULL, $this->assistiveID->ReadOnly);

			// preEx_illness
			$this->preEx_illness->SetDbValueDef($rsnew, $this->preEx_illness->CurrentValue, NULL, $this->preEx_illness->ReadOnly);

			// illnessID
			$this->illnessID->SetDbValueDef($rsnew, $this->illnessID->CurrentValue, NULL, $this->illnessID->ReadOnly);

			// physconditionID
			$this->physconditionID->SetDbValueDef($rsnew, $this->physconditionID->CurrentValue, NULL, $this->physconditionID->ReadOnly);

			// CreatedBy
			$this->CreatedBy->SetDbValueDef($rsnew, CurrentUserID(), NULL);
			$rsnew['CreatedBy'] = &$this->CreatedBy->DbValue;

			// CreatedDate
			$this->CreatedDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
			$rsnew['CreatedDate'] = &$this->CreatedDate->DbValue;

			// UpdatedBy
			$this->UpdatedBy->SetDbValueDef($rsnew, CurrentUserID(), NULL);
			$rsnew['UpdatedBy'] = &$this->UpdatedBy->DbValue;

			// UpdatedDate
			$this->UpdatedDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
			$rsnew['UpdatedDate'] = &$this->UpdatedDate->DbValue;

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = 'ew_ErrorFn';
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		if ($EditRow) {
			$this->WriteAuditTrailOnEdit($rsold, $rsnew);
		}
		$rs->Close();
		return $EditRow;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Set up foreign key field value from Session
			if ($this->getCurrentMasterTable() == "tbl_pensioner") {
				$this->PensionerID->CurrentValue = $this->PensionerID->getSessionValue();
			}
		if ($this->PensionerID->CurrentValue <> "") { // Check field with unique index
			$sFilter = "(PensionerID = '" . ew_AdjustSql($this->PensionerID->CurrentValue) . "')";
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->PensionerID->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->PensionerID->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
		}

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// PensionerID
		$this->PensionerID->SetDbValueDef($rsnew, $this->PensionerID->CurrentValue, NULL, FALSE);

		// family_support
		$this->family_support->SetDbValueDef($rsnew, $this->family_support->CurrentValue, NULL, FALSE);

		// KindSupID
		$this->KindSupID->SetDbValueDef($rsnew, $this->KindSupID->CurrentValue, NULL, FALSE);

		// meals
		$this->meals->SetDbValueDef($rsnew, $this->meals->CurrentValue, NULL, FALSE);

		// disability
		$this->disability->SetDbValueDef($rsnew, $this->disability->CurrentValue, NULL, FALSE);

		// disabilityID
		$this->disabilityID->SetDbValueDef($rsnew, $this->disabilityID->CurrentValue, NULL, FALSE);

		// immobile
		$this->immobile->SetDbValueDef($rsnew, $this->immobile->CurrentValue, NULL, FALSE);

		// assistiveID
		$this->assistiveID->SetDbValueDef($rsnew, $this->assistiveID->CurrentValue, NULL, FALSE);

		// preEx_illness
		$this->preEx_illness->SetDbValueDef($rsnew, $this->preEx_illness->CurrentValue, NULL, FALSE);

		// illnessID
		$this->illnessID->SetDbValueDef($rsnew, $this->illnessID->CurrentValue, NULL, FALSE);

		// physconditionID
		$this->physconditionID->SetDbValueDef($rsnew, $this->physconditionID->CurrentValue, NULL, FALSE);

		// CreatedBy
		$this->CreatedBy->SetDbValueDef($rsnew, CurrentUserID(), NULL);
		$rsnew['CreatedBy'] = &$this->CreatedBy->DbValue;

		// CreatedDate
		$this->CreatedDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
		$rsnew['CreatedDate'] = &$this->CreatedDate->DbValue;

		// UpdatedBy
		$this->UpdatedBy->SetDbValueDef($rsnew, CurrentUserID(), NULL);
		$rsnew['UpdatedBy'] = &$this->UpdatedBy->DbValue;

		// UpdatedDate
		$this->UpdatedDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
		$rsnew['UpdatedDate'] = &$this->UpdatedDate->DbValue;

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
			$this->supportID->setDbValue($conn->Insert_ID());
			$rsnew['supportID'] = $this->supportID->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
			$this->WriteAuditTrailOnAdd($rsnew);
		}
		return $AddRow;
	}

	// Set up master/detail based on QueryString
	function SetUpMasterParms() {

		// Hide foreign keys
		$sMasterTblVar = $this->getCurrentMasterTable();
		if ($sMasterTblVar == "tbl_pensioner") {
			$this->PensionerID->Visible = FALSE;
			if ($GLOBALS["tbl_pensioner"]->EventCancelled) $this->EventCancelled = TRUE;
		}
		$this->DbMasterFilter = $this->GetMasterFilter(); //  Get master filter
		$this->DbDetailFilter = $this->GetDetailFilter(); // Get detail filter
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_support';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'tbl_support';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['supportID'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $usr = CurrentUserID();
		foreach (array_keys($rs) as $fldname) {
			if ($this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$newvalue = $rs[$fldname];
					else
						$newvalue = "[MEMO]"; // Memo Field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$newvalue = "[XML]"; // XML Field
				} else {
					$newvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $usr, "A", $table, $fldname, $key, "", $newvalue);
			}
		}
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		if (!$this->AuditTrailOnEdit) return;
		$table = 'tbl_support';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['supportID'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $usr = CurrentUserID();
		foreach (array_keys($rsnew) as $fldname) {
			if ($this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_DATE) { // DateTime field
					$modified = (ew_FormatDateTime($rsold[$fldname], 0) <> ew_FormatDateTime($rsnew[$fldname], 0));
				} else {
					$modified = !ew_CompareValue($rsold[$fldname], $rsnew[$fldname]);
				}
				if ($modified) {
					if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) { // Memo field
						if (EW_AUDIT_TRAIL_TO_DATABASE) {
							$oldvalue = $rsold[$fldname];
							$newvalue = $rsnew[$fldname];
						} else {
							$oldvalue = "[MEMO]";
							$newvalue = "[MEMO]";
						}
					} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) { // XML field
						$oldvalue = "[XML]";
						$newvalue = "[XML]";
					} else {
						$oldvalue = $rsold[$fldname];
						$newvalue = $rsnew[$fldname];
					}
					ew_WriteAuditTrail("log", $dt, $id, $usr, "U", $table, $fldname, $key, $oldvalue, $newvalue);
				}
			}
		}
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'tbl_support';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['supportID'];

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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
