<?php 
class InsertParseDAO extends DAO{
	private $idInc;
	private $InclusionDate;
	private $hh_id;
	private $osca_ID;
	private $PlaceIssued;
	private $DateIssued;
	private $firstname;
	private $middlename;
	private $lastname;
	private $extname;
	private $Birthdate;
	private $sex;
	private $MaritalID;
	private $psgc_region;
	private $psgc_province;
	private $psgc_municipality;
	private $psgc_brgy;
	private $given_add;
	private $codeGen;
	private $currentUserID;
	private $beneStatus;
	private $payment_mode;
	
	// setter
	function setidInc($idInc){
		$this->idInc = $idInc;
	}
	function setInclusionDate($InclusionDate){
		$this->InclusionDate = $InclusionDate;
	}
	function sethh_id($hh_id){
		$this->hh_id = $hh_id;
	}
	function setosca_ID($osca_ID){
		$this->osca_ID = $osca_ID;
	}
	function setPlaceIssued($PlaceIssued){
		$this->PlaceIssued = $PlaceIssued;
	}
	function setDateIssued($DateIssued){
		$this->DateIssued = $DateIssued;
	}
	function setfirstname($firstname){
		$this->firstname = $firstname;
	}
	function setmiddlename($middlename){
		$this->middlename = $middlename;
	}
	function setlastname($lastname){
		$this->lastname = $lastname;
	}
	function setextname($extname){
		$this->extname = $extname;
	}
	function setBirthdate($Birthdate){
		$this->Birthdate = $Birthdate;
	}
	function setsex($sex){
		$this->sex = $sex;
	}
	function setMaritalID($MaritalID){
		$this->MaritalID = $MaritalID;
	}
	function setpsgc_region($psgc_region){
		$this->psgc_region = $psgc_region;
	}
	function setpsgc_province($psgc_province){
		$this->psgc_province = $psgc_province;
	}
	function setpsgc_municipality($psgc_municipality){
		$this->psgc_municipality = $psgc_municipality;
	}
	function setpsgc_brgy($psgc_brgy){
		$this->psgc_brgy = $psgc_brgy;
	}
	function setgiven_add($given_add){
		$this->given_add = $given_add;
	}
	function setcodeGen($codeGen){
		$this->codeGen = $codeGen;
	}
	function setcurrentUserID($currentUserID){
		$this->currentUserID = $currentUserID;
	}
	function setbeneStatus($beneStatus){
		$this->beneStatus = $beneStatus;
	}
	function setpayment_mode($payment_mode){
		$this->payment_mode = $payment_mode;
	}
	
	// getter
	function getidInc(){
		return $this->idInc;
	}
	function getInclusionDate(){
		return $this->InclusionDate;
	}
	function gethh_id(){
		return $this->hh_id;
	}
	function getosca_ID(){
		return $this->osca_ID;
	}
	function getPlaceIssued(){
		return $this->PlaceIssued;
	}
	function getDateIssued(){
		return $this->DateIssued;
	}
	function getfirstname(){
		return $this->firstname;
	}
	function getmiddlename(){
		return $this->middlename;
	}
	function getlastname(){
		return $this->lastname;
	}
	function getextname(){
		return $this->extname;
	}
	function getBirthdate(){
		return $this->Birthdate;
	}
	function getsex(){
		return $this->sex;
	}
	function getMaritalID(){
		return $this->MaritalID;
	}
	function getpsgc_region(){
		return $this->psgc_region;
	}
	function getpsgc_province(){
		return $this->psgc_province;
	}
	function getpsgc_municipality(){
		return $this->psgc_municipality;
	}
	function getpsgc_brgy(){
		return $this->psgc_brgy;
	}
	function getgiven_add(){
		return $this->given_add;
	}
	function getcodeGen(){
		return $this->codeGen;
	}
	function getcurrentUserID(){
		return $this->currentUserID;
	}
	function getbeneStatus(){
		return $this->beneStatus;
	}
	function getpayment_mode(){
		return $this->payment_mode;
	}
	
