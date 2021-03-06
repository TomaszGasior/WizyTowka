<?php

/**
* WizyTówka 5
* Set of functions used to prepare template variables and render website HTML code.
*/
namespace WizyTowka;

class WebsiteRenderer
{
	private $_page;
	private $_404Error = false;

	private $_HTMLLayout;
	private $_HTMLTemplate;
	private $_HTMLHead;
	private $_HTMLMessage;

	private $_theme;

	public function __construct(HTMLTemplate $HTMLLayout, Page $page = null, ContentTypeAPI $contentTypeAPI = null)
	{
		$this->_settings = WT()->settings;

		if ($page) {
			$this->_page = $page;
		}
		else {
			$this->_page = new Page;   // Fake object.
			$this->_page->title = $this->_settings->website404ErrorTitle;

			$this->_404Error = true;
		}

		// Load theme.
		if (!$this->_theme = Theme::getByName($this->_settings->themeName)) {
			throw WebsiteRendererException::themeNotExists($this->_settings->themeName);
		}

		// Prepare HTML layout template.
		$this->_HTMLLayout = $HTMLLayout;
		$this->_HTMLLayout->setTemplate('WebsiteLayout');
		$this->_setupTemplatePath($this->_HTMLLayout);

		// Prepare HTML <head> section.
		$this->_HTMLHead = $this->_prepareHead();

		// Initialize HTML message box.
		$this->_HTMLMessage = new HTMLMessage('wt_message');

		// Initialize HTML template for content type.
		$this->_HTMLTemplate = new HTMLTemplate;
		if ($contentTypeAPI) {
			// Template name and path are set by content type here.
			$contentTypeAPI->setHTMLParts($this->_HTMLTemplate, $this->_HTMLHead, $this->_HTMLMessage);
		}
	}

	public function prepareTemplate() : void
	{
		$layout = $this->_HTMLLayout;

		$layout->lang = $this->_settings->websiteLanguage;
		$layout->head = $this->_HTMLHead;

		$layout->setRaw('websiteHeader', $this->_variable_websiteHeader());
		$layout->setRaw('websiteFooter', $this->_variable_websiteFooter());

		$layout->setRaw('pageHeader',  $this->_variable_pageHeader());
		$layout->setRaw('pageContent', $this->_variable_pageContent());

		$layout->setRaw('menu', function(...$a){ return $this->_function_menu(...$a); });
		$layout->setRaw('area', function(...$a){ return $this->_function_area(...$a); });
		$layout->setRaw('info', function(...$a){ return $this->_function_info(...$a); });

		// Change HTML <head> assets path to theme path. Thanks to this adding assets from theme layout will be
		// more convenient. Assets path must be changed at the end of this method because other assets path
		// is set by ContentTypeAPI class for content types purposes.
		$this->_HTMLHead->setAssetsPath($this->_theme->getURL());
	}

	private function _setupTemplatePath(HTMLTemplate $template) : void
	{
		// Themes can override HTML templates of website layout if it's specified in addon.conf.
		$template->setTemplatePath(
			in_array($template->getTemplate(), $this->_theme->templates) ? $this->_theme->getPath() : SYSTEM_DIR
			. '/templates'
		);
	}

	private function _prepareHead() : HTMLHead
	{
		$head = new HTMLHead;

		if (!$this->_settings->websiteAddressRelative) {
			$head->setAssetsPathBase($this->_settings->websiteAddress);
		}
		$head->setAssetsPath($this->_theme->getURL());

		// Base website information.
		$head->setTitlePattern(HTML::correctTypography($this->_settings->websiteTitlePattern));
		$head->title(HTML::correctTypography(
			$this->_page->titleHead ? $this->_page->titleHead : $this->_page->title
		));
		if ($this->_settings->websiteAuthor) {
			$head->meta('author', HTML::correctTypography($this->_settings->websiteAuthor));
		}

		// Search engines information.
		if ($description = $this->_page->description ? $this->_page->description : $this->_settings->searchEnginesDescription) {
			$head->meta('description', HTML::correctTypography($description));
		}
		$robotsTag = explode(',', $this->_settings->searchEnginesRobots);
		if ($this->_page->noIndex) {
			$robotsTag[] = 'noindex';
		}
		if ($robotsTag) {
			$robotsTag = implode(', ', array_unique(array_map('trim', $robotsTag)));
			$head->meta('robots', $robotsTag);

			header('X-Robots-Tag: ' . $robotsTag); // Useful when content type breaks HTML rendering.
		}

		// Theme stylesheet.
		$head->stylesheet($this->_theme->minified ? 'style.min.css' : 'style.css');
		if ($this->_theme->responsive) {
			$head->meta('viewport', 'width=device-width, initial-scale=1');
		}

		// DO NOT REMOVE THIS LINE.
		$head->meta('generator', 'WizyTówka CMS — https://wizytowka.tomaszgasior.pl');

		return $head;
	}

