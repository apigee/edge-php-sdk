<?php

namespace Apigee\Mint;

use Apigee\Util\OrgConfig;

class ApplicationCategory extends Base\BaseObject
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     * read-only uuid; auto-generated
     */
    private $id;

    public function __construct(OrgConfig $config)
    {
        $base_url = '/mint/organizations/' . rawurlencode($config->orgName) . '/application-categories';
        $this->init($config, $base_url);
        $this->idField = 'id';
        $this->wrapperTag = 'applicationCategory';
        $this->initValues();
    }

    protected function initValues()
    {
        $this->description = '';
        $this->name = '';
        $this->id = '';
    }

    public function loadFromRawData($data, $reset = false)
    {
        if ($reset) {
            $this->initValues();
        }
        foreach (array('description', 'id', 'name') as $field) {
            $this->$field = (isset($data[$field]) ? $data[$field] : null);
        }
    }

    public function __toString()
    {
        $obj = array(
            'description' => $this->description,
            'id' => $this->id,
            'name' => $this->name
        );
        return json_encode($obj);
    }

    public function instantiateNew()
    {
        return new ApplicationCategory($this->config);
    }

    /*
     * accessors (getters/setters)
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($desc)
    {
        $this->description = (string)$desc;
    }

    public function getId()
    {
        return $this->id;
    }

    // Used in data load invoked by $this->loadFromRawData()
    private function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = (string)$name;
    }
}
