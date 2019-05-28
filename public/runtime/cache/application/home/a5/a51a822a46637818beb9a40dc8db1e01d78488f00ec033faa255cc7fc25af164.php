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
class __TwigTemplate_fc897c7e0828f45c4af62abf0a358c71841f798bace75d00ea2c7e1d14accdaa extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
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
    <title>";
        // line 7
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
</head>
<body>
<style>
    ul {list-style: none;margin: 0;padding: 0;}
    h4 {font-weight: 200;}
</style>
<div>
    ";
        // line 15
        $this->displayBlock('content', $context, $blocks);
        // line 16
        echo "</div>
</body>
</html>";
    }

    // line 7
    public function block_title($context, array $blocks = [])
    {
    }

    // line 15
    public function block_content($context, array $blocks = [])
    {
    }

    public function getTemplateName()
    {
        return "@base/Common/base.html";
    }

    public function getDebugInfo()
    {
        return array (  69 => 15,  64 => 7,  58 => 16,  56 => 15,  45 => 7,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!doctype html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>{% block title %}{% endblock %}</title>
</head>
<body>
<style>
    ul {list-style: none;margin: 0;padding: 0;}
    h4 {font-weight: 200;}
</style>
<div>
    {% block content %}{% endblock %}
</div>
</body>
</html>", "@base/Common/base.html", "D:\\www\\TOP\\application\\home\\view\\Common\\base.html");
    }
}
