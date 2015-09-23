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
 * @package    local
 * @subpackage pluginboletas
 * @copyright  2015  Mark Michaelsen (mmichaelsen678@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once (dirname(dirname(dirname(__FILE__)))."/config.php");

$context = context_system::instance();

$url = new moodle_url("/local/pluginboletas/users.php");
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout("standard");
global $DB, $PAGE, $OUTPUT;

require_login();
if (isguestuser()){
	die();
}

$PAGE->set_title(get_string("title", "local_pluginboletas"));
$PAGE->set_heading(get_string("users_heading", "local_pluginboletas"));
echo $OUTPUT->header();

// Query for retrieving users records, skipping Guest and Admin users
$sql = "SELECT id, CONCAT(firstname, ' ', lastname) AS name, email
		FROM {user}
		WHERE id>2";
$users = $DB->get_records_sql($sql, array(1));
$userstable = new html_table();

if (count($users) > 0){
	$userstable->head = array(
			"ID",
			get_string("name", "local_pluginboletas"),
			"E-mail"
	);
	
	foreach ($users as $user){	
		$userstable->data[] = array(
				$user->id,
				$user->name,
				$user->email,
		);
	}
}


$toprow = array();
$toprow[] = new tabobject(
		get_string("receipts", "local_pluginboletas"),
		new moodle_url("/local/pluginboletas/index.php"),
		get_string("receipts", "local_pluginboletas")
);
$toprow[] = new tabobject(
		get_string("users", "local_pluginboletas"),
		new moodle_url("/local/pluginboletas/users.php"),
		get_string("users", "local_pluginboletas")
);
$toprow[] = new tabobject(
		get_string("addresses", "local_pluginboletas"),
		new moodle_url("/local/pluginboletas/sedes.php"),
		get_string("addresses", "local_pluginboletas")
);

echo $OUTPUT->tabtree($toprow, get_string("users", "local_pluginboletas"));
if (count($users) == 0){
	echo html_writer::nonempty_tag("h4", get_string("nousers", "local_pluginboletas"), array("align" => "center"));
}else{
	echo html_writer::table($userstable);
}

echo $OUTPUT->footer();