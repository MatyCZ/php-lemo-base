<?php

/**
 * @namespace
 */
namespace LemoBase\Grid\Exception;

/**
 * @uses       LemoBase\Grid\Exception
 * @uses       \BadMethodCallException
 * @category   LemoBase
 * @package    LemoBase_Grid
 */
class BadMethodCallException
    extends \BadMethodCallException
    implements \LemoBase\Grid\Exception
{}
