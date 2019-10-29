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
 *
 * @package     mod_ildembform
 * @copyright   2019 Stefan Bomanns, ILD, Technische Hochschule Lübeck, <stefan.bomanns@th-luebeck.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

if ($oldversion < XXXXXXXXXX) {

    // Define table ildembform to be created.
    $table = new xmldb_table('ildembform');

    // Adding fields to table ildembform.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('contentview', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
    $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('emails', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

    // Adding keys to table ildembform.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Adding indexes to table ildembform.
    $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

    // Conditionally launch create table for ildembform.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Ildembform savepoint reached.
    upgrade_mod_savepoint(true, XXXXXXXXXX, 'ildembform');
}