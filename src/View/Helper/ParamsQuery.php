<?php

namespace Lemo\Base\View\Helper;

use Laminas\View\Helper\AbstractHelper;

use function array_key_exists;
use function implode;
use function parse_str;
use function parse_url;

class ParamsQuery extends AbstractHelper
{
    protected array $params = [];

    public function __invoke(): self
    {
        $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $params);

            $this->params = $params;
        }

        return $this;
    }

    public function render(): string
    {
        $stringParts = array();
        foreach ($this->params as $key => $value) {
            $stringParts[] = $key . '=' . $value;
        }

        return '?' . implode('&', $stringParts);
    }

    public function __toString()
    {
        return $this->render();
    }

    /**
     * Set a named query param
     */
    public function set(string $name, mixed $value): self
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Retrieve a named query param
     */
    public function get(string $name): mixed
    {
        if (!$this->has($name)) {
            return null;
        }

        return $this->params[$name];
    }

    /**
     * Does the query have a param by the given name?
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->params);
    }

    /**
     * Remove a named query param
     */
    public function remove(string $name): self
    {
        if (!$this->has($name)) {
            return $this;
        }

        unset($this->params[$name]);

        return $this;
    }
}
