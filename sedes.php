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

$url = new moodle_url("/local/pluginboletas/sedes.php");
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout("standard");

// Possible actions -> view, add, edit or delete. Standard is view mode
$action = optional_param("action", "view", PARAM_TEXT);
$idlocal = optional_param("idlocal", null, PARAM_INT);
global $DB, $PAGE, $OUTPUT;

require_login();
if (isguestuser()){
	die();
}

$PAGE->set_title("Sedes");
$PAGE->set_heading("Sedes");
echo $OUTPUT->header();

if ($action == "view"){
	$locals = $DB->get_records("pluginboletas_sedes");
	$localstable = new html_table();
	if (count($locals) > 0){
		$localstable->head = array(
				"ID",
				"Direcci&oacute;n",
				"Ajustes"
		);

		foreach($locals as $local){
			$deleteurl_local = new moodle_url("/local/pluginboletas/sedes.php", array(
					"action" => "delete",
					"idlocal" => $local->id,
			));
			$deleteicon_local = new pix_icon ("t/delete", "Borrar");
			$deleteaction_local = $OUTPUT->action_icon(
					$deleteurl_local,
					$deleteicon_local,
					new confirm_action ("Desea borrar el registro de la sede?")
			);

			$editurl_local = new moodle_url("/local/pluginboletas/sedes.php", array(
					"action" => "edit",
					"idlocal" => $local->id
			));
			$editicon_local = new pix_icon ("i/edit", "Editar");
			$editaction_local = $OUTPUT->action_icon(
					$editurl_local,
					$editicon_local,
					new confirm_action("Desea editar el registro de la sede?")
			);

			$localstable->data[] = array(
					$local->id,
					$local->direccion,
					$deleteaction_local.$editaction_local
			);
		}
	}

	$buttonurl = new moodle_url("/local/pluginboletas/locals.php", array("action" => "add"));

	$toprow = array();
	$toprow[] = new tabobject(
			"Boletas",
			new moodle_url("/local/pluginboletas/index.php"),
			"Boletas"
	);
	$toprow[] = new tabobject(
			"Usuarios",
			new moodle_url("/local/pluginboletas/locals.php"),
			"Usuarios"
	);
	$toprow[] = new tabobject(
			"Sedes",
			new moodle_url("/local/pluginboletas/sedes.php"),
			"Sedes"
	);
}

if ($action == "view"){
	echo $OUTPUT->tabtree($toprow, "Sedes");
	if (count($locals) == 0){
		echo html_writer::nonempty_tag("h4", "No existen registros de sedes", array("align" => "center"));
	}else{
		echo html_writer::table($localstable);
	}

	echo html_writer::nonempty_tag("div", $OUTPUT->single_button($buttonurl, "Anadir registro de sede"), array("align" => "center"));
}

echo $OUTPUT->footer();