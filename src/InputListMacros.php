<?php

namespace VojtechDobes\NetteForms;

use Nette;
use Nette\Latte;
use Nette\Latte\MacroNode;


/**
 * Macros for alternative Radiolist & CheckboxList
 *
 * - {inputlist name as $key => $label} ... {/inputlist}
 * - alternative {input} and {label}
 *
 * @author Vojtěch Dobeš
 */
class InputListMacros extends Latte\Macros\MacroSet
{

	/** @var bool */
	private $inList = FALSE;



	public static function install(Latte\Compiler $parser)
	{
		$me = new static($parser);
		$me->addMacro('inputlist', array($me, 'macroStartList'), array($me, 'macroEndList'));
		$me->addMacro('input', array($me, 'macroInput'));
		$me->addMacro('label', array($me, 'macroLabel'), '?></label><?php');
	}



	/********************* macros ****************v*d**/



	/**
	 * {inputlist}
	 */
	public function macroStartList(MacroNode $node, $writer)
	{
		$this->inList = TRUE;
	}



	/**
	 * {/inputlist}
	 */
	public function macroEndList(MacroNode $node, $writer)
	{
		$this->inList = FALSE;
		$node->openingCode = $writer->write('<?php $_inputlist = is_object(%node.word) ? %node.word : $_form[%node.word];');
		if (preg_match('#\W(\$iterator|include|require|get_defined_vars)\W#', $this->getCompiler()->expandTokens($node->content))) {
			$node->openingCode .= '$iterations = 0; foreach ($iterator = $_l->its[] = new Nette\Iterators\CachingIterator($_inputlist->getItems() as ';
			$node->closingCode = '<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its);';
		} else {
			$node->openingCode .= '$iterations = 0; foreach ($_inputlist->getItems() as ';
			$node->closingCode = '<?php $iterations++; endforeach;';
		}
		$as = preg_replace('#\s?as\s+(.*)#i', '$1', $writer->formatArgs(), 1);
		$withKey = preg_match('#\s+=>\s+#i', $as);
		if ($withKey !== 1) {
			$as = '$_inputlistKey => ' . $as;
		}
		$node->openingCode .= $as . '): ' . preg_replace('#(.*)\s=>\s(.*)#i', '$_inputlistKey = $1', $as) . ' ?>';
		$node->closingCode .= ' unset($_inputlist) ?>';
	}



	/**
	 * {input} in {inputlist}
	 */
	public function macroInput(MacroNode $node, $writer)
	{
		if (!$this->inList || (strlen($node->args) > 0 && strpos($node->args, '=>') === FALSE)) {
			return FALSE;
		}
		return $writer->write('echo $_inputlist->getItemControl($_inputlistKey)->addAttributes(%node.array)');
	}



	/**
	 * {label} for {input} in {inputlist}
	 */
	public function macroLabel(MacroNode $node, $writer)
	{
		if (!$this->inList || ($node->args && $node->args !== '/' && strpos($node->args, '=>') === FALSE)) {
			return FALSE;
		}
		$cmd = 'echo $_inputlist->getItemLabel($_inputlistKey)->addAttributes(%node.array)';
		if ($node->isEmpty = (substr($node->args, -1) === '/')) {
			$node->setArgs(substr($node->args, 0, -1));
			return $writer->write($cmd);
		} else {
			return $writer->write($cmd . '->startTag()');
		}
	}

}
