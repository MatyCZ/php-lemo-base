<?php

namespace LemoBase\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ParamsQuery extends AbstractHelper
{
    /**
     * @var array
     */
    protected $params = array();

    /**
     * __invoke
     *
     * @return ParamsQuery
     */
    public function __invoke()
    {
        $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

        $urlQuery = null;
        if(isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $params);

            $this->params = $params;
        }

        return $this;
    }

    /**
     * Render Query string
     *
     * @return string
     */
    public function render()
    {
        $stringParts = array();
        foreach($this->params as $key => $value) {
            $stringParts[] = $key . '=' . $value;
        }

        return '?' . implode('&', $stringParts);
    }

    /**
     * Render query string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Set a named query param
     *
     * @param  string $name
     * @param  string $value
     * @return ParamsQuery
     */
    public function set($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Retrieve a named query param
     *
     * @param  string $name
     * @return string
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            return null;
        }
        return $this->params[$name];
    }

    /**
     * Does the query have an param by the given name?
     *
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->params);
    }

    /**
     * Remove a named query param
     *
     * @param  string $name
     * @return ParamsQuery
     */
    public function remove($name)
    {
        if (!$this->has($name)) {
            return $this;
        }
        unset($this->params[$name]);

        return $this;
    }
}
