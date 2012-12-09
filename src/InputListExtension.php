<?php

namespace VojtechDobes\NetteForms;

use Nette\Config\CompilerExtension;
use Nette\Utils\PhpGenerator\ClassType;


/**
 * Registers helper methods 'addMultiRadio' & 'addMultiCheckbox'
 * and corresponding Latte macros for convenient manual rendering
 *
 * @author Vojtěch Dobeš
 */
class InputListExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$latte = $container->getDefinition('nette.latte');
		$latte->addSetup('VojtechDobes\NetteForms\InputListMacros::install(?->compiler)', array('@self'));
	}



	public function afterCompile(ClassType $class)
	{
		$initialize = $class->methods['initialize'];
		$initialize->addBody('VojtechDobes\NetteForms\RadioList::register();');
		$initialize->addBody('VojtechDobes\NetteForms\CheckboxList::register();');
	}

}
