<?php

namespace Lemo\Base\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Lemo\Base\Mvc\Plugin\Notice as ControllerPluginNotice;

class Notice extends AbstractHelper
{
    /**
     * Append text string
     *
     * @var string
     */
    protected $textAppendString = null;

    /**
     * Prepend text string
     *
     * @var string
     */
    protected $textPrependString = null;

    /**
     * Append title string
     *
     * @var string
     */
    protected $titleAppendString = null;

    /**
     * Prepend title string
     *
     * @var string
     */
    protected $titlePrependString = null;

    /**
     * Whether or not auto-translation is enabled
     * @var boolean
     */
    protected $translate = true;

    public function __construct(
        private ControllerPluginNotice $notice
    ) {
        $this->notice = $notice;
    }

    public function __invoke(): self
    {
        return $this;
    }

    /**
     * String representation
     *
     * @return string
     */
    public function render(): string
    {
        if (false === $this->notice->hasMessages() && false === $this->notice->hasCurrentMessages()) {
            return '';
        }

        $xhtml[] = '<script type="text/javascript">';

        $messages = array_merge(
            $this->notice->getCurrentMessages(),
            $this->notice->getMessages()
        );

        $this->notice->clearMessages();
        $this->notice->clearCurrentMessages();

        foreach($messages as $message) {
            // Prepare title
            if (!empty($message['title'])) {
                if (is_array($message['title'])) {
                    $messageTitles = '';
                    foreach ($message['title'] as $title) {
                        if ($this->translate) {
                            $messageTitles[] = $this->getView()->translate($title);
                        } else {
                            $messageTitles[] = $title;
                        }
                    }

                    $message['title'] = implode('<br>', $messageTitles);
                } else {
                    if ($this->translate) {
                        $message['title'] = $this->getView()->translate($message['title']);
                    }
                }

                $message['title'] = $this->getTitlePrependString() . $message['title'] . $this->getTitleAppendString();
            }

            // Prepare text
            if (is_array($message['text'])) {
                $messageTexts = [];
                foreach ($message['text'] as $text) {
                    if ($this->translate) {
                        $messageTexts[] = $this->getView()->translate($text);
                    } else {
                        $messageTexts[] = $text;
                    }
                }

                if ('danger' == $message['type']) {
                    $message['text'] = '<ul class="padding-left-30"><li>' . implode('</li><li>', $messageTexts) . '</li></ul>';
                } else {
                    $message['text'] = implode('<br>', $messageTexts);
                }
            } else {
                if ($this->translate) {
                    $message['text'] = $this->getView()->translate($message['text']);
                }
            }

            // Replace
            $message['text'] = str_replace(["\n", "\r"], '', nl2br((string) $message['text']));

            // Id
            if(empty($message['id'])) {
                $message['id'] = str_replace('.', '', uniqid('alert_', true));
            }

            if (ControllerPluginNotice::ERROR_FORM != $message['type']) {
                $message['text'] = $this->getTextPrependString() . $message['text'] . $this->getTextAppendString();
            }

            $xhtml[] = "Lemo_Alert.build('" .$message['type'] . "', '" .addslashes($message['title']) . "', '" .addslashes(str_replace("'", '`', $message['text'])) . "', '" . $message['id'] . "');";
        }

        $xhtml[] = '</script>';

        return implode(PHP_EOL, $xhtml);
    }

    /**
     * Set text append string
     *
     * @param  string $textAppendString
     * @return Notice
     */
    public function setTextAppendString($textAppendString)
    {
        $this->textAppendString = $textAppendString;
        return $this;
    }

    /**
     * Return text append string
     *
     * @return string
     */
    public function getTextAppendString()
    {
        return $this->textAppendString;
    }

    /**
     * Set message prepend string
     *
     * @param  string $textPrependString
     * @return Notice
     */
    public function setTextPrependString($textPrependString)
    {
        $this->textPrependString = $textPrependString;
        return $this;
    }

    /**
     * Return message prepend string
     *
     * @return string
     */
    public function getTextPrependString()
    {
        return $this->textPrependString;
    }

    /**
     * Set title append string
     *
     * @param  string $titleAppendString
     * @return Notice
     */
    public function setTitleAppendString($titleAppendString)
    {
        $this->titleAppendString = $titleAppendString;
        return $this;
    }

    /**
     * Return title append string
     *
     * @return string
     */
    public function getTitleAppendString()
    {
        return $this->titleAppendString;
    }

    /**
     * Set title prepend string
     *
     * @param  string $titlePrependString
     * @return Notice
     */
    public function setTitlePrependString($titlePrependString)
    {
        $this->titlePrependString = $titlePrependString;
        return $this;
    }

    /**
     * Return title prepend string
     *
     * @return string
     */
    public function getTitlePrependString()
    {
        return $this->titlePrependString;
    }

    // Translator

    /**
     * Enables translation
     *
     * @return Notice
     */
    public function enableTranslation()
    {
        $this->translate = true;
        return $this;
    }

    /**
     * Disables translation
     *
     * @return Notice
     */
    public function disableTranslation()
    {
        $this->translate = false;
        return $this;
    }
}
