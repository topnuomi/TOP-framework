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

/* @base/Index/index.html */
class __TwigTemplate_77f6f4434e23a407b1c0b188f9b24dae360b4fa23942695959cc1259bf1a51d4 extends \Twig\Template
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
        return "@base/Common/base.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $this->parent = $this->loadTemplate("@base/Common/base.html", "@base/Index/index.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        // line 4
        echo "    <h1>Twig</h1>
    ";
        // line 5
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["lists"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["value"]) {
            // line 6
            echo "        ";
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "@base/Index/index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  54 => 6,  50 => 5,  47 => 4,  44 => 3,  34 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"@base/Common/base.html\" %}

{% block content %}
    <h1>Twig</h1>
    {% for value in lists %}
        {{ value }}
    {% endfor %}
{% endblock %}", "@base/Index/index.html", "D:\\www\\TOP-framework-1.1\\application\\home\\view\\Index\\index.html");
    }
}
