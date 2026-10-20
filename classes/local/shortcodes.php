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

namespace filter_slider\local;

use context;
use context_block;
use core_course_category;
use html_writer;

/**
 * Zamiana kodów [SLIDER-id] na slajder z bloku block_slider.
 *
 * @package     filter_slider
 * @author      Kamil Łuczak <kamil@limsko.pl>
 * @copyright   (C) 2020-2026 Kamil Łuczak (https://limsko.pl)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shortcodes {
    /** @var string Wzorzec kodu slajdera. */
    public const PATTERN = '/\[SLIDER-(\d{1,10})\]/i';

    /** @var int Głębokość zagnieżdżenia (napis slajdu może zawierać kod innego slajdera). */
    protected static $depth = 0;

    /**
     * Zamienia wszystkie kody w tekście.
     *
     * @param string $text tekst
     * @return string
     */
    public static function apply(string $text): string {
        if (stripos($text, '[slider-') === false || self::$depth > 0) {
            return $text;
        }

        self::$depth++;
        try {
            return preg_replace_callback(self::PATTERN, function (array $matches): string {
                return self::render((int) $matches[1]);
            }, $text);
        } finally {
            self::$depth--;
        }
    }

    /**
     * Slajder bloku albo pusty tekst, gdy blok nie istnieje lub jest niedostępny.
     *
     * Osoby edytujące stronę widzą zamiast tego komunikat, żeby mogły poprawić kod.
     *
     * @param int $blockid identyfikator instancji bloku
     * @return string
     */
    public static function render(int $blockid): string {
        global $DB, $PAGE;

        $instance = $DB->get_record('block_instances', ['id' => $blockid, 'blockname' => 'slider']);
        if (!$instance) {
            return self::notice('block_id_not_exists', $blockid);
        }
        $context = context_block::instance($instance->id);
        if (!self::can_view($context)) {
            return self::notice('not_allowed', $blockid);
        }

        $block = block_instance('slider', $instance, $PAGE);
        $content = $block ? $block->get_content() : null;
        if (empty($content->text)) {
            return '';
        }
        return html_writer::div($content->text, 'filter-slider');
    }

    /**
     * Czy bieżący użytkownik może zobaczyć blok w jego oryginalnym miejscu.
     *
     * @param context_block $context kontekst bloku
     * @return bool
     */
    public static function can_view(context_block $context): bool {
        global $USER;

        if (!has_capability('moodle/block:view', $context)) {
            return false;
        }
        $parent = $context->get_parent_context();
        switch ($parent->contextlevel) {
            case CONTEXT_COURSE:
                return (int) $parent->instanceid === (int) SITEID || can_access_course(get_course($parent->instanceid));
            case CONTEXT_MODULE:
                $courseid = $parent->get_course_context()->instanceid;
                $cm = get_fast_modinfo($courseid)->get_cm($parent->instanceid);
                return can_access_course(get_course($courseid)) && $cm->uservisible;
            case CONTEXT_COURSECAT:
                return core_course_category::get($parent->instanceid, IGNORE_MISSING) !== null;
            case CONTEXT_USER:
                // Prywatny kokpit - tylko właściciel.
                return (int) $parent->instanceid === (int) $USER->id;
            default:
                return true;
        }
    }

    /**
     * Komunikat dla osób edytujących stronę, dla pozostałych pusty tekst.
     *
     * @param string $identifier identyfikator tekstu
     * @param int $blockid identyfikator bloku
     * @return string
     */
    protected static function notice(string $identifier, int $blockid): string {
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return '';
        }
        return html_writer::div(get_string($identifier, 'filter_slider', $blockid), 'filter-slider-notice alert alert-warning');
    }
}
