<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* @base/base.html */
class __TwigTemplate_9346a6ed041db7ccce2d88bcd8422211c6d22448c0127f38f2564d0e40de13d0 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<!doctype html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\"
          content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>测试111</title>
</head>
<body>
<div id=\"content\">
    ";
        // line 12
        $this->displayBlock('content', $context, $blocks);
        // line 13
        echo "</div>
</body>
</html>";
    }

    // line 12
    public function block_content($context, array $blocks = [])
    {
    }

    public function getTemplateName()
    {
        return "@base/base.html";
    }

    public function getDebugInfo()
    {
        return array (  57 => 12,  51 => 13,  49 => 12,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!doctype html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\"
          content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>测试111</title>
</head>
<body>
<div id=\"content\">
    {% block content %}{% endblock %}
</div>
</body>
</html>", "@base/base.html", "D:\\www\\hongzheng\\application\\home\\view\\base.html");
    }
}
