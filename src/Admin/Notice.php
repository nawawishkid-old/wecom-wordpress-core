<?php

namespace WCWP\Admin;

use WCWP\Admin\NoticeInterface;
use WCWP\Admin\Page;

class Notice implements NoticeInterface
{
    /**
     * Empty string means anyone can see this notice.
     * 
     * @var string
     */
    protected $capability = '';
    protected $contentCallback;

    /**
     * Array of pages this notice will be displayed
     * 
     * @var array
     */
    protected $pages = [];

    public function setCapability(string $capability) : NoticeInterface
    {
        $this->capability = $capability;
        
        return $this;
    }

    public function getCapability() : string
    {
        return $this->capability;
    }

    public function setContentCallback(callable $callback) : NoticeInterface
    {
        $this->contentCallback = $callback;

        return $this;
    }

    public function getContentCallback() : callable
    {
        return $this->contentCallback;
    }

    public function render() : void
    {
        $this->getContentCallback()();
    }

    public function addPage($page) : NoticeInterface
    {
        $this->pages[] = $page;

        return $this;
    }

    public function getPages() : array
    {
        return $this->pages;
    }

    public function register() : void
    {
        add_action('admin_init', [$this, '_register']);
    }

    public function _register() : void
    {
        $cap = $this->getCapability();

        if (!empty($cap) && !current_user_can($cap)) {
            return;
        }

        $current_page_slug = Page::getCurrentPageSlug();
        $should_display = false;


        foreach ($this->getPages() as $page) {
            $page = Page::getInstance($page);

            /** If $page is page slug */
            if (
                $page->getSlug() === $current_page_slug 
                || is_string($page) 
                && $current_page_slug === $page
            ) 
            {
                $should_display = true;
                
                break;
            }
        }

        if ($should_display) {
            add_action('all_admin_notices', [$this, 'render']);
        }
    }

    public static function getInstance($notice)
    {
        if (!is_a($notice, NoticeInterface::class, true)) {
            // Exception here!
            $error_string = sprintf(
                'Notice must be an instance of inherited <b>%s</b> class, <b>%s</b> given.',
                NoticeInterface::class,
                get_class($notice)
            );
            trigger_error($error_string, E_USER_WARNING);
        }

        // Instantiate inherited Notice::class
        if (is_string($notice) && class_exists($notice)) {
            $notice = new $notice;
        }

        return $notice;
    }
}