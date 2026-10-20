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
 * Filtr dla Moodle 4.2 - 4.4. Od 4.5 rdzeń używa klasy \filter_slider\text_filter i nie ładuje tego pliku.
 *
 * @package     filter_slider
 * @author      Kamil Łuczak <kamil@limsko.pl>
 * @copyright   (C) 2020-2026 Kamil Łuczak (https://limsko.pl)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use filter_slider\local\shortcodes;

/**
 * Filtr wyświetlający slajder z bloku w dowolnej treści (Moodle 4.2 - 4.4).
 *
 * @package     filter_slider
 * @copyright   (C) 2020-2026 Kamil Łuczak (https://limsko.pl)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_slider extends moodle_text_filter {
    /**
     * Zamienia kody [SLIDER-id] na slajdery.
     *
     * @param string $text tekst
     * @param array $options opcje filtra
     * @return string
     */
    public function filter($text, array $options = []) {
        return shortcodes::apply((string) $text);
    }
}
