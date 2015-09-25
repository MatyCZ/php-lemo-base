<?php

namespace LemoBase\Mvc\Controller\Plugin;

use Zend\Form\FormInterface;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class Notice extends FlashMessenger
{
    const ERROR       = 'danger';
    const ERROR_FORM  = 'danger';
    const INFORMATION = 'info';
    const SUCCESS     = 'success';
    const WARNING     = 'warning';

    /**
     * List of input labels which be replaced
     *
     * @var array
     */
    protected $inputLabels = [];

    /**
     * Add new error notice to the buffer
     *
     * @param  string      $text
     * @param  string|null $title
     * @param  string|null $id
     * @return \LemoBase\Mvc\Controller\Plugin\Notice
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
     * @return Notice
     */
    public function errorForm(FormInterface $form)
    {
        $formError = [];
        $messages = $form->getInputFilter()->getMessages();

        // Grab errors from subforms
        foreach ($form->getFieldsets() as $fieldset) {
            if ($fieldset instanceof FormInterface) {
                $elements = $fieldset->getElements();
                $inputFilter = $fieldset->getInputFilter();

                if (null !== $inputFilter) {
                    foreach ($inputFilter->getMessages() as $errors) {
                        foreach ($errors as $element => $fieldsetMessages) {

                            if (array_key_exists($element, $elements)) {
                                foreach ($fieldsetMessages as $message) {
                                    $label = $element;

                                    // Element exists, get its label
                                    if (array_key_exists($element, $elements)) {
                                        $label = $elements[$element]->getLabel();
                                    }

                                    // Input has custom label, use it
                                    if (isset($this->inputLabels[$element])) {
                                        $label = $this->inputLabels[$element];
                                    }

                                    // Add label to messages
                                    $formError[$message][] = $label;
                                }
                            }
                        }
                    }
                }
                unset($messages[$fieldset->getName()]);
            }
        }

        // Grab errors from form
        $elements = $form->getElements();
        foreach ($messages as $element => $errors) {
            foreach ($errors as $message) {
                $label = $element;

                // Element exists, get its label
                if (array_key_exists($element, $elements)) {
                    $label = $elements[$element]->getLabel();
                }

                // Input has custom label, use it
                if (isset($this->inputLabels[$element])) {
                    $label = $this->inputLabels[$element];
                }

                // Add label to messages
                $formError[$message][] = $label;
            }
        }

        // Add error notices
        foreach ($formError as $message => $elements) {
            sort($elements);

            $this->_addNotice(self::ERROR_FORM, $elements, $message . ':');
        }

        return $this;
    }

    /**
     * Add new information notice to the buffer
     *
     * @param  string      $text
     * @param  string|null $title
     * @param  string|null $id
     * @return \LemoBase\Mvc\Controller\Plugin\Notice
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
     * @return \LemoBase\Mvc\Controller\Plugin\Notice
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
     * @return \LemoBase\Mvc\Controller\Plugin\Notice
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

    /**
     * @param  string $inputName
     * @param  string $inputLabel
     * @return Notice
     */
    public function addInputLabel($inputName, $inputLabel)
    {
        $this->inputLabels[$inputName] = $inputLabel;

        return $this;
    }

    /**
     * @param  string $inputName
     * @return Notice
     */
    public function removeInputLabel($inputName)
    {
        if (isset($this->inputLabels[$inputName])) {
            unset($this->inputLabels[$inputName]);
        }

        return $this;
    }

    /**
     * @param  array $inputLabels
     * @return Notice
     */
    public function setInputLabels($inputLabels)
    {
        $this->inputLabels = $inputLabels;

        return $this;
    }

    /**
     * @return array
     */
    public function getInputLabels()
    {
        return $this->inputLabels;
    }
}
