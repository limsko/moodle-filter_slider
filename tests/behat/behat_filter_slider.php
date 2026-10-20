<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Kroki Behat filtra slajdera.
 *
 * @package     filter_slider
 * @category    test
 * @author      Kamil Łuczak <kamil@limsko.pl>
 * @copyright   (C) 2026 Kamil Łuczak (https://limsko.pl)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Kroki osadzania slajdera w treści.
 *
 * @package     filter_slider
 * @category    test
 * @copyright   (C) 2026 Kamil Łuczak (https://limsko.pl)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_filter_slider extends behat_base {
    /**
     * Wstawia kod slajdera o podanym tytule bloku do treści aktywności Strona.
     *
     * @Given /^the slider block "(?P<title_string>(?:[^"]|\\")*)" is embedded in the "(?P<page_string>(?:[^"]|\\")*)" page$/
     * @param string $title tytuł bloku slajdera
     * @param string $pagename nazwa aktywności Strona
     */
    public function the_slider_block_is_embedded_in_the_page(string $title, string $pagename): void {
        global $DB;

        $blockid = null;
        foreach ($DB->get_records('block_instances', ['blockname' => 'slider'], 'id ASC', 'id, configdata') as $instance) {
            $config = empty($instance->configdata) ? null : unserialize_object(base64_decode($instance->configdata));
            if (is_object($config) && isset($config->title) && $config->title === $title) {
                $blockid = (int) $instance->id;
            }
        }
        if (!$blockid) {
            throw new Exception('Slider block with title "' . $title . '" does not exist');
        }
        $page = $DB->get_record('page', ['name' => $pagename], '*', MUST_EXIST);
        $content = '<p>Text before the slider.</p>[SLIDER-' . $blockid . ']<p>Text after the slider.</p>';
        $DB->set_field('page', 'content', $content, ['id' => $page->id]);
    }
}
