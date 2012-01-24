================================
TYPO3 Extension "cz_wkhtmltopdf"
================================

Introduction
============

.. WARNING::
   This extension is marked as *experimental*.
   Although it typically should work, there has not yet been any testing on how the extension behaves under load.
   So (regardless that it is FOSS) be aware that you use this software at your own risk.

What does it do?
----------------

This is yet another integration of `wkhtmltopdf <http://code.google.com/p/wkhtmltopdf/>`_, a library to
convert html to pdf using the webkit rendering engine, and qt.

Features
--------

* **Outstanding CSS support**

  It uses the webkit rendering engine also used by Safari and Chrome.

* **Flexible configuration through TypoScript**

  Your pdf view is just another page type. So you are free to do whatever you like.

  This way it also seamlessly integrates into TYPO3's internals like access rights, versioning, etc.

* **Caching of generated files**

  The extension uses the internal TYPO3 caching mechanisms. So if you clear the cache of just one page using TYPO3 methods,
  only the pdf of this one file is dropped. You might also disable PDF generation for non-cached pages (USER_INT).

* **runs on (some) Shared Hosts**

  You don't need your own server to get wkhtmltopdf running. There are some Shared Hosters where you can run your own
  binaries. The server does not need to run XServer - only some libraries are needed. Ask your provider on their support
  or just give it a try. Binaries for i386 and amd64 are included - so plug in and start.

Prerequisites
-------------

* Your hoster needs to allow you to run binaries.
* Your hoster/server needs to have some ``X11 client libraries`` installed. Ask your provider or just give it a try.
* PHP function ``proc_open()`` must not be disabled.
* wkhtmltopdf supports your architecture and operating system. But if you use some Linux, Windows or Mac OS on an Intel
  architecture you should be fine.

How does it differ from other extensions?
-----------------------------------------

I hope I don't need to say too much about *PHP-based extensions* that use libraries like ``html2pdf``, ``FPDF`` or ``PDFlib``.
Their main advantage is that they run on most environments as they usually just use PHP and some related libraries. But
the problem with these libraries usually is that they are slow, have very poor CSS support and usually run out of memory for
more complex pages.

`cz_wkhtmltopdf` integrates the `wkhtmltopdf <http://code.google.com/p/wkhtmltopdf/>`_ project. It uses a binary - it's
hard to get any faster than this with just PHP. The rendering is done with the help of the webkit engine that is also used
by Google's Chrome and Apple's Safari. Think of it as putting the print functionality of Chrome and its "Print to PDF"
into a binary. Needless to say that CSS support of this binary is outstanding.

On the other hand there are few extensions that already integrate `wkhtmltopdf` into TYPO3 in TER:

`Webkit PDFs (webkitpdf) <http://typo3.org/extensions/repository/view/webkitpdf/current/>`_ seems to be the oldest
extension of its kind in TER. I personally dislike the architecture of this extension. It is a frontend plugin that you'll
pass the url of the page to render via an url parameter. This poses several problems:

* *It will accept any urls from your TYPO3 domain.*

  You can either enable this extension for all of your pages and types or disable it completely.
  This makes it also hard to render some other page type then the default as PDF. You usually don't want navigation, some
  side columns or your fancy header animation in the PDF, so having a separate page type sounds reasonable.

* *Ignores caching and access routines of TYPO3.*

  Each rendered PDF is cached for a specified time depending on its URL. There are no checks
  if caching of a page was disabled. And because each page is fetched as an anonymous user, there is no way to render
  PDFs of access restricted pages.
* *Cached PDFs can't be dropped selectively.*

  The extension does not notice if content on a page has changed to invalidate
  the corresponding cache. Your only choice is to wait for the cache to expire or to drop *all* cached PDFs.

`cz_wkhtmltopdf` circumvents all these issues by using a hook that modifies the content just before it is returned to the
user. It integrates with TYPO3 internals, like caching and access restrictions. And as it is no more than *just another
page type* to TYPO3 it invalidates the cached PDF just like it would happen with any HTML page.

