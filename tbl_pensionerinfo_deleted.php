<?php

// Global variable for table object
$tbl_pensioner = NULL;

//
// Table class for tbl_pensioner
//
class ctbl_pensioner extends cTable {
	var $SeniorID;
	var $PensionerID;
	var $InclusionDate;
	var $hh_id;
	var $osca_ID;
	var $PlaceIssued;
	var $DateIssued;
	var $firstname;
	var $middlename;
	var $lastname;
	var $extname;
	var $Birthdate;
	var $sex;
	var $MaritalID;
	var $affliationID;
	var $psgc_region;
	var $psgc_province;
	var $psgc_municipality;
	var $psgc_brgy;
	var $given_add;
	var $Status;
	var $paymentmodeID;
	var $approved;
	var $approvedby;
	var $DateApproved;
	var $ArrangementID;
	var $is_4ps;
	var $abandoned;
	var $Createdby;
	var $CreatedDate;
	var $UpdatedBy;
	var $UpdatedDate;
	var $UpdateRemarks;
	var $codeGen;
	var $picture;
	var $picturename;
	var $picturetype;
	var $picturewidth;
	var $pictureheight;
	var $picturesize;
	var $hyperlink;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'tbl_pensioner';
		$this->TableName = 'tbl_pensioner';
		$this->TableType = 'TABLE';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// SeniorID
		$this->SeniorID = new cField('tbl_pensioner', 'tbl_pensioner', 'x_SeniorID', 'SeniorID', '`SeniorID`', '`SeniorID`', 19, -1, FALSE, '`SeniorID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->SeniorID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['SeniorID'] = &$this->SeniorID;

		// PensionerID
		$this->PensionerID = new cField('tbl_pensioner', 'tbl_pensioner', 'x_PensionerID', 'PensionerID', '`PensionerID`', '`PensionerID`', 200, -1, FALSE, '`PensionerID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['PensionerID'] = &$this->PensionerID;

		// InclusionDate
		$this->InclusionDate = new cField('tbl_pensioner', 'tbl_pensioner', 'x_InclusionDate', 'InclusionDate', '`InclusionDate`', 'DATE_FORMAT(`InclusionDate`, \'%m/%d/%Y\')', 133, 6, FALSE, '`InclusionDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->InclusionDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['InclusionDate'] = &$this->InclusionDate;

		// hh_id
		$this->hh_id = new cField('tbl_pensioner', 'tbl_pensioner', 'x_hh_id', 'hh_id', '`hh_id`', '`hh_id`', 200, -1, FALSE, '`hh_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['hh_id'] = &$this->hh_id;

		// osca_ID
		$this->osca_ID = new cField('tbl_pensioner', 'tbl_pensioner', 'x_osca_ID', 'osca_ID', '`osca_ID`', '`osca_ID`', 200, -1, FALSE, '`osca_ID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['osca_ID'] = &$this->osca_ID;

		// PlaceIssued
		$this->PlaceIssued = new cField('tbl_pensioner', 'tbl_pensioner', 'x_PlaceIssued', 'PlaceIssued', '`PlaceIssued`', '`PlaceIssued`', 200, -1, FALSE, '`PlaceIssued`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['PlaceIssued'] = &$this->PlaceIssued;

		// DateIssued
		$this->DateIssued = new cField('tbl_pensioner', 'tbl_pensioner', 'x_DateIssued', 'DateIssued', '`DateIssued`', 'DATE_FORMAT(`DateIssued`, \'%m/%d/%Y\')', 135, 6, FALSE, '`DateIssued`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->DateIssued->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['DateIssued'] = &$this->DateIssued;

		// firstname
		$this->firstname = new cField('tbl_pensioner', 'tbl_pensioner', 'x_firstname', 'firstname', '`firstname`', '`firstname`', 200, -1, FALSE, '`firstname`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['firstname'] = &$this->firstname;

		// middlename
		$this->middlename = new cField('tbl_pensioner', 'tbl_pensioner', 'x_middlename', 'middlename', '`middlename`', '`middlename`', 200, -1, FALSE, '`middlename`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['middlename'] = &$this->middlename;

		// lastname
		$this->lastname = new cField('tbl_pensioner', 'tbl_pensioner', 'x_lastname', 'lastname', '`lastname`', '`lastname`', 200, -1, FALSE, '`lastname`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['lastname'] = &$this->lastname;

		// extname
		$this->extname = new cField('tbl_pensioner', 'tbl_pensioner', 'x_extname', 'extname', '`extname`', '`extname`', 200, -1, FALSE, '`extname`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['extname'] = &$this->extname;

		// Birthdate
		$this->Birthdate = new cField('tbl_pensioner', 'tbl_pensioner', 'x_Birthdate', 'Birthdate', '`Birthdate`', 'DATE_FORMAT(`Birthdate`, \'%m/%d/%Y\')', 133, 6, FALSE, '`Birthdate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Birthdate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['Birthdate'] = &$this->Birthdate;

		// sex
		$this->sex = new cField('tbl_pensioner', 'tbl_pensioner', 'x_sex', 'sex', '`sex`', '`sex`', 16, -1, FALSE, '`sex`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->sex->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['sex'] = &$this->sex;

		// MaritalID
		$this->MaritalID = new cField('tbl_pensioner', 'tbl_pensioner', 'x_MaritalID', 'MaritalID', '`MaritalID`', '`MaritalID`', 3, -1, FALSE, '`MaritalID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->MaritalID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['MaritalID'] = &$this->MaritalID;

		// affliationID
		$this->affliationID = new cField('tbl_pensioner', 'tbl_pensioner', 'x_affliationID', 'affliationID', '`affliationID`', '`affliationID`', 3, -1, FALSE, '`affliationID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->affliationID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['affliationID'] = &$this->affliationID;

		// psgc_region
		$this->psgc_region = new cField('tbl_pensioner', 'tbl_pensioner', 'x_psgc_region', 'psgc_region', '`psgc_region`', '`psgc_region`', 21, -1, FALSE, '`psgc_region`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->psgc_region->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['psgc_region'] = &$this->psgc_region;

		// psgc_province
		$this->psgc_province = new cField('tbl_pensioner', 'tbl_pensioner', 'x_psgc_province', 'psgc_province', '`psgc_province`', '`psgc_province`', 21, -1, FALSE, '`psgc_province`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->psgc_province->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['psgc_province'] = &$this->psgc_province;

		// psgc_municipality
		$this->psgc_municipality = new cField('tbl_pensioner', 'tbl_pensioner', 'x_psgc_municipality', 'psgc_municipality', '`psgc_municipality`', '`psgc_municipality`', 21, -1, FALSE, '`psgc_municipality`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->psgc_municipality->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['psgc_municipality'] = &$this->psgc_municipality;

		// psgc_brgy
		$this->psgc_brgy = new cField('tbl_pensioner', 'tbl_pensioner', 'x_psgc_brgy', 'psgc_brgy', '`psgc_brgy`', '`psgc_brgy`', 21, -1, FALSE, '`psgc_brgy`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->psgc_brgy->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['psgc_brgy'] = &$this->psgc_brgy;

		// given_add
		$this->given_add = new cField('tbl_pensioner', 'tbl_pensioner', 'x_given_add', 'given_add', '`given_add`', '`given_add`', 200, -1, FALSE, '`given_add`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['given_add'] = &$this->given_add;

		// Status
		$this->Status = new cField('tbl_pensioner', 'tbl_pensioner', 'x_Status', 'Status', '`Status`', '`Status`', 3, -1, FALSE, '`Status`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Status->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Status'] = &$this->Status;

		// paymentmodeID
		$this->paymentmodeID = new cField('tbl_pensioner', 'tbl_pensioner', 'x_paymentmodeID', 'paymentmodeID', '`paymentmodeID`', '`paymentmodeID`', 3, -1, FALSE, '`paymentmodeID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->paymentmodeID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['paymentmodeID'] = &$this->paymentmodeID;

		// approved
		$this->approved = new cField('tbl_pensioner', 'tbl_pensioner', 'x_approved', 'approved', '`approved`', '`approved`', 16, -1, FALSE, '`approved`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->approved->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['approved'] = &$this->approved;

		// approvedby
		$this->approvedby = new cField('tbl_pensioner', 'tbl_pensioner', 'x_approvedby', 'approvedby', '`approvedby`', '`approvedby`', 3, -1, FALSE, '`approvedby`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->approvedby->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['approvedby'] = &$this->approvedby;

		// DateApproved
		$this->DateApproved = new cField('tbl_pensioner', 'tbl_pensioner', 'x_DateApproved', 'DateApproved', '`DateApproved`', 'DATE_FORMAT(`DateApproved`, \'%m/%d/%Y\')', 135, 6, FALSE, '`DateApproved`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->DateApproved->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['DateApproved'] = &$this->DateApproved;

		// ArrangementID
		$this->ArrangementID = new cField('tbl_pensioner', 'tbl_pensioner', 'x_ArrangementID', 'ArrangementID', '`ArrangementID`', '`ArrangementID`', 3, -1, FALSE, '`ArrangementID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->ArrangementID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['ArrangementID'] = &$this->ArrangementID;

		// is_4ps
		$this->is_4ps = new cField('tbl_pensioner', 'tbl_pensioner', 'x_is_4ps', 'is_4ps', '`is_4ps`', '`is_4ps`', 16, -1, FALSE, '`is_4ps`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->is_4ps->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['is_4ps'] = &$this->is_4ps;

		// abandoned
		$this->abandoned = new cField('tbl_pensioner', 'tbl_pensioner', 'x_abandoned', 'abandoned', '`abandoned`', '`abandoned`', 16, -1, FALSE, '`abandoned`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->abandoned->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['abandoned'] = &$this->abandoned;

		// Createdby
		$this->Createdby = new cField('tbl_pensioner', 'tbl_pensioner', 'x_Createdby', 'Createdby', '`Createdby`', '`Createdby`', 3, -1, FALSE, '`Createdby`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Createdby->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Createdby'] = &$this->Createdby;

		// CreatedDate
		$this->CreatedDate = new cField('tbl_pensioner', 'tbl_pensioner', 'x_CreatedDate', 'CreatedDate', '`CreatedDate`', 'DATE_FORMAT(`CreatedDate`, \'%m/%d/%Y\')', 135, 6, FALSE, '`CreatedDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->CreatedDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['CreatedDate'] = &$this->CreatedDate;

		// UpdatedBy
		$this->UpdatedBy = new cField('tbl_pensioner', 'tbl_pensioner', 'x_UpdatedBy', 'UpdatedBy', '`UpdatedBy`', '`UpdatedBy`', 3, -1, FALSE, '`UpdatedBy`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->UpdatedBy->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['UpdatedBy'] = &$this->UpdatedBy;

		// UpdatedDate
		$this->UpdatedDate = new cField('tbl_pensioner', 'tbl_pensioner', 'x_UpdatedDate', 'UpdatedDate', '`UpdatedDate`', 'DATE_FORMAT(`UpdatedDate`, \'%m/%d/%Y\')', 135, 6, FALSE, '`UpdatedDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->UpdatedDate->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateMDY"));
		$this->fields['UpdatedDate'] = &$this->UpdatedDate;

		// UpdateRemarks
		$this->UpdateRemarks = new cField('tbl_pensioner', 'tbl_pensioner', 'x_UpdateRemarks', 'UpdateRemarks', '`UpdateRemarks`', '`UpdateRemarks`', 200, -1, FALSE, '`UpdateRemarks`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['UpdateRemarks'] = &$this->UpdateRemarks;

		// codeGen
		$this->codeGen = new cField('tbl_pensioner', 'tbl_pensioner', 'x_codeGen', 'codeGen', '`codeGen`', '`codeGen`', 200, -1, FALSE, '`codeGen`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['codeGen'] = &$this->codeGen;

		// picture
		$this->picture = new cField('tbl_pensioner', 'tbl_pensioner', 'x_picture', 'picture', '`picture`', '`picture`', 205, -1, TRUE, '`picture`', FALSE, FALSE, FALSE, 'IMAGE');
		$this->fields['picture'] = &$this->picture;

		// picturename
		$this->picturename = new cField('tbl_pensioner', 'tbl_pensioner', 'x_picturename', 'picturename', '`picturename`', '`picturename`', 200, -1, FALSE, '`picturename`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['picturename'] = &$this->picturename;

		// picturetype
		$this->picturetype = new cField('tbl_pensioner', 'tbl_pensioner', 'x_picturetype', 'picturetype', '`picturetype`', '`picturetype`', 200, -1, FALSE, '`picturetype`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['picturetype'] = &$this->picturetype;

		// picturewidth
		$this->picturewidth = new cField('tbl_pensioner', 'tbl_pensioner', 'x_picturewidth', 'picturewidth', '`picturewidth`', '`picturewidth`', 3, -1, FALSE, '`picturewidth`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->picturewidth->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['picturewidth'] = &$this->picturewidth;

		// pictureheight
		$this->pictureheight = new cField('tbl_pensioner', 'tbl_pensioner', 'x_pictureheight', 'pictureheight', '`pictureheight`', '`pictureheight`', 3, -1, FALSE, '`pictureheight`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->pictureheight->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['pictureheight'] = &$this->pictureheight;

		// picturesize
		$this->picturesize = new cField('tbl_pensioner', 'tbl_pensioner', 'x_picturesize', 'picturesize', '`picturesize`', '`picturesize`', 3, -1, FALSE, '`picturesize`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->picturesize->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['picturesize'] = &$this->picturesize;

		// hyperlink
		$this->hyperlink = new cField('tbl_pensioner', 'tbl_pensioner', 'x_hyperlink', 'hyperlink', '`hyperlink`', '`hyperlink`', 200, -1, FALSE, '`hyperlink`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['hyperlink'] = &$this->hyperlink;
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Current detail table name
	function getCurrentDetailTable() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_DETAIL_TABLE];
	}

	function setCurrentDetailTable($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_DETAIL_TABLE] = $v;
	}

	// Get detail url
	function GetDetailUrl() {

		// Detail url
		$sDetailUrl = "";
		if ($this->getCurrentDetailTable() == "tbl_representative") {
			$sDetailUrl = $GLOBALS["tbl_representative"]->GetListUrl() . "?showmaster=" . $this->TableVar;
			$sDetailUrl .= "&PensionerID=" . $this->PensionerID->CurrentValue;
		}
		if ($this->getCurrentDetailTable() == "tbl_support") {
			$sDetailUrl = $GLOBALS["tbl_support"]->GetListUrl() . "?showmaster=" . $this->TableVar;
			$sDetailUrl .= "&PensionerID=" . $this->PensionerID->CurrentValue;
		}
		if ($this->getCurrentDetailTable() == "tbl_updates") {
			$sDetailUrl = $GLOBALS["tbl_updates"]->GetListUrl() . "?showmaster=" . $this->TableVar;
			$sDetailUrl .= "&PensionerID=" . $this->PensionerID->CurrentValue;
		}
		if ($sDetailUrl == "") {
			$sDetailUrl = "tbl_pensionerlist.php";
		}
		return $sDetailUrl;
	}

	// Table level SQL
	function SqlFrom() { // From
		return "`tbl_pensioner`";
	}

	function SqlSelect() { // Select
		return "SELECT * FROM " . $this->SqlFrom();
	}

	function SqlWhere() { // Where
		$sWhere = "DELETED = 1";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlGroupBy() { // Group By
		return "";
	}

	function SqlHaving() { // Having
		return "";
	}

	function SqlOrderBy() { // Order By
		return "";
	}

	// Check if Anonymous User is allowed
	function AllowAnonymousUser() {
		switch (@$this->PageID) {
			case "add":
			case "register":
			case "addopt":
				return FALSE;
			case "edit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return FALSE;
			case "delete":
				return FALSE;
			case "view":
				return FALSE;
			case "search":
				return FALSE;
			default:
				return FALSE;
		}
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(), $this->SqlGroupBy(),
			$this->SqlHaving(), $this->SqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->SqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		global $conn;
		$cnt = -1;
		if ($this->TableType == 'TABLE' || $this->TableType == 'VIEW') {
			$sSql = "SELECT COUNT(*) FROM" . substr($sSql, 13);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		global $conn;
		$origFilter = $this->CurrentFilter;
		$this->Recordset_Selecting($this->CurrentFilter);
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Update Table
	var $UpdateTable = "`tbl_pensioner`";

	// INSERT statement
	function InsertSQL(&$rs) {
		global $conn;
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		global $conn;
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL) {
		global $conn;

		// Cascade update detail field 'PensionerID'
		if (!is_null($rsold) && (isset($rs['PensionerID']) && $rsold['PensionerID'] <> $rs['PensionerID'])) {
			if (!isset($GLOBALS["tbl_representative"])) $GLOBALS["tbl_representative"] = new ctbl_representative();
			$rscascade = array();
			$rscascade['PensionerID'] = $rs['PensionerID']; 
			$GLOBALS["tbl_representative"]->Update($rscascade, "`PensionerID` = " . ew_QuotedValue($rsold['PensionerID'], EW_DATATYPE_STRING));
		}

		// Cascade update detail field 'PensionerID'
		if (!is_null($rsold) && (isset($rs['PensionerID']) && $rsold['PensionerID'] <> $rs['PensionerID'])) {
			if (!isset($GLOBALS["tbl_support"])) $GLOBALS["tbl_support"] = new ctbl_support();
			$rscascade = array();
			$rscascade['PensionerID'] = $rs['PensionerID']; 
			$GLOBALS["tbl_support"]->Update($rscascade, "`PensionerID` = " . ew_QuotedValue($rsold['PensionerID'], EW_DATATYPE_STRING));
		}

		// Cascade update detail field 'PensionerID'
		if (!is_null($rsold) && (isset($rs['PensionerID']) && $rsold['PensionerID'] <> $rs['PensionerID'])) {
			if (!isset($GLOBALS["tbl_updates"])) $GLOBALS["tbl_updates"] = new ctbl_updates();
			$rscascade = array();
			$rscascade['PensionerID'] = $rs['PensionerID']; 
			$GLOBALS["tbl_updates"]->Update($rscascade, "`PensionerID` = " . ew_QuotedValue($rsold['PensionerID'], EW_DATATYPE_STRING));
		}
		return $conn->Execute($this->UpdateSQL($rs, $where));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET DELETED = '1' WHERE ";
		if ($rs) {
			if (array_key_exists('SeniorID', $rs))
				ew_AddFilter($where, ew_QuotedName('SeniorID') . '=' . ew_QuotedValue($rs['SeniorID'], $this->SeniorID->FldDataType));
		}
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "") {
		global $conn;

		// Cascade delete detail table 'tbl_representative'
		if (!isset($GLOBALS["tbl_representative"])) $GLOBALS["tbl_representative"] = new ctbl_representative();
		$rscascade = array();
		$GLOBALS["tbl_representative"]->Delete($rscascade, "`PensionerID` = " . ew_QuotedValue($rs['PensionerID'], EW_DATATYPE_STRING));

		// Cascade delete detail table 'tbl_support'
		if (!isset($GLOBALS["tbl_support"])) $GLOBALS["tbl_support"] = new ctbl_support();
		$rscascade = array();
		$GLOBALS["tbl_support"]->Delete($rscascade, "`PensionerID` = " . ew_QuotedValue($rs['PensionerID'], EW_DATATYPE_STRING));

		// Cascade delete detail table 'tbl_updates'
		if (!isset($GLOBALS["tbl_updates"])) $GLOBALS["tbl_updates"] = new ctbl_updates();
		$rscascade = array();
		$GLOBALS["tbl_updates"]->Delete($rscascade, "`PensionerID` = " . ew_QuotedValue($rs['PensionerID'], EW_DATATYPE_STRING));
		return $conn->Execute($this->DeleteSQL($rs, $where));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`SeniorID` = @SeniorID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->SeniorID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@SeniorID@", ew_AdjustSql($this->SeniorID->CurrentValue), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "tbl_pensionerlist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "tbl_pensionerlist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("tbl_pensionerview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("tbl_pensionerview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "tbl_pensioneradd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("tbl_pensioneredit.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("tbl_pensioneredit.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("tbl_pensioneradd.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("tbl_pensioneradd.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("tbl_pensionerdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->SeniorID->CurrentValue)) {
			$sUrl .= "SeniorID=" . urlencode($this->SeniorID->CurrentValue);
		} else {
			return "javascript:alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET)) {
			$arKeys[] = @$_GET["SeniorID"]; // SeniorID

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		foreach ($arKeys as $key) {
			if (!is_numeric($key))
				continue;
			$ar[] = $key;
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->SeniorID->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {
		global $conn;

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
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

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
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
				$this->picture->ImageWidth = $this->picturewidth->CurrentValue;
			} else {
				$this->picture->ImageWidth = 0;
			}
			if (!is_null($this->pictureheight->CurrentValue)) {
				$this->picture->ImageHeight = $this->pictureheight->CurrentValue;
			} else {
				$this->picture->ImageHeight = 0;
			}
			$this->picture->ImageAlt = $this->picture->FldAlt();
			$this->picture->ViewValue = "tbl_pensioner_picture_bv.php?" . "SeniorID=" . $this->SeniorID->CurrentValue;
		} else {
			$this->picture->ViewValue = "";
		}
		$this->picture->ViewCustomAttributes = "";

		// picturename
		$this->picturename->ViewValue = $this->picturename->CurrentValue;
		$this->picturename->ViewCustomAttributes = "";

		// picturetype
		$this->picturetype->ViewValue = $this->picturetype->CurrentValue;
		$this->picturetype->ViewCustomAttributes = "";

		// picturewidth
		$this->picturewidth->ViewValue = $this->picturewidth->CurrentValue;
		$this->picturewidth->ViewCustomAttributes = "";

		// pictureheight
		$this->pictureheight->ViewValue = $this->pictureheight->CurrentValue;
		$this->pictureheight->ViewCustomAttributes = "";

		// picturesize
		$this->picturesize->ViewValue = $this->picturesize->CurrentValue;
		$this->picturesize->ViewCustomAttributes = "";

		// hyperlink
		$this->hyperlink->ViewValue = $this->hyperlink->CurrentValue;
		$this->hyperlink->ViewCustomAttributes = "";

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

		// picturename
		$this->picturename->LinkCustomAttributes = "";
		$this->picturename->HrefValue = "";
		$this->picturename->TooltipValue = "";

		// picturetype
		$this->picturetype->LinkCustomAttributes = "";
		$this->picturetype->HrefValue = "";
		$this->picturetype->TooltipValue = "";

		// picturewidth
		$this->picturewidth->LinkCustomAttributes = "";
		$this->picturewidth->HrefValue = "";
		$this->picturewidth->TooltipValue = "";

		// pictureheight
		$this->pictureheight->LinkCustomAttributes = "";
		$this->pictureheight->HrefValue = "";
		$this->pictureheight->TooltipValue = "";

		// picturesize
		$this->picturesize->LinkCustomAttributes = "";
		$this->picturesize->HrefValue = "";
		$this->picturesize->TooltipValue = "";

		// hyperlink
		$this->hyperlink->LinkCustomAttributes = "";
		$this->hyperlink->HrefValue = "";
		$this->hyperlink->TooltipValue = "";

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
	}

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;

		// Write header
		$Doc->ExportTableHeader();
		if ($Doc->Horizontal) { // Horizontal format, write header
			$Doc->BeginExportRow();
			if ($ExportPageType == "view") {
				if ($this->SeniorID->Exportable) $Doc->ExportCaption($this->SeniorID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->InclusionDate->Exportable) $Doc->ExportCaption($this->InclusionDate);
				if ($this->hh_id->Exportable) $Doc->ExportCaption($this->hh_id);
				if ($this->osca_ID->Exportable) $Doc->ExportCaption($this->osca_ID);
				if ($this->PlaceIssued->Exportable) $Doc->ExportCaption($this->PlaceIssued);
				if ($this->DateIssued->Exportable) $Doc->ExportCaption($this->DateIssued);
				if ($this->firstname->Exportable) $Doc->ExportCaption($this->firstname);
				if ($this->middlename->Exportable) $Doc->ExportCaption($this->middlename);
				if ($this->lastname->Exportable) $Doc->ExportCaption($this->lastname);
				if ($this->extname->Exportable) $Doc->ExportCaption($this->extname);
				if ($this->Birthdate->Exportable) $Doc->ExportCaption($this->Birthdate);
				if ($this->sex->Exportable) $Doc->ExportCaption($this->sex);
				if ($this->MaritalID->Exportable) $Doc->ExportCaption($this->MaritalID);
				if ($this->affliationID->Exportable) $Doc->ExportCaption($this->affliationID);
				if ($this->psgc_region->Exportable) $Doc->ExportCaption($this->psgc_region);
				if ($this->psgc_province->Exportable) $Doc->ExportCaption($this->psgc_province);
				if ($this->psgc_municipality->Exportable) $Doc->ExportCaption($this->psgc_municipality);
				if ($this->psgc_brgy->Exportable) $Doc->ExportCaption($this->psgc_brgy);
				if ($this->given_add->Exportable) $Doc->ExportCaption($this->given_add);
				if ($this->Status->Exportable) $Doc->ExportCaption($this->Status);
				if ($this->paymentmodeID->Exportable) $Doc->ExportCaption($this->paymentmodeID);
				if ($this->approved->Exportable) $Doc->ExportCaption($this->approved);
				if ($this->approvedby->Exportable) $Doc->ExportCaption($this->approvedby);
				if ($this->DateApproved->Exportable) $Doc->ExportCaption($this->DateApproved);
				if ($this->ArrangementID->Exportable) $Doc->ExportCaption($this->ArrangementID);
				if ($this->is_4ps->Exportable) $Doc->ExportCaption($this->is_4ps);
				if ($this->abandoned->Exportable) $Doc->ExportCaption($this->abandoned);
				if ($this->Createdby->Exportable) $Doc->ExportCaption($this->Createdby);
				if ($this->CreatedDate->Exportable) $Doc->ExportCaption($this->CreatedDate);
				if ($this->UpdatedBy->Exportable) $Doc->ExportCaption($this->UpdatedBy);
				if ($this->UpdatedDate->Exportable) $Doc->ExportCaption($this->UpdatedDate);
				if ($this->UpdateRemarks->Exportable) $Doc->ExportCaption($this->UpdateRemarks);
				if ($this->codeGen->Exportable) $Doc->ExportCaption($this->codeGen);
				if ($this->picture->Exportable) $Doc->ExportCaption($this->picture);
			} else {
				if ($this->SeniorID->Exportable) $Doc->ExportCaption($this->SeniorID);
				if ($this->PensionerID->Exportable) $Doc->ExportCaption($this->PensionerID);
				if ($this->InclusionDate->Exportable) $Doc->ExportCaption($this->InclusionDate);
				if ($this->hh_id->Exportable) $Doc->ExportCaption($this->hh_id);
				if ($this->osca_ID->Exportable) $Doc->ExportCaption($this->osca_ID);
				if ($this->PlaceIssued->Exportable) $Doc->ExportCaption($this->PlaceIssued);
				if ($this->DateIssued->Exportable) $Doc->ExportCaption($this->DateIssued);
				if ($this->firstname->Exportable) $Doc->ExportCaption($this->firstname);
				if ($this->middlename->Exportable) $Doc->ExportCaption($this->middlename);
				if ($this->lastname->Exportable) $Doc->ExportCaption($this->lastname);
				if ($this->extname->Exportable) $Doc->ExportCaption($this->extname);
				if ($this->Birthdate->Exportable) $Doc->ExportCaption($this->Birthdate);
				if ($this->sex->Exportable) $Doc->ExportCaption($this->sex);
				if ($this->MaritalID->Exportable) $Doc->ExportCaption($this->MaritalID);
				if ($this->affliationID->Exportable) $Doc->ExportCaption($this->affliationID);
				if ($this->psgc_region->Exportable) $Doc->ExportCaption($this->psgc_region);
				if ($this->psgc_province->Exportable) $Doc->ExportCaption($this->psgc_province);
				if ($this->psgc_municipality->Exportable) $Doc->ExportCaption($this->psgc_municipality);
				if ($this->psgc_brgy->Exportable) $Doc->ExportCaption($this->psgc_brgy);
				if ($this->given_add->Exportable) $Doc->ExportCaption($this->given_add);
				if ($this->Status->Exportable) $Doc->ExportCaption($this->Status);
				if ($this->paymentmodeID->Exportable) $Doc->ExportCaption($this->paymentmodeID);
				if ($this->approved->Exportable) $Doc->ExportCaption($this->approved);
				if ($this->approvedby->Exportable) $Doc->ExportCaption($this->approvedby);
				if ($this->DateApproved->Exportable) $Doc->ExportCaption($this->DateApproved);
				if ($this->ArrangementID->Exportable) $Doc->ExportCaption($this->ArrangementID);
				if ($this->is_4ps->Exportable) $Doc->ExportCaption($this->is_4ps);
				if ($this->abandoned->Exportable) $Doc->ExportCaption($this->abandoned);
				if ($this->Createdby->Exportable) $Doc->ExportCaption($this->Createdby);
				if ($this->CreatedDate->Exportable) $Doc->ExportCaption($this->CreatedDate);
				if ($this->UpdatedBy->Exportable) $Doc->ExportCaption($this->UpdatedBy);
				if ($this->UpdatedDate->Exportable) $Doc->ExportCaption($this->UpdatedDate);
				if ($this->UpdateRemarks->Exportable) $Doc->ExportCaption($this->UpdateRemarks);
				if ($this->codeGen->Exportable) $Doc->ExportCaption($this->codeGen);
			}
			$Doc->EndExportRow();
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
				if ($ExportPageType == "view") {
					if ($this->SeniorID->Exportable) $Doc->ExportField($this->SeniorID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->InclusionDate->Exportable) $Doc->ExportField($this->InclusionDate);
					if ($this->hh_id->Exportable) $Doc->ExportField($this->hh_id);
					if ($this->osca_ID->Exportable) $Doc->ExportField($this->osca_ID);
					if ($this->PlaceIssued->Exportable) $Doc->ExportField($this->PlaceIssued);
					if ($this->DateIssued->Exportable) $Doc->ExportField($this->DateIssued);
					if ($this->firstname->Exportable) $Doc->ExportField($this->firstname);
					if ($this->middlename->Exportable) $Doc->ExportField($this->middlename);
					if ($this->lastname->Exportable) $Doc->ExportField($this->lastname);
					if ($this->extname->Exportable) $Doc->ExportField($this->extname);
					if ($this->Birthdate->Exportable) $Doc->ExportField($this->Birthdate);
					if ($this->sex->Exportable) $Doc->ExportField($this->sex);
					if ($this->MaritalID->Exportable) $Doc->ExportField($this->MaritalID);
					if ($this->affliationID->Exportable) $Doc->ExportField($this->affliationID);
					if ($this->psgc_region->Exportable) $Doc->ExportField($this->psgc_region);
					if ($this->psgc_province->Exportable) $Doc->ExportField($this->psgc_province);
					if ($this->psgc_municipality->Exportable) $Doc->ExportField($this->psgc_municipality);
					if ($this->psgc_brgy->Exportable) $Doc->ExportField($this->psgc_brgy);
					if ($this->given_add->Exportable) $Doc->ExportField($this->given_add);
					if ($this->Status->Exportable) $Doc->ExportField($this->Status);
					if ($this->paymentmodeID->Exportable) $Doc->ExportField($this->paymentmodeID);
					if ($this->approved->Exportable) $Doc->ExportField($this->approved);
					if ($this->approvedby->Exportable) $Doc->ExportField($this->approvedby);
					if ($this->DateApproved->Exportable) $Doc->ExportField($this->DateApproved);
					if ($this->ArrangementID->Exportable) $Doc->ExportField($this->ArrangementID);
					if ($this->is_4ps->Exportable) $Doc->ExportField($this->is_4ps);
					if ($this->abandoned->Exportable) $Doc->ExportField($this->abandoned);
					if ($this->Createdby->Exportable) $Doc->ExportField($this->Createdby);
					if ($this->CreatedDate->Exportable) $Doc->ExportField($this->CreatedDate);
					if ($this->UpdatedBy->Exportable) $Doc->ExportField($this->UpdatedBy);
					if ($this->UpdatedDate->Exportable) $Doc->ExportField($this->UpdatedDate);
					if ($this->UpdateRemarks->Exportable) $Doc->ExportField($this->UpdateRemarks);
					if ($this->codeGen->Exportable) $Doc->ExportField($this->codeGen);
					if ($this->picture->Exportable) $Doc->ExportField($this->picture);
				} else {
					if ($this->SeniorID->Exportable) $Doc->ExportField($this->SeniorID);
					if ($this->PensionerID->Exportable) $Doc->ExportField($this->PensionerID);
					if ($this->InclusionDate->Exportable) $Doc->ExportField($this->InclusionDate);
					if ($this->hh_id->Exportable) $Doc->ExportField($this->hh_id);
					if ($this->osca_ID->Exportable) $Doc->ExportField($this->osca_ID);
					if ($this->PlaceIssued->Exportable) $Doc->ExportField($this->PlaceIssued);
					if ($this->DateIssued->Exportable) $Doc->ExportField($this->DateIssued);
					if ($this->firstname->Exportable) $Doc->ExportField($this->firstname);
					if ($this->middlename->Exportable) $Doc->ExportField($this->middlename);
					if ($this->lastname->Exportable) $Doc->ExportField($this->lastname);
					if ($this->extname->Exportable) $Doc->ExportField($this->extname);
					if ($this->Birthdate->Exportable) $Doc->ExportField($this->Birthdate);
					if ($this->sex->Exportable) $Doc->ExportField($this->sex);
					if ($this->MaritalID->Exportable) $Doc->ExportField($this->MaritalID);
					if ($this->affliationID->Exportable) $Doc->ExportField($this->affliationID);
					if ($this->psgc_region->Exportable) $Doc->ExportField($this->psgc_region);
					if ($this->psgc_province->Exportable) $Doc->ExportField($this->psgc_province);
					if ($this->psgc_municipality->Exportable) $Doc->ExportField($this->psgc_municipality);
					if ($this->psgc_brgy->Exportable) $Doc->ExportField($this->psgc_brgy);
					if ($this->given_add->Exportable) $Doc->ExportField($this->given_add);
					if ($this->Status->Exportable) $Doc->ExportField($this->Status);
					if ($this->paymentmodeID->Exportable) $Doc->ExportField($this->paymentmodeID);
					if ($this->approved->Exportable) $Doc->ExportField($this->approved);
					if ($this->approvedby->Exportable) $Doc->ExportField($this->approvedby);
					if ($this->DateApproved->Exportable) $Doc->ExportField($this->DateApproved);
					if ($this->ArrangementID->Exportable) $Doc->ExportField($this->ArrangementID);
					if ($this->is_4ps->Exportable) $Doc->ExportField($this->is_4ps);
					if ($this->abandoned->Exportable) $Doc->ExportField($this->abandoned);
					if ($this->Createdby->Exportable) $Doc->ExportField($this->Createdby);
					if ($this->CreatedDate->Exportable) $Doc->ExportField($this->CreatedDate);
					if ($this->UpdatedBy->Exportable) $Doc->ExportField($this->UpdatedBy);
					if ($this->UpdatedDate->Exportable) $Doc->ExportField($this->UpdatedDate);
					if ($this->UpdateRemarks->Exportable) $Doc->ExportField($this->UpdateRemarks);
					if ($this->codeGen->Exportable) $Doc->ExportField($this->codeGen);
				}
				$Doc->EndExportRow();
			}
			$Recordset->MoveNext();
		}
		$Doc->ExportTableFooter();
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
