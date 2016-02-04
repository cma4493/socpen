<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/22/2016
 * Time: 2:13 PM
 */
class PensionerIDCustom extends DAO
{
    protected function getPensionerID($PayrollID)
    {
        $sql = 'SELECT PensionerID FROM tbl_pension_payroll WHERE PayrollID=:PayrollID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PayrollID',$PayrollID);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['PensionerID'];
    }

    public function getSeniorID($PayrollID)
    {
        $PensionerID = $this->getPensionerID($PayrollID);
        $sql = 'SELECT SeniorID FROM tbl_pensioner WHERE PensionerID=:PensionerID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PensionerID',$PensionerID);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['SeniorID'];
    }
}