<?php

class BarController extends \Zend_Controller_Action
{
    static public $called = array('baz'=>0, 'test'=>0, 'setObject'=>0);
    
    public $object;
    
    public function bazAction()
    {
        self::$called['baz']++;
        $this->_forward('test');
    }
    
    public function testAction()
    {
        self::$called['test']++;
    }
    
    public function setObject( \SplObjectStorage $object )
    {
        self::$called['setObject']++;
        $this->object = $object;
    }
}