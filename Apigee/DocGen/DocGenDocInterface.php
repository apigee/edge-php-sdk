<?php
namespace Apigee\DocGen;

interface DocGenDocInterface
{
    /**
     * Grabs the HTML of a given operation.
     *
     * @param $data = array('nid', 'revision', 'resource', 'operation')
     *     Revision, Resource, and Operation should all be UUIDs.
     * @param $mid
     * @return array
     */
    public function requestOperation($data, $mid, $name);
}