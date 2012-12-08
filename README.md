# Audit Trail Extension

The [Audit Trail](http://wordpress.org/extend/plugins/audit-trail/) plugin for WordPress facilitates logging of administrative events, and is extensible to let developers add custom events. There's no documentation for extending it, though, so in the past developers would have to spend a few hours digging through the code to learn how it works internally before being able to add their own events. This plugin builds a framework for adding custom events, and provides two examples -- plugin activation/deactivation and updating options -- to help developers quickly add their own custom events.

It's intended to be forked and customized, rather than interacting with it via an API.
 

## TODO
* Add CSS to pretty it up
* Add notice if Audit Trail isn't activated
* IP address link disappears when viewing entry detail
* Add options for which options to ignore, ignore transients or not, ignore private or not


## License

This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

In jurisdictions that recognize copyright laws, the author or authors
of this software dedicate any and all copyright interest in the
software to the public domain. We make this dedication for the benefit
of the public at large and to the detriment of our heirs and
successors. We intend this dedication to be an overt act of
relinquishment in perpetuity of all present and future rights to this
software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <http://unlicense.org/>