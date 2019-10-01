<!DOCTYPE html>
<html>
    <head>
        <title><?php if (!empty($title)): echo h($title); else: ?>Welcome to LightRailMVC<?php endif;?></title>
        <base href="<?php echo BASEURL ?>/">
        <link rel="stylesheet" href="public/css/demo.css">
    </head>
    <body>
        <header>
            <h1><?php if (!empty($title)): echo h($title); else: ?>Welcome to LightRail<?php endif; ?></h1>
            <h2>This is the heading</h2>

            <p>
                It comes from the layout file: <span class="code">application/views/_layouts/default.php</span>
            </p>
            <nav>
                Example navigation links:
                <ul>
                    <li><a href="site/home">site/home</a></li>
                    <li><a href="site/page-two">site/page-two</a></li>
                    <li><a href="site/test-url-params/one/two/three/four?greeting=hi&parting=good+bye">site/test-url-params/one/two/three/four?greeting=hi&parting=good+bye</a></li>
                </ul>
            </nav>
        </header>

        <p class="info-box">
            What follows is view content specified for the selected <strong>controller</strong> class and <strong>action</strong> method.

            <?php if (!empty($this->view)): ?>It comes from the view file: <span class="code">application/views/<?php echo $this->view ?></span><?php endif; ?>

        </p>

        <main>

<?php

if (!empty($this->view_html)) { // Allow the controller to set HTML for the view directly if desired.
    echo $this->view_html;
} elseif (!empty($this->view)) { // Load the view file for this action method. (if it exists)
    @include('views/' . $this->view);
}

?>

        </main>
        <footer>
            This is the footer. It is also part of the layout file.

            <p>You can find LightRailMVC documentation at <a href="https://github.com/trentreimer/LightRailMVC">https://github.com/trentreimer/LightRailMVC</a></p>
        </footer>
    </body>
</html>
