<?php

namespace Songshenzong\Log\DataCollector;

use Songshenzong\Log\Bridge\Twig\TwigCollector;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\DataCollector\Util\ValueExporter;

/**
 * Class ViewCollector
 *
 * @package Songshenzong\Log\DataCollector
 */
class ViewCollector extends TwigCollector
{
    /**
     * @var array
     */
    protected $templates = [];
    /**
     * @var bool
     */
    protected $collect_data;

    /**
     * Create a ViewCollector
     *
     * @param bool $collectData Collects view data when tru
     */
    public function __construct($collectData = true)
    {
        $this->collect_data = $collectData;
        $this->name = 'views';
        $this->templates = [];
        $this->exporter = new ValueExporter();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'views';
    }



    /**
     * Add a View instance to the Collector
     *
     * @param \Illuminate\View\View $view
     */
    public function addView(View $view)
    {
        $name = $view->getName();
        $path = $view->getPath();
        
        if (!is_object($path)) {
            if ($path) {
                $path = ltrim(str_replace(base_path(), '', realpath($path)), '/');
            }

            if (substr($path, -10) == '.blade.php') {
                $type = 'blade';
            } else {
                $type = pathinfo($path, PATHINFO_EXTENSION);
            }
        } else {
            $type = get_class($view);
            $path = '';
        }

        if (!$this->collect_data) {
            $params = array_keys($view->getData());
        } else {
            $data = [];
            foreach ($view->getData() as $key => $value) {
                $data[$key] = $this->exporter->exportValue($value);
            }
            $params = $data;
        }

        $this->templates[] = [
            'name' => $path ? sprintf('%s (%s)', $name, $path) : $name,
            'param_count' => count($params),
            'params' => $params,
            'type' => $type,
        ];
    }

    /**
     * @return array
     */
    public function collect()
    {
        $templates = $this->templates;

        return [
            'nb_templates' => count($templates),
            'templates' => $templates,
        ];
    }
}
