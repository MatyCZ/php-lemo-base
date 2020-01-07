<?php

/**
 * @namespace
 */
namespace LemoBase\Validator;

use Laminas\Validator\AbstractValidator;

class Uri extends AbstractValidator
{
    const INVALID          = 'invalid';
    const INVALID_FRAGMENT = 'invalidFragment';
    const INVALID_HOST     = 'invalidHost';
    const INVALID_PORT     = 'invalidPort';
    const INVALID_PATH     = 'invalidPath';
    const INVALID_QUERY    = 'invalidQuery';
    const INVALID_SCHEME   = 'invalidScheme';
    const INVALID_USERINFO = 'invalidUserInfo';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID          => "The value is not a valid uri. Use the basic format scheme://host/",
        self::INVALID_FRAGMENT => "'%fragment%' is not a valid fragment",
        self::INVALID_HOST     => "'%host%' is not a valid host",
        self::INVALID_PORT     => "'%port%' is not a valid port",
        self::INVALID_PATH     => "'%path%' is not a valid path",
        self::INVALID_QUERY    => "'%query%' is not a valid query",
        self::INVALID_SCHEME   => "'%scheme%' is not a valid scheme",
        self::INVALID_USERINFO => "'%userinfo%' is not a valid useinfo",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'fragment' => '_fragment',
        'host'     => '_host',
        'port'     => '_port',
        'path'     => '_path',
        'query'    => '_query',
        'scheme'   => '_scheme',
        'userinfo' => '_userinfo',
    );

    /**
     * @var string
     */
    protected $_fragment;

    /**
     * @var string
     */
    protected $_host;

    /**
     * @var string
     */
    protected $_port;

    /**
     * @var string
     */
    protected $_path;

    /**
     * @var string
     */
    protected $_query;

    /**
     * @var string
     */
    protected $_scheme;

    /**
     * @var string
     */
    protected $_userinfo;

    /**
     * Returns true if $value is valid URI
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $uri = new \Laminas\Uri\Uri($value);

        // Basic validation
        if(!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->_scheme = $uri->getScheme();
        $this->_userinfo = $uri->getUserInfo();
        $this->_host = $uri->getHost();
        $this->_port = $uri->getPort();
        $this->_path = $uri->getPath();
        $this->_query = $uri->getQuery();
        $this->_fragment= $uri->getFragment();

        // Validate scheme
        if(null !== $uri->getScheme() && false === $uri->validateScheme($uri->getScheme())) {
            $this->error(self::INVALID_SCHEME);
            return false;
        }

        // Validate userinfo
        if(null !== $uri->getUserInfo() && false === $uri->validateUserInfo($uri->getUserInfo())) {
            $this->error(self::INVALID_USERINFO);
            return false;
        }

        // Validate host
        if(null !== $uri->getHost() && false === $uri->validateHost($uri->getHost())) {
            $this->error(self::INVALID_HOST);
            return false;
        }

        // Validate port
        if(null !== $uri->getPort() && false === $uri->validatePort($uri->getPort())) {
            $this->error(self::INVALID_PORT);
            return false;
        }

        // Validate path
        if(null !== $uri->getPath() && false === $uri->validatePath($uri->getPath())) {
            $this->error(self::INVALID_PATH);
            return false;
        }

        // Validate query
        if(null !== $uri->getQuery() && false === $uri->validateQueryFragment($uri->getQuery())) {
            $this->error(self::INVALID_QUERY);
            return false;
        }

        // Validate fragment
        if(null !== $uri->getFragment() && false === $uri->validateQueryFragment($uri->getFragment())) {
            $this->error(self::INVALID_FRAGMENT);
            return false;
        }

        return true;
    }
}
