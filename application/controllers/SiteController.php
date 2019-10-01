<?php

/**
 * A basic controller templete
 */

class SiteController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $this->redirect('site/home'); // Perform a redirect to show how the URL routing works
    }

    public function homeAction()
    {
        $title = 'Welcome to LightRailMVC';
        $test_variable = 'This is the $test_variable value assigned in the action method <span class="code">SiteController::homeAction()</span>';

        // The default layout automatically includes the action method's view as well.
        include $this->layout;
    }

    public function pageTwoAction()
    {
        $title = 'Page Two Title';
        include $this->layout;
    }

    /**
     * PAGE testUrlParams
     * This method shows how you can use the URL to pass additional arguments to the method.
     * It also outputs directly without a template.
     */
    public function testUrlParamsAction($arg1, $arg2, $arg3)
    {
        header('Content-type: text/plain');

        echo "This content is output directly from the SiteController::testUrlParams method without a template or view file.\n\n";

        $output = "request URL:       {$_SERVER['REQUEST_URI']}\n"
                . "PHP self:          {$_SERVER['PHP_SELF']}\n"
                . "query string:      {$_SERVER['QUERY_STRING']}\n";

        for ($i = 1; $i <= 3; $i ++) {
            $arg = ${'arg'.$i};
            $output .= "URL argument $i:    $arg\n";
        }

        $output .= '$_GET = ' . print_r($_GET, true);

        echo $output;
    }
}
