<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/27/2016
 * Time: 1:35 PM
 */
class EditOtherPensionerDetails extends DAO
{
    public function getPensionerReps($PensionerID,$authID)
    {
        $sql = 'SELECT * FROM tbl_representative WHERE PensionerID=:PensionerID AND authID=:authID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PensionerID',$PensionerID);
        $this->bindQueryParam(':authID',$authID);
        $result = $this->executeQuery();
        $this->closeDB();
        return array(
            'authID' => $result[0]['authID'],
            'fname' => $result[0]['fname'],
            'mname' => $result[0]['mname'],
            'lname' => $result[0]['lname'],
            'relToPensioner' => $result[0]['relToPensioner'],
            'ContactNo' => $result[0]['ContactNo'],
            'auth_Region' => $result[0]['auth_Region'],
            'auth_prov' => $result[0]['auth_prov'],
            'auth_city' => $result[0]['auth_city'],
            'auth_brgy' => $result[0]['auth_brgy'],
            'houseNo' => $result[0]['houseNo']
        );
    }
}