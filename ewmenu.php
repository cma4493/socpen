<!-- Begin Main Menu -->
<!-- <div class="ewMenu">-->
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(31, $Language->MenuPhrase("31", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(23, $Language->MenuPhrase("23", "MenuText"), "tbl_pensionerlist.php", 31, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}tbl_pensioner'), FALSE);
$RootMenu->AddMenuItem(23, "Pensioner Archive", "tbl_pensionerlist_deleted.php", 31, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}tbl_pensioner'), FALSE);
/*$RootMenu->AddMenuItem(23, "Updates Approval", "Pensioner_Updateslist.php", 31, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}Pensioner_Updates'), FALSE);*/
$RootMenu->AddMenuItem(22, $Language->MenuPhrase("22", "MenuText"), "tbl_pension_payrolllist.php", 31, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}tbl_pension_payroll'), FALSE);
$RootMenu->AddMenuItem(22, "Generate Payroll", "tbl_pension_payrollgen.php", 31, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}tbl_pension_payroll'), FALSE);
$RootMenu->AddMenuItem(22, "Print Payroll", "tbl_pension_payrollprint.php", 31, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}tbl_pension_payroll'), FALSE);
$RootMenu->AddMenuItem(99, "Reports", "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(22, "Dashboard", "tbl_pension_payrollreport.php", 99, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}tbl_pension_payroll'), FALSE);
$RootMenu->AddMenuItem(22, "List of Claimed Payments", "tbl_pension_payrollclaimed.php", 99, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}tbl_pension_payroll'), FALSE);
$RootMenu->AddMenuItem(22, "List of Unclaimed Payments", "tbl_pension_payrollunclaimed.php", 99, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}tbl_pension_payroll'), FALSE);
$RootMenu->AddMenuItem(22, "Summary of Payments", "tbl_pension_payrollclaimed_amount.php", 99, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}tbl_pension_payroll'), FALSE);
$RootMenu->AddMenuItem(32, $Language->MenuPhrase("32", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(34, $Language->MenuPhrase("34", "MenuText"), "", 32, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(15, $Language->MenuPhrase("15", "MenuText"), "lib_regionslist.php", 34, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_regions'), FALSE);
$RootMenu->AddMenuItem(14, $Language->MenuPhrase("14", "MenuText"), "lib_provinceslist.php", 34, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_provinces'), FALSE);
$RootMenu->AddMenuItem(5, $Language->MenuPhrase("5", "MenuText"), "lib_citieslist.php", 34, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_cities'), FALSE);
$RootMenu->AddMenuItem(4, $Language->MenuPhrase("4", "MenuText"), "lib_brgylist.php", 34, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_brgy'), FALSE);
$RootMenu->AddMenuItem(35, $Language->MenuPhrase("35", "MenuText"), "", 32, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(21, $Language->MenuPhrase("21", "MenuText"), "lib_yearlist.php", 35, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_year'), FALSE);
$RootMenu->AddMenuItem(11, $Language->MenuPhrase("11", "MenuText"), "lib_monthlist.php", 35, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_month'), FALSE);
$RootMenu->AddMenuItem(1, $Language->MenuPhrase("1", "MenuText"), "lib_affliationlist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_affliation'), FALSE);
$RootMenu->AddMenuItem(2, $Language->MenuPhrase("2", "MenuText"), "lib_arrangementlist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_arrangement'), FALSE);
$RootMenu->AddMenuItem(3, $Language->MenuPhrase("3", "MenuText"), "lib_assistivelist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_assistive'), FALSE);
$RootMenu->AddMenuItem(6, $Language->MenuPhrase("6", "MenuText"), "lib_civilstatuslist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_civilstatus'), FALSE);
$RootMenu->AddMenuItem(7, $Language->MenuPhrase("7", "MenuText"), "lib_delistreasonlist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_delistreason'), FALSE);
$RootMenu->AddMenuItem(8, $Language->MenuPhrase("8", "MenuText"), "lib_disabilitylist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_disability'), FALSE);
$RootMenu->AddMenuItem(9, $Language->MenuPhrase("9", "MenuText"), "lib_famsupportlist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_famsupport'), FALSE);
$RootMenu->AddMenuItem(10, $Language->MenuPhrase("10", "MenuText"), "lib_illnesslist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_illness'), FALSE);
$RootMenu->AddMenuItem(12, $Language->MenuPhrase("12", "MenuText"), "lib_paymentmodelist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_paymentmode'), FALSE);
$RootMenu->AddMenuItem(13, $Language->MenuPhrase("13", "MenuText"), "lib_physical_conditionlist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_physical_condition'), FALSE);
$RootMenu->AddMenuItem(16, $Language->MenuPhrase("16", "MenuText"), "lib_relationshiplist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_relationship'), FALSE);
$RootMenu->AddMenuItem(17, $Language->MenuPhrase("17", "MenuText"), "lib_rulelist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_rule'), FALSE);
$RootMenu->AddMenuItem(18, $Language->MenuPhrase("18", "MenuText"), "lib_statuslist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_status'), FALSE);
$RootMenu->AddMenuItem(19, $Language->MenuPhrase("19", "MenuText"), "lib_supportlist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_support'), FALSE);
/*$RootMenu->AddMenuItem(20, $Language->MenuPhrase("20", "MenuText"), "lib_utilizationdetaillist.php", 32, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}lib_utilizationdetail'), FALSE);*/
$RootMenu->AddMenuItem(33, $Language->MenuPhrase("33", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(27, $Language->MenuPhrase("27", "MenuText"), "tbl_userlist.php", 33, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}tbl_user'), FALSE);
$RootMenu->AddMenuItem(28, $Language->MenuPhrase("28", "MenuText"), "userlevelpermissionslist.php", 33, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(29, $Language->MenuPhrase("29", "MenuText"), "userlevelslist.php", 33, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(30, $Language->MenuPhrase("30", "MenuText"), "audittraillist.php", 33, "", AllowListMenu('{AC00512B-B959-4ABC-B03E-21192746C63D}audittrail'), FALSE);
$RootMenu->AddMenuItem(-2, $Language->Phrase("ChangePwd"), "changepwd.php", -1, "", IsLoggedIn() && !IsSysAdmin());
$RootMenu->AddMenuItem(-1, $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- </div> -->
<!-- End Main Menu -->
