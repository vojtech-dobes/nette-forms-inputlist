<?php

namespace VojtechDobes\NetteForms;

use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;


/**
 * Alternative RadioList
 *
 * @author David Grudl (of original RadioList in Nette Framework)
 * @author Vojtěch Dobeš
 */
class RadioList extends BaseControl
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
	 * @param  string label
	 * @param  array options from which to choose
	 */
	public function __construct($label = NULL, array $items = NULL)
	{
		parent::__construct($label);
		$this->control->type = 'radio';
		$this->container = Html::el();
		$this->separator = Html::el('br');
		if ($items !== NULL) {
			$this->setItems($items);
		}
	}



	/**
	 * Returns selected radio value.
	 *
	 * @param  bool
	 * @return mixed
	 */
	public function getValue($raw = FALSE)
	{
		return is_scalar($this->value) && ($raw || isset($this->items[$this->value])) ? $this->value : NULL;
	}



	/**
	 * Has been any radio button selected?
	 *
	 * @return bool
	 */
	public function isFilled()
	{
		return $this->getValue() !== NULL;
	}



	/**
	 * Sets options from which to choose.
	 *
	 * @param  array
	 * @return RadioList provides a fluent interface
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
	final public function getItems()
	{
		return $this->items;
	}



	/**
	 * Returns separator HTML element template.
	 *
	 * @return Html
	 */
	final public function getSeparatorPrototype()
	{
		return $this->separator;
	}



	/**
	 * Returns container HTML element template.
	 *
	 * @return Html
	 */
	final public function getContainerPrototype()
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
	 * @return Html
	 */
	public function getControl()
	{
		$container = clone $this->container;
		$separator = (string) $this->separator;
		$control = parent::getControl();

		$id = $control->id;
		$counter = -1;
		$value = $this->value === NULL ? NULL : (string) $this->getValue();
		$label = Html::el('label');

		foreach ($this->items as $k => $val) {
			$counter++;

			$control->id = $label->for = $id . '-' . $counter;
			$control->checked = (string) $k === $value;
			$control->value = $k;

			if ($val instanceof Html) {
				$label->setHtml($val);
			} else {
				$label->setText($this->translate((string) $val));
			}

			$pair = (string) $control . (string) $label;
			if ($counter + 1 === $this->itemsCount && !$this->omitLastSeparator) {
				$pair .= $separator;
			}
			$container->add($pair);
			$control->data('nette-rules', NULL);
		}
		return $container;
	}



	/**
	 * Generates label's HTML element.
	 *
	 * @param  string
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
			throw new InvalidArgumentException("Key '$key' is not present in $items.");
		}

		$item = $this->itemsProcessed[$key];
		$control = clone parent::getControl();
		$control->id = $control->id . '-' . $item['counter'];
		$control->checked = (string) $key === ($this->value === NULL ? NULL : (string) $this->getValue());
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
			throw new InvalidArgumentException("Key '$key' is not present in $items.");
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
	 * Adds addMultiRadio() method to Nette\Forms\Container
	 */
	public static function register()
	{
		Container::extensionMethod('addMultiRadio', function (Container $_this, $name, $label, array $items = NULL) {
			return $_this[$name] = new RadioList($label, $items);
		});
	}

    /**
     * Validator for regular expressions for radioList
     *
     * @param RadioList
     * @param string
     * @return bool
     */
    public static function validateregexp(RadioList $control, $regexp)
    {
        return (bool) preg_match($regexp,$control->getValue());
    }

}
