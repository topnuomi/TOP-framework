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

/* @base/Common/base.html */
class __TwigTemplate_58f56e065670a2ba0f4483477816cc4b7190c3da3bfb80d30757d6f4cbd2a077 extends \Twig\Template
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
    <meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>Page</title>
</head>
<body>
<div>
    ";
        // line 11
        $this->displayBlock('content', $context, $blocks);
        // line 12
        echo "</div>
</body>
</html>";
    }

    // line 11
    public function block_content($context, array $blocks = [])
    {
    }

    public function getTemplateName()
    {
        return "@base/Common/base.html";
    }

    public function getDebugInfo()
    {
        return array (  56 => 11,  50 => 12,  48 => 11,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!doctype html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>Page</title>
</head>
<body>
<div>
    {% block content %}{% endblock %}
</div>
</body>
</html>", "@base/Common/base.html", "D:\\WWW\\top\\TOP-framework-1.1\\application\\home\\view\\Common\\base.html");
    }
}
