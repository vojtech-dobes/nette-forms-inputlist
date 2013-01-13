<?php

namespace VojtechDobes\NetteForms;

use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;


/**
 * CheckboxList
 *
 * @author David Grudl (original authorship)
 * @author Jan Vlcek (original authorship)
 * @author Filip Procházka (original authorship)
 * @author Vojtěch Dobeš
 */
class CheckboxList extends BaseControl
{

	/** @var Html separator element template */
	private $separator;

	/** @var Html container element template */
	private $container;

	/** @var array */
	private $items = array();

	/** @var array */
	private $itemsProcessed;

	/** @var int */
	private $itemsCount = 0;

	/** @var bool */
	private $omitLastSeparator = FALSE;



	/**
	 * @param  string
	 * @param  array options from which to choose
	 */
	public function __construct($label, array $items = NULL)
	{
		parent::__construct($label);
		$this->control->type = 'checkbox';
		$this->container = Html::el();
		$this->separator = Html::el('br');
		if ($items !== NULL) {
			$this->setItems($items);
		}
	}



	/**
	 * Returns selected radio value. NULL means nothing have been checked.
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return is_array($this->value) ? $this->value : NULL;
	}



	/**
	 * Sets options from which to choose.
	 *
	 * @param  array
	 * @return CheckboxList provides a fluent interface
	 */
	public function setItems(array $items)
	{
		$this->items = $items;
		$counter = -1;
		$this->itemsProcessed = array_map(function ($item) use (& $counter) {
			return array(
				'counter' => ++$counter,
				'label' => $item,
			);
		}, $items);
		$this->itemsCount = $counter + 1;
		return $this;
	}



	/**
	 * Returns options from which to choose.
	 *
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}



	/**
	 * Returns separator HTML element template.
	 *
	 * @return Html
	 */
	public function getSeparatorPrototype()
	{
		return $this->separator;
	}



	/**
	 * Returns container HTML element template.
	 *
	 * @return Html
	 */
	public function getContainerPrototype()
	{
		return $this->container;
	}



	/**
	 * Separator before last item will be omitted
	 *
	 * @return CheckboxList provides a fluent interface
	 */
	public function omitLastSeparator()
	{
		$this->omitLastSeparator = TRUE;
		return $this;
	}



	/**
	 * Generates control's HTML element.
	 *
	 * @param  mixed specify a key if you want to render just a single checkbox
	 * @return Html
	 */
	public function getControl()
	{
		$container = clone $this->container;
		$separator = (string) $this->separator;
		$control = parent::getControl();

		$control->name .= '[]';
		$id = $control->id;
		$counter = -1;
		$values = $this->value === NULL ? NULL : (array) $this->getValue();
		$label = Html::el('label');

		foreach ($this->items as $k => $val) {
			$counter++;

			$control->id = $label->for = $id . '-' . $counter;
			$control->checked = (count($values) > 0) ? in_array($k, $values) : false;
			$control->value = $k;

			if ($val instanceof Html) {
				$label->setHtml($val);
			} else {
				$label->setText($this->translate($val));
			}

			$pair = (string) $control . (string) $label;
			if ($counter + 1 === $this->itemsCount && !$this->omitLastSeparator) {
				$pair .= $separator;
			}
			$container->add($pair);
		}

		return $container;
	}



	/**
	 * Generates label's HTML element.
	 *
	 * @return Html
	 */
	public function getLabel($caption = NULL)
	{
		$label = parent::getLabel($caption);
		$label->for = NULL;
		return $label;
	}



	/**
	 * Generates control's HTML element for specific item.
	 *
	 * @param  string
	 * @return Html
	 * @throws InvalidArgumentException if key is not present in items list
	 */
	public function getItemControl($key)
	{
		if (!isset($this->items[$key])) {
			throw new InvalidArgumentException("Key '$key' is not present in \$items.");
		}

		$item = $this->itemsProcessed[$key];
		$control = clone parent::getControl();
		$values = $this->value === NULL ? NULL : (array) $this->getValue();
		$control->name .= '[]';
		$control->id = $control->id . '-' . $item['counter'];
		$control->checked = (count($values) > 0) ? in_array($key, $values) : false;
		$control->value = $key;
		$control->data('nette-rules', NULL);
		return $control;
	}



	/**
	 * Generates label's HTML element for specific item.
	 *
	 * @param  string
	 * @return Html
	 * @throws InvalidArgumentException if key is not present in items list
	 */
	public function getItemLabel($key)
	{
		if (!isset($this->items[$key])) {
			throw new InvalidArgumentException("Key '$key' is not present in \$items.");
		}

		$item = $this->itemsProcessed[$key];
		$label = Html::el('label');
		$label->for = parent::getControl()->id . '-' . $item['counter'];
		if ($item['label'] instanceof Html) {
			$label->setHtml($item['label']);
		} else {
			$label->setText($this->translate((string) $item['label']));
		}
		return $label;
	}



	/**
	 * Filled validator: has been any checkbox checked?
	 *
	 * @param  CheckboxList
	 * @return bool
	 */
	public static function validateChecked(CheckboxList $control)
	{
		return $control->getValue() !== NULL;
	}



	/**
	 * Adds addMultiCheckbox() method to Nette\Forms\Container
	 */
	public static function register()
	{
		Container::extensionMethod('addMultiCheckbox', function (Container $_this, $name, $label, array $items = NULL) {
			return $_this[$name] = new CheckboxList($label, $items);
		});
	}

}
