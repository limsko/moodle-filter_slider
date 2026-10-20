# Slider in content (filter_slider)

Text filter that shows a [Slider block](https://github.com/limsko/moodle-block_slider) anywhere in Moodle
content: pages, labels, course summaries, forum posts and other texts processed by filters.

## Usage

1. Install the Slider block and this filter.
2. Enable the filter in *Site administration > Plugins > Filters > Manage filters*.
3. Create a Slider block on any page and copy its code from *Manage slides*, for example `[SLIDER-25]`.
4. Paste the code into any text. It is replaced with the slider, with the same slides and display settings.

Several codes can be used in one text. The slider is shown only to users who can see the original block:
users with access to its course (or activity), anyone on the front page, only the owner for a block on a
personal Dashboard. Users editing the page see a notice when the code is wrong or the block is not
available to them.

## Requirements

| Branch | Moodle | Slider block |
|---|---|---|
| `MOODLE_405_STABLE` | 4.2 - 4.5 | 1.0.0 or later |
| `MOODLE_503_STABLE` | 5.0 - 5.3 | 1.0.0 or later |

Version numbers (`YYYYMMDDXX`) end with 00-49 on `MOODLE_405_STABLE` and 50-99 on `MOODLE_503_STABLE`,
so for the same date the 5.x build is always newer. Release fixes on both branches together.

## Installation

Install the plugin into `filter/slider` (`public/filter/slider` in Moodle 5.1 and later) or use the plugin
installer and choose the plugin type "Text filter". See
[Installing plugins](https://docs.moodle.org/en/Installing_plugins).

## Version history

### 2.0.0
* Support for Slider block 1.0.0, Moodle 4.2 - 4.5 and 5.0 - 5.3.
* All codes in a text are replaced (previously every code showed the first slider).
* Access checks for courses, activities, categories and personal Dashboards.
* Protection against a slider code inside the slider captions.
* Notices only for users editing the page.
* PHPUnit and Behat tests.

### 1.2
* Moodle 4.5 filter class.

### 1.1
* Fixed issues #3, #5 and #6.

### 1.0
* First release.

## License

GNU GPL v3 or later. (C) 2020-2026 Kamil Łuczak
