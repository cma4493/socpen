<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/22/2016
 * Time: 8:43 PM
 */
class UserClass extends DAO
{
    private $uid;

    protected function getUid()
    {
        return $this->uid;
    }

    public function __construct($uid)
    {
        $this->uid = $uid;
    }

    public function getUserName()
    {
        $sql = 'SELECT firstname,middlename,surname,extensionname FROM tbl_user WHERE uid=:uid';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':uid',$this->getUid());
        $result = $this->executeQuery();
        $this->closeDB();
        return ucfirst($result[0]['surname'] . ' ' . $result[0]['firstname'] . ' ' . $result[0]['middlename'] . ' ' . $result[0]['extensionname']);
    }
}