	private function _variable_websiteHeader() : string
	{
		$template = new HTMLTemplate('WebsiteHeader');
		$this->_setupTemplatePath($template);

		$template->websiteTitle       = HTML::correctTypography($this->_settings->websiteTitle);
		$template->websiteDescription = HTML::correctTypography($this->_settings->websiteDescription);

		return (string)$template;
	}

	private function _variable_websiteFooter() : string
	{
		$template = new HTMLTemplate('WebsiteFooter');
		$this->_setupTemplatePath($template);

		$elements = [
			0   => '&copy; ' . HTML::correctTypography(
				$this->_settings->websiteAuthor ? $this->_settings->websiteAuthor : $this->_settings->websiteTitle
			),

			// DO NOT REMOVE THIS LINE.
			999 => '<a href="https://wizytowka.tomaszgasior.pl" title="Ta witryna jest oparta na systemie zarządzania treścią WizyTówka." target="_blank">WizyTówka</a>',
		];

		ksort($elements);
		$template->setRaw('elements', $elements);

		return (string)$template;
	}

	private function _variable_pageHeader() : string
	{
		$template = new HTMLTemplate('WebsitePageHeader');
		$this->_setupTemplatePath($template);

		$template->pageTitle = HTML::correctTypography($this->_page->title);

		$properties = [];

		if (!$this->_404Error) {
			if ($user = User::getById($this->_page->userId) and !$this->_settings->lockdownUsers) {
				$properties['Autor'] = HTML::correctTypography($user->name);
			}
			$properties['Data utworzenia']  = HTML::formatDateTime($this->_page->createdTime);
			$properties['Data modyfikacji'] = HTML::formatDateTime($this->_page->updatedTime);
		}

		$template->setRaw('properties', $properties);

		return (string)$template;
	}

	private function _variable_pageContent() : string
	{
		$template = new HTMLTemplate('WebsitePageContent');
		$this->_setupTemplatePath($template);

		// Setup 404 error message if it's needed.
		if ($this->_404Error) {
			$this->_HTMLTemplate->setTemplate('Website404Error');
			$this->_setupTemplatePath($this->_HTMLTemplate);

			$this->_HTMLTemplate->homePageURL = Website::URL($this->_settings->websiteHomepageId);
		}

		$template->message = HTML::correctTypography($this->_HTMLMessage);
		$template->setRaw('content', $this->_HTMLTemplate);

		return (string)$template;
	}


	private function _function_menu(int $menuPositionNumber) : string
	{
		// More comming soon.
		$pages = Page::getAll();
		$menu  = new HTMLMenu;

		foreach ($pages as $page) {
			$menu->append(
				HTML::escape(HTML::correctTypography($page->title)),
				Website::URL($page->id), $page->slug
			);
		}

		return (string)$menu;
	}

	private function _function_area(int $areaPositionNumber) : string
	{
		return '<!-- area ' . $areaPositionNumber . ' comming soon -->';
	}

	private function _function_info(string $option)
	{
		switch ($option) {
			case 'websiteTitle':       return HTML::correctTypography($this->_settings->websiteTitle);
			case 'websiteDescription': return HTML::correctTypography($this->_settings->websiteDescription);
			case 'websiteAuthor':      return HTML::correctTypography($this->_settings->websiteAuthor);
			case 'pageTitle':          return HTML::correctTypography($this->_page->title);
			case 'pageIsDraft':        return $this->_page->isDraft;
			case 'pageContentType':    return $this->_page->contentType;
			case 'systemVersion':      return VERSION;
		}
	}
}

class WebsiteRendererException
{
	static public function themeNotExists($name)
	{
		return self('Theme "' . $name . '" does not exists.', 1);
	}
}