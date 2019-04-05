<?php

namespace WCWP\Admin;

use WCWP\Admin\PageInterface;
use WCWP\Admin\NoticeInterface;

class Page implements PageInterface
{
    /**
     * Page slug
     * 
     * @var string
     */
    protected $slug; 

    /**
     * User capability to access this page
     * 
     * @var string
     */
    protected $capability;

    /**
     * Title of HTML document
     * 
     * @var string
     */
    protected $pageTitle;

    /**
     * Title of admin menu
     * 
     * @var string
     */
    protected $menuTitle;

    /**
     * Content callback
     * 
     * @var callable
     */
    protected $contentCallback;

    protected $iconURL = '';
    protected $position;

    /**
     * Left footer text of page
     * 
     * @var string
     */
    protected $leftFooterText;
    
    /**
     * Right footer text of page
     * 
     * @var string
     */
    protected $rightFooterText;

    /**
     * Array of subpages
     * 
     * @var array
     */
    protected $subpages = [];

    /**
     * Page's notices
     * 
     * @var array
     */
    protected $notices = [];

    /**
     * Parent page of this page
     * 
     * @var PageInterface
     */
    protected $parent;

    protected $isTopPage = true;

    public function isTopPage() : bool
    {
        return $this->isTopPage;
    }

    public function setSlug(string $slug) : PageInterface
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @param string $url WordPress's built-in icon name, base64-encoded SVG, 'none'
     * @return Page
     */
    public function setIconURL(string $url) : PageInterface
    {
        $this->iconURL = $url;

        return $this;
    }

    public function setPosition(int $position) : PageInterface
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Additional method to set both page and menu title.
     */
    public function setTitle(string $title) : PageInterface
    {
        return $this->setPageTitle($title)->setMenuTitle($title);
    }

    public function setPageTitle(string $title) : PageInterface
    {
        $this->pageTitle = $title;

        return $this;
    }

    public function setMenuTitle(string $title) : PageInterface
    {
        $this->menuTitle = $title;

        return $this;
    }

    public function setCapability(string $capability) : PageInterface
    {
        $this->capability = $capability;

        return $this;
    }

    public function setLeftFooterText(string $text) : PageInterface
    {
        $this->leftFooterText = $text;

        return $this;
    }

    public function setRightFooterText(string $text) : PageInterface
    {
        $this->rightFooterText = $text;

        return $this;
    }

    public function setContentCallback(callable $callback) : PageInterface
    {
        $this->contentCallback = $callback;

        return $this;
    }

    public function getContentCallback() : callable
    {
        return $this->contentCallback;
    }

    public function getParent() : PageInterface
    {
        return self::getInstance($this->parent);
    }

    public function setParent(PageInterface $parent) : PageInterface
    {
        $this->parent = $parent;

        return $this;
    }

    public function toBeTopPage() : PageInterface
    {
        $this->isTopPage = true;

        return $this;
    }

    public function toBeSubpage() : PageInterface
    {
        $this->isTopPage = false;

        return $this;
    }

    /**
     * Get menu's icon URL.
     * 
     * @return string
     */
    public function getIconURL() : string
    {
        return $this->iconURL; 
    }

    /**
     * Get page menu position.
     * 
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Get title of page's HTML document.
     * 
     * @api
     * @return string Page's HTML document title.
     */
    public function getPageTitle() : string
    {
        return $this->pageTitle;
    }

    /**
     * Get title of page's admin menu.
     * 
     * @api
     * @return string Menu title.
     */
    public function getMenuTitle() : string
    {
        return $this->menuTitle;
    }

    /**
     * Get page' slug.
     * 
     * @api
     * @return string Page' slug.
     */
    public function getSlug() : string
    {
        return $this->slug;
    }

    /**
     * Get capability level for accessing this page.
     * 
     * @api
     * @return string Page's capability level.
     */
    public function getCapability() : string
    {
        return $this->capability;
    }

    /**
     * Get left footer text of page.
     * 
     * @api
     * @return string Left footer text.
     */
    public function getLeftFooterText(string $text) : string
    {
        return empty($this->leftFooterText) ? $text : $this->leftFooterText;
    }

