<?php
/*
 * This file is part of the package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\Log\Bridge\Twig;

use Twig_Template;
use Twig_TemplateInterface;

/**
 * Wraps a Twig_Template to add profiling features
 */
class TraceableTwigTemplate implements Twig_TemplateInterface
{
    /**
     * @var Twig_Template
     */
    protected $template;

    /**
     * @param TraceableTwigEnvironment $env
     * @param Twig_Template $template
     */
    public function __construct(TraceableTwigEnvironment $env, Twig_Template $template)
    {
        $this->env = $env;
        $this->template = $template;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->template, $name), $arguments);
    }

    /**
     * @return mixed
     */
    public function getTemplateName()
    {
        return $this->template->getTemplateName();
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->template->getEnvironment();
    }

    /**
     * @param array $context
     *
     * @return mixed
     */
    public function getParent(array $context)
    {
        return $this->template->getParent($context);
    }

    /**
     * @return mixed
     */
    public function isTraitable()
    {
        return $this->template->isTraitable();
    }

    /**
     * @param       $name
     * @param array $context
     * @param array $blocks
     */
    public function displayParentBlock($name, array $context, array $blocks = array())
    {
        $this->template->displayParentBlock($name, $context, $blocks);
    }

    /**
     * @param       $name
     * @param array $context
     * @param array $blocks
     * @param bool  $useBlocks
     */
    public function displayBlock($name, array $context, array $blocks = array(), $useBlocks = true)
    {
        $this->template->displayBlock($name, $context, $blocks, $useBlocks);
    }

    /**
     * @param       $name
     * @param array $context
     * @param array $blocks
     *
     * @return mixed
     */
    public function renderParentBlock($name, array $context, array $blocks = array())
    {
        return $this->template->renderParentBlock($name, $context, $blocks);
    }

    /**
     * @param       $name
     * @param array $context
     * @param array $blocks
     * @param bool  $useBlocks
     *
     * @return mixed
     */
    public function renderBlock($name, array $context, array $blocks = array(), $useBlocks = true)
    {
        return $this->template->renderBlock($name, $context, $blocks, $useBlocks);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function hasBlock($name)
    {
        return $this->template->hasBlock($name);
    }

    /**
     * @return mixed
     */
    public function getBlockNames()
    {
        return $this->template->getBlockNames();
    }

    /**
     * @return mixed
     */
    public function getBlocks()
    {
        return $this->template->getBlocks();
    }

    /**
     * @param array $context
     * @param array $blocks
     */
    public function display(array $context, array $blocks = array())
    {
        $start = microtime(true);
        $this->template->display($context, $blocks);
        $end = microtime(true);

        if ($timeDataCollector = $this->env->getTimeDataCollector()) {
            $name = sprintf("twig.render(%s)", $this->template->getTemplateName());
            $timeDataCollector->addMeasure($name, $start, $end);
        }

        $this->env->addRenderedTemplate(array(
            'name' => $this->template->getTemplateName(),
            'render_time' => $end - $start
        ));
    }

    /**
     * @param array $context
     *
     * @return string
     * @throws \Exception
     */
    public function render(array $context)
    {
        $level = ob_get_level();
        ob_start();
        try {
            $this->display($context);
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }

        return ob_get_clean();
    }

    public static function clearCache()
    {
        Twig_Template::clearCache();
    }
}
