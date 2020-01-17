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
 * @package    filter
 * @copyright  2020 Kamil Łuczak <kamil@limsko.pl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class filter_slider
 */
class filter_slider extends moodle_text_filter
{

    /**
     * Apply the filter to the text
     *
     * @param string $text to be processed by the text
     * @param array $options filter options
     * @return string text after processing
     * @see filter_manager::apply_filter_chain()
     */
    public function filter($text, array $options = array()) {
        global $CFG, $DB, $OUTPUT, $PAGE;
        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->dirroot . '/blocks/slider/block_slider.php');
        $pattern = "/\[SLIDER-(\d{1,3})\]/i";
        if (preg_match($pattern, $text, $output)) {
            $sliderid = $output[1];
            if ($blockinstance = $DB->get_record('block_instances', array('id' => $sliderid, 'blockname' => 'slider'))) {
                $block = new block_slider();
                $block->_load_instance($blockinstance, $PAGE);
                $content = $block->get_content();
                $text = preg_replace($pattern, '<div class="block_slider">' . $content->text . '</div>', $text);
            } else {
                $text = preg_replace($pattern, 'Slider Block with ID ' . $sliderid . ' doesnt exists!', $text);
            }
        }
        return $text;
    }
}