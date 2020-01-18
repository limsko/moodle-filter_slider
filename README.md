Slider Filter
=========================
This filter allows You to include slider from block_slider anywhere in Moodle content.


Release notes
=========================

* 1.0.1 Fixed missing block_base class during page edit.
* 1.0 This is first release of this filter.


Requirements
=========================
First You have to install block Slider version 0.2 available at https://moodle.org/plugins/block_slider


Installation
=========================
Install the plugin like any other plugin to folder /filter/slider

If You're installing with Plugin installer using zip archive choose plugin type: Text filter (filter)

See http://docs.moodle.org/28/en/Installing_plugins for details on installing Moodle plugins


Usage
=========================
First, then activate the filter_slider plugin in Site Administration -> Plugins -> Filters -> Manage filters
Use this code `[SLIDER-##]` where ## put sliderid, You can get sliderid from browser URL when managing slider.
For example if the URL is: https://moodle.limsko.pl/blocks/slider/manage_images.php?sliderid=25
Code should look like this: `[SLIDER-25]`

The code "[SLIDER-25]" will be replaced with slider content. Block instance should be created on other page
than the filter will be used on.

Settings
=========================
There is no settings for this plugin.

Copyright
=========================
Written by Kamil Łuczak
kamil@limsko.pl
https://www.limsko.pl