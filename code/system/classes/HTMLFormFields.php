<?php

/**
* WizyTówka 5
* Generator of form fieldsets HTML code.
*/
namespace WizyTowka;

class HTMLFormFields extends HTMLTag
{
	private $_fields = [];
	private $_disabled;

	public function __construct(bool $disabled = false, ...$arguments)
	{
		$this->_disabled = (bool)$disabled;

		parent::__construct(...$arguments);
	}

	public function __debugInfo() : array
	{
		return $this->_fields;
	}

	public function text(string $label, string $name, ?string $value, array $HTMLAttributes = []) : self
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = str_replace("\n", null, HTML::escape($value));

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function number(string $label, string $name, $value, array $HTMLAttributes = []) : self
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = is_numeric($value) ? str_replace(',', '.', $value) : '0';
		// Number can be wrong formatted because of locale settings.

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function color(string $label, string $name, ?string $value, array $HTMLAttributes = []) : self
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = preg_match('/#[0-9a-f]{6}/i', $value) ? $value : '#ffffff';

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function url(string $label, string $name, ?string $value, array $HTMLAttributes = []) : self
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = filter_var($value, FILTER_VALIDATE_URL) ? $value : '';

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function email(string $label, string $name, ?string $value, array $HTMLAttributes = []) : self
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : '';

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function password(string $label, string $name, array $HTMLAttributes = []) : self
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		unset($HTMLAttributes['value']);
		// Don't allow predefined value on password field.

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function checkbox(string $label, string $name, $currentValue, array $HTMLAttributes = []) : self
	{
		$type = 'checkable';
		$HTMLAttributes['type']    = __FUNCTION__;
		$HTMLAttributes['name']    = $name;
		$HTMLAttributes['checked'] = (bool)$currentValue;

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function radio(string $label, string $name, $fieldValue, $currentValue, array $HTMLAttributes = []) : self
	{
		$type = 'checkable';
		$HTMLAttributes['type']    = __FUNCTION__;
		$HTMLAttributes['name']    = $name;
		$HTMLAttributes['value']   = is_scalar($fieldValue) ? $fieldValue : '';
		$HTMLAttributes['checked'] = (is_scalar($fieldValue) and ($currentValue == $fieldValue));

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function option(...$arguments) : self
	{
		return $this->radio(...$arguments);
	}

	public function textarea(string $label, string $name, ?string $content, array $HTMLAttributes = []) : self
	{
		$type = __FUNCTION__;
		$HTMLAttributes['name'] = $name;

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label', 'content');
		return $this;
	}

	public function select(string $label, string $name, ?string $selected, array $valuesList, array $HTMLAttributes = []) : self
	{
		$type = __FUNCTION__;
		$HTMLAttributes['name']  = $name;

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label', 'valuesList', 'selected');
		return $this;
	}

	public function textWithHints(string $label, string $name, ?string $value, array $hints, array $HTMLAttributes = []) : self
	{
		$type = __FUNCTION__;
		$HTMLAttributes['type']  = 'text';
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = str_replace("\n", null, HTML::escape($value));

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label', 'hints');
		return $this;
	}

	public function skip() : self
	{
		return $this;
	}

	public function remove(string $name) : self
	{
		foreach ($this->_fields as $key => $field) {
			if ($field['HTMLAttributes']['name'] == $name) {
				unset($this->_fields[$key]);
			}
		}

		return $this;
	}

	public function output() : void
	{
		echo '<fieldset', $this->_CSSClass ? ' class="' . $this->_CSSClass . '"' : '',
		     $this->_disabled ? ' disabled>' : '>';

		foreach ($this->_fields as $field) {
			$id = $field['HTMLAttributes']['name'];
			if ($field['type'] == 'checkable' and $field['HTMLAttributes']['type'] == 'radio') {
				$id .= '_' . $field['HTMLAttributes']['value'];
			}
			$field['HTMLAttributes']['id'] = $id;   // Unique ID is used to assign form control to label.

			$isCheckable = ($field['type'] == 'checkable');

			echo '<div', $isCheckable ? ' class="checkable">' : '>';

			if ($isCheckable) {
				$fieldTitle = !empty($field['HTMLAttributes']['title']) ? $field['HTMLAttributes']['title'] : '';
				// If checkbox/radio has "title" attribute apply it to label also.

				$this->_renderHTMLOpenTag('input', $field['HTMLAttributes']);
				echo '<label for="', $id, '"', ($fieldTitle ? ' title="' . $fieldTitle . '">' : '>'),
				     $field['label'], '</label>';
			}
			else {
				echo '<label for="', $id, '">', $field['label'], '</label>';
				echo '<span>';

				switch ($field['type']) {
					case 'textWithHints':
						$hintsId = 'hints_' . $id;
						echo '<datalist id="', $hintsId, '">';
						foreach ($field['hints'] as $hint) {
							echo '<option>', $hint, '</option>';
						}
						echo '</datalist>';
						$field['HTMLAttributes']['list'] = $hintsId;
						// No break. Share code with standard input.

					case 'simple':
						$this->_renderHTMLOpenTag('input', $field['HTMLAttributes']);
						break;

					case 'textarea':
						$this->_renderHTMLOpenTag('textarea', $field['HTMLAttributes']);
						echo $field['content'], '</textarea>';
						break;

					case 'select':
						$this->_renderHTMLOpenTag('select', $field['HTMLAttributes']);
						foreach ($field['valuesList'] as $value => $label) {
							echo '<option value="', $value, ($value == $field['selected']) ? '" selected>' : '">', $label, '</option>';
						}
						echo '</select>';
				}

				echo '</span>';
			}

			echo '</div>';
		}

		echo '</fieldset>';
	}
}