<?php

namespace Kronofoto;

//use Interop\Container\ContainerInterface;

class DonorController
{
    public function read(Request $request, Response $response, array $args) 
    {
        echo 'donor';
    }

    public function getDonors(Request $request, Response $response, array $args) 
    {
        echo 'donors';
    }
}
