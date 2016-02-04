<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php include_once "securimage/securimage.php" ?>
<?php

//
// Page class
//

$login = NULL; // Initialize page object first

class clogin extends ctbl_user {

	// Page ID
	var $PageID = 'login';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Page object name
	var $PageObjName = 'login';

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
		return TRUE;
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

		// Table object (tbl_user)
		if (!isset($GLOBALS["tbl_user"])) {
			$GLOBALS["tbl_user"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_user"];
		}
		if (!isset($GLOBALS["tbl_user"])) $GLOBALS["tbl_user"] = &$this;

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'login', TRUE);

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
	var $Username;
	var $LoginType;

	//
	// Page main
	//
	function Page_Main() {
		$Securimage = new Securimage();
		global $Security, $Language, $UserProfile, $gsFormError;
		global $Breadcrumb;
		$Breadcrumb = new cBreadcrumb;
		$Breadcrumb->Add("login", "<span id=\"ewPageCaption\">" . $Language->Phrase("LoginPage") . "</span>", ew_CurrentUrl());
		$sPassword = "";
		$sLastUrl = $Security->LastUrl(); // Get last URL
		if ($sLastUrl == "")
			$sLastUrl = "index.php";
		if (IsLoggingIn()) {
			$this->Username = @$_SESSION[EW_SESSION_USER_PROFILE_USER_NAME];
			$sPassword = @$_SESSION[EW_SESSION_USER_PROFILE_PASSWORD];
			$this->LoginType = @$_SESSION[EW_SESSION_USER_PROFILE_LOGIN_TYPE];
			$bValidPwd = $Security->ValidateUser($this->Username, $sPassword, FALSE);
			if ($bValidPwd) {
				$_SESSION[EW_SESSION_USER_PROFILE_USER_NAME] = "";
				$_SESSION[EW_SESSION_USER_PROFILE_PASSWORD] = "";
				$_SESSION[EW_SESSION_USER_PROFILE_LOGIN_TYPE] = "";
			}
		} else {
			if (!$Security->IsLoggedIn())
				$Security->AutoLogin();
			$Security->LoadUserLevel(); // Load user level
			$this->Username = ""; // Initialize
			if (@$_POST["username"] <> "") {

				// Setup variables
				$this->Username = ew_RemoveXSS(ew_StripSlashes(@$_POST["username"]));
				$sPassword = ew_RemoveXSS(ew_StripSlashes(@$_POST["password"]));
				$this->LoginType = strtolower(ew_RemoveXSS(@$_POST["type"]));
			}
			if ($this->Username <> "") {
				$bValidate = $this->ValidateForm($this->Username, $sPassword);
				if (!$bValidate)
					$this->setFailureMessage($gsFormError);
				$_SESSION[EW_SESSION_USER_PROFILE_USER_NAME] = $this->Username; // Save login user name
				$_SESSION[EW_SESSION_USER_PROFILE_LOGIN_TYPE] = $this->LoginType; // Save login type
				/**
				 * CAPTCHA VALIDATION JFSBALDO 12142015: CUSTOM VALIDATION, requires user captcha to login
				 * if the entered captcha value is invalid, terminate loading and return to login.php

				if ($Securimage->check($_POST['captcha_code']) == FALSE){
					if ($this->getFailureMessage() == "")
						$this->setFailureMessage("The Captcha you Enter is Invalid"); // Login cancelled
					$this->Page_Terminate($sLastUrl); // Return to last accessed page
				}
				/* ./CAPTCHA VALIDATION */
			} else {
				if ($Security->IsLoggedIn()) {
					if ($this->getFailureMessage() == "")
						$this->Page_Terminate($sLastUrl); // Return to last accessed page
				}
				$bValidate = FALSE;

				// Restore settings
				if (@$_COOKIE[EW_PROJECT_NAME]['Checksum'] == strval(crc32(md5(EW_RANDOM_KEY))))
					$this->Username = ew_Decrypt(@$_COOKIE[EW_PROJECT_NAME]['Username']);
				if (@$_COOKIE[EW_PROJECT_NAME]['AutoLogin'] == "autologin") {
					$this->LoginType = "a";
				} elseif (@$_COOKIE[EW_PROJECT_NAME]['AutoLogin'] == "rememberusername") {
					$this->LoginType = "u";
				} else {
					$this->LoginType = "";
				}
			}
			$bValidPwd = FALSE;
			if ($bValidate) {

				// Call Logging In event
				$bValidate = $this->User_LoggingIn($this->Username, $sPassword);
				if ($bValidate) {
					$bValidPwd = $Security->ValidateUser($this->Username, $sPassword, FALSE); // Manual login
					if (!$bValidPwd) {

						// Password expired, force change password
						if (IsPasswordExpired()) {
							$this->setFailureMessage($Language->Phrase("PasswordExpired"));
							$this->Page_Terminate("changepwd.php");
						}
						if ($this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("InvalidUidPwd")); // Invalid user id/password

					// Password changed date not initialized, set as today
					} elseif ($UserProfile->EmptyPasswordChangedDate()) {
						if ($UserProfile->LoadProfileFromDatabase($this->Username)) {
							$UserProfile->SetValue(EW_USER_PROFILE_LAST_PASSWORD_CHANGED_DATE, ew_StdCurrentDate());
							$UserProfile->SaveProfileToDatabase($this->Username);
							$_SESSION[EW_SESSION_USER_PROFILE] = $UserProfile->ProfileToString(); // Save to session also
						}
					}
				} else {
					if ($this->getFailureMessage() == "")
						$this->setFailureMessage($Language->Phrase("LoginCancelled")); // Login cancelled
				}
			}
		}
		if ($bValidPwd) {

			// Write cookies
			if ($this->LoginType == "a") { // Auto login
				setcookie(EW_PROJECT_NAME . '[AutoLogin]',  "autologin", EW_COOKIE_EXPIRY_TIME); // Set autologin cookie
				setcookie(EW_PROJECT_NAME . '[Username]', ew_Encrypt($this->Username), EW_COOKIE_EXPIRY_TIME); // Set user name cookie
				setcookie(EW_PROJECT_NAME . '[Password]', ew_Encrypt($sPassword), EW_COOKIE_EXPIRY_TIME); // Set password cookie
				setcookie(EW_PROJECT_NAME . '[Checksum]', crc32(md5(EW_RANDOM_KEY)), EW_COOKIE_EXPIRY_TIME);
			} elseif ($this->LoginType == "u") { // Remember user name
				setcookie(EW_PROJECT_NAME . '[AutoLogin]', "rememberusername", EW_COOKIE_EXPIRY_TIME); // Set remember user name cookie
				setcookie(EW_PROJECT_NAME . '[Username]', ew_Encrypt($this->Username), EW_COOKIE_EXPIRY_TIME); // Set user name cookie
				setcookie(EW_PROJECT_NAME . '[Checksum]', crc32(md5(EW_RANDOM_KEY)), EW_COOKIE_EXPIRY_TIME);
			} else {
				setcookie(EW_PROJECT_NAME . '[AutoLogin]', "", EW_COOKIE_EXPIRY_TIME); // Clear auto login cookie
			}
			setcookie(EW_PROJECT_NAME . '[' . EW_USER_PROFILE_SESSION_ID . ']', session_id(), EW_COOKIE_EXPIRY_TIME); // Save current Session ID

			// Call loggedin event
			$this->User_LoggedIn($this->Username);
			$this->WriteAuditTrailOnLogin($this->Username);
			$this->Page_Terminate($sLastUrl); // Return to last accessed URL
		} elseif ($this->Username <> "" && $sPassword <> "") {

			// Call user login error event
			$this->User_LoginError($this->Username, $sPassword);
		}
	}

