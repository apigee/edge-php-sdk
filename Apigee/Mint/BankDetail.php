<?php
namespace Apigee\Mint;

use Apigee\Mint\DataStructures\Address;
use Apigee\Util\OrgConfig;

class BankDetail extends Base\BaseObject
{

    /**
     * @var string
     *   Developer's email owning this banks details
     */
    private $devEmail;

    /**
     * @var Address
     *     Bank's Addresses
     */
    private $address;

    /**
     * @var string
     *     Bank's ABAN
     */
    private $aban;

    /**
     * @var string
     *     Account name
     */
    private $accountName;

    /**
     * @var string
     *     Account number
     */
    private $accountNumber;

    /**
     * @var string
     *     Bank's BIC
     */
    private $bic;

    /**
     * @var string
     *     ISO 4217 currency code
     */
    private $currency;

    /**
     * @var string
     *     Bank's IBAN/Router number
     */
    private $ibanNumber;

    /**
     * @var string
     *     Bank Detail object id
     */
    protected $id;

    /**
     * @var string
     *     Bank's name
     */
    private $name;

    /**
     * @var string
     *     Bank's Sort Code
     */
    private $sortCode;


    public function __construct($developer_email, OrgConfig $config)
    {
        $base_url = '/mint/organizations/'
            . rawurlencode($config->orgName)
            . '/developers/'
            . rawurldecode($developer_email)
            . '/bank-details';
        $this->init($config, $base_url);
        $this->devEmail = $developer_email;
        $this->wrapperTag = 'bankDetail';
        $this->idField = 'id';

        $this->initValues();
    }

    protected function initValues()
    {
        $this->aban = null;
        $this->accountName = null;
        $this->accountNumber = null;
        $this->address = null;
        $this->bic = null;
        $this->currency = null;
        $this->ibanNumber = null;
        $this->id = null;
        $this->name = null;
        $this->sortCode = null;
    }

    public function instantiateNew()
    {
        return new BankDetail($this->devEmail, $this->config);
    }

    public function load($id = null)
    {
        $url = rawurlencode($id);
        $this->get($url);
        $data = $this->responseObj;
        foreach ($data[$this->wrapperTag] as $bank_detail_data) {
            $this->loadFromRawData($bank_detail_data);
            break;
        }
    }

    public function loadFromRawData($data, $reset = false)
    {
        if ($reset) {
            $this->initValues();
        }
        $excluded_properties = array('address');
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

        if (isset($data['address']) && is_array($data['address']) && count($data['address']) > 0) {
            $this->address = new DataStructures\Address($data['address']);
        }
    }

    public function save($save_method)
    {
        if ($this->id == null) {
            $this->post(null, $this->__toString());
        } else {
            $baseUrl = '/mint/organizations/' . rawurlencode($this->config->orgName) . '/bank-details/' . $this->id;
            $this->setBaseUrl($baseUrl);
            $this->put(null, $this->__toString());
            $this->restoreBaseUrl();
        }
    }

    public function delete()
    {
        $this->setBaseUrl('/mint/organizations/' . rawurlencode($this->config->orgName) . '/bank-details/' . $this->id);
        $this->httpDelete();
        $this->restoreBaseUrl();
    }

    public function __toString()
    {
        $object = array(
            'name' => $this->name,
            'accountName' => $this->accountName,
            'accountNumber' => $this->accountNumber,
            'currency' => $this->currency,
            'sortCode' => $this->sortCode,
            'aban' => $this->aban,
            'bic' => $this->bic,
            'ibanNumber' => $this->ibanNumber,
        );

        if (isset($this->id)) {
            $object['id'] = $this->id;
        }
        if (isset($this->address)) {
            $object['address'] = array(
                'address1' => $this->address->getAddress1(),
                'address2' => $this->address->getAddress2(),
                'isPrimary' => 'true',
                'city' => $this->address->getCity(),
                'state' => $this->address->getState(),
                'zip' => $this->address->getZip(),
                'country' => $this->address->getCountry(),
                'id' => $this->address->getId(),
            );
        }
        return json_encode((object)$object);
    }

    public function getAban()
    {
        return $this->aban;
    }

    public function setAban($aban)
    {
        // TODO: validate
        $this->aban = $aban;
    }

    public function getAccountName()
    {
        return $this->accountName;
    }

    public function setAccountName($name)
    {
        $this->accountName = $name;
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function setAccountNumber($num)
    {
        $this->accountNumber = $num;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress(DataStructures\Address $addr)
    {
        $this->address = $addr;
    }

    public function getBic()
    {
        return $this->bic;
    }

    public function setBic($bic)
    {
        $this->bic = $bic;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($curr)
    {
        // TODO: validate
        $this->currency = $curr;
    }

    public function getIbanNumber()
    {
        return $this->ibanNumber;
    }

    public function setIbanNumber($num)
    {
        // TODO: validate?
        $this->ibanNumber = $num;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSortCode()
    {
        return $this->sortCode;
    }

    public function setSortCode($code)
    {
        // TODO: validate
        $this->sortCode = $code;
    }
}
