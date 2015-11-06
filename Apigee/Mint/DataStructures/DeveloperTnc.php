<?php

namespace Apigee\Mint\DataStructures;

use Apigee\Mint\Types\DeveloperTncsActionType;
use Apigee\Mint\TermAndCondition;

class DeveloperTnc extends DataStructure
{

    private $action;

    private $auditDate;

    private $id;

    private $tnc;

    public function __construct($data = null)
    {
        if (is_array($data)) {
            $excluded_properties = array('tnc');
            $this->loadFromRawData($data, $excluded_properties);
        }
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->action = DeveloperTncsActionType::get($action);
    }

    public function getAuditDate()
    {
        return $this->auditDate;
    }

    public function setAuditDate($audit_date)
    {
        $this->auditDate = $audit_date;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return TermAndCondition
     */
    public function getTnc()
    {
        return $this->tnc;
    }

    /**
     * @param TermAndCondition $tnc
     */
    public function setTnc(TermAndCondition $tnc)
    {
        $this->tnc = $tnc;
    }

    public function __toString()
    {
        $object = array(
            'action' => $this->action,
            'auditDate' => $this->auditDate,
            'id' => $this->id,
        );
        return json_encode($object, JSON_FORCE_OBJECT);
    }
}
