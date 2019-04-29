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

/* @base/A/index.html */
class __TwigTemplate_4b5d68cfba4ae92c729158681afc6aa7bb90a11d9e0fe522b84ebc06a68e6763 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "@base/base.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $this->parent = $this->loadTemplate("@base/base.html", "@base/A/index.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        // line 4
        echo "<h1>Index</h1>
<p class=\"important\">
    Welcome to my awesome homepage.我是A->index输出
</p>
";
    }

    public function getTemplateName()
    {
        return "@base/A/index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  47 => 4,  44 => 3,  34 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"@base/base.html\" %}

{% block content %}
<h1>Index</h1>
<p class=\"important\">
    Welcome to my awesome homepage.我是A->index输出
</p>
{% endblock %}", "@base/A/index.html", "D:\\www\\hongzheng\\application\\home\\view\\A\\index.html");
    }
}
