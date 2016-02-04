<?php

/**
 * Created By: JOSEF FRIEDRICH S. BALDO
 * Date & Time: 1/6/2016 1:34 PM
 */
class pensionerotherdetails extends DAO
{
    private $PensionerID;

    public function setPensionerID($PensionerID)
    {
        $this->PensionerID = $PensionerID;
    }

    protected function getPensionerID()
    {
        return $this->PensionerID;
    }

    public function __construct($PensionerID)
    {
        $this->PensionerID = $PensionerID;
    }

    public function checkReps()
    {
        $sql = 'SELECT COUNT(authID) as counter FROM tbl_representative WHERE PensionerID=:PensionerID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PensionerID',$this->getPensionerID());
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['counter'];
    }

    public function checkSupportData()
    {
        $sql = 'SELECT COUNT(supportID) as counter FROM tbl_support WHERE PensionerID=:PensionerID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PensionerID',$this->getPensionerID());
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['counter'];
    }

    public function getRepresentativeData()
    {
        $sql = 'SELECT * FROM tbl_representative WHERE PensionerID=:PensionerID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PensionerID',$this->getPensionerID());
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'authID' => $rowData['authID'],
                'fname' => $rowData['fname'],
                'mname' => $rowData['mname'],
                'lname' => $rowData['lname'],
                'relToPensioner' => $this->getRelationshipDetails($rowData['relToPensioner']),
                'ContactNo' => $rowData['ContactNo'],
                'auth_Region' => $this->getRegionName($rowData['auth_Region']),
                'auth_prov' => $this->getProvName($rowData['auth_prov']),
                'auth_city' => $this->getCityName($rowData['auth_city']),
                'auth_brgy' => $this->getBrgyName($rowData['auth_brgy']),
                'houseNo' => $rowData['houseNo'],
                'PensionerID' => $this->getPensionerName($rowData['PensionerID'])
            );
        }
        return $recordlist;
    }

    public function getSupport()
    {
        $sql = 'SELECT * FROM tbl_support WHERE PensionerID=:PensionerID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PensionerID',$this->getPensionerID());
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'supportID' => $rowData['supportID'],
                'family_support' => $this->yesorno($rowData['family_support']),
                'KindSupID' => $this->getLibSupport($rowData['KindSupID']),
                'meals' => $rowData['meals'],
                'disability' => $this->yesorno($rowData['disability']),
                'disabilityID' => $this->getDisabilityName($rowData['disabilityID']),
                'immobile' => $this->yesorno($rowData['immobile']),
                'assistiveID' => $this->getAssistiveDevice($rowData['assistiveID']),
                'preEx_illness' => $this->yesorno($rowData['preEx_illness']),
                'illnessID' => $this->getIllnessName($rowData['illnessID']),
                'PensionerID' => $this->getPensionerName($rowData['PensionerID']),
                'physconditionID' => $this->getPhysCondition($rowData['physconditionID'])
            );
        }
        return $recordlist;
    }

    /**
     * JFSBALDO: PROTECTED FUNCTIONS (ADDITIONAL DATA)
     */

    protected function getRelationshipDetails($id)
    {
        $sql = 'SELECT Description FROM lib_relationship WHERE RelationID=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['Description'];
    }

    protected function getRegionName($id)
    {
        $sql = 'SELECT region_name FROM lib_regions WHERE region_code=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['region_name'];
    }

    protected function getProvName($id)
    {
        $sql = 'SELECT prov_name FROM lib_provinces WHERE prov_code=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['prov_name'];
    }

    protected function getCityName($id)
    {
        $sql = 'SELECT city_name FROM lib_cities WHERE city_code=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['city_name'];
    }

    protected function getBrgyName($id)
    {
        $sql = 'SELECT brgy_name FROM lib_brgy WHERE brgy_code=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['brgy_name'];
    }

    protected function yesorno($param)
    {
        if ($param == 0)
        {
            return '0 - No';
        }
        elseif ($param == 1)
        {
            return '1 - Yes';
        }
        else
        {
            return 'N/A';
        }
    }

    protected function getLibSupport($id)
    {
        $sql = 'SELECT SupportKind FROM lib_support WHERE SupportID=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['SupportKind'];
    }

    protected function getDisabilityName($id)
    {
        $sql = 'SELECT Description FROM lib_disability WHERE disabilityID=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['Description'];
    }

    protected function getAssistiveDevice($id)
    {
        $sql = 'SELECT Device FROM lib_assistive WHERE assistiveID=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['Device'];
    }

    protected function getIllnessName($id)
    {
        $sql = 'SELECT description FROM lib_illness WHERE illnessID=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['description'];
    }

    protected function getPhysCondition($id)
    {
        $sql = 'SELECT physconditionName FROM lib_physical_condition WHERE physconditionID=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['physconditionName'];
    }

    protected function getPensionerName($id)
    {
        $sql = 'SELECT lastname,firstname,middlename,extname FROM tbl_pensioner WHERE PensionerID=:id';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':id',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['lastname'] . ' ' . $result[0]['firstname'] . ' ' . $result[0]['middlename'] . ' ' . $result[0]['extname'];
    }
}