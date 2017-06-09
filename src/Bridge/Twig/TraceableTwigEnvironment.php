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

use Songshenzong\Log\DataCollector\TimeDataCollector;
use Twig_CompilerInterface;
use Twig_Environment;
use Twig_ExtensionInterface;
use Twig_LexerInterface;
use Twig_LoaderInterface;
use Twig_NodeInterface;
use Twig_NodeVisitorInterface;
use Twig_ParserInterface;
use Twig_TokenParserInterface;
use Twig_TokenStream;

/**
 * Wrapped a Twig Environment to provide profiling features
 */
class TraceableTwigEnvironment extends Twig_Environment
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $renderedTemplates = [];

    /**
     * @var TimeDataCollector
     */
    protected $timeDataCollector;

    /**
     * @param Twig_Environment  $twig
     * @param TimeDataCollector $timeDataCollector
     */
    public function __construct(Twig_Environment $twig, TimeDataCollector $timeDataCollector = null)
    {
        $this->twig              = $twig;
        $this->timeDataCollector = $timeDataCollector;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->twig, $name], $arguments);
    }

    /**
     * @return array
     */
    public function getRenderedTemplates()
    {
        return $this->renderedTemplates;
    }

    /**
     * @param array $info
     */
    public function addRenderedTemplate(array $info)
    {
        $this->renderedTemplates[] = $info;
    }

    /**
     * @return TimeDataCollector
     */
    public function getTimeDataCollector()
    {
        return $this->timeDataCollector;
    }

    /**
     * @return mixed
     */
    public function getBaseTemplateClass()
    {
        return $this->twig->getBaseTemplateClass();
    }

    /**
     * @param $class
     */
    public function setBaseTemplateClass($class)
    {
        $this->twig->setBaseTemplateClass($class);
    }

    public function enableDebug()
    {
        $this->twig->enableDebug();
    }

    public function disableDebug()
    {
        $this->twig->disableDebug();
    }

    /**
     * @return mixed
     */
    public function isDebug()
    {
        return $this->twig->isDebug();
    }

    public function enableAutoReload()
    {
        $this->twig->enableAutoReload();
    }

    public function disableAutoReload()
    {
        $this->twig->disableAutoReload();
    }

    /**
     * @return mixed
     */
    public function isAutoReload()
    {
        return $this->twig->isAutoReload();
    }

    public function enableStrictVariables()
    {
        $this->twig->enableStrictVariables();
    }

    public function disableStrictVariables()
    {
        $this->twig->disableStrictVariables();
    }

    /**
     * @return mixed
     */
    public function isStrictVariables()
    {
        return $this->twig->isStrictVariables();
    }

    /**
     * @param bool $original
     *
     * @return mixed
     */
    public function getCache($original = true)
    {
        return $this->twig->getCache($original);
    }

    /**
     * @param $cache
     */
    public function setCache($cache)
    {
        $this->twig->setCache($cache);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getCacheFilename($name)
    {
        return $this->twig->getCacheFilename($name);
    }

    /**
     * @param      $name
     * @param null $index
     *
     * @return mixed
     */
    public function getTemplateClass($name, $index = null)
    {
        return $this->twig->getTemplateClass($name, $index);
    }

    /**
     * @return mixed
     */
    public function getTemplateClassPrefix()
    {
        return $this->twig->getTemplateClassPrefix();
    }

    /**
     * @param       $name
     * @param array $context
     *
     * @return string
     * @throws \Exception
     */
    public function render($name, array $context = [])
    {
        return $this->loadTemplate($name)->render($context);
    }

    /**
     * @param       $name
     * @param array $context
     */
    public function display($name, array $context = [])
    {
        $this->loadTemplate($name)->display($context);
    }

    /**
     * @param      $name
     * @param null $index
     *
     * @return TraceableTwigTemplate
     */
    public function loadTemplate($name, $index = null)
    {
        $cls = $this->twig->getTemplateClass($name, $index);

        if (isset($this->twig->loadedTemplates[$cls])) {
            return $this->twig->loadedTemplates[$cls];
        }

        if (!class_exists($cls, false)) {
            if (false === $cache = $this->getCacheFilename($name)) {
                eval('?>' . $this->compileSource($this->getLoader()->getSource($name), $name));
            } else {
                if (!is_file($cache) || ($this->isAutoReload() && !$this->isTemplateFresh($name, filemtime($cache)))) {
                    $this->writeCacheFile($cache, $this->compileSource($this->getLoader()->getSource($name), $name));
                }

                require_once $cache;
            }
        }

        if (!$this->twig->runtimeInitialized) {
            $this->initRuntime();
        }

        return $this->twig->loadedTemplates[$cls] = new TraceableTwigTemplate($this, new $cls($this));
    }

    /**
     * @param $name
     * @param $time
     *
     * @return mixed
     */
    public function isTemplateFresh($name, $time)
    {
        return $this->twig->isTemplateFresh($name, $time);
    }

    /**
     * @param $names
     *
     * @return mixed
     */
    public function resolveTemplate($names)
    {
        return $this->twig->resolveTemplate($names);
    }

    public function clearTemplateCache()
    {
        $this->twig->clearTemplateCache();
    }

    public function clearCacheFiles()
    {
        $this->twig->clearCacheFiles();
    }

    /**
     * @return mixed
     */
    public function getLexer()
    {
        return $this->twig->getLexer();
    }

    /**
     * @param Twig_LexerInterface $lexer
     */
    public function setLexer(Twig_LexerInterface $lexer)
    {
        $this->twig->setLexer($lexer);
    }

    /**
     * @param      $source
     * @param null $name
     *
     * @return mixed
     */
    public function tokenize($source, $name = null)
    {
        return $this->twig->tokenize($source, $name);
    }

    /**
     * @return mixed
     */
    public function getParser()
    {
        return $this->twig->getParser();
    }

    /**
     * @param Twig_ParserInterface $parser
     */
    public function setParser(Twig_ParserInterface $parser)
    {
        $this->twig->setParser($parser);
    }

    /**
     * @param Twig_TokenStream $tokens
     *
     * @return mixed
     */
    public function parse(Twig_TokenStream $tokens)
    {
        return $this->twig->parse($tokens);
    }

    /**
     * @return mixed
     */
    public function getCompiler()
    {
        return $this->twig->getCompiler();
    }

    /**
     * @param Twig_CompilerInterface $compiler
     */
    public function setCompiler(Twig_CompilerInterface $compiler)
    {
        $this->twig->setCompiler($compiler);
    }

    /**
     * @param Twig_NodeInterface $node
     *
     * @return mixed
     */
    public function compile(Twig_NodeInterface $node)
    {
        return $this->twig->compile($node);
    }

    /**
     * @param      $source
     * @param null $name
     *
     * @return mixed
     */
    public function compileSource($source, $name = null)
    {
        return $this->twig->compileSource($source, $name);
    }

    /**
     * @param Twig_LoaderInterface $loader
     */
    public function setLoader(Twig_LoaderInterface $loader)
    {
        $this->twig->setLoader($loader);
    }

    /**
     * @return mixed
     */
    public function getLoader()
    {
        return $this->twig->getLoader();
    }

    /**
     * @param $charset
     */
    public function setCharset($charset)
    {
        $this->twig->setCharset($charset);
    }

    /**
     * @return mixed
     */
    public function getCharset()
    {
        return $this->twig->getCharset();
    }

    public function initRuntime()
    {
        $this->twig->initRuntime();
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function hasExtension($name)
    {
        return $this->twig->hasExtension($name);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getExtension($name)
    {
        return $this->twig->getExtension($name);
    }

    /**
     * @param Twig_ExtensionInterface $extension
     */
    public function addExtension(Twig_ExtensionInterface $extension)
    {
        $this->twig->addExtension($extension);
    }

    /**
     * @param $name
     */
    public function removeExtension($name)
    {
        $this->twig->removeExtension($name);
    }

    /**
     * @param array $extensions
     */
    public function setExtensions(array $extensions)
    {
        $this->twig->setExtensions($extensions);
    }

    /**
     * @return mixed
     */
    public function getExtensions()
    {
        return $this->twig->getExtensions();
    }

    /**
     * @param Twig_TokenParserInterface $parser
     */
    public function addTokenParser(Twig_TokenParserInterface $parser)
    {
        $this->twig->addTokenParser($parser);
    }

    /**
     * @return mixed
     */
    public function getTokenParsers()
    {
        return $this->twig->getTokenParsers();
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->twig->getTags();
    }

    /**
     * @param Twig_NodeVisitorInterface $visitor
     */
    public function addNodeVisitor(Twig_NodeVisitorInterface $visitor)
    {
        $this->twig->addNodeVisitor($visitor);
    }

    /**
     * @return mixed
     */
    public function getNodeVisitors()
    {
        return $this->twig->getNodeVisitors();
    }

    /**
     * @param      $name
     * @param null $filter
     */
    public function addFilter($name, $filter = null)
    {
        $this->twig->addFilter($name, $filter);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getFilter($name)
    {
        return $this->twig->getFilter($name);
    }

    /**
     * @param $callable
     */
    public function registerUndefinedFilterCallback($callable)
    {
        $this->twig->registerUndefinedFilterCallback($callable);
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->twig->getFilters();
    }

    /**
     * @param      $name
     * @param null $test
     */
    public function addTest($name, $test = null)
    {
        $this->twig->addTest($name, $test);
    }

    /**
     * @return mixed
     */
    public function getTests()
    {
        return $this->twig->getTests();
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getTest($name)
    {
        return $this->twig->getTest($name);
    }

    /**
     * @param      $name
     * @param null $function
     */
    public function addFunction($name, $function = null)
    {
        $this->twig->addFunction($name, $function);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getFunction($name)
    {
        return $this->twig->getFunction($name);
    }

    /**
     * @param $callable
     */
    public function registerUndefinedFunctionCallback($callable)
    {
        $this->twig->registerUndefinedFunctionCallback($callable);
    }

    /**
     * @return mixed
     */
    public function getFunctions()
    {
        return $this->twig->getFunctions();
    }

    /**
     * @param $name
     * @param $value
     */
    public function addGlobal($name, $value)
    {
        $this->twig->addGlobal($name, $value);
    }

    /**
     * @return mixed
     */
    public function getGlobals()
    {
        return $this->twig->getGlobals();
    }

    /**
     * @param array $context
     *
     * @return mixed
     */
    public function mergeGlobals(array $context)
    {
        return $this->twig->mergeGlobals($context);
    }

    /**
     * @return mixed
     */
    public function getUnaryOperators()
    {
        return $this->twig->getUnaryOperators();
    }

    /**
     * @return mixed
     */
    public function getBinaryOperators()
    {
        return $this->twig->getBinaryOperators();
    }

    /**
     * @param $name
     * @param $items
     *
     * @return mixed
     */
    public function computeAlternatives($name, $items)
    {
        return $this->twig->computeAlternatives($name, $items);
    }
}