	public function __construct($hh_id,$firstname,$middlename,$lastname,$extname,$Birthdate,$sex,$psgc_region,$psgc_province,$psgc_municipality,
								$psgc_brgy,$given_add,$codeGen,$currentUserID,$beneStatus,$payment_mode){
		$this->hh_id = $hh_id;
		$this->firstname = $firstname;
		$this->middlename = $middlename;
		$this->lastname = $lastname;
		$this->extname = $extname;
		$this->Birthdate = $Birthdate;
		$this->sex = $sex;
		$this->psgc_region = $psgc_region;
		$this->psgc_province = $psgc_province;
		$this->psgc_municipality = $psgc_municipality;
		$this->psgc_brgy = $psgc_brgy;
		$this->given_add = $given_add;
		$this->codeGen = $codeGen;
		$this->currentUserID = $currentUserID;
		$this->beneStatus = $beneStatus;
		$this->payment_mode = $payment_mode;
	}
	
	function entryChecker($firstname,$middlename,$lastname,$extname,$Birthdate,$sex){
		$sql = "SELECT count(PensionerID) as counterz FROM tbl_pensioner 
				WHERE firstname=:firstname
				AND middlename=:middlename
				AND lastname=:lastname
				AND extname=:extname
				AND Birthdate=:Birthdate
				AND sex=:sex";
		$this->openDB();
		$this->prepareQuery($sql);
		$this->bindQueryParam(':firstname',$firstname);
		$this->bindQueryParam(':middlename',$middlename);
		$this->bindQueryParam(':lastname',$lastname);
		$this->bindQueryParam(':extname',$extname);
		$this->bindQueryParam(':Birthdate',$Birthdate);
		$this->bindQueryParam(':sex',$sex);
		$result = $this->executeQuery();
		$this->closeDB();
		return $result[0]['counterz'];
	}
	
	function _InsertParse(){
		$eskyuel = "
					INSERT INTO tbl_pensioner(
					hh_id,
					firstname,
					middlename,
					lastname,
					extname,
					Birthdate,
					sex,
					psgc_region,
					psgc_province,
					psgc_municipality,
					psgc_brgy,
					given_add,
					codeGen,
					Createdby,
					CreatedDate,
					`Status`,
					paymentmodeID
					)
					VALUES(
					:hh_id,
					:firstname,
					:middlename,
					:lastname,
					:extname,
					:Birthdate,
					:sex,
					:psgc_region,
					:psgc_province,
					:psgc_municipality,
					:psgc_brgy,
					:given_add,
					:codeGen,
					:curretnUserID,
					NOW(),
					:status,
					:payment_mode
					)";
		$this->openDB();
		$this->prepareQuery($eskyuel);
		// $this->bindQueryParam(':PensionerID',$this->getidInc());
		// $this->bindQueryParam(':InclusionDate',$this->getInclusionDate());
		$this->bindQueryParam(':hh_id',$this->gethh_id());
		// $this->bindQueryParam(':osca_ID',$this->getosca_ID());
		// $this->bindQueryParam(':PlaceIssued',$this->getPlaceIssued());
		// $this->bindQueryParam(':DateIssued',$this->getDateIssued());
		$this->bindQueryParam(':firstname',$this->getfirstname());
		$this->bindQueryParam(':middlename',$this->getmiddlename());
		$this->bindQueryParam(':lastname',$this->getlastname());
		$this->bindQueryParam(':extname',$this->getextname());
		$this->bindQueryParam(':Birthdate',$this->getBirthdate());
		$this->bindQueryParam(':sex',$this->getsex());
		// $this->bindQueryParam(':MaritalID',$this->getMaritalID());
		$this->bindQueryParam(':psgc_region',$this->getpsgc_region());
		$this->bindQueryParam(':psgc_province',$this->getpsgc_province());
		$this->bindQueryParam(':psgc_municipality',$this->getpsgc_municipality());
		$this->bindQueryParam(':psgc_brgy',$this->getpsgc_brgy());
		$this->bindQueryParam(':given_add',$this->getgiven_add());
		$this->bindQueryParam(':codeGen',$this->getcodeGen());
		$this->bindQueryParam(':curretnUserID',$this->getcurrentUserID());
		$this->bindQueryParam(':status',$this->getbeneStatus());
		$this->bindQueryParam(':payment_mode',$this->getpayment_mode());
		$this->beginTrans();
		$result = $this->executeUpdate();
		if ($result){
			$this->commitTrans();
			$commitResult = "1";
		} else {
			$this->rollbackTrans();
			$commitResult = "0";
		}
		$this->closeDB();
		return $commitResult;
	}
}
?>