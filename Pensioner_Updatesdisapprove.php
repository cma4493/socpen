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
	var $ProjectID = "{6A2D1166-E78D-4185-AF54-9032030AE3DF}";

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
		$hidden = TRUE;
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
						$this->setSuccessMessage("Updates Cancelled"); // Set up success message
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
		$this->_field->setDbValue($rs->fields('field'));
		$this->new_value->setDbValue($rs->fields('new_value'));
		$this->old_value->setDbValue($rs->fields('old_value'));
		$this->dateUpdated->setDbValue($rs->fields('dateUpdated'));
		$this->approved->setDbValue($rs->fields('approved'));
		$this->deathDate->setDbValue($rs->fields('deathDate'));
		$this->paymentmodeID->setDbValue($rs->fields('paymentmodeID'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
		$this->Createdby->setDbValue($rs->fields('Createdby'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->Remarks->setDbValue($rs->fields('Remarks'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->updatesID->DbValue = $row['updatesID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->status->DbValue = $row['status'];
		$this->_field->DbValue = $row['field'];
		$this->new_value->DbValue = $row['new_value'];
		$this->old_value->DbValue = $row['old_value'];
		$this->dateUpdated->DbValue = $row['dateUpdated'];
		$this->approved->DbValue = $row['approved'];
		$this->deathDate->DbValue = $row['deathDate'];
		$this->paymentmodeID->DbValue = $row['paymentmodeID'];
		$this->UpdatedBy->DbValue = $row['UpdatedBy'];
		$this->UpdatedDate->DbValue = $row['UpdatedDate'];
		$this->Createdby->DbValue = $row['Createdby'];
		$this->CreatedDate->DbValue = $row['CreatedDate'];
		$this->Remarks->DbValue = $row['Remarks'];
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
		// field
		// new_value
		// old_value
		// dateUpdated
		// approved
		// deathDate
		// paymentmodeID
		// UpdatedBy
		// UpdatedDate
		// Createdby
		// CreatedDate
		// Remarks

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// updatesID
			$this->updatesID->ViewValue = $this->updatesID->CurrentValue;
			$this->updatesID->ViewCustomAttributes = "";

			// PensionerID
			if (strval($this->PensionerID->CurrentValue) <> "") {
				$sFilterWrk = "`PensionerID`" . ew_SearchString("=", $this->PensionerID->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `PensionerID`, `PensionerID` AS `DispFld`, `lastname` AS `Disp2Fld`, `firstname` AS `Disp3Fld`, `middlename` AS `Disp4Fld` FROM `tbl_pensioner`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->PensionerID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
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

			// status
			if (strval($this->status->CurrentValue) <> "") {
				$sFilterWrk = "`statusID`" . ew_SearchString("=", $this->status->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `statusID`, `status` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_status`";
			$sWhereWrk = "";
			$lookuptblfilter = "`statusID`!='0' AND `statusID`!='1'";
			if (strval($lookuptblfilter) <> "") {
				ew_AddFilter($sWhereWrk, $lookuptblfilter);
			}
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->status, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->status->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->status->ViewValue = $this->status->CurrentValue;
				}
			} else {
				$this->status->ViewValue = NULL;
			}
			$this->status->ViewCustomAttributes = "";

			// field
			$this->_field->ViewValue = $this->_field->CurrentValue;
			$this->_field->ViewCustomAttributes = "";

			// new_value
			$this->new_value->ViewValue = $this->new_value->CurrentValue;
			$this->new_value->ViewCustomAttributes = "";

			// old_value
			$this->old_value->ViewValue = $this->old_value->CurrentValue;
			$this->old_value->ViewCustomAttributes = "";

			// dateUpdated
			$this->dateUpdated->ViewValue = $this->dateUpdated->CurrentValue;
			$this->dateUpdated->ViewValue = ew_FormatDateTime($this->dateUpdated->ViewValue, 5);
			$this->dateUpdated->ViewCustomAttributes = "";

			// approved
			$this->approved->ViewValue = $this->approved->CurrentValue;
			$this->approved->ViewCustomAttributes = "";

			// deathDate
			$this->deathDate->ViewValue = $this->deathDate->CurrentValue;
			$this->deathDate->ViewValue = ew_FormatDateTime($this->deathDate->ViewValue, 5);
			$this->deathDate->ViewCustomAttributes = "";

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

			// UpdatedBy
			$this->UpdatedBy->ViewValue = $this->UpdatedBy->CurrentValue;
			$this->UpdatedBy->ViewCustomAttributes = "";

			// UpdatedDate
			$this->UpdatedDate->ViewValue = $this->UpdatedDate->CurrentValue;
			$this->UpdatedDate->ViewValue = ew_FormatDateTime($this->UpdatedDate->ViewValue, 5);
			$this->UpdatedDate->ViewCustomAttributes = "";

			// Createdby
			$this->Createdby->ViewValue = $this->Createdby->CurrentValue;
			$this->Createdby->ViewCustomAttributes = "";

			// CreatedDate
			$this->CreatedDate->ViewValue = $this->CreatedDate->CurrentValue;
			$this->CreatedDate->ViewValue = ew_FormatDateTime($this->CreatedDate->ViewValue, 5);
			$this->CreatedDate->ViewCustomAttributes = "";

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

			// dateUpdated
			$this->dateUpdated->LinkCustomAttributes = "";
			$this->dateUpdated->HrefValue = "";
			$this->dateUpdated->TooltipValue = "";

			// approved
			$this->approved->LinkCustomAttributes = "";
			$this->approved->HrefValue = "";
			$this->approved->TooltipValue = "";

			// Createdby
			$this->Createdby->LinkCustomAttributes = "";
			$this->Createdby->HrefValue = "";
			$this->Createdby->TooltipValue = "";

			// CreatedDate
			$this->CreatedDate->LinkCustomAttributes = "";
			$this->CreatedDate->HrefValue = "";
			$this->CreatedDate->TooltipValue = "";
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
				$sThisKey .= $row['updatesID'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Disapprove($row); // Delete
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "Pensioner_Updateslist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'Pensioner Updates';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'Pensioner Updates';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['updatesID'];

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
<?php if ($Pensioner_Updates->_field->Visible) { // field ?>
		<td><span id="elh_Pensioner_Updates__field" class="Pensioner_Updates__field"><?php echo $Pensioner_Updates->_field->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->new_value->Visible) { // new_value ?>
		<td><span id="elh_Pensioner_Updates_new_value" class="Pensioner_Updates_new_value"><?php echo $Pensioner_Updates->new_value->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->old_value->Visible) { // old_value ?>
		<td><span id="elh_Pensioner_Updates_old_value" class="Pensioner_Updates_old_value"><?php echo $Pensioner_Updates->old_value->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->dateUpdated->Visible) { // dateUpdated ?>
		<td><span id="elh_Pensioner_Updates_dateUpdated" class="Pensioner_Updates_dateUpdated"><?php echo $Pensioner_Updates->dateUpdated->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->approved->Visible) { // approved ?>
		<td><span id="elh_Pensioner_Updates_approved" class="Pensioner_Updates_approved"><?php echo $Pensioner_Updates->approved->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->Createdby->Visible) { // Createdby ?>
		<td><span id="elh_Pensioner_Updates_Createdby" class="Pensioner_Updates_Createdby"><?php echo $Pensioner_Updates->Createdby->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Pensioner_Updates->CreatedDate->Visible) { // CreatedDate ?>
		<td><span id="elh_Pensioner_Updates_CreatedDate" class="Pensioner_Updates_CreatedDate"><?php echo $Pensioner_Updates->CreatedDate->FldCaption() ?></span></td>
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
<?php if ($Pensioner_Updates->_field->Visible) { // field ?>
		<td<?php echo $Pensioner_Updates->_field->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates__field" class="control-group Pensioner_Updates__field">
<span<?php echo $Pensioner_Updates->_field->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->_field->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->new_value->Visible) { // new_value ?>
		<td<?php echo $Pensioner_Updates->new_value->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_new_value" class="control-group Pensioner_Updates_new_value">
<span<?php echo $Pensioner_Updates->new_value->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->new_value->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Pensioner_Updates->old_value->Visible) { // old_value ?>
		<td<?php echo $Pensioner_Updates->old_value->CellAttributes() ?>>
<span id="el<?php echo $Pensioner_Updates_delete->RowCnt ?>_Pensioner_Updates_old_value" class="control-group Pensioner_Updates_old_value">
<span<?php echo $Pensioner_Updates->old_value->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->old_value->ListViewValue() ?></span>
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
