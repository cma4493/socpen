<?php

/**
 * Created By: JOSEF FRIEDRICH S. BALDO
 * Date & Time: 1/13/2016 12:42 PM
 */
class payrollGeneration extends DAO
{

    public function checkifExisting($region = NULL,$province = NULL,$municipality = NULL,$barangay = NULL)
    {
        if ($province <> '')
        {
            $where_province = ' AND psgc_province = "'.$province.'" ';
        }
        if ($municipality <> '')
        {
            $where_municipality = ' AND psgc_municipality = "'.$municipality.'" ';
        }
        if ($barangay <> '')
        {
            $where_barangay = ' AND psgc_brgy = "'.$barangay.'" ';
        }
        define("PAYMENT_MODE_ON",TRUE,TRUE); // set to TRUE to turn on validation for payment mode
        if(PAYMENT_MODE_ON)
        {
            $where_paymentMode = ' AND paymentmodeID <> ""';
        }
        else
        {
            $where_paymentMode = '';
        }
        $sql = 'SELECT COUNT(PensionerID) AS counter FROM tbl_pensioner WHERE PensionerID <> ""  AND TIMESTAMPDIFF(YEAR,Birthdate,CURDATE()) >= "60" AND psgc_region = :region' . $where_province . $where_municipality . $where_barangay . $where_paymentMode;
        /*echo '<pre>' . $sql . '</pre>';*/
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':region',$region);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['counter'];
    }

    public function getPensionerAged60($region = NULL,$province = NULL,$municipality = NULL,$barangay = NULL)
    {
        if ($province <> '')
        {
            $where_province = ' AND psgc_province = "'.$province.'" ';
        }
        if ($municipality <> '')
        {
            $where_municipality = ' AND psgc_municipality = "'.$municipality.'" ';
        }
        if ($barangay <> '')
        {
            $where_barangay = ' AND psgc_brgy = "'.$barangay.'" ';
        }
        define("PAYMENT_MODE_ON",TRUE,TRUE); // set to TRUE to turn on validation for payment mode
        if(PAYMENT_MODE_ON)
        {
            $where_paymentMode = ' AND paymentmodeID <> ""';
        }
        else
        {
            $where_paymentMode = '';
        }
        $sql = 'SELECT PensionerID,paymentmodeID,TIMESTAMPDIFF(YEAR,Birthdate,CURDATE()) as `age` FROM tbl_pensioner WHERE PensionerID <> ""  AND TIMESTAMPDIFF(YEAR,Birthdate,CURDATE()) >= "60" AND psgc_region = :region' . $where_province . $where_municipality . $where_barangay . $where_paymentMode;
        /*echo '<pre>' . $sql . '</pre>';*/
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':region',$region);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $index => $rowData)
        {
            $recordlist[$index] = array(
                'PensionerID' => $rowData['PensionerID'],
                'paymentmodeID' => $rowData['paymentmodeID'],
                'age' => $rowData['age']
            );
        }
        return $recordlist;
    }

    public function savePayroll($PayrollYear,$cMonth,$amount,$PensionerID,$paymentmodeID,$Createdby)
    {
        $sql = 'INSERT INTO tbl_pension_payroll(PayrollYear, cMonth, amount, PensionerID, paymentmodeID, Createdby, CreatedDate)
                VALUES
                (
                :PayrollYear,
                :cMonth,
                :amount,
                :PensionerID,
                :paymentmodeID,
                :Createdby,
                NOW()
                )';
        /*echo '<pre>' . $sql . '</pre>';*/
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PayrollYear',$PayrollYear);
        $this->bindQueryParam(':cMonth',$cMonth);
        $this->bindQueryParam(':amount',$amount);
        $this->bindQueryParam(':PensionerID',$PensionerID);
        $this->bindQueryParam(':paymentmodeID',$paymentmodeID);
        $this->bindQueryParam(':Createdby',$Createdby);
        $this->beginTrans();
        $result = $this->executeUpdate();
        if($result)
        {
            $this->commitTrans();
            $exe_result = 1;
        }
        else
        {
            $this->rollbackTrans();
                $exe_result = 0;
        }
        $this->closeDB();
        return $exe_result;
    }
}