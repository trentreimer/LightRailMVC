<?php

abstract class Controller
{
    protected $layout = 'views/_layouts/default.php';
    protected $view = null;
    protected $view_html = null;
    protected $url_segments = array();
    protected $flash_msg = array();

    public function __construct()
    {
        // Grab any flash messages set in $_SESSION and reset the variable.
        if (!empty($_SESSION['flash_msg']) && is_array($_SESSION['flash_msg'])) {
            foreach ($_SESSION['flash_msg'] as $val) $this->flash_msg[] = $val;
        }
        $_SESSION['flash_msg'] = array();

        // Set the default view for this request.
        $this->view = (isset($this->url_segments[0]) ? $this->url_segments[0] : strtolower(CONTROLLER)) . '/' . (isset($this->url_segments[1]) ? $this->url_segments[1] : strtolower(ACTION)) . '.php';

        // NOTE: You can remove the following code block if BASEURL is defined in bootstrap.php
        if (!defined('BASEURL')) {
            $baseURL = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'];
            if (!in_array($_SERVER['SERVER_PORT'], array(80, 443))) $baseURL .= ":{$_SERVER['SERVER_PORT']}";
            $p = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
            if (!empty($p)) $baseURL .= "/$p";
            define('BASEURL', "$baseURL");
        }
    }

    /**
     * Return a usable PDO instance
     */
    protected function db()
    {
        return LightRailPDOInstance::getInstance();
    }

    /**
     * Redirect to an application URL, or a full URL if specified.
     */
    protected function redirect($url = '')
    {
        // If there is anything in the flash_msg array save it to $_SESSION for the next view.
        if (!empty($this->flash_msg) && is_array($this->flash_msg)) {
            $_SESSION['flash_msg'] = $this->flash_msg;
        }

        $url = strval($url);

        if (!in_array(strtolower(substr($url, 0, 7)), array('https:/', 'http://'))) {
            $url = BASEURL . '/' . ltrim($url, '/');
        }

        header('Location: ' . $url);

        exit;
    }

    /**
     * Save a message to $_SESSION variable so that it can be shown in this action
     * or upon redirection to another action method.
     *
     * NOTE: HTML passed to this method is used verbatim.
     * Escape any variable content BEFORE passing it to this method.
     */
    protected function flashMsg($html)
    {
        $this->flash_msg[] = $html;
    }

    /**
     * Return the $flash_msg array.
     */
    protected function getFlashMsg()
    {
        return (array) $this->flash_msg;
    }
}
