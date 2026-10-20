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
 * Informacje o wersji filtra slajdera.
 *
 * @package     filter_slider
 * @author      Kamil Łuczak <kamil@limsko.pl>
 * @copyright   (C) 2020-2026 Kamil Łuczak (https://limsko.pl)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'filter_slider';
$plugin->version = 2026102000;
$plugin->requires = 2025041400;
$plugin->supported = [500, 503];
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '2.0.0';
$plugin->dependencies = [
    'block_slider' => 2026102000,
];
