(function (window, undefined) {

if (!window.Nette) {
	return console.error('Load netteForms.js first!');
}

var RangeValidator = Nette.validators.range;
Nette.validators.range = function (elem, arg, val) {
	if (Nette.isArray(val)) {
		val = val.length;
	}
	return RangeValidator(elem, arg, val);
};

/**
 * Following lines of code are original part nextras/forms
 * repository created by Jan Skrasek.
 *
 * @license    MIT
 * @link       https://github.com/nextras
 * @author     Jan Skrasek
 */
var getValue = Nette.getValue;
window.Nette.getValue = function (elem) {
	if (!elem || !elem.nodeName || !(elem.nodeName.toLowerCase() == 'input' && elem.name.match(/\[\]$/))) {
		return getValue(elem);
	} else {
		var value = [];
		for (var i = 0; i < elem.form.elements.length; i++) {
			var e = elem.form.elements[i];
			if (e.nodeName.toLowerCase() == 'input' && e.name == elem.name && e.checked) {
				value.push(e.value);
			}
		}

		return value.length == 0 ? null : value;
	}
};

})(window);
