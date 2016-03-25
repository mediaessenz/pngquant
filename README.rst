Integration of pngquant library
===============================

The pngquant library is used to lossy compress PNG images (https://pngquant.org/).
The gain can be significant (often around 70% of the file size) without losing noticeable quality for most images.

This extension integrates pngquant into TYPO3 and provides the following functionalities :

- TYPO3 processed PNG images are automatically compressed
- Uploaded images can be automatically compressed, or kept as masters
- Basic options of pngquand are available (Disable Floyd-Steinberg dithering, Speed/quality trade-off, Quality, Workaround for IE6)

.. contents::

Project info and releases
-------------------------

Home page of the project: https://github.com/morinfa/pngquant

Stable version: http://typo3.org/extensions/repository/view/pngquant


Setup
=====
1. Download and install pngquant command-line tool from https://pngquant.org/
2. Install and activate extension ``pngquant``
3. Configure the extension with the executable path of pngquand (e.g. /usr/bin/pngquant)


Options
=======
- ``basic.executable`` : Full path to pngquant executable. Default ``/usr/local/bin/pngquant``
- ``basic.keepOriginal`` : If set, uploaded images will never be overwritten by pngquant (i.e. only processed images will be converted). Default ``1``
- ``basic.nofs`` : Disable Floyd-Steinberg dithering. Default ``0``
- ``basic.speed`` : Speed/quality trade-off. Default ``3``
- ``basic.quality`` : Don't save below min, use less colors below max (0-100).
- ``basic.iebug`` : Workaround for IE6. Default ``0``

Warning
=======
If you unset the extension option ``basic.keepOriginal``, all your PNG images will be automatically compressed at upload, and the original file will not be kept.

Debug
=====
All errors are logged using TYPO3 logging system. By default messages are logged in typo3temp/logs/typo3.log.

