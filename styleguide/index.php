<?php
require_once("vendor/autoload.php");

$loader = new \Twig_Loader_Filesystem(__DIR__.'/templates');
$twig = new \Twig_Environment($loader);

$renderCodeFunction = new Twig_SimpleFunction('render_code', function ($template, $code, $args = []) {
    global $twig;

    $result = '<div class="reset-bem">';
    $htmlCode = $twig->render($template, $args);
    $result .= $htmlCode;
    $result .= '</div>';
    $result .= '<pre style="display:none;" data-code="' . $code . '"><code class="html">' . htmlspecialchars($htmlCode) . '</code></pre>';

    return $result;
});
$twig->addFunction($renderCodeFunction);

$yaml = \Symfony\Component\Yaml\Yaml::parse(file_get_contents('config.yml'));

?><!DOCTYPE html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" > <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akeneo PIM Documentation &mdash; Akeneo PIM documentation</title>
    <link rel="shortcut icon" href="/_static/favicon.ico"/>
    <link rel="stylesheet" href="/_static/css/theme.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/docsearch.js/1/docsearch.min.css" type="text/css" />
    <link rel="stylesheet" href="/_static/css/akeneo.css" type="text/css" />
    <link rel="top" title="Akeneo PIM documentation" href="#"/>
    <link rel="next" title="Installation" href="developer_guide/installation/index.html"/>
    <script src="/_static/js/modernizr.min.js"></script>

    <!-- Styleguide -->
    <link rel="stylesheet" href="//highlightjs.org/static/demo/styles/androidstudio.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/highlight.min.js"></script>
    <script src="/web/bundles/pimui/lib/jquery/jquery-1.10.2.js"></script>
    <script src="/web/bundles/pimui/lib/bootstrap/js/bootstrap.js"></script>
    <link rel="stylesheet" href="/web/css/pim.css" type="text/css" />
    <link rel="stylesheet" href="/styleguide/styleguide.css" type="text/css" />
    <script src="/styleguide/styleguide.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
</head>

<body class="wy-body-for-nav" role="document">
<div class="wy-grid-for-nav">
    <nav data-toggle="wy-nav-shift" class="wy-nav-side">
        <div class="wy-side-scroll">
            <div class="wy-side-nav-search">
                <a href="#">
                    <img src="/_static/logo.png" class="logo" />
                </a>
            </div>
            <div class="wy-menu wy-menu-vertical" data-spy="affix" role="navigation" aria-label="main navigation">
                <ul>
                    <?php
                    foreach ($yaml['groups'] as $groupName => $elements) {
                        echo '<li class="AknStyleGuide-menuItem" data-group="' . $groupName . '">';
                        echo '<a href="#" class="toctree-l1" onclick="setAnchor(\'' . $groupName . '\'); return false;" class="AknStyleGuide-groupLink">' . $groupName . '</a>';
                        echo '<ul class="AknStyleGuide-subMenu toctree-l1">';
                        foreach ($elements as $elementName => $elementConfig) {
                            echo '<li class="AknStyleGuide-subMenuItem toctree-l2" data-module="' . $elementName . '"><a onclick="setAnchor(\'' . $groupName . '-' . $elementName . '\'); return false;">' . $elementName . '</a></li>';
                        }
                        echo '</ul>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <section data-toggle="wy-nav-shift" class="wy-nav-content-wrap">
        <nav class="wy-nav-top" role="navigation" aria-label="top navigation">
            <i data-toggle="wy-nav-top" class="fa fa-bars"></i>
            <a href="#">Akeneo PIM</a>
        </nav>
        <div class="wy-nav-content">
            <div class="rst-content">
                <div role="navigation" aria-label="breadcrumbs navigation">
                    <ul class="wy-breadcrumbs">
                        <li><a href="/">Docs</a> &raquo;</li>
                        <li>Akeneo style guide</li>
                        <li class="wy-breadcrumbs-aside">
                            <a href="https://github.com/akeneo/pim-docs/blob/master/index.rst" class="fa fa-github"> Edit on GitHub</a>
                        </li>
                    </ul>
                    <hr/>
                </div>
                <div role="main" class="document" itemscope="itemscope" itemtype="http://schema.org/Article">
                    <div itemprop="articleBody">
                        <div class="section" id="akeneo-pim-documentation">
                            <?php
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
                <footer>
                    <div class="rst-footer-buttons" role="navigation" aria-label="footer navigation">
                        <a href="developer_guide/installation/index.html" class="btn btn-neutral float-right" title="Installation" accesskey="n">Next <span class="fa fa-arrow-circle-right"></span></a>
                    </div>
                    <hr/>
                    <div role="contentinfo">
                        <p>
                            &copy; Copyright 2016, Akeneo SAS.
                        </p>
                    </div>
                </footer>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    DOCUMENTATION_OPTIONS = [];
</script>
<script type="text/javascript" src="/_static/jquery.js"></script>
<script type="text/javascript" src="/_static/underscore.js"></script>
<script type="text/javascript" src="/_static/doctools.js"></script>
<script type="text/javascript" src="/_static/js/theme.js"></script>
<script type="text/javascript">
    jQuery(function () {
        SphinxRtdTheme.StickyNav.enable();
    });
</script>

</body>
</html>
