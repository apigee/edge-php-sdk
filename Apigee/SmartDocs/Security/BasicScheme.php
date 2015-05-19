<?php

namespace Apigee\SmartDocs\Security;

/**
 * Class BasicScheme
 *
 * @package Apigee\SmartDocs\Security
 */
class BasicScheme extends SecurityScheme
{
    /**
     * {@inheritdoc}
     */
    public function getType($humanReadable = false)
    {
        if ($humanReadable) {
            return 'Basic';
        }
        return 'BASIC';
    }
}