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

namespace filter_slider;

use filter_slider\local\shortcodes;

/**
 * Testy filtra slajdera.
 *
 * @package     filter_slider
 * @category    test
 * @author      Kamil Łuczak <kamil@limsko.pl>
 * @copyright   (C) 2026 Kamil Łuczak (https://limsko.pl)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(text_filter::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(shortcodes::class)]
final class text_filter_test extends \advanced_testcase {
    /**
     * Strona, na której filtrowany jest tekst.
     */
    protected function setUp(): void {
        global $PAGE;

        parent::setUp();
        $this->resetAfterTest();
        $PAGE->set_url('/');
        $PAGE->set_context(\context_system::instance());
    }

    /**
     * Slajder w podanym kontekście z jednym slajdem.
     *
     * @param \context $parent kontekst nadrzędny bloku
     * @param string $title tytuł slajdu
     * @return int identyfikator bloku
     */
    protected function create_slider(\context $parent, string $title): int {
        $generator = $this->getDataGenerator()->get_plugin_generator('block_slider');
        $block = $generator->create_instance(['parentcontextid' => $parent->id]);
        $generator->create_slide(['blockinstanceid' => $block->id, 'title' => $title]);
        return (int) $block->id;
    }

    /**
     * Filtr z kontekstem systemowym.
     *
     * @param string $text tekst
     * @return string
     */
    protected function filter(string $text): string {
        return (new text_filter(\context_system::instance(), []))->filter($text);
    }

    /**
     * Tekst bez kodów zostaje bez zmian.
     */
    public function test_text_without_shortcode(): void {
        $text = '<p>Plain text with [brackets] and SLIDER word.</p>';
        $this->assertSame($text, $this->filter($text));
    }

    /**
     * Każdy kod zamieniany jest na swój slajder.
     */
    public function test_replaces_every_shortcode(): void {
        $this->setAdminUser();
        $first = $this->create_slider(\context_system::instance(), 'First slider');
        $second = $this->create_slider(\context_system::instance(), 'Second slider');

        $html = $this->filter("<p>A</p>[SLIDER-{$first}]<p>B</p>[slider-{$second}]");

        $this->assertStringNotContainsString('[SLIDER-', $html);
        $this->assertStringContainsString('class="filter-slider"', $html);
        $this->assertStringContainsString('block-slider-slide', $html);
        $this->assertLessThan(strpos($html, 'Second slider'), strpos($html, 'First slider'));
        $this->assertLessThan(strpos($html, '<p>B</p>'), strpos($html, 'First slider'));
    }

    /**
     * Nieistniejący blok albo blok innego typu: pusty tekst, a w trybie edycji komunikat.
     */
    public function test_missing_block(): void {
        global $USER;

        $html = $this->getDataGenerator()->create_block('html');
        $this->setAdminUser();
        $this->assertSame('<p>x</p>', $this->filter('<p>x</p>[SLIDER-999999]'));
        $this->assertSame('', $this->filter("[SLIDER-{$html->id}]"));

        $USER->editing = 1;
        $notice = $this->filter('[SLIDER-999999]');
        $this->assertStringContainsString(get_string('block_id_not_exists', 'filter_slider', 999999), $notice);
    }

    /**
     * Slajder z kursu tylko dla osób z dostępem do kursu.
     */
    public function test_course_access(): void {
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $outsider = $this->getDataGenerator()->create_user();
        $blockid = $this->create_slider(\context_course::instance($course->id), 'Course slider');

        $this->setUser($student);
        $this->assertStringContainsString('Course slider', $this->filter("[SLIDER-{$blockid}]"));

        $this->setUser($outsider);
        $this->assertSame('', $this->filter("[SLIDER-{$blockid}]"));
    }

    /**
     * Slajder z kokpitu widzi tylko jego właściciel.
     */
    public function test_dashboard_is_private(): void {
        $owner = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();
        $blockid = $this->create_slider(\context_user::instance($owner->id), 'Private slider');

        $this->setUser($owner);
        $this->assertStringContainsString('Private slider', $this->filter("[SLIDER-{$blockid}]"));

        $this->setUser($other);
        $this->assertSame('', $this->filter("[SLIDER-{$blockid}]"));
    }

    /**
     * Slajder na stronie głównej jest dostępny także dla gości.
     */
    public function test_front_page_for_guest(): void {
        $blockid = $this->create_slider(\context_course::instance(SITEID), 'Front page slider');

        $this->setGuestUser();
        $this->assertStringContainsString('Front page slider', $this->filter("[SLIDER-{$blockid}]"));
    }

    /**
     * Kod w napisie slajdu nie powoduje zapętlenia.
     */
    public function test_nested_shortcode(): void {
        $this->setAdminUser();
        $system = \context_system::instance();
        $generator = $this->getDataGenerator()->get_plugin_generator('block_slider');
        $block = $generator->create_instance(['parentcontextid' => $system->id]);
        $generator->create_slide(['blockinstanceid' => $block->id, 'title' => "Nested [SLIDER-{$block->id}]"]);
        set_config('filterall', 1);
        filter_set_global_state('slider', TEXTFILTER_ON);

        $html = format_text("[SLIDER-{$block->id}]", FORMAT_HTML, ['context' => $system]);

        $this->assertStringContainsString("Nested [SLIDER-{$block->id}]", $html);
        $this->assertSame(1, substr_count($html, 'class="filter-slider"'));
    }

    /**
     * Pusty slajder (bez slajdów) nie zostawia znaczników.
     */
    public function test_slider_without_slides(): void {
        $this->setAdminUser();
        $block = $this->getDataGenerator()->get_plugin_generator('block_slider')->create_instance();

        $this->assertSame('', shortcodes::render((int) $block->id));
    }
}
