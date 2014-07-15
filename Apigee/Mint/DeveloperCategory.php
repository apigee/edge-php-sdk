<?php

namespace Apigee\Mint;

class DeveloperCategory extends Base\BaseObject
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

    /**
     * Class constructor
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct(\Apigee\Util\OrgConfig $config)
    {
        $base_url = '/mint/organizations/' . rawurlencode($config->orgName) . '/developer-categories';
        $this->init($config, $base_url);

        $this->wrapper_tag = 'developerCategory';
        $this->id_field = 'id';

        $this->initValues();
    }

    /**
     * Implements Base\BaseObject::instantiateNew().
     *
     * @return DeveloperCategory
     */
    public function instantiateNew()
    {
        return new DeveloperCategory($this->config);
    }

    /**
     * Implements Base\BaseObject::loadFromRawData().
     *
     * @param array $data
     * @param bool $reset
     */
    public function loadFromRawData($data, $reset = false)
    {
        if ($reset) {
            $this->initValues();
        }
        $excluded_properties = array();
        foreach (array_keys($data) as $property) {
            if (in_array($property, $excluded_properties)) {
                continue;
            }

            // form the setter method name to invoke setXxxx
            $setter_method = 'set' . ucfirst($property);

            if (method_exists($this, $setter_method)) {
                $this->$setter_method($data[$property]);
            } else {
                self::$logger->notice('No setter method was found for property "' . $property . '"');
            }
        }
    }

    protected function initValues()
    {
        $this->id = null;
        $this->name = null;
        $this->description = null;
    }

    public function __toString()
    {
        $obj = array(
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description
        );
        return json_encode($obj);
    }


    /*
     * accessors (getters/setters)
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = (string)$name;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($desc)
    {
        $this->description = (string)$desc;
    }
}