	//
	// Validate form
	//
	function ValidateForm($usr, $pwd) {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;
		if (trim($usr) == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterUid"));
		}
		if (trim($pwd) == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterPwd"));
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form Custom Validate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	function ValidateIntCus($int){
		if ($int == 1){
			return ew_AddMessage($gsFormError, "Tralala");
		} else {
			return ew_AddMessage($gsFormError, "Tralala2");
		}
	}

	//
	// Write audit trail on login
	//
	function WriteAuditTrailOnLogin($usr) {
		global $Language;
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $Language->Phrase("AuditTrailLogin"), ew_CurrentUserIP(), "", "", "", "");
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

	// User Logging In event
	function User_LoggingIn($usr, &$pwd) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// User Logged In event
	function User_LoggedIn($usr) {

		//echo "User Logged In";
	}

	// User Login Error event
	function User_LoginError($usr, $pwd) {

		//echo "User Login Error";
	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($login)) $login = new clogin();

// Page init
$login->Page_Init();

// Page main
$login->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$login->Page_Render();
?>
<?php // include_once "header.php" ?>
<!--header.php-->

<?php

if (!isset($Language)) {
	include_once "ewcfg10.php";
	include_once "ewshared10.php";
	$Language = new cLanguage();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $Language->ProjectPhrase("BodyTitle") ?></title>
	<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
		<!-- <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css"> -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="assets/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">
		<!-- <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300"> -->
		<link rel="stylesheet" href="assets/css/google.css">
		<link rel="stylesheet" href="assets/css/ace.min.css">
		<link rel="stylesheet" href="assets/css/ace-rtl.min.css">
		<link rel="stylesheet" href="assets/css/ace-skins.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker.min.css">
		<script src="assets/js/ace-extra.min.js"></script>

	<?php
	$pageparts = explode(".",ew_CurrentPage());
	$page = str_replace("TBL_","",str_replace("LIB_","",strtoupper($pageparts[0])));
	?>
	<?php if($page == "DTR_ADJUSTMENTS_FORM"){ ?>
		<script type="text/javascript"
				src="js/jquery.min.js">
		</script>
		<script type="text/javascript"
				src="bootstrap/js/bootstrap.min.js">
		</script>
		<script type="text/javascript"
				src="assets/js/bootstrap-datetimepicker.min.js">
		</script>
	<?php } ?>
	<?php } ?>
	<?php if (@$gsExport == "") { ?>
		<link rel="stylesheet" href="phpcss/jquery.fileupload-ui.css">
	<?php } ?>
	<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
		<link rel="stylesheet" type="text/css" href="<?php echo EW_PROJECT_STYLESHEET_FILENAME ?>">
	<?php if (@$gsExport == "print" && @$_GET["pdf"] == "1" && EW_PDF_STYLESHEET_FILENAME <> "") { ?>
	<link rel="stylesheet" type="text/css" href="<?php echo EW_PDF_STYLESHEET_FILENAME ?>">
	<?php } ?>
		<script type="text/javascript" src="<?php echo ew_jQueryFile("jquery-%v.min.js") ?>"></script>
	<?php } ?>
	<?php if (@$gsExport == "") { ?>
		<script type="text/javascript" src="jqueryfileupload/jquery.ui.widget.js"></script>
		<script type="text/javascript" src="jqueryfileupload/jqueryfileupload.min.js"></script>
		<link href="calendar/calendar.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="calendar/calendar.min.js"></script>
		<script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
		<script type="text/javascript" src="calendar/calendar-setup.js"></script>
		<script type="text/javascript" src="phpjs/ewcalendar.js"></script>
		<script type="text/javascript">
			var EW_LANGUAGE_ID = "<?php echo $gsLanguage ?>";
			var EW_DATE_SEPARATOR = "/" || "/"; // Default date separator
			var EW_DECIMAL_POINT = "<?php echo $DEFAULT_DECIMAL_POINT ?>";
			var EW_THOUSANDS_SEP = "<?php echo $DEFAULT_THOUSANDS_SEP ?>";
			var EW_MAX_FILE_SIZE = <?php echo EW_MAX_FILE_SIZE ?>; // Upload max file size
			var EW_UPLOAD_ALLOWED_FILE_EXT = "gif,jpg,jpeg,bmp,png,doc,xls,pdf,zip,tiff,xlsx,docx,pptx,ppt"; // Allowed upload file extension

			// Ajax settings
			var EW_LOOKUP_FILE_NAME = "ewlookup10.php"; // Lookup file name
			var EW_AUTO_SUGGEST_MAX_ENTRIES = <?php echo EW_AUTO_SUGGEST_MAX_ENTRIES ?>; // Auto-Suggest max entries

			// Common JavaScript messages
			var EW_DISABLE_BUTTON_ON_SUBMIT = true;
			var EW_IMAGE_FOLDER = "phpimages/"; // Image folder
			var EW_UPLOAD_URL = "<?php echo EW_UPLOAD_URL ?>"; // Upload url
			var EW_UPLOAD_THUMBNAIL_WIDTH = <?php echo EW_UPLOAD_THUMBNAIL_WIDTH ?>; // Upload thumbnail width
			var EW_UPLOAD_THUMBNAIL_HEIGHT = <?php echo EW_UPLOAD_THUMBNAIL_HEIGHT ?>; // Upload thumbnail height
			var EW_USE_JAVASCRIPT_MESSAGE = true;
			<?php if (ew_IsMobile()) { ?>
			var EW_IS_MOBILE = true;
			<?php } else { ?>
			var EW_IS_MOBILE = false;
			<?php } ?>
		</script>
	<?php } ?>
	<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
		<script type="text/javascript" src="phpjs/jsrender.min.js"></script>
		<script type="text/javascript" src="phpjs/ewp10.js"></script>
	<?php } ?>
	<?php if (@$gsExport == "") { ?>
		<script type="text/javascript" src="phpjs/userfn10.js"></script>
		<script type="text/javascript">
			<?php echo $Language->ToJSON() ?>
		</script>
		<script type="text/javascript">
			function printPage(id)
			{
				var html="<html>";
				html+= document.getElementById(id).innerHTML;
				html+="</html>";

				var printWin = window.open('','','left=0,top=0,width=800,height=800,toolbar=0,scrollbars=0,status=0');
				printWin.document.write(html);
				printWin.document.close();
				printWin.focus();
				printWin.print();
				printWin.close();
			}
		</script>

	<?php } ?>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<!-- <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="<?php echo ew_ConvertFullUrl("dswd1.ico") ?>">
<link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo ew_ConvertFullUrl("dswd1.ico") ?>"> -->
	<link rel="shortcut icon" href="phpimages/dswdfavicon.png"/>
</head>

<!--/header.php-->


<script type="text/javascript">
	// Write your client script here, no need to add script tags.
</script>
<script type="text/javascript">
	var flogin = new ew_Form("flogin");

