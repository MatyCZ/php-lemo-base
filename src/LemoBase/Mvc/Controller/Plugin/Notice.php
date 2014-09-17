<?php

namespace LemoBase\Mvc\Controller\Plugin;

use Zend\Form\Form;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class Notice extends FlashMessenger
{
    const ERROR       = 'danger';
    const ERROR_FORM  = 'danger';
    const INFORMATION = 'info';
    const SUCCESS     = 'success';
    const WARNING     = 'warning';

    protected $translator;

    /**
     * Add new error notice to the buffer
     *
     * @param  string      $text
     * @param  string|null $title
     * @return \LemoBase\Mvc\Controller\Plugin\Notice
     */
    public function error($text, $title = null)
    {
        if(null === $title) {
            $title = 'Chyba';
        }

        $this->_addNotice($text, $title, self::ERROR);

        return $this;
    }

    /**
     * Add errors notices from form
     *
     * @param Form $form
     * @return \LemoBase\Mvc\Controller\Plugin\Notice
     */
    public function errorForm(Form $form)
    {
        $formError = array();
        $messages = $form->getInputFilter()->getMessages();

        // Grab errors from subforms
        foreach($form->getFieldsets() as $fieldset) {
            if ($fieldset instanceof Form) {
                $elements = $fieldset->getElements();
                $inputFilter = $fieldset->getInputFilter();

                if (null !== $inputFilter) {
                    foreach($inputFilter->getMessages() as $errors) {
                        foreach($errors as $element => $fieldsetMessages) {
                            if(isset($elements[$element])) {
                                foreach($fieldsetMessages as $message) {
                                    $formError[$message][] = $elements[$element]->getLabel();
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
        foreach($messages as $element => $errors) {
            foreach($errors as $message) {
                if(array_key_exists($element, $elements)) {
                    if ('' != $elements[$element]->getLabel()) {
                        $formError[$message][] = $this->getController()->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer')->translate($elements[$element]->getLabel());
                    } else {
                        $formError[$message][] = $elements[$element]->getLabel();
                    }
                }
            }
        }

        // Add error notices
        foreach($formError as $message => $elements) {
            sort($elements);

            $this->_addNotice(implode(', ', $elements), $message . ':', self::ERROR_FORM);
        }

        return $this;
    }

    /**
     * Add new information notice to the buffer
     *
     * @param  string      $text
     * @param  string|null $title
     * @return \LemoBase\Mvc\Controller\Plugin\Notice
     */
    public function information($text, $title = null)
    {
        if(null === $title) {
            $title = 'Informace';
        }

        $this->_addNotice($text, $title, self::INFORMATION);

        return $this;
    }

    /**
     * Add new success notice to the buffer
     *
     * @param  string      $text
     * @param  string|null $title
     * @return \LemoBase\Mvc\Controller\Plugin\Notice
     */
    public function success($text, $title = null)
    {
        if(null === $title) {
            $title = 'Úspěch';
        }

        $this->_addNotice($text, $title, self::SUCCESS);

        return $this;
    }

    /**
     * Add new warning notice to the buffer
     *
     * @param  string      $text
     * @param  string|null $title
     * @return \LemoBase\Mvc\Controller\Plugin\Notice
     */
    public function warning($text, $title = null)
    {
        if(null === $title) {
            $title = 'Upozornění';
        }

        $this->_addNotice($text, $title, self::WARNING);

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
     * @param string $text
     * @param string|null $title
     * @param danger|information|success|warning $type
     */
    protected function _addNotice($text, $title, $type)
    {
        $text = (string) $text;

        if(!in_array($type, array(self::ERROR, self::ERROR_FORM, self::INFORMATION, self::SUCCESS, self::WARNING))) {
            throw new \Exception("Message type '{$type}' isn`t supported.");
        }

        if(null === $title) {
            $title = $type;
        }

        $text = array(
            'type' => $type,
            'title' => $title,
            'text' => $text,
        );

        parent::addMessage($text);
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    public function getTranslator()
    {
        return $this->translator;
    }
}
