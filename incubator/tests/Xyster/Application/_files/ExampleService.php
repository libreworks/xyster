<?php

require_once 'Xyster/Application/Service/Abstract.php';

class ExampleService extends Xyster_Application_Service_Abstract
{
    /**
     * Just a dummy method
     *
     * @return int
     */
    public function getTwoPlusTwo()
    {
        return 4;
    }
}