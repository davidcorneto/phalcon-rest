<?php

namespace PhalconRest;

use PhalconRest\Api\Resource;
use PhalconRest\Constant\ErrorCode;
use PhalconRest\Constant\Service;
use PhalconRest\Exception;

class Api extends \Phalcon\Mvc\Micro
{
    protected $resources = [];

    public function getCurrentResource()
    {
        return null;
    }

    public function getCurrentEndpoint()
    {
        return null;
    }

    /**
     * @return Resource[]
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @param $name
     *
     * @return Resource
     */
    public function getResource($name)
    {
        return array_key_exists($name, $this->resources) ? $this->resources[$name] : null;
    }

    /**
     * @param string $name
     * @param Resource $resource
     *
     * @return static
     * @throws Exception
     */
    public function resource($name, Resource $resource)
    {
        $resource->name($name);
        $this->mount($resource);

        return $this;
    }

    public function mount(\Phalcon\Mvc\Micro\CollectionInterface $collection)
    {
        if($collection instanceof Resource){

            $resourceName = $collection->getName();
            if(!$resourceName){
                throw new Exception(ErrorCode::GEN_SYSTEM, 'No name provided for resource');
            }

            $this->resources[$resourceName] = $collection;
        }

        return parent::mount($collection);
    }

    /**
     * Attaches middleware to the API
     *
     * @param $middleware
     * @return static
     */
    public function attach($middleware)
    {
        if(!$this->getEventsManager()){
            $this->setEventsManager($this->getDI()->get(Service::EVENTS_MANAGER));
        }

        $this->getEventsManager()->attach('micro', $middleware);

        return $this;
    }
}