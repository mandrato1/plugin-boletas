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

// Possible actions -> view, add, edit or delete. Standard is view mode
$action = optional_param("action", "view", PARAM_TEXT);
$iduser = optional_param("iduser", null, PARAM_INT);
$date = date("Ymd", time());
global $DB, $PAGE, $OUTPUT;

require_login();
if (isguestuser()){
	die();
}

$PAGE->set_title("Usuarios");
$PAGE->set_heading("Usuarios");
echo $OUTPUT->header();

if ($action == "view")
{
	$sql = "SELECT id, CONCAT(firstname, ' ', lastname) AS name, email
			FROM {user}";
	$users = $DB->get_records_sql($sql, array(1));
	$userstable = new html_table();
	if (count($users) > 0)
	{
		$userstable->head = array(
				"ID",
				"Nombre",
				"E-mail",
				"Ajustes"
		);

		foreach ($users as $user)
		{
			$deleteurl_user = new moodle_url ("/local/pluginboletas/users.php", array (
					"action" => "delete",
					"iduser" => $user->id,
			));
			$deleteicon_user = new pix_icon ("t/delete", "Borrar");
			$deleteaction_user = $OUTPUT->action_icon (
					$deleteurl_user,
					$deleteicon_user,
					new confirm_action ("Desea borrar el registro del usuario?")
			);
				
			$editurl_user = new moodle_url ("/local/pluginboletas/users.php", array (
					"action" => "edit",
					"iduser" => $user->id
			));
			$editicon_user = new pix_icon ("i/edit", "Editar");
			$editaction_user = $OUTPUT->action_icon(
					$editurl_user,
					$editicon_user,
					new confirm_action("Desea editar el registro del usuario?")
			);
				
			$userstable->data[] = array(
					$user->id,
					$user->name,
					$user->email,
					$deleteaction_user.$editaction_user
			);
		}
	}

	$buttonurl = new moodle_url("/local/pluginboletas/users.php", array("action" => "add"));

	$toprow = array();
	$toprow[] = new tabobject(
			"Boletas",
			new moodle_url("/local/pluginboletas/index.php"),
			"Boletas"
	);
	$toprow[] = new tabobject(
			"Usuarios",
			new moodle_url("/local/pluginboletas/users.php"),
			"Usuarios"
	);
	$toprow[] = new tabobject(
			"Sedes",
			new moodle_url("/local/pluginboletas/sedes.php"),
			"Sedes"
	);
}

if ($action == "view")
{
	echo $OUTPUT->tabtree($toprow, "Usuarios");
	if (count($users) == 0)
	{
		echo html_writer::nonempty_tag("h4", "No existen registros de usuarios", array("align" => "center"));
	}
	else
	{
		echo html_writer::table($userstable);
	}

	echo html_writer::nonempty_tag("div", $OUTPUT->single_button($buttonurl, "Anadir registro de usuario"), array("align" => "center"));
}

echo $OUTPUT->footer();