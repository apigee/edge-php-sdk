<?php
namespace Apigee\Mint;

use Apigee\Util\CacheFactory;
use Apigee\Exceptions\ParameterException;
use Apigee\Util\OrgConfig;

class PricePoint extends Base\BaseObject
{

    /**
     * PricePont id
     * @var string
     */
    private $id;

    /**
     * Net Price Start range
     * @var double
     */
    private $netStartPrice;

    /**
     * Net Price End range
     * @var double
     */
    private $netEndPrice;

    /**
     * Gross Price Start range
     * @var double
     */
    private $grossStartPrice;

    /**
     * Gross Price End range
     * @var double
     */
    private $grossEndPrice;

    /**
     * Is published?
     * @var bool
     */
    private $published;

    /**
     * Effective Start date
     * @var string
     */
    private $startDate;

    /**
     * Effective End date
     * @var string
     */
    private $endDate;

    /**
     * Organization
     * @var \Apigee\Mint\Organization
     */
    private $organization;

    /**
     * Product id this PricePoint is in
     * @var string
     */
    private $productId;

    /**
     * PricePoint class constructor
     * @param string $product_id Product Id this PricePoint is in
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct($product_id, OrgConfig $config)
    {
        $base_url = '/mint/organizations/'
            . rawurlencode($config->orgName)
            . '/products/'
            . rawurlencode($product_id)
            . '/price-points';

        $this->init($config, $base_url);
        $this->productId = $product_id;
        $this->wrapperTag = 'pricePoint';
        $this->idField = 'id';

        $this->initValues();
    }

    // Override of BaseObject methods

    /**
     * @see \Apigee\Mint\Base\BaseObject::save()
     * @param string $save_method Allowed values: update
     * @throws \Apigee\Exceptions\ParameterException;
     */
    public function save($save_method = 'auto')
    {
        if ($save_method != 'update') {
            throw new ParameterException("Only update method is supported");
        }
        parent::save('update');
    }

    // Implementation of BaseObject abstract methods

    public function instantiateNew()
    {
        return new PricePoint($this->productId, $this->config);
    }

    public function loadFromRawData($data, $reset = false)
    {
        if ($reset) {
            $this->initValues();
        }

        $excluded_properties = array('org', 'productId', 'organization');
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

        if (isset($data['organization'])) {
            $organization = new Organization($this->config);
            $organization->loadFromRawData($data['organization']);
            $this->organization = $organization;
        }
    }

    public function initValues()
    {
        $this->id = '';
        $this->netStartPrice = null;
        $this->netEndPrice = null;
        $this->grossStartPrice = null;
        $this->grossEndPrice = null;
        $this->published = false;
        $this->startDate = '';
        $this->endDate = '';
        $this->organization = false;
    }

    public function __toString()
    {
        // @TODO Verify
        $obj = array();
        $properties = array_keys(get_object_vars($this));
        $excluded_properties = array('org', 'productId');
        $excluded_properties = array_merge(array_keys(get_class_vars(get_parent_class($this))), $excluded_properties);
        foreach ($properties as $property) {
            if (in_array($property, $excluded_properties)) {
                continue;
            }
            if (isset($this->$property)) {
                if (is_object($this->$property)) {
                    $obj[$property] = json_decode((string)$this->$property, true);
                } else {
                    $obj[$property] = $this->$property;
                }
            }
        }
        return json_encode($obj);
    }

    // getters/setters

    /**
     * Get Price Point id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Price Point id
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get Net Price Start range
     * @return double
     */
    public function getNetStartPrice()
    {
        return $this->netStartPrice;
    }

    /**
     * Set Net Price Start range
     * @param double $net_start_price
     * @return void
     */
    public function setNetStartPrice($net_start_price)
    {
        $this->netStartPrice = $net_start_price;
    }

    /**
     * Get Net Price End range
     * @return double
     */
    public function getNetEndPrice()
    {
        return $this->netEndPrice;
    }

    /**
     * Set Net Price End range
     * @param double $net_end_price
     * @return void
     */
    public function setNetEndPrice($net_end_price)
    {
        $this->netEndPrice = $net_end_price;
    }

    /**
     * Get Gross Price Start range
     * @return double
     */
    public function getGrossStartPrice()
    {
        return $this->grossStartPrice;
    }

    /**
     * Set Gross Price Start range
     * @param double $gross_start_price
     * @return void
     */
    public function setGrossStartPrice($gross_start_price)
    {
        $this->grossStartPrice = $gross_start_price;
    }

    /**
     * Get Gross Price End range
     * @return double
     */
    public function getGrossEndPrice()
    {
        return $this->grossEndPrice;
    }

    /**
     * Set Gross Price End range
     * @param double $gross_end_price
     * @return void
     */
    public function setGrossEndPrice($gross_end_price)
    {
        $this->grossEndPrice = $gross_end_price;
    }

    /**
     * Retrieve if this Price Point is published?
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Set if this Price Point is published
     * @param bool $published
     * @return void
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * Get Effective Start date
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set Effective Start date
     * @param string $start_date
     * @return void
     */
    public function setStartDate($start_date)
    {
        $this->startDate = $start_date;
    }

    /**
     * Get Effective End date
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set Effective End date
     * @param string $end_date
     * @return void
     */
    public function setEndDate($end_date)
    {
        $this->endDate = $end_date;
    }

    /**
     * Get Organization
     * @return \Apigee\Mint\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set Organization
     * @param \Apigee\Mint\Organization $organization
     * @return void
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function getList($page_num = null, $page_size = 20)
    {
        $cache_manager = CacheFactory::getCacheManager();
        $data = $cache_manager->get('price_points:' . $this->productId, null);
        if (!isset($data)) {
            $this->get();
            $data = $this->responseObj;
            $cache_manager->set('price_points:' . $this->productId, $data);
        }

        $return_objects = array();

        foreach ($data[$this->wrapperTag] as $response_data) {
            $obj = $this->instantiateNew();
            $obj->loadFromRawData($response_data);
            $return_objects[] = $obj;
        }
        return $return_objects;
    }
}
