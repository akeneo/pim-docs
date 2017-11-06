<?php
require_once("../../vendor/autoload.php");

use Symfony\Component\Yaml\Yaml;

$loader = new \Twig_Loader_Filesystem(__DIR__.'/templates');
$twig = new \Twig_Environment($loader);

$renderCodeFunction = new Twig_SimpleFunction('render_code', function ($template, $code, $args = []) {
    global $twig;

    $result = '<div class="reset-bem' . (array_key_exists('dark', $args) ? ' dark' : '') . '">';
    $htmlCode = $twig->render($template, $args);
    $result .= $htmlCode;
    $result .= '</div>';
    $result .= '<div class="highlight-html"><div class="highlight"><pre class="hljs" style="display:none;" data-code="' . $code . '"><code class="html">' . htmlspecialchars($htmlCode) . '</code></pre></div></div>';

    return $result;
});
$twig->addFunction($renderCodeFunction);

$yaml = Yaml::parse(file_get_contents('config.yml'));

?><!DOCTYPE html>
<html lang="en"><head>
    <meta charset="UTF-8">
    <title>Akeneo Styleguides</title>
    <meta name="description" content="Description">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/docsearch.js/1/docsearch.min.css" type="text/css">
    <link rel="stylesheet" href="../../_static/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../../_static/css/variables-43bf553955.css" type="text/css">
    <script async="" src="https://www.google-analytics.com/analytics.js"></script>
    <script type="text/javascript" src="../../_static/js/jquery.min.js"></script>
    <script type="text/javascript" src="styleguide.js"></script>
    <script type="text/javascript" src="../../_static/js/bootstrap.min.js"></script>
    <link rel="shortcut icon" href="../../_static/favicon.ico">
    <script async="" src="https://www.google-analytics.com/analytics.js"></script>
    <link href="https://fonts.googleapis.com/css?family=PT+Mono" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/docsearch.js/2/docsearch.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/docsearch.js/2/docsearch.min.js"></script>
    <link rel="stylesheet" href="/../../web/css/pim.css">
    <link rel="stylesheet" href="styleguide.css">
    <style>
        .highlighttable { width: 100%; }
        .highlight-yaml { width: 100%; overflow: auto; }
        .hljs { font-size: 12.5px; }
        .hljs-comment, .hljs-quote { color: #a1a9b7; }
        .hljs-string, .hljs-title, .hljs-name, .hljs-type, .hljs-symbol, .hljs-bullet, .hljs-addition, .hljs-variable, .hljs-template-tag, .hljs-template-variable, .hljs-regexp { font-weight: bold; }
        nav { transition: box-shadow 0.2s ease-in-out; }
        .nav .dropdown:first-child { float: right; }
        .nav .dropdown:nth-child(2) .dropdown-menu { left: -175px; }
        .algolia-docsearch-suggestion--category-header { background-color: white; }
    </style>
</head>
<body data-spy="scroll" data-target="#navbar">
<nav class="navbar navbar-default navbar-fixed-top scroll">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="#" class="navbar-brand">
                <img height="18" src="../../_static/akeneo.svg">
            </a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li>
                <a href="../../" class="collapse navbar-collapse">
                    Back to docs
                </a>
            </li>
        </ul>
    </div>
</nav>

<div id="mainContent" style="">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>Akeneo styleguides</h1>
            </div>
        </div>
        <div class="row">
            <div id="navbar" class="col-sm-3 hidden-xs">
                <nav role="tablist" id="navbar-nav" data-spy="affix" data-offset-top="80" class="affix-top">
                    <ul class="nav nav-stacked">
                        <li class="toctree-l2" data-group="Overview">
                            <a href="#" class="reference internal" onclick="setAnchor('Overview'); return false;">Overview</a>
                        </li>
                        <?php
                        foreach ($yaml['groups'] as $groupName => $elements) {
                            echo '<li class="toctree-l2 AknStyleGuide-menuItem" data-group="' . $groupName . '">';
                            echo '<a href="#" class="reference internal" onclick="setAnchor(\'' . $groupName . '\'); return false;">' . $groupName . '</a>';
                            echo '<ul class="nav AknStyleGuide-subMenu toctree-l1">';
                            foreach ($elements as $elementName => $elementConfig) {
                                echo '<li class="AknStyleGuide-subMenuItem toctree-l3" data-module="' . $elementName . '"><a href="#" onclick="setAnchor(\'' . $groupName . '-' . $elementName . '\'); return false;">' . $elementName . '</a></li>';
                            }
                            echo '</ul>';
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </nav>
            </div>
            <div class="section col-xs-12 col-sm-9">
                <a class="anchor" id="design-the-user-interfaces"></a><div class="section">
                    <div class="section" id="akeneo-pim-documentation">
                        <?php
                        echo $twig->render('overview.html.twig');

                        foreach ($yaml['groups'] as $groupName => $elements) {
                            foreach ($elements as $elementName => $elementConfig) {
                                $baseTemplate = $elementName . '/base.html.twig';

                                echo $twig->render('element.html.twig', [
                                    'groupName'     => $groupName,
                                    'elementName'   => $elementName,
                                    'elementConfig' => $elementConfig,
                                    'template'      => $baseTemplate,
                                ]);
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-offset-3 col-xs-9">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <p class="text-muted text-right">
                    <i class="fa fa-pencil"></i> Found a typo or a hole in the documentation and feel like contributing?<br>
                    <a href="https://github.com/akeneo/pim-docs/blob/2.0/design_pim/styleguide"><i class="fa fa-github"></i> Join us on Github!</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-35417741-6', 'auto');
    ga('send', 'pageview');
</script>

<script>
    $(function(){
        var navbar = $('.navbar');
        $(window).scroll(function(){
            if($(window).scrollTop() <= 40){
                navbar.removeClass('scroll');
            } else {
                navbar.addClass('scroll');
            }
        });
    });
</script>

</body>
</html>
