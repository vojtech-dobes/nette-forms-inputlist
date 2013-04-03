## For Nette Framework

Alternative RadioList & CheckboxList for Nette Framework

##### License

New BSD

##### Dependencies

Nette 2.0.0

## Installation

1. Get the source code from Github or via Composer (`vojtech-dobes/nette-forms-inputlist`).
2. Register `VojtechDobes\NetteForms\InputListExtension` as extension for `$configurator`.

```php
$configurator->onCompile[] = function ($configurator, $compiler) {
	$compiler->addExtension('inputlist', new VojtechDobes\NetteForms\InputListExtension);
};
```

## Usage

#### RadioList

```php
$form->addMultiRadio('sex', 'Sex:', array(
	'male' => 'Male',
	'female' => 'Female',
));
```

> Method's name was chosen to not interfere with native `addRadiolist`.

#### CheckboxList

```php
$checkboxlist = $form->addMultiCheckbox('topics', 'I like:', array(
	'l' => 'lifestyle',
	'm' => 'military',
	'c' => 'computers',
	'f' => 'flowers',
));
```

##### Defaults

```php
$checkboxlist->setDefaultValue(array('l', 'm')); // lifestyle, military
```

##### Returned values

```php
$checkboxlist->getValue() === array(0 => 'l', 1 => 'm')
```

### Rendering

#### Automatic

Both `RadioList` and `CheckboxList` provide standard mechanism like `getControlPrototype` etc. You can also force omitting of last separator:

```php
$radiolist->omitLastSeparator();
```

#### Manual

There is special new macro for Latte templates: `{inputlist}`. It behaves exactly like `{foreach}`, but it's specifically design to work with `*List` form elements.

```html
{form formName}
	{inputlist sex as $key => $label}
		{input} {label /} {sep}<br>{/sep}
	{/inputlist}
{/form}
```

Macros `{input}` and `{label}` behave the same way as always, but when used without element identificator, they will render proper elements for iteration specific item. If you use it with identificator, it will render appropriate element from form.

```html
{form formName}
	{inputlist sex as $key => $label}
		{input} {label /}<br>
		{input send} {* standard button, no problem *}
	{/inputlist}
{/form}
```

You can add HTML attributes to them as usually.

```html
{form formName}
	{inputlist sex as $key => $label}
		{input class => 'input-radio'} {label}{$label}{/label}<br>
	{/inputlist}
{/form}
```

Attribute version of `{inputlist}` is also possible:

```html
{form formName}
	<ul n:inner-inputlist="sex as $key => $label">
		<li>{input} {label /}</li>
	</ul>
{/form}
```

### Validation

Here supported rules are listed:

#### CheckboxList

<table>
	<tr>
		<th>Form::FILLED</th>
		<td>At least one box must be checked.</td>
	</tr>
	<tr>
		<th>Form::LENGTH</th>
		<td>Exact amount of boxes that must be checked.</td>
	</tr>
	<tr>
		<th>Form::MIN_LENGTH</th>
		<td>Minimum amount of boxes that must be checked.</td>
	</tr>
	<tr>
		<th>Form::MAX_LENGTH</th>
		<td>Maximum amount of boxes that must be checked.</td>
	</tr>
	<tr>
		<th>Form::RANGE</th>
		<td>Minimum and maximum amount of boxes that must be checked.</td>
	</tr>
	<tr>
		<th>Form::REGEXP</th>
		<td>Checks if selected checkbox values matches regular expression.</td>
	</tr>
</table>

#### RadioList

<table>
	<tr>
		<th>Form::FILLED</th>
		<td>One button must be selected.</td>
	</tr>
	<tr>
		<th>Form::REGEXP</th>
		<td>Checks if selected radio value matches regular expression.</td>
	</tr>
</table>

All rules are also supported on client-side.