`wkhtmltopdf as TYPO3 service (wkhtmltopdf) <http://typo3.org/extensions/repository/view/wkhtmltopdf/current/>`_ is,
as the title suggests, just a service. If you want to generate PDFs that are usually not your pages this extension
might better fit your needs. But if you want an alternate view of your existing pages this extension won't help you much
as it is just a library.

`exinit | wkhtmltopdf (exinit_wkhtmltopdf) <http://typo3.org/extensions/repository/view/exinit_wkhtmltopdf/current/>`_ was
published after the work on this extension started. It seems to do a nice job with TemplaVoila. If you use TemplaVoila,
you should give it a try. `cz_wkhtmltopdf` was not tested with TemplaVoila, just the good old TYPO3 templating engine.


Users manual
============

Quickstart
----------

1. **Install the extension.**

   After that head to the Extension Configuration to select which binary (i368, amd64 or a custom) should be used.

2. **Check the reports module**

   If you have the System Extension ``reports`` enabled check it for errors. cz_wkhtmltopdf comes with some tests to
   point you to (possible) issues. You can also find out which architecture your server is using.

3. **Create a new page type.**

   This is done exactly as you create your ``page`` in TypoScript, but you have to give a different ``typeNum``.

   The easiest setup is this::

       pdf < page
       pdf.typeNum = 806870

   If you call http://www.example.com?type=806870 you'll see an exact copy of your usual HTML homepage. This is useful
   if you are trying to debug your output before running it through the PDF converter. Use Chrome (it has built-in developer tools)
   to modify your output until you are fine with it.

4. **Enable PDF conversion.**

   Example configuration::

       pdf.config {
           tx_czwkhtmltopdf {
               enable = 1
               disableInt = 0
           }

           # ask the users browser to treat the content as a PDF file
           additionalHeaders = Content-Type: application/pdf
           # needed to have absolute links in your PDF
           absRefPrefix = http://www.example.com/
       }

  If you call http://www.example.com?type=806870 now you should hopefully see a nice PDF.

5. **Link to your new page-type.**

   Here is a simple snippet to link to the current page with your new page type. It keeps all additional query parameters,
   so this also works with tt_news single view for example.

   ::

       lib.pdfLink = TEXT
       lib.pdfLink {
           if.isFalse.data = TSFE:no_cache // field: no_search
           typolink {
               parameter.data = TSFE:id
               addQueryString = 1
               addQueryString.method = GET
               addQueryString.exclude = type
               additionalParams = &type=806870
               useCacheHash = 1
               returnLast = url
           }
       }

The Reports Module
------------------

If you enabled the System Extension ``reports`` you will find some useful information there. Here is a list of
what could happen and how to solve it.

Configuration
	Just checks that you have hit the "Update" button in the Extension Manager. If you did not you would face a whole
	bunch of exceptions.

``proc_open()`` is available
	This method is needed to call the binary from PHP. If this check fails, ask your administrator to enable it.

wkhtmltopdf binary
	Calls the configured binary. If it fails please read the description below. It might point you to the error. Most
	likely you have selected the wrong binary for your architecture or operating system or your administrator does not
	allow you to run arbitrary binaries.

Operating System
	Just tells you what operating system and architecture you are on to figure out the right binary.

Hooks
	The extension uses two hooks to convert the output from HTML to PDF. Other extensions might interfere and break
	the output. In most cases it is best to have ``tx_CzWkhtmltopdf_Controller`` at the bottom of the list. Just make
	sure that any hooked class expecting HTML is loaded before converting to PDF.


Configuration
=============

Except for the path of the binary every configuration is done via Typoscript in the ``CONFIG`` object on a per-page basis.

If you used the configuration above this would be ``pdf.config.tx_czwkhtmltopdf``.

``enable`` (boolean + stdWrap)
	If set to ``1`` the conversion from HTML to PDF is triggered. You can also use stdWrap here.

	You can easily set this value to ``0`` and remove ``Content-Type: application/pdf`` from ``additionalHeaders`` to
	check how your HTML output looks like.

