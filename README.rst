================================
TYPO3 Extension "cz_wkhtmltopdf"
================================

.. WARNING::
   This extension is marked as *experimental*.
   Although it typically should work, there has not yet been any testing on how the extension behaves under load.
   So (regardless that it is FOSS) be aware that you use this software at your own risk.

What does it do?
================

This is yet another integration of `wkhtmltopdf <http://code.google.com/p/wkhtmltopdf/>`_, a library to
convert html to pdf using the webkit rendering engine, and qt.

Features
========

Here are some of the features:

* **Outstanding CSS support**

  It uses the webkit rendering engine also used by Safari and Chrome.

* **Flexible configuration through TypoScript**

  Your pdf view is just another pageType. So you are free to do whatever you like.

  This way it also seamlessly integrates into TYPO3's internals like access rights, versioning, etc.

* **Caching of generated files**

  The extension uses the internal TYPO3 caching mechanisms. So if you clear the cache of just one page using TYPO3 methods,
  only the pdf of this one file is dropped. You might also disable pdf generation for non-cached pages (USER_INT).

* **runs on (some) Shared Hosts**

  You don't need your own server to get wkhtmltopdf running. There are some Shared Hosters where you can run your own
  binaries. The server does not need to run XServer - only some libraries are needed. Ask your provider on their support
  or just give it a try. Binaries for i386 and amd64 are included - so plug in and start.

Quickstart
==========

.. NOTE::
   A full documentation is coming soon™. Stay tuned.

Prerequisites
-------------

* Your hoster needs to allow you to run binaries.
* PHP function ``proc_open()`` must not be disabled.
* wkhtmltopdf supports your architecture. But if you use some Linux, Windows or Mac OS it should work.

Setup
-----

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
               # this enables the conversion
               enable = 1
               # enable this if you don't want non-cacheable pages to be converted
               disableInt = 0

               # parameters passed along to the wkhtmltopdf binary
               # format is (name) = (value|[blank])
               # run the binary from the shell with the ``-H`` option for a full list
               binParameters {
                   # this will add a page number to the foot of each printed page
                   footer-right = [page]/[toPage]
               }
           }

           # ask the users browser to treat the content as a PDF file
           additionalHeaders = Content-Type: application/pdf
           # needed to have absolute links in your PDF
           absRefPrefix = http://www.example.com/
       }

   .. NOTE:: Note on disableInt
      You should turn this feature for stability reasons. If a non-cached page is requested as PDF a 404-Error will be thrown.
      Please keep in mind, that if you are logged-in to the backend of TYPO3 non of your requested pages will be cached.
      So you won't see any PDF files until you log out or use a different browser.

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

If you are lucky the extension should run now. So there's nothing left for me than to wish you "Good Luck!" :)