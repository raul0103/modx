<?php

require_once dirname(__DIR__) . '/classes/session.class.php';

class clear
{
    public $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function process()
    {
        $this->session->unset();
    }
}
