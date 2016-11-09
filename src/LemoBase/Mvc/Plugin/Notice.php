<?php

namespace LemoBase\Mvc\Plugin;

use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;

class Notice extends FlashMessenger
{
    const ERROR       = 'danger';
    const ERROR_FORM  = 'danger';
    const INFORMATION = 'info';
    const SUCCESS     = 'success';
    const WARNING     = 'warning';

    /**
     * Add new error notice to the buffer
     *
     * @param  string      $text
     * @param  string|null $title
     * @param  string|null $id
     * @return $this
     */
    public function error($text, $title = null, $id = null)
    {
        if (null === $title) {
            $title = 'Error';
        }

        $this->_addNotice(self::ERROR, $text, $title, $id);

        return $this;
    }

    /**
     * Add errors notices from form
     *
     * @param  FormInterface $form
     * @return $this
     */
    public function errorForm(FormInterface $form)
    {
        $messagesByElements = $this->createFlattenMessages($form, $form->getMessages());

        // Grab errors from form
        $formError = [];
        foreach ($messagesByElements as $elementLabel => $elementErrors) {
            foreach ($elementErrors as $message) {
                if (!empty($message)) {
                    $formError[$message][] = $elementLabel;
                }
            }
        }

        // Add error notices
        foreach ($formError as $message => $elements) {
            sort($elements);

            $this->_addNotice(self::ERROR_FORM, $elements, $message . ':');
        }

        return $this;
    }

    public function createFlattenMessages($fieldset, $errorMessages, $parentNames = [])
    {
        $names = [];
        foreach ($errorMessages as $elementName => $elementErrorMessages) {
            if ($fieldset->has($elementName)) {
                $element = $fieldset->get($elementName);

                if ($element instanceof FieldsetInterface) {
                    if (!empty($element->getLabel())) {
                        $parentNames[] = $element->getLabel();
                    }

                    $names = array_merge($names, $this->createFlattenMessages($element, $elementErrorMessages, $parentNames));
                } else {

                    // Create element name
                    $name = $element->getLabel();
                    if (empty($name)) {
                        $name = $element->getLabel();
                    }

                    // Create element name for fieldset element
                    if (!empty($parentNames)) {
                        $name = implode(' > ', $parentNames) . ' > ' . $name;
                    }

                    $name = strip_tags($name);
                    $name = trim($name);

                    // Append error messages to element
                    foreach ($elementErrorMessages as $errorKey => $errorMessage) {
                        $names[$name][$errorKey] = $errorMessage;
                    }
                }
            }
        }

        return $names;
    }

    /**
     * Add new information notice to the buffer
     *
     * @param  string      $text
     * @param  string|null $title
     * @param  string|null $id
     * @return $this
     */
    public function information($text, $title = null, $id = null)
    {
        if (null === $title) {
            $title = 'Information';
        }

        $this->_addNotice(self::INFORMATION, $text, $title, $id);

        return $this;
    }

    /**
     * Add new success notice to the buffer
     *
     * @param  string      $text
     * @param  string|null $title
     * @param  string|null $id
     * @return $this
     */
    public function success($text, $title = null, $id = null)
    {
        if (null === $title) {
            $title = 'Success';
        }

        $this->_addNotice(self::SUCCESS, $text, $title, $id);

        return $this;
    }

    /**
     * Add new warning notice to the buffer
     *
     * @param  string      $text
     * @param  string|null $title
     * @param  string|null $id
     * @return $this
     */
    public function warning($text, $title = null, $id = null)
    {
        if (null === $title) {
            $title = 'Warning';
        }

        $this->_addNotice(self::WARNING, $text, $title, $id);

        return $this;
    }

    /**
     * Get messages that have been added to the current
     * namespace within this request
     *
     * @return array
     */
    public function getCurrentMessagesAndClear()
    {
        $messages = $this->getCurrentMessages();

        $this->clearCurrentMessages();

        return $messages;
    }

    /**
     * Add new notice to the flashMessanger buffer
     *
     * @param string      $type danger|information|success|warning
     * @param string      $text
     * @param string|null $title
     * @param string|null $id
     * @throws \Exception
     */
    protected function _addNotice($type, $text, $title = null, $id = null)
    {
        if (!in_array($type, [self::ERROR, self::ERROR_FORM, self::INFORMATION, self::SUCCESS, self::WARNING])) {
            throw new \Exception("Message type '{$type}' is not supported.");
        }

        $message = [
            'type'  => $type,
            'title' => $title,
            'text'  => $text,
            'id'    => $id,
        ];

        parent::addMessage($message);
    }
}