	// Validate function
	flogin.Validate = function()
	{
		var fobj = this.Form;
		if (!this.ValidateRequired)
			return true; // Ignore validation
		if (!ew_HasValue(fobj.username))
			return this.OnError(fobj.username, ewLanguage.Phrase("EnterUid"));
		if (!ew_HasValue(fobj.password))
			return this.OnError(fobj.password, ewLanguage.Phrase("EnterPwd"));

		// Call Form Custom Validate event
		if (!this.Form_CustomValidate(fobj)) return false;
		return true;
	}

	// Form_CustomValidate function
	flogin.Form_CustomValidate =
		function(fobj) { // DO NOT CHANGE THIS LINE!

			// Your custom validation code here, return false if invalid.
			return true;
		}

	// Requires js validation
	<?php if (EW_CLIENT_VALIDATE) { ?>
	flogin.ValidateRequired = true;
	<?php } else { ?>
	flogin.ValidateRequired = false;
	<?php } ?>
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $login->ShowPageHeader(); ?>
<?php
$login->ShowMessage();
?>

<!--login form-->

<body class="login-layout">
<div class="main-container">
	<div class="main-content">
		<div class="row">
			<div class="col-sm-10 col-sm-offset-1">
				<div class="login-container">
					<div class="center">
						<h1>
							<i class="icon-gear white"></i>
							<span class="red"><?php echo $Language->ProjectPhrase("bodytitle") ?></span>
						</h1>
						<h6 class="blue">&copy;Powered by:&nbsp;<span class="white">TEAM SOCPEN</span></h6>
					</div>