    /**
     * Check if this page has left footer text.
     * 
     * @return bool Check result.
     */
    public function hasLeftFooterText() : bool
    {
        $test = 'x';

        return $this->getLeftFooterText($test) !== $test;
    }

    /**
     * Get right footer text of page.
     * 
     * @api
     * @return string Right footer text.
     */
    public function getRightFooterText(string $text) : string
    {
        return empty($this->rightFooterText) ? $text : $this->rightFooterText;
    }

    /**
     * Check if this page has right footer text.
     * 
     * @return bool Check result.
     */
    public function hasRightFooterText() : bool
    {
        $test = 'x';

        return $this->getRightFooterText($test) !== $test;
    }

    /**
     * Get page's notices.
     * 
     * @api
     * @return array Array of page's notices.
     */
    public function getNotices() : array
    {
        return $this->notices;
    }

    /**
     * Echo page's content.
     * 
     * @api
     */
    public function render()
    {
        $this->getContentCallback()();
    }

    /**
     * Add page's notice.
     * 
     * @api
     * @param NoticeInterface $notice Notice instance to be added.
     * @return PageInterface 
     */
    public function addNotice(NoticeInterface $notice) : PageInterface
    {
        $this->notices[] = $notice;

        return $this;
    }

    public function register()
    {
        add_action('admin_menu', [$this, '_register']);
    }

    /**
     * Register all pages to WordPress admin
     * 
     * @api
     * @return void
     */
    public function _register()
    {
        $is_top_page = $this->isTopPage();

        /**
         * Register subpages for top-level page.
         */
        if ($is_top_page) {
            call_user_func_array(
                'add_menu_page', 
                self::getArgumentsForWordPressAddPageFunction($this)
            );
            $this->registerSubpages();
        } else {
            call_user_func_array(
                'add_submenu_page',
                self::getArgumentsForWordPressAddPageFunction($this, $this->getParent())
            );
        }

        $this->registerPageNotices();
        $this->registerPageFooterText();
    }

    private function registerSubpages()
    {
        $parent_slug = $this->getSlug();

        foreach ($this->getSubpages() as $subpage) {
            $subpage = self::getInstance($subpage);

            if ($subpage->isTopPage()) {
                continue;
            }

            $subpage->setParent($this)->_register();
        }
    }

    /**
     * Get array of arguments of WordPress 'add_menu_page' or 'add_submenu_page' based on given $page.
     * 
     * @param Page|string $page Instance of Page or Page::class string.
     * @param Page|string $parent (Optional) Instance of Page or Page::class string to be used as $page's parent page.
     * @return array Array of arguments. 
     */
    private static function getArgumentsForWordPressAddPageFunction(
        Page $page, 
        $parent = null
    ) : array
    {
        $args = [
            $page->getPageTitle(),
            $page->getMenuTitle(),
            $page->getCapability(),
            $page->getSlug(),
            [$page, 'render']
        ];

        /**
         * Alter arguments for specific WordPress add page function.
         */
        if ($page->isTopPage()) {
            array_push($args, $page->getIconURL(), $page->getPosition());
        } else {
            array_unshift($args, self::getInstance($parent)->getSlug());
        }

        return $args;
    } 

    public function addSubpage(PageInterface $subpage) : PageInterface
    {
        $this->subpages[] = $subpage;

        return $this;
    }

    public function getSubpages() : array
    {
        return $this->subpages;
    }

    /**
     * Add page notices.
     * 
     * $notice can be an instance of inherited NoticeInterface class
     * or NoticeInterface::class string await to be instantiate.
     * 
     * @return void
     */
    private function registerPageNotices() : void
    {
        if (!$this->isCurrentPage()) {
            return;
        }

        foreach ($this->getNotices() as $notice) {
            $notice = Notice::getInstance($notice);

            $notice->addPage($this)->_register();

            /**
             * Use 'all_admin_notices' to avoid remove_all_action('admin_notices')
             */
            // $this->runner->on('all_admin_notices', [$notice, 'render']);
            // add_action('all_admin_notices', [$notice, 'render']);
        }
    }

