<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$default = NULL; // Initialize page object first

class cdefault {

	// Page ID
	var $PageID = 'default';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Page object name
	var $PageObjName = 'default';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
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

		// User table object (tbl_user)
		if (!isset($GLOBALS["tbl_user"])) $GLOBALS["tbl_user"] = new ctbl_user;

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'default', TRUE);

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

	//
	// Page main
	//
	function Page_Main() {
		global $Security, $Language;
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		$Security->LoadUserLevel(); // Load User Level
		if ($Security->AllowList(CurrentProjectID() . 'tbl_pensioner'))
		$this->Page_Terminate("tbl_pensionerlist.php"); // Exit and go to default page
		if ($Security->AllowList(CurrentProjectID() . 'audittrail'))
			$this->Page_Terminate("audittraillist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_affliation'))
			$this->Page_Terminate("lib_affliationlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_arrangement'))
			$this->Page_Terminate("lib_arrangementlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_assistive'))
			$this->Page_Terminate("lib_assistivelist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_brgy'))
			$this->Page_Terminate("lib_brgylist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_cities'))
			$this->Page_Terminate("lib_citieslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_civilstatus'))
			$this->Page_Terminate("lib_civilstatuslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_delistreason'))
			$this->Page_Terminate("lib_delistreasonlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_disability'))
			$this->Page_Terminate("lib_disabilitylist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_famsupport'))
			$this->Page_Terminate("lib_famsupportlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_illness'))
			$this->Page_Terminate("lib_illnesslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_month'))
			$this->Page_Terminate("lib_monthlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_paymentmode'))
			$this->Page_Terminate("lib_paymentmodelist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_physical_condition'))
			$this->Page_Terminate("lib_physical_conditionlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_provinces'))
			$this->Page_Terminate("lib_provinceslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_regions'))
			$this->Page_Terminate("lib_regionslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_relationship'))
			$this->Page_Terminate("lib_relationshiplist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_rule'))
			$this->Page_Terminate("lib_rulelist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_status'))
			$this->Page_Terminate("lib_statuslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_support'))
			$this->Page_Terminate("lib_supportlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_utilizationdetail'))
			$this->Page_Terminate("lib_utilizationdetaillist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lib_year'))
			$this->Page_Terminate("lib_yearlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'tbl_pension_payroll'))
			$this->Page_Terminate("tbl_pension_payrolllist.php");
		if ($Security->AllowList(CurrentProjectID() . 'tbl_representative'))
			$this->Page_Terminate("tbl_representativelist.php");
		if ($Security->AllowList(CurrentProjectID() . 'tbl_support'))
			$this->Page_Terminate("tbl_supportlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'tbl_updates'))
			$this->Page_Terminate("tbl_updateslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'tbl_user'))
			$this->Page_Terminate("tbl_userlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'userlevelpermissions'))
			$this->Page_Terminate("userlevelpermissionslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'userlevels'))
			$this->Page_Terminate("userlevelslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'Pensioner Updates'))
			$this->Page_Terminate("Pensioner_Updateslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'tbl_user_rito'))
			$this->Page_Terminate("tbl_user_ritolist.php");
		if ($Security->IsLoggedIn()) {
			$this->setFailureMessage($Language->Phrase("NoPermission") . "<br><br><a href=\"logout.php\">" . $Language->Phrase("BackToLogin") . "</a>");
		} else {
			$this->Page_Terminate("login.php"); // Exit and go to login page
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
	// $type = ''|'success'|'failure'
	function Message_Showing(&$msg, $type) {

		// Example:
		//if ($type == 'success') $msg = "your success message";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($default)) $default = new cdefault();

// Page init
$default->Page_Init();

// Page main
$default->Page_Main();
?>
<?php include_once "header.php" ?>
<?php
$default->ShowMessage();
?>
<?php include_once "footer.php" ?>
<?php
$default->Page_Terminate();
?>