``binOptions``:
	You can pass your own options to the binary. ``binOptions`` is an array where each key is used as option name
	and the value - if there is one - as option value.

	For example this::

		binOptions {
		   footer-right = [page]/[toPage]
		   grayscale =
		}

	would translate to ``--footer-right = [page]/[toPage] --grayscale`` in the binary call.

	For a full list of all available options call the binary with the ``-H`` option or see the list in the next chapter
	for a list of regularly used options.

``disableInt``

	.. WARNING::
		This option is deprecated. Use the stdWrap capabilities of ``enable`` instead.

	Converting pages puts some load on your server. That's usually acceptable as long as the result is cached for the
	next few requests. But for a non-cached request, where the conversion is done for every request, this might be
	a way to `DoS attack <http://en.wikipedia.org/wiki/Denial-of-service_attack>`_ your server.

	To disable conversion of non-cached pages, set this option to ``1``. Your user will see a 404-error when trying to
	access such a page.

	.. NOTE::
		Please keep in mind, that if you are logged-in to the backend of TYPO3 none of your requested pages will be cached.
		So you won't see any PDF files until you log out or use a different browser.



wkhtmltopdf options
-------------------

Here is a selection of often used options. For a full list of all available options call the binary with the ``-H`` option.

.. NOTE::
	These options are supported by the binaries bundled with this extension. If you use a different binary these might differ.

``grayscale``
	PDF will be created in grayscale.
``image-dpi = <integer>``
	When embedding images scale them down to this dpi (default 600)
``image-quality = <integer>``
	When jpeg compressing images use this quality (default 94)
``lowquality``
	Generates lower quality pdf/ps. Useful to shrink the result document space
``margin-bottom = <unitreal>``, ``margin-left = <unitreal>``, ``margin-right = <unitreal>``, ``margin-top = <unitreal>``
	Set the page margins (default 10mm)
``orientation = <orientation>``
    Set orientation to Landscape or Portrait (default Portrait)
``page-height = <unitreal>``
    Page height
``page-width = <unitreal>``
    Page width
``page-size = <Size>``
	Set paper size to: A4, Letter, etc. (default A4)
``footer-center = <text>``
	Centered footer text
``footer-html = <url>``
	Adds a html footer
``footer-left = <text>``
	Left aligned footer text
``footer-right = <text>``
	Right aligned footer text
``footer-spacing = <real>``
	Spacing between footer and content in mm (default 0)
``header-(...)``
	Same options as for footer.

Tutorial
========

realurl
-------

The realurl configuration is very simple. If you used the configuration and typeNum from the Quickstart you just need
to add this to the ``['fileName']['index']``-array::

	'print.pdf' => array (
	   'keyValues' => array (
			'type' => '806870',
		),
	),

Troubleshooting
===============

The best point to start is the Reports Module. It checks for some basic things and might help you figuring out what's wrong.
See the according chapter in "Users Manual" above for some useful hints.

.. HINT::
	If you don't see the Reports Module switch over to the Extension Manager to install it. The extension ``reports``
	is a system extension so you don't have to download anything. Type ``reports`` into the filter box and click the
	grey brick in front of the extension.

My PDF viewer tells me the file was broken
------------------------------------------

This usually happens when TYPO3 sends the header to identify a PDF but the body contains HTML. You have either forgotten to set
``pdf.config.tx_czwkhtmltopdf.enable = 1`` or TYPO3 tries to show an error message.

Remove ``Content-Type: application/pdf`` from ``additionalHeaders`` or save the pdf and open it with the text editor of
your choice to see what's up.

Error: "PDF generation was disabled for this page."
---------------------------------------------------

This error usually pops up if you have set the ``disableInt`` option and try to convert a non-cached page.

First you should check if this also happens in a browser you are not currently logged in to the backend.
Sometimes pages are not cached when you are logged in to the backend.

If this does not help, check why TYPO3 treats your pages as non-cachable. Make sure ``config.no_cache = 0`` is set and you
don't have any USER_INT objects on this page.
