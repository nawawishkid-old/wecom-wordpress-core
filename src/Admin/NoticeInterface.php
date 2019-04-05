<?php

namespace WCWP\Admin;

use WCWP\Admin\Page;

interface NoticeInterface
{
    public function setCapability(string $capability) : NoticeInterface;
    public function getCapability() : string;
    public function setContentCallback(callable $callback) : NoticeInterface;
    public function getContentCallback() : callable;

    /**
     * Add page this notice will be displayed.
     * 
     * @param Page|string $page Page instance, Page::class string, or page' slug.
     * 
     * @return NoticeInterface
     */
    public function addPage($page) : NoticeInterface;
    public function getPages() : array;
    public function render();
    public function register();
}