    /**
     * Register page's footer text both left and right, if has any.
     * 
     * @return void
     */
    private function registerPageFooterText() : void
    {
        if (!$this->isCurrentPage()) {
            return;
        }

        /**
         * Add left/right footer text of the page.
         */
        if ($this->hasLeftFooterText()) {
            // $this->runner->addFilter(
            //     "admin_footer_text",
            //     [$this, 'getLeftFooterText']
            // );
            add_filter("admin_footer_text", [$this, 'getLeftFooterText']);
        }

        if ($this->hasRightFooterText()) {
            /**
             * Priority 11 to filter after WordPress prints the current version and update information, using core_update_footer() at priority 10.
             *  
             * @see https://developer.wordpress.org/reference/hooks/update_footer/
             */
            // $this->runner->addFilter(
            //     'update_footer',
            //     [$page, 'getRightFooterText'], 
            //     11
            // );
            add_filter('update_footer', [$this, 'getRightFooterText'], 11);
        }
    }

    /**
     * Check if this page is current page.
     * 
     * @return bool
     */
    private function isCurrentPage() : bool
    {
        return self::getCurrentPageSlug() === $this->getSlug();
    }

    /**
     * Get current admin page slug.
     * 
     * @return string Current page slug.
     */
    public static function getCurrentPageSlug() : string
    {
        if (isset($_GET['page'])) {
            return $_GET['page'];
        }

        $basename = basename($_SERVER['REQUEST_URI']);

        return empty($basename) || $basename === 'wp-admin' 
            ? 'index.php' 
            : $basename;
    }

    /**
     * Instantiate Page::class if required to enable flexibility of accepting both Page instance and Page::class string.
     * 
     * @param Page|string $page Instance of Page or Page::class string.
     * @return Page
     */
    public static function getInstance($page) : PageInterface
    {
        if (!is_a($page, Page::class, true)) {
            // Exception here!
            $error_string = sprintf(
                'Page must be a direct instance of, or inherited instance of <b>%s</b>, <b>%s</b> given.',
                Page::class,
                get_class($page)
            );
            trigger_error($error_string, E_USER_ERROR);
        }

        if (is_string($page) && class_exists($page)) {
            $page = new $page;
        }

        return $page;
    }

    /**
     * Hide page of given slug if current user doesn't have given capability.
     * 
     * @param string $capability User capability to see this page.
     * @param string $slug Page slug to be hidden. 
     * @param string $parent_slug (Optional) If given, this method will hide subpage of given parent slug.
     * 
     * @return void
     */
    public static function hideIfNot(string $capability, string $slug, string $parent_slug = null, bool $limit_access = false) : void
    {
        add_action('admin_menu', function () use ($slug, $capability, $parent_slug) {
            if (current_user_can($capability)) {
                return;
            }

            if (is_string($parent_slug)) {
                remove_submenu_page($parent_slug, $slug);
            } else {
                remove_menu_page($slug);
            }
        }, 9999);

        if ($limit_access) {
            self::limitAccessIfNot($capability, $slug);
        }
    }

    /**
     * Limit access to page with given slug if user is not capable of.
     *
     * @note This is kind of useless because WordPress is automatically restricted access to the page that is remove by remove_menu/submenu_page functions.
     * 
     * @param string $capability Minimum capability to access given page.
     * @param string $slug Slug of the page to be limited access.
     * @param string $redirect_slug Page slug for user to be redirected to.
     * 
     * @return void
     */
    public static function limitAccessIfNot(string $capability, string $slug, string $redirect_slug = null) : void
    {
        add_action('init', function () use ($slug, $capability, $redirect_slug) {
            if (self::getCurrentPageSlug() !== $slug || current_user_can($capability)) {
                return;
            }
            
            if (is_string($redirect_slug)) {
                wp_redirect(admin_url($redirect_slug));
                exit;
            }

            $text = apply_filters(
                'wecom_message_admin_page_not_allowed', 
                __('Sorry, you are not allowed to access this page.')
            );
            wp_die($text, 403);
        });
    }
}