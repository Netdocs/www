##### 2017-05-04 4.0.1

* Fixed: inview-lazyload not working correctly.

##### 2017-05-04 4.0.0

* New: 'volume' attribute takes 1-100 for HTML5 video.
* Improved: Simplified the inview-lazyload option to on/off - was getting to complicated. On uses it when it makes sense (on all mobiles, and when there is no thumbnail detected or set).
* Improved: Scripts like inview and lity lightbox are now only loaded when they are actually needed.
* Improved: Pointer cursor when hovering over the thumbnails, for themes that do not already add it.

##### 2017-04-30 3.9.5

* Code needed for the new way ARVE handles sandbox
* Updated objectFitPolyfill

##### 2017-04-03 3.9.4

* Fix: Deals with other crappy coded plugins that load the Mobile_Detect class without checking if its already loaded.
* Improved: Make sure ARVE Pro always loads its own, possible more up to date version of Mobile_Detect
* Improved: Updated that Mobile_Detect class.
* Improved how aspect ratio is handled with HTML5 videos.

##### 2017-03-25 3.9.3

* Fix: Licensing storage mess. Some users may have to reenter their keys on the licensing page when updating form very old versions.
* Improved: Better Browser support by using Autoprefixer with the [config from bootstrap](https://github.com/twbs/bootstrap/blob/v4-dev/grunt/postcss.config.js)
* Some minor code improvements.

##### 2017-03-20 3.9.2

* Fix: Broken CSS.

##### 2017-03-20 3.9.1

* Fix: Lightboxes with not html5 videos did not close.

##### 2017-03-20 3.9.0

* Fix: Lightboxes are now sized correctly.
* Fix: HTML5 videos in lightbox mode could not be opened twice.
* Improved: HTML5 video now automatically pauses when lightboxes are closes and resume when reopened.
* Improved: Now minified files are served when `WP_DEBUG` is not `true`

##### 2017-03-12 3.8.4

* Improved: Removed incorrect href on <button> and some other smaller code improvements
* Fix: Updated objectFitPolyfill to newest version.

##### 3.8.2

* Fix: Updated objectFitPolyfill to newest version, last version had a bug that caused it to fail.

##### 3.8.1

* Fix missing object-fit-polyfill files

##### 3.8.0

* Fixed unintended autoplay for html5 videos when using inview-lazyload.
* CSS improvements.
* CSS is now again loaded only when there is actually a video in the page.

##### 3.7.0

* Fixed incompatibility with divi theme
* New Feature: Use youtube channel URL in [arve] shortcode to get the latest video (cached/updated hourly)
* Updated Lity and Mobile Detect

##### 3.6.9

* Fixed wrong path for CSS file that loads in the WP editor
* Improved 404 error message for API calls and makes it filterable