					<div class="space-6"></div>

					<div class="position-relative">
						<div id="login-box" class="login-box visible widget-box no-border">
							<div class="widget-body">
								<div class="widget-main">
									<h4 class="header blue lighter bigger">
										<i class="icon-coffee green"></i>
										Please Enter Your Information
									</h4>

									<div class="space-6"></div>

									<!--<form>-->
									<form name="flogin" id="flogin" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
										<fieldset>
											<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="text" name="username" id="username" class="form-control" value="<?php echo $login->Username ?>" placeholder="<?php echo $Language->Phrase("Username") ?>">
															<i class="icon-user"></i>
														</span>
											</label>

											<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" name="password" id="password" class="form-control" placeholder="<?php echo $Language->Phrase("Password") ?>">
															<i class="icon-lock"></i>
														</span>
											</label>

											<div class="space"></div>
											<!--<img id="captcha" src="securimage/securimage_show.php" alt="CAPTCHA Image" />
											<input type="text" name="captcha_code" size="10" maxlength="6" />
											<a href="#" onclick="document.getElementById('captcha').src = 'securimage/securimage_show.php?' + Math.random(); return false">[ Load Different Image ]</a>-->

											<div class="clearfix">
												<!-- <label class="inline">
													<!--<input type="checkbox" class="ace" />
                                                    <span class="lbl"> Remember Me</span>
													<input type="radio" name="type" id="type" value="a"<?php if ($login->LoginType == "a") { ?> checked="checked"<?php } ?>>
													<span class="lbl"><?php echo $Language->Phrase("AutoLogin") ?></span>
													<br /><input type="radio" name="type" id="type" value="u"<?php if ($login->LoginType == "u") { ?>  checked="checked"<?php } ?>>
													<span class="lbl"><?php echo $Language->Phrase("SaveUserName") ?></span>
													<br /><input type="radio" name="type" id="type" value=""<?php if ($login->LoginType == "") { ?> checked="checked"<?php } ?>>
													<span class="lbl"><?php echo $Language->Phrase("AlwaysAsk") ?></span>
												</label> -->

