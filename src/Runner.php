<?php

namespace WCWP;

/**
 * WordPress event (action hook) handler.
 */
class Runner
{
    /**
     * Add event listener (WordPress 'add_action' wrapper).
     * 
     * @api
     * @see https://developer.wordpress.org/reference/functions/add_action/
     * @return Runner 
     */
    public function on(
        string $event,
        $callback,
        $priority = 10,
        $accepted_args = 1
    ) : Runner
    {
        add_action($event, $callback, $priority, $accepted_args);

        return $this;
    }

    /**
     * Emit event (WordPress 'do_action' wrapper).
     * 
     * @api
     * @see https://developer.wordpress.org/reference/functions/do_action/
     * @return void
     */
    public function emit(string $event, $arg = '') : void
    {
        do_action($event, $arg);
    }

    /**
     * Remove registered event (WordPress 'remove_action' wrapper).
     * 
     * @api
     * @see https://codex.wordpress.org/Function_Reference/remove_action
     * @return void
     */
    public function removeAction(string $event_name, $callback, $priority = 10) : void
    {
        remove_action($event_name, $callback, $priority);
    }

    /**
     * WordPess 'add_filter' wrapper.
     * 
     * @api
     * @see https://developer.wordpress.org/reference/functions/add_filter/
     */
    public function addFilter(
        string $name,
        $callback,
        $priority = 10,
        $accepted_args = 1
    ) : Runner
    {
        add_filter($name, $callback, $priority, $accepted_args);

        return $this;
    }

    /**
     * WordPress 'appy_filters' wrapper.
     * 
     * @api
     * @see https://developer.wordpress.org/reference/functions/apply_filters/
     */
    public function filter(string $name, $value, ...$args)
    {
        return apply_filters($name, $value, ...$args);
    }
}