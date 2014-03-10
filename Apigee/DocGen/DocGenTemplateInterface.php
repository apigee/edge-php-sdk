<?php
namespace Apigee\DocGen;

interface DocGenTemplateInterface
{
    /**
     * Gets the inndex template.
     *
     * @param $apiId
     * @return array|string
     */
    public function getIndexTemplate($apiId, $name);

    /**
     * Gets the operation HTML.
     *
     * @param $apiId
     * @return array|string
     */
    public function getOperationTemplate($apiId, $name);

    /**
     * Saves the template back to the modeling API.
     */
    public function saveTemplate($apiId, $type, $name, $html);

    /**
     * Updates the template back to the modeling API.
     */
    public function updateTemplate($apiId, $type, $name, $html);
}