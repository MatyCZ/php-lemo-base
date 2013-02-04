<?php

namespace LemoBase\Form\View\Helper;

use Zend\Form\Form;
use Zend\View\Helper\AbstractHelper;

class FormValidator extends AbstractHelper
{
	/**
	 * Render script with notices
	 *
	 * @param  Form $form
	 * @param  string $url
	 * @return string
	 */
	public function __invoke(Form $form, $url, $urlSave = null)
	{
		$script[] = "var fN = '{$form->getName()}';";
		$script[] = "var uV = '{$url}';";

		if(null !== $urlSave) {
			$script[] = "var uS = '{$urlSave}';";
		}

		if(null !== $urlSave) {
			$script[] = "var uS = '{$urlSave}';";
		}

		$this->getView()->inlineScript()->appendScript(implode(PHP_EOL, $script));
		$this->getView()->headScript()->prependFile('/js/lemo/validator.js');

		return $this;
	}
}
