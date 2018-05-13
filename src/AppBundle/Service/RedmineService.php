<?php

namespace AppBundle\Service;

use Redmine\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RedmineService
{
    private $pass;
    private $name;
    private $apiKey;
    private $apiUrl;

    public function __construct(ContainerInterface $container)
    {
        $this->apiKey = $container->getParameter('redmine_api_key');
        $this->apiUrl = $container->getParameter('redmine_url');
        $this->name = $container->getParameter('redmine_name');
        $this->pass = $container->getParameter('redmine_password');
    }


    /**
     * @return Client
     */
    public function getClient()
    {
        return new Client($this->apiUrl , $this->apiKey);
    }
}