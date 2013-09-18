<?php

namespace LemoBase\Form\View\Helper;

use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormRenderRow extends AbstractHelper
{
    /**
     * @var \Zend\Form\Form
     */
    protected $form;

    /**
     * Invoke helper as functor
     * Proxies to {@link render()}.
     *
     * @param array      $elements
     * @param array $hideConditions
     * @param bool       $hideOn
     * @param array $options
     * @return string
     */
    public function __invoke($elements = null, array $hideConditions = array(), $hideOn = false, $options = array())
    {
        if($elements instanceof Form) {
            $this->setForm($elements);

            return $this;
        }

        if (!$elements) {
            return $this;
        }

        if(!is_array($elements)) {
            $elements = array($elements);
        }

        return $this->render($elements, $hideConditions, $hideOn, $options);
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @param array      $elements
     * @param null|array $hideConditions
     * @param bool       $hideConditionIs
     * @param array      $options
     * @return string
     */
    public function render(array $elements, $hideConditions = null, $hideConditionIs = false, array $options)
    {
        // Verify if elements exists
        $foundElements = array();
        $firstElementName = null;
        foreach($elements as $name => $attr) {
            if(is_numeric($name)) { $name = $attr; $attr = array(); }

            if($this->getForm()->has($name)) {
                $foundElements[$name] = $attr;

                if(null === $firstElementName) {
                    $firstElementName = $name;
                }
            }
        }

        // No elements found, return empty string
        if(null === $firstElementName) {
            return '';
        }

        $groupClass = null;
        $lineClass = null;
        if(!empty($hideConditions)) {
            foreach($hideConditions as $field => $value) {
                if(!is_array($value)) {
                    $value = array(0 => $value);
                }

                if(null === $lineClass && (false === $hideConditionIs && !in_array($this->getForm()->get($field)->getValue(), $value) || true === $hideConditionIs && in_array($this->getForm()->get($field)->getValue(), $value))) {
                    $lineClass = ' hide';
                }
            }
        }

        if(isset($options['name'])) {
            $lineName = $options['name'];
        } else {
            $lineName = $this->getId($this->getForm()->get($firstElementName));
        }

        $isValid = true;
        if(count($this->getForm()->get($name)->getMessages()) > 0) {
            $groupClass .= ' error';
            $isValid = false;
        }

        if(isset($attr['append']) || isset($attr['append_button'])) {
            $groupClass .= ' input-append';
        }

        $xhtml = array();
        $xhtml[] = '<div class="form-group' . $lineClass . '' . $groupClass . '" id="form-group-' . $lineName . '">';
        $xhtml[] = $this->view->formControlLabel($this->getForm()->get($firstElementName));
        $xhtml[] = '<div class="col-lg-8">';

        // Render elements
        foreach($foundElements as $name => $attr)
        {
            $prepend = '';
            if(isset($attr['prepend'])) {
                $prepend = $attr['prepend'];
            }
            $append = '';
            if(isset($attr['append'])) {
                $append = $attr['append'];
            }
            $appendButton = '';
            if(isset($attr['append_button'])) {
                $appendButton = $attr['append_button'];
            }
            $separator = '';
            if(isset($attr['separator'])) {
                $separator = '<span class="separator">' . $attr['separator'] . '</span>';
            }

            if(null === $this->getForm()->get($name)->getAttribute('id')) {
                $this->getForm()->get($name)->setAttribute('id', $this->getId($this->getForm()->get($name)));
            }
            if($this->getForm()->get($name)->getValue() instanceof \DateTime) {
                $this->getForm()->get($name)->setValue($this->view->dateFormat($this->getForm()->get($name)->getValue(), \IntlDateFormatter::MEDIUM));
            }

            $xhtml[] = '		' . $prepend . ' ' . $this->view->formElement($this->getForm()->get($name)) . $separator . ' ' . $appendButton . ' ' . $append;

            if(isset($attr['append'])) {
                $xhtml[] = '	<span class="add-on">' . $attr['append'] . '</span>';
            }
        }

        // Render errors
        if(false === $isValid) {
            $messages = array();

            foreach($foundElements as $name => $attr) {
                $messages = array_merge($messages, $this->getForm()->get($name)->getMessages());
            }

            $xhtml[] = '<span class="help-inline">' . current($messages) . '</span>';
        } else {
            if(isset($options['help'])) {
                $xhtml[] = '<span class="help-inline">' . $options['help'] . '</span>';
            }
        }

        $xhtml[] = '</div>';
        $xhtml[] = '</div>';

        return implode(PHP_EOL, $xhtml);
    }

    /**
     * @param FormInterface $form
     * @return FormRenderRow
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }
}
