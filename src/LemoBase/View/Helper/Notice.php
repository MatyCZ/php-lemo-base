<?php

namespace LemoBase\View\Helper;

use LemoBase\Mvc\Controller\Plugin\Notice as NoticeControllerPlugin,
    Zend\View\Helper\AbstractHelper,
    Zend\View\Exception;

class Notice extends AbstractHelper
{
    /**
     * @var \LemoBase\Mvc\Controller\Plugin\Notice
     */
    protected $_notice = null;

    /**
     * Append text string
     *
     * @var string
     */
    protected $_textAppendString = null;

    /**
     * Prepend text string
     *
     * @var string
     */
    protected $_textPrependString = null;

    /**
     * Append title string
     *
     * @var string
     */
    protected $_titleAppendString = null;

    /**
     * Prepend title string
     *
     * @var string
     */
    protected $_titlePrependString = null;

    /**
     * Whether or not auto-translation is enabled
     * @var boolean
     */
    protected $_translate = true;

    /**
     * Render script with notices
     *
     * @return string
     */
    public function __invoke()
    {
        if(null === $this->_notice) {
            $this->_notice = new NoticeControllerPlugin();
        }

        return $this;
    }

    /**
     * String representation
     *
     * @return string
     */
    public function toString()
    {
        if(!$this->_notice->hasMessages()) {
            return '';
        }

        $xhtml[] = '<script type="text/javascript">';

        foreach($this->_notice->getMessages() as $message) {
            $message['title'] = $this->getTitlePrependString() . $message['title'] . $this->getTitleAppendString();

            if(NoticeControllerPlugin::ERROR_FORM != $message['type']) {
                $message['text'] = $this->getTextPrependString() . $message['text'] . $this->getTextAppendString();
            }

            if($this->_translate) {
                $message['title'] = $this->getView()->translate($message['title']);
                $message['text'] = $this->getView()->translate($message['text']);
            }
        }

        $xhtml[] = "Lemo_Alert.build('" .$message['type'] . "', '" .addslashes($message['title']) . "', '" .addslashes(str_replace("'", '`', $message['text'])) . "');";
        $xhtml[] = '</script>';

        return implode(PHP_EOL, $xhtml);
    }

    /**
     * Cast to string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Set text append string
     *
     * @param string $textAppendString
     * @return \LemoBase\View\Helper\Notice
     */
    public function setTextAppendString($textAppendString)
    {
        $this->_textAppendString = $textAppendString;

        return $this;
    }

    /**
     * Return text append string
     *
     * @return string
     */
    public function getTextAppendString()
    {
        return $this->_textAppendString;
    }

    /**
     * Set message prepend string
     *
     * @param string $textPrependString
     * @return \LemoBase\View\Helper\Notice
     */
    public function setTextPrependString($textPrependString)
    {
        $this->_textPrependString = $textPrependString;

        return $this;
    }

    /**
     * Return message prepend string
     *
     * @return string
     */
    public function getTextPrependString()
    {
        return $this->_textPrependString;
    }

    /**
     * Set title append string
     *
     * @param string $titleAppendString
     * @return \LemoBase\View\Helper\Notice
     */
    public function setTitleAppendString($titleAppendString)
    {
        $this->_titleAppendString = $titleAppendString;

        return $this;
    }

    /**
     * Return title append string
     *
     * @return string
     */
    public function getTitleAppendString()
    {
        return $this->_titleAppendString;
    }

    /**
     * Set title prepend string
     *
     * @param string $titlePrependString
     * @return \LemoBase\View\Helper\Notice
     */
    public function setTitlePrependString($titlePrependString)
    {
        $this->_titlePrependString = $titlePrependString;

        return $this;
    }

    /**
     * Return title prepend string
     *
     * @return string
     */
    public function getTitlePrependString()
    {
        return $this->_titlePrependString;
    }

    // Translator

    /**
     * Enables translation
     *
     * @return \LemoBase\View\Helper\Notice
     */
    public function enableTranslation()
    {
        $this->_translate = true;

        return $this;
    }

    /**
     * Disables translation
     *
     * @return \LemoBase\View\Helper\Notice
     */
    public function disableTranslation()
    {
        $this->_translate = false;

        return $this;
    }
}
