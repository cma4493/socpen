<?php

/**
 * Created By: JOSEF FRIEDRICH S. BALDO
 * Date & Time: 1/13/2016 9:32 AM
 */
class custom_rep_add extends DAO
{
    public function _executeInsert($fname,$mname,$lname,$relToPensioner,$ContactNo,$auth_Region,$auth_prov,$auth_city,$auth_brgy,$houseNo,$PensionerID,$CreatedBy)
    {
        $sql = 'INSERT INTO tbl_representative(fname, mname, lname, relToPensioner, ContactNo, auth_Region, auth_prov, auth_city, auth_brgy, houseNo, PensionerID, CreatedBy, CreatedDate)
                VALUES
                (
                :fname,
                :mname,
                :lname,
                :relToPensioner,
                :ContactNo,
                :auth_Region,
                :auth_prov,
                :auth_city,
                :auth_brgy,
                :houseNo,
                :PensionerID,
                :CreatedBy,
                NOW()
                )';
        /*echo '<pre>' . $sql . '</pre>';*/
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':fname',$fname);
        $this->bindQueryParam(':mname',$mname);
        $this->bindQueryParam(':lname',$lname);
        $this->bindQueryParam(':relToPensioner',$relToPensioner);
        $this->bindQueryParam(':ContactNo',$ContactNo);
        $this->bindQueryParam(':auth_Region',$auth_Region);
        $this->bindQueryParam(':auth_prov',$auth_prov);
        $this->bindQueryParam(':auth_city',$auth_city);
        $this->bindQueryParam(':auth_brgy',$auth_brgy);
        $this->bindQueryParam(':houseNo',$houseNo);
        $this->bindQueryParam(':PensionerID',$PensionerID);
        $this->bindQueryParam(':CreatedBy',$CreatedBy);
        $this->beginTrans();
        $result = $this->executeUpdate();
        if($result)
        {
            $this->commitTrans();
            $exec_result = 1;
        }
        else
        {
            $this->rollbackTrans();
            $exec_result = 0;
        }
        $this->closeDB();
        return $exec_result;
    }

    public function _executeInsertSupport($family_support, $KindSupID, $meals, $disability, $disabilityID, $immobile, $assistiveID, $preEx_illness, $illnessID, $PensionerID, $CreatedBy, $physconditionID)
    {
        $sql = 'INSERT INTO tbl_support(family_support, KindSupID, meals, disability, disabilityID, immobile, assistiveID, preEx_illness, illnessID, PensionerID, CreatedBy, CreatedDate, physconditionID)
                VALUES(
                :family_support,
                :KindSupID,
                :meals,
                :disability,
                :disabilityID,
                :immobile,
                :assistiveID,
                :preEx_illness,
                :illnessID,
                :PensionerID,
                :CreatedBy,
                NOW(),
                :physconditionID
                )';
        /*echo '<pre>' . $sql . '</pre>';
        echo '<pre>' . $family_support . '</pre>';
        echo '<pre>' . $KindSupID . '</pre>';
        echo '<pre>' . $meals . '</pre>';
        echo '<pre>' . $disability . '</pre>';
        echo '<pre>' . $disabilityID . '</pre>';
        echo '<pre>' . $immobile . '</pre>';
        echo '<pre>' . $assistiveID . '</pre>';
        echo '<pre>' . $preEx_illness . '</pre>';
        echo '<pre>' . $illnessID . '</pre>';
        echo '<pre>' . $PensionerID . '</pre>';
        echo '<pre>' . $CreatedBy . '</pre>';
        echo '<pre>' . $physconditionID . '</pre>';*/
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':family_support',$family_support);
        $this->bindQueryParam(':KindSupID',$KindSupID);
        $this->bindQueryParam(':meals',$meals);
        $this->bindQueryParam(':disability',$disability);
        $this->bindQueryParam(':disabilityID',$disabilityID);
        $this->bindQueryParam(':immobile',$immobile);
        $this->bindQueryParam(':assistiveID',$assistiveID);
        $this->bindQueryParam(':preEx_illness',$preEx_illness);
        $this->bindQueryParam(':illnessID',$illnessID);
        $this->bindQueryParam(':PensionerID',$PensionerID);
        $this->bindQueryParam(':CreatedBy',$CreatedBy);
        $this->bindQueryParam(':physconditionID',$physconditionID);
        $this->beginTrans();
        $result = $this->executeUpdate();
        if($result)
        {
            $this->commitTrans();
            $exec_result = 1;
        }
        else
        {
            $this->rollbackTrans();
            $exec_result = 0;
        }
        $this->closeDB();
        return $exec_result;
    }
}