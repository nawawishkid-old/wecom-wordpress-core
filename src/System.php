<?php

namespace WCWP;

use WCWP\Runner;
use WCWP\Admin\PageManager;
use WCWP\Admin\Page;

class System
{
    /**
     * System's modules
     * 
     * @var array
     */
    protected $modules = [];

    /**
     * @param Runner $runner Event runner.
     */
    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * Getter for getting system's modules.
     * 
     * @param string $name Module name.
     * @return mixed Module instance.
     */
    public function __get($name)
    {
        if (empty($this->modules[$name])) {
            trigger_error(
                sprintf("Module not found: <b>%s</b>", $name), 
                E_USER_ERROR
            );
        } else {
            return $this->modules[$name];
        }
    }

    /**
     * Initialize
     * 
     * @return void
     */
    public function start() : void
    {
        if (is_a($this->page, PageManager::class)) {
            $this->page->init();
        }
    }

    /**
     * Add module name to the system for later instantiation.
     * 
     * @TODO: should accept both class string name and object instance.
     * 
     * @api
     * @param string $module_name Name of module to be added.
     * @return System
     */
    public function add(string $module_name)
    {
        switch ($module_name) {
            case PageManager::class:
                $this->modules['page'] = new $module_name($this->runner);
                break;
            
            default:
                trigger_error(
                    sprintf('Unknown system module <b>%s</b> has been rejected.', $module_name), 
                    E_USER_WARNING
                );
                break;
        }

        return $this;
    }
}