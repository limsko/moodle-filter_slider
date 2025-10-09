<?php
namespace filter_slider;

defined('MOODLE_INTERNAL') || die();

use block_slider;
use context_block;

class text_filter extends \core_filters\text_filter {

    public function filter($text, array $options = []) {
        global $CFG, $DB, $PAGE, $USER, $SITE;
        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
        require_once($CFG->dirroot . '/blocks/slider/block_slider.php');
        $pattern = "/\[SLIDER-(\d{1,5})\]/i";
        if (preg_match($pattern, $text, $output)) {
            $sliderid = $output[1];
            if ($blockinstance = $DB->get_record('block_instances', array('id' => $sliderid, 'blockname' => 'slider'))) {
                $block = new block_slider();
                $block->_load_instance($blockinstance, $PAGE);
                $content = $block->get_content();
                $contextblock = context_block::instance($blockinstance->id);
                $parentcontext = $contextblock->get_parent_context();
                $blockonfrontpage = ($SITE->id == $parentcontext->instanceid); // Skip enrolment and course capability check.
                if (!has_capability('moodle/block:view', $contextblock)
                        OR !$blockonfrontpage AND ($parentcontext->contextlevel == CONTEXT_COURSE AND !is_enrolled($parentcontext))
                        AND ($parentcontext->contextlevel == CONTEXT_COURSE
                                AND !has_capability('moodle/course:view', $parentcontext)
                        )
                ) {
                    // This user is not allowed to see this block.
                    if (isset($USER->editing) && $USER->editing) {
                        // Only when editing user can see the message.
                        return get_string('not_allowed', 'filter_slider', $sliderid);
                    }
                    // Specifically, I do not display any message to avoid confusion among users.
                    return '';
                }
                $text = preg_replace($pattern, '<div class="block_slider">' . $content->text . '</div>', $text);
            } else {
                $text = preg_replace($pattern, get_string('block_id_not_exists', 'filter_slider', $sliderid), $text);
            }
        }
        return $text;
    }
}
