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
class __TwigTemplate_4e45f269a5dc64d05d88cfbc6e8b152f94e9fc9f436e98fdcb2be40004d33059 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
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

    // line 2
    public function block_title($context, array $blocks = [])
    {
        echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        // line 4
        echo "    <h4>SQL：";
        echo twig_escape_filter($this->env, ($context["query"] ?? null), "html", null, true);
        echo "</h4>
    <ul>
    ";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["lists"] ?? null));
        foreach ($context['_seq'] as $context["key"] => $context["value"]) {
            // line 7
            echo "        <li>";
            echo twig_escape_filter($this->env, ($context["key"] + 1), "html", null, true);
            echo "，";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["value"], "title", [], "any", false, false, false, 7), "html", null, true);
            echo "，";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["value"], "name", [], "any", false, false, false, 7), "html", null, true);
            echo "</li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 9
        echo "    </ul>
";
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
        return array (  77 => 9,  64 => 7,  60 => 6,  54 => 4,  51 => 3,  45 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"@base/Common/base.html\" %}
{% block title %}{{ title }}{% endblock %}
{% block content %}
    <h4>SQL：{{ query }}</h4>
    <ul>
    {% for key,value in lists %}
        <li>{{ key+1 }}，{{ value.title }}，{{ value.name }}</li>
    {% endfor %}
    </ul>
{% endblock %}", "@base/Index/index.html", "D:\\www\\TOP\\application\\home\\view\\Index\\index.html");
    }
}
