<?php

/**
* WizyTówka 5
* HTML formatting utilities.
*/
namespace WizyTowka;

trait HTML
{
	static public function escape(?string $text) : string
	{
		return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
	}

	static public function unescape(?string $text) : string
	{
		return htmlspecialchars_decode($text, ENT_QUOTES | ENT_HTML5);
	}

	static public function correctTypography(?string $text) : string
	{
		$settings = WT()->settings;

		$flags = ($settings->typographyOther   ? Text::TYPOGRAPHY_OTHER   : 0) |
		         ($settings->typographyDashes  ? Text::TYPOGRAPHY_DASHES  : 0) |
		         ($settings->typographyQuotes  ? Text::TYPOGRAPHY_QUOTES  : 0) |
		         ($settings->typographyOrphans ? Text::TYPOGRAPHY_ORPHANS : 0);

		return $flags ? (new Text($text))->correctTypography($flags)->get() : $text;
	}

	static private function _prepateTimeTag($timestamp, string $visibleFormat, string $HTMLFormat) : string
	{
		$value     = (new Text($timestamp))->formatAsDateTime($visibleFormat);
		$HTMLValue = (new Text($timestamp))->formatAsDateTime($HTMLFormat);

		return '<time datetime="' . $HTMLValue . '">' . $value . '</time>';
	}

	static public function formatDateTime($timestamp) : string
	{
		$settings = WT()->settings;

		$format = [$settings->dateDateFormat, $settings->dateSeparator, $settings->dateTimeFormat];
		if ($settings->dateSwapTime) {
			$format = array_reverse($format);
		}

		return self::_prepateTimeTag($timestamp, join($format), '%FT%T%z');
	}

	static public function formatDate($timestamp) : string
	{
		return self::_prepateTimeTag($timestamp, WT()->settings->dateDateFormat, '%F');
	}

	static public function formatTime($timestamp) : string
	{
		return self::_prepateTimeTag($timestamp, WT()->settings->dateTimeFormat, '%T%z');
	}

	static public function formatFileSize(int $bytes) : string
	{
		return (new Text((string)$bytes))->formatAsFileSize(WT()->settings->filesUseBinaryUnitForSizes)->get();
	}
}