<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/21/2016
 * Time: 3:16 PM
 */
class SummaryBeneficiary extends DAO
{
    private $region;
    private $province;
    private $municipality;
    private $barangay;
    private $quarter;
    private $year;
    private $paid_upaid;

    protected function getregion()
    {
        return $this->region;
    }
    protected function getprovince()
    {
        return $this->province;
    }
    protected function getmunicipality()
    {
        return $this->municipality;
    }
    protected function getbarangay()
    {
        return $this->barangay;
    }
    protected function getQuarter()
    {
        return $this->quarter;
    }
    protected function getYear()
    {
        return $this->year;
    }
    protected function getpaid_unpaid()
    {
        return $this->paid_upaid;
    }

    public function __construct($region = null,$province = null,$municipality = null,$barangay = null,$quarter = null,$year = null,$paid_upaid = null)
    {
        $this->region = $region;
        $this->province = $province;
        $this->municipality = $municipality;
        $this->barangay = $barangay;
        $this->quarter = $quarter;
        $this->year = $year;
        $this->paid_upaid = $paid_upaid;
    }

    protected function getSumPaid()
    {
        $quarter = $this->getQuarter();
        if($quarter == 1)
        {
            $month1 = 1;
            $month2 = 3;
        }
        elseif($quarter == 2)
        {
            $month1 = 4;
            $month2 = 6;
        }
        elseif($quarter == 3)
        {
            $month1 = 7;
            $month2 = 9;
        }
        elseif($quarter == 4)
        {
            $month1 = 10;
            $month2 = 12;
        }
        $sql = 'SELECT SUM(amount) as total_amt FROM tbl_pension_payroll WHERE Claimed = 1 AND cMonth >= :month1 AND cMonth <= :month2 AND PayrollYear = :payrollyear';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':month1',$month1);
        $this->bindQueryParam(':month2',$month2);
        $this->bindQueryParam(':payrollyear',$this->getYear());
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['total_amt'];
    }

    protected function getSumUnpaid()
    {
        $quarter = $this->getQuarter();
        if($quarter == 1)
        {
            $month1 = 1;
            $month2 = 3;
        }
        elseif($quarter == 2)
        {
            $month1 = 4;
            $month2 = 6;
        }
        elseif($quarter == 3)
        {
            $month1 = 7;
            $month2 = 9;
        }
        elseif($quarter == 4)
        {
            $month1 = 10;
            $month2 = 12;
        }
        $sql = 'SELECT SUM(amount) as total_amt FROM tbl_pension_payroll WHERE Claimed = 0 AND cMonth >= :month1 AND cMonth <= :month2 AND PayrollYear = :payrollyear';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':month1',$month1);
        $this->bindQueryParam(':month2',$month2);
        $this->bindQueryParam(':payrollyear',$this->getYear());
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['total_amt'];
    }

    public function getPensionerByPsgc()
    {
        if ($this->getregion() <> '')
        {
            $region_where = ' WHERE psgc_region = :region';
        }
        else
        {
            $limit_where = ' LIMIT 1';
        }
        if ($this->getprovince() <> '')
        {
            $province_where = ' AND psgc_province = :province';
        }
        if ($this->getmunicipality() <> '')
        {
            $municipality_where = ' AND psgc_municipality = :municipality';
        }
        if ($this->getbarangay() <> '')
        {
            $barangay_where = ' AND psgc_brgy = :barangay';
        }
        $sql = 'SELECT PensionerID,TIMESTAMPDIFF(YEAR,Birthdate,CURDATE()) as `age` FROM tbl_pensioner'.$region_where.$province_where.$municipality_where.$barangay_where.' ORDER BY PensionerID ASC'.$limit_where;
        /*echo '<pre>' . $sql . '</pre>';*/
        $this->openDB();
        $this->prepareQuery($sql);
        if ($this->getregion() <> '')
        {
            $this->bindQueryParam(':region',$this->getregion());
        }
        if ($this->getprovince() <> '')
        {
            $this->bindQueryParam(':province',$this->getprovince());
        }
        if ($this->getmunicipality() <> '')
        {
            $this->bindQueryParam(':municipality',$this->getmunicipality());
        }
        if ($this->getbarangay() <> '')
        {
            $this->bindQueryParam(':barangay',$this->getbarangay());
        }
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = $rowData['PensionerID'];
        }
        return $recordlist;
    }

    public function checkifExisting()
    {
        $pensionersRaw = $this->getPensionerByPsgc();
        $unformatData = "";
        foreach($pensionersRaw as $pensionerID)
        {
            $unformatData .= '"' . $pensionerID . '",';
        }
        $formattedIds = substr($unformatData,0,-1);
        $quarter = $this->getQuarter();
        $paid_unpaid = $this->getpaid_unpaid();
        if($quarter == 1)
        {
            $month1 = 1;
            $month2 = 3;
        }
        elseif($quarter == 2)
        {
            $month1 = 4;
            $month2 = 6;
        }
        elseif($quarter == 3)
        {
            $month1 = 7;
            $month2 = 9;
        }
        elseif($quarter == 4)
        {
            $month1 = 10;
            $month2 = 12;
        }
        if ($paid_unpaid == 1)
        {
            $operator = '=';
        }
        elseif ($paid_unpaid == 0)
        {
            $operator = '<';
        }
        $sql = 'SELECT COUNT(PensionerID) as counter,SUM(Claimed) as fully_claimed FROM tbl_pension_payroll WHERE PensionerID IN ('.$formattedIds.') AND cMonth >= :month1 AND cMonth <= :month2 AND PayrollYear = :payrollyear GROUP BY PensionerID HAVING SUM(Claimed) '.$operator.' 3';
        /*echo '<pre>'.$sql.'</pre>';*/
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':month1',$month1);
        $this->bindQueryParam(':month2',$month2);
        $this->bindQueryParam(':payrollyear',$this->getYear());
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['counter'];
    }

    protected function getFullyPaidBeneficiary()
    {
        $pensionersRaw = $this->getPensionerByPsgc();
        $unformatData = "";
        foreach($pensionersRaw as $pensionerID)
        {
            $unformatData .= '"' . $pensionerID . '",';
        }
        $formattedIds = substr($unformatData,0,-1);
        $quarter = $this->getQuarter();
        $paid_unpaid = $this->getpaid_unpaid();
        if($quarter == 1)
        {
            $month1 = 1;
            $month2 = 3;
        }
        elseif($quarter == 2)
        {
            $month1 = 4;
            $month2 = 6;
        }
        elseif($quarter == 3)
        {
            $month1 = 7;
            $month2 = 9;
        }
        elseif($quarter == 4)
        {
            $month1 = 10;
            $month2 = 12;
        }
        if ($paid_unpaid == 1)
        {
            $operator = '=';
        }
        elseif ($paid_unpaid == 0)
        {
            $operator = '<';
        }
        $sql = 'SELECT PensionerID,SUM(Claimed) as fully_claimed ,SUM(amount) as total_amt FROM tbl_pension_payroll WHERE PensionerID IN ('.$formattedIds.') AND cMonth >= :month1 AND cMonth <= :month2 AND PayrollYear = :payrollyear GROUP BY PensionerID HAVING SUM(Claimed) '.$operator.' 3';
        /*echo '<pre>'.$sql.'</pre>';*/
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':month1',$month1);
        $this->bindQueryParam(':month2',$month2);
        $this->bindQueryParam(':payrollyear',$this->getYear());
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'PensionerID' => $rowData['PensionerID'],
                'name' => $this->pensionerName($rowData['PensionerID']),
                'fully_claimed' => $rowData['fully_claimed'],
                'total_amt' => $rowData['total_amt'],
                'quarter' => $this->quarterName($quarter),
                'year' => $this->getYear()
            );
        }
        return $recordlist;
    }

    protected function quarterName($quarter)
    {
        $quarter = $this->getQuarter();
        switch($quarter)
        {
            case 1:
                $text = '1st Quarter';
                break;
            case 2:
                $text = '2nd Quarter';
                break;
            case 3:
                $text = '3rd Quarter';
                break;
            case 4:
                $text = '4th Quarter';
                break;
            default:
                $text = 'N/A';
                break;
        }
        return $text;
    }

    protected function pensionerName($PensionerID)
    {
        $sql = 'SELECT lastname,firstname,middlename,extname FROM tbl_pensioner WHERE PensionerID=:PensionerID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PensionerID',$PensionerID);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['lastname'] . ' ' . $result[0]['firstname'] . ' ' . $result[0]['middlename'] . ' ' . $result[0]['extname'];
    }

    public function renderHtml($class = NULL,$name_id = NULL,$border = NULL)
    {
        $total_amount = '';
        $html = '';
        $html .= '<table class="'.$class.'" name="'.$name_id.'" id="'.$name_id.'" border="'.$border.'px">
                <thead>
                <tr>
                    <th>PensionerID</th>
                    <th>Name</th>
                    <th>Total Amount Received</th>
                    <th>Quarter</th>
                    <th>Year</th>
                </tr>
                </thead>
                <tbody>';
        foreach($this->getFullyPaidBeneficiary() as $beneData){
            $html .= '<tr>
                    <td>'.$beneData['PensionerID'].'</td>
                    <td>'.$beneData['name'].'</td>
                    <td>Php'.number_format($beneData['total_amt'],2).'</td>
                    <td>'.$beneData['quarter'].'</td>
                    <td>'.$beneData['year'].'</td>
                </tr>';
            $total_amount += $beneData['total_amt'];
        }
        $html .= '<tr><td colspan="2"><strong><span style="font-size: x-large;">Total Amount</span></strong></td><td colspan="3"><strong><span style="font-size: x-large;">Php'.number_format($total_amount,2).'</span></strong></td></tr>';
               $html .= '</tbody>
            </table>';
        return $html;
    }

    public function getTotalBenefitsReceived($PensionerID)
    {
        $sql = 'SELECT SUM(amount) as total_amt FROM tbl_pension_payroll WHERE Claimed = 1 AND PensionerID=:PensionerID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PensionerID',$PensionerID);
        $result = $this->executeQuery();
        $this->closeDB();
        return 'Php' . number_format($result[0]['total_amt'],2);
    }

    public function getTotalBenefitsToBeReceived($PensionerID)
    {
        $sql = 'SELECT SUM(amount) as total_amt FROM tbl_pension_payroll WHERE Claimed = 0 AND PensionerID=:PensionerID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PensionerID',$PensionerID);
        $result = $this->executeQuery();
        $this->closeDB();
        return 'Php' . number_format($result[0]['total_amt'],2);
    }
}