<?php
namespace Apigee\ManagementAPI;

/**
 * The interface that an API Product object in the Management API must implement.
 *
 * @author djohnson
 */
interface APIProductInterface
{
    /**
     * Queries the Management API and populates self's properties from
     * the result.
     *
     * If neither $name nor $result is passed, tries to load from $this->name.
     * If $name is passed, loads from $name instead of $this->name.
     * If $response is passed, bypasses API query and uses the given array
     * instead.
     *
     * @param null|string $name
     * @param null|array $response
     */
    public function load($name = null, $response = null);

    /**
     * POSTs self's properties to Management API. This handles both
     * inserts and updates.
     */
    public function save();

    /**
     * Deletes an API Product.
     *
     * If $name is not passed, uses $this->name.
     *
     * @param null|string $name
     */
    public function delete($name = null);

    /**
     * Returns a detailed list of all products. This list may have been cached
     * from a previous call.
     *
     * If $show_nonpublic is true, even API Products which are marked as hidden
     * or internal are returned.
     *
     * @param bool $show_nonpublic
     * @return array
     */
    public function listProducts();

    /**
     * Returns the attributes array of name/value pairs.
     * @return array
     */
    public function getAttributes();

    /**
     * Returns a specific attribute value, or null if the attribute does not exist.
     *
     * @param $name
     */
    public function getAttribute($name);

    /**
     * Sets an attribute value.
     * @param $name
     * @param $value
     */
    public function setAttribute($name, $value);

    /**
     * Clears the attributes array.
     */
    public function clearAttributes();

    /**
     * Returns the UNIX time when the API Product was created.
     * @return integer
     */
    public function getCreatedAt();

    /**
     * Returns the username of the user who created the API Product.
     * @return string
     */
    public function getCreatedBy();

    /**
     * Returns the UNIX time when the API Product was most recently updated.
     * @return integer
     */
    public function getModifiedAt();

    /**
     * Returns the username of the user who most recently updated the API Product.
     * @return string
     */
    public function getModifiedBy();

    /**
     * Returns the array of environment names in an organization.
     * @return array
     */
    public function getEnvironments();

    /**
     * Returns the internal name of the API Product.
     * @return string
     */
    public function getName();

    /**
     * Returns the array of API proxy names in an organization.
     * @return array
     */
    public function getProxies();

    /**
     * Returns the number of request messages permitted by this API product.
     * @return integer
     */
    public function getQuotaLimit();

    /**
     * Returns the time interval over which the number of request messages is calculated.
     * @return integer
     */
    public function getQuotaInterval();

    /**
     * Returns the time unit defined for the quota interval.
     * @return string
     */
    public function getQuotaTimeUnit();

    /**
     * Returns the name to be displayed in the UI or developer portal to
     * developers registering for API access..
     * @return string
     */
    public function getDisplayName();

    /**
     * Returns the string describing the API Product.
     * @return string
     */
    public function getDescription();

    /**
     * Adds an API resource to the API Product.
     * @param $resource
     */
    public function addApiResource($resource);

    /**
     * Removes an API resource from the API Product.
     * @param $resource
     */
    public function removeApiResource($resource);

    /**
     * Returns the array of API resources.
     * @return array
     */
    public function getApiResources();

    /**
     * Returns the API product approval type as 'manual' or 'auto'.
     * @return string
     */
    public function getApprovalType();

    /**
     * Sets the API product approval type as 'manual' or 'auto'.
     * @param string $type
     */
    public function setApprovalType($type);
}
