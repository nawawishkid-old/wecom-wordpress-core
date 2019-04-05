<?php

namespace WCWP\Admin;

use WCWP\Admin\NoticeInterface;

interface PageInterface
{
    public function setSlug(string $slug) : PageInterface;
    public function setPageTitle(string $title) : PageInterface;
    public function setMenuTitle(string $title) : PageInterface;
    public function setCapability(string $capability) : PageInterface;
    public function setLeftFooterText(string $text) : PageInterface;
    public function setRightFooterText(string $text) : PageInterface;
    public function setContentCallback(callable $callback) : PageInterface;
    public function getContentCallback() : callable;
    public function toBeTopPage() : PageInterface;
    public function toBeSubpage() : PageInterface;

    /**
     * Use only parent' slug.
     */
    public function setParent(PageInterface $parent) : PageInterface;
    public function getParent() : PageInterface;
    
    /**
     * Get page' slug.
     * 
     * @return string Page' slug.
     */
    public function getSlug() : string;
   
    /**
     * Get title of page's HTML document.
     * 
     * @return string Page's HTML document title.
     */
    public function getPageTitle() : string;

    /**
     * Get title of page's admin menu.
     * 
     * @return string Page's admin menu title.
     */
    public function getMenuTitle() : string;

    /**
     * Get capability level for accessing this page.
     * 
     * @return string Page's capability level.
     */
    public function getCapability() : string;

    /**
     * Get left footer text of page.
     * 
     * @return string Left footer text.
     */
    public function getLeftFooterText(string $text) : string;

    /**
     * Get right footer text of page.
     * 
     * @return string Right footer text.
     */
    public function getRightFooterText(string $text) : string;

    /**
     * Get page's notices.
     * 
     * @return array Array of page's notices.
     */ 
    public function getNotices() : array;

    /**
     * Add page's notice.
     * 
     * @param NoticeInterface $notice Notice instance to be added.
     */
    public function addNotice(NoticeInterface $notice);

    public function addSubpage(PageInterface $page) : PageInterface;
    public function getSubpages() : array;
    public function isTopPage() : bool;
    
    /**
     * Echo page's content.
     */
    public function render();

    /**
     * Register this page to WordPress
     */
    public function register();
}