												<button class="width-35 pull-right btn btn-sm btn-primary" name="btnsubmit" id="btnsubmit" type="submit">
													<i class="icon-key"></i>
													Login
												</button>
											</div>

											<div class="space-4"></div>
										</fieldset>
									</form>

								</div><!-- /widget-main -->

								<div class="toolbar clearfix">
									<div>
										<a href="forgotpwd.php" class="forgot-password-link">
											<i class="icon-arrow-left"></i>
											I forgot my password
										</a>
									</div>

									<div>
										<a href="register.php" class="user-signup-link">
											I want to register
											<i class="icon-arrow-right"></i>
										</a>
									</div>
								</div>
							</div><!-- /widget-body -->
						</div><!-- /login-box -->
					</div><!-- /position-relative -->
				</div>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div>

	<div id="ewMsgBox" class="modal fade" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<div class="table-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							<span class="white">&times;</span>
						</button>
						Alert!
					</div>
				</div>
				<div class="modal-body no-padding">
					<div id="ewTooltip"></div>
				</div>
				<div class="modal-footer no-margin-top">
					<button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
						<i class="icon-remove"></i>
						<?php echo $Language->Phrase("MessageOK") ?>
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>

</div><!-- /.main-container -->
</body><!--/body-->

<!--/login form-->

<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
	<?php ew_Reflow(); ?>
<?php } ?>
</script>
<?php
$login->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your startup script here
// document.write("page loaded");

</script>
<?php //include_once "footer2.php" ?>

<!--footer.php-->

<script type="text/javascript">
	if("ontouchend" in document) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>
<script src="assets/js/bootstrap.min.js"></script>
<!-- ace scripts -->
<script src="assets/js/ace-elements.min.js"></script>
<script src="assets/js/ace.min.js"></script>
<script src="assets/js/typeahead-bs2.min.js"></script>
<!-- page specific plugin scripts -->
<script src="assets/js/jquery.dataTables.min.js"></script>
<script src="assets/js/jquery.dataTables.bootstrap.js"></script>
</body>
</html>

<!--/footer.php-->
<?php // include_once "footer.php" ?>
<?php
$login->Page_Terminate();
?>
