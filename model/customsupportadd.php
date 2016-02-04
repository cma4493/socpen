<?php

/**
 * Created By: JOSEF FRIEDRICH S. BALDO
 * Date & Time: 1/7/2016 2:57 PM
 */
class customsupportadd extends DAO
{
    private $lib_support;
    private $lib_disability;
    private $lib_assistive;
    private $lib_illness;
    private $lib_physical_condition;
    private $lib_relationship;

    public function paramGetter($lib_support,$lib_disability,$lib_assistive,$lib_illness,$lib_physical_condition,$lib_relationship)
    {
        $this->lib_support = $lib_support;
        $this->lib_disability = $lib_disability;
        $this->lib_assistive = $lib_assistive;
        $this->lib_illness = $lib_illness;
        $this->lib_physical_condition = $lib_physical_condition;
        $this->lib_relationship = $lib_relationship;
    }

    protected function getlib_support()
    {
        return $this->lib_support;
    }

    protected function getlib_disability()
    {
        return $this->lib_disability;
    }

    protected function getlib_assistive()
    {
        return $this->lib_assistive;
    }

    protected function getlib_illness()
    {
        return $this->lib_illness;
    }

    protected function getlib_physical_condition()
    {
        return $this->lib_physical_condition;
    }

    protected function getlib_relationship()
    {
        return $this->lib_relationship;
    }

    protected function getKindSupport()
    {
        $sql = 'SELECT * FROM lib_support';
        $this->openDB();
        $this->prepareQuery($sql);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'SupportID' => $rowData['SupportID'],
                'SupportKind' => $rowData['SupportKind']
            );
        }
        return $recordlist;
    }

    protected function getLibDisability()
    {
        $sql = 'SELECT * FROM lib_disability';
        $this->openDB();
        $this->prepareQuery($sql);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'disabilityID' => $rowData['disabilityID'],
                'Description' => $rowData['Description']
            );
        }
        return $recordlist;
    }

    protected function getLibAssistiveDevice()
    {
        $sql = 'SELECT * FROM lib_assistive';
        $this->openDB();
        $this->prepareQuery($sql);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'assistiveID' => $rowData['assistiveID'],
                'Device' => $rowData['Device']
            );
        }
        return $recordlist;
    }

    protected function getLibIllness()
    {
        $sql = 'SELECT * FROM lib_illness';
        $this->openDB();
        $this->prepareQuery($sql);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'illnessID' => $rowData['illnessID'],
                'description' => $rowData['description']
            );
        }
        return $recordlist;
    }

    protected function getLibPhysicalCondition()
    {
        $sql = 'SELECT * FROM lib_physical_condition WHERE DELETED=0';
        $this->openDB();
        $this->prepareQuery($sql);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'physconditionID' => $rowData['physconditionID'],
                'physconditionName' => $rowData['physconditionName']
            );
        }
        return $recordlist;
    }

    protected function getLibRelationship()
    {
        $sql = 'SELECT * FROM lib_relationship';
        $this->openDB();
        $this->prepareQuery($sql);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'RelationID' => $rowData['RelationID'],
                'Description' => $rowData['Description']
            );
        }
        return $recordlist;
    }

    protected function selectoptionKindSupport($name_id) // jb_kindsupport
    {
        $html = '<select id="'.$name_id.'" name="'.$name_id.'">';
        $html .= '<option value="" selected>Please Select</option>';
        foreach($this->getKindSupport() as $SupportData){
            $html .= '<option value="' . $SupportData['SupportID'] . '">' . $SupportData['SupportKind'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    protected function selectoptionlibdisability($name_id) //jb_disability
    {
        $html = '<select id="'.$name_id.'" name="'.$name_id.'">';
        $html .= '<option value="" selected>Please Select</option>';
        foreach($this->getLibDisability() as $SupportData){
            $html .= '<option value="' . $SupportData['disabilityID'] . '">' . $SupportData['Description'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    protected function selectoptionlibassistive($name_id) // jb_assistive
    {
        $html = '<select id="'.$name_id.'" name="'.$name_id.'">';
        $html .= '<option value="" selected>Please Select</option>';
        foreach($this->getLibAssistiveDevice() as $SupportData){
            $html .= '<option value="' . $SupportData['assistiveID'] . '">' . $SupportData['Device'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    protected function selectoptionlibillness($name_id) // jb_illness
    {
        $html = '<select id="'.$name_id.'" name="'.$name_id.'">';
        $html .= '<option value="" selected>Please Select</option>';
        foreach($this->getLibIllness() as $SupportData){
            $html .= '<option value="' . $SupportData['illnessID'] . '">' . $SupportData['description'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    protected function selectoptionlibphyscondition($name_id) // jb_physicalcondition
    {
        $html = '<select id="'.$name_id.'" name="'.$name_id.'">';
        $html .= '<option value="" selected>Please Select</option>';
        foreach($this->getLibPhysicalCondition() as $SupportData){
            $html .= '<option value="' . $SupportData['physconditionID'] . '">' . $SupportData['physconditionName'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    protected function selectoptionlibrelationship($name_id) // jb_lib_relationship
    {
        $html = '<select id="'.$name_id.'" name="'.$name_id.'">';
        $html .= '<option value="" selected>Please Select</option>';
        foreach($this->getLibRelationship() as $SupportData){
            $html .= '<option value="' . $SupportData['RelationID'] . '">' . $SupportData['Description'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public function inputoptions()
    {
        return array(
            'lib_support' => $this->selectoptionKindSupport($this->getlib_support()),
            'lib_disability' => $this->selectoptionlibdisability($this->getlib_disability()),
            'lib_assistive' => $this->selectoptionlibassistive($this->getlib_assistive()),
            'lib_illness' => $this->selectoptionlibillness($this->getlib_illness()),
            'lib_physical_condition' => $this->selectoptionlibphyscondition($this->getlib_physical_condition()),
            'lib_relationship' => $this->selectoptionlibrelationship($this->getlib_relationship())
        );
    }
}