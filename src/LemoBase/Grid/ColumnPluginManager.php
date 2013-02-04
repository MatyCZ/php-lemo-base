<?php

/**
 * @namespace
 */
namespace LemoBase\Grid;

use Zend\ServiceManager\AbstractPluginManager;

class ColumnPluginManager extends AbstractPluginManager
{
    /**
     * Default set of columns
     *
     * @var array
     */
    protected $invokableClasses = array(
		'concat'	  => 'LemoBase\Grid\Column\Concat',
		'option'	  => 'LemoBase\Grid\Column\Option',
		'text'		  => 'LemoBase\Grid\Column\Text',
		'url'		  => 'LemoBase\Grid\Column\Url',
    );

    /**
     * @var bool Do not share by default
     */
    protected $shareByDefault = false;

    /**
     * Validate the plugin
     *
     * Checks that the column loaded is an instance of Column\ColumnInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Column\ColumnInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Column\ColumnInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
