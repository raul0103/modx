<?php

class Session
{
    public $session_key = "session-cart";

    public function __construct()
    {
        session_start();
    }

    public function get()
    {
        global $modx;

        return $_SESSION[$modx->context->key][$this->session_key];
    }

    public function set($data)
    {
        global $modx;

        $_SESSION[$modx->context->key][$this->session_key] = $data;
    }

    public function unset()
    {
        global $modx;

        unset($_SESSION[$modx->context->key][$this->session_key]);
    }
}
