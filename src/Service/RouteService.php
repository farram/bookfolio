<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouteService
{
    private $planRepository;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function routeTerms()
    {
        return $this->router->generate('cgu');
    }

    public function routePoliticy()
    {
        return $this->router->generate('politicy');
    }
}
