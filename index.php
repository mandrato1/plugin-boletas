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
require_once ($CFG->dirroot."/local/pluginboletas/forms/boletas_form.php");

$context = context_system::instance();

$url = new moodle_url("/local/pluginboletas/index.php");
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout("standard");

// Possible actions -> view, add, edit or delete. Standard is view mode
$action = optional_param("action", "view", PARAM_TEXT);
$idboleta = optional_param("idboleta", null, PARAM_INT);
$date = date("Ymd", time());
global $DB, $PAGE, $OUTPUT;
echo time();

require_login();
if (isguestuser()) {
	die();
}

$PAGE->set_title("Boletas UAI");
$PAGE->set_heading("Boletas UAI");
echo $OUTPUT->header();


if ($action == "add")
{
	$addform = new addboleta_form();
	if ($addform->is_cancelled())
	{
		$action = "view";
	}
	else if ($creationdata = $addform->get_data())
	{


		$record = new stdClass();
		$record->usuarios_id = $creationdata->usuarios_id;
		$record->sedes_id = $creationdata->sedes_id;
		$record->fecha = time();
		$record->monto = $creationdata->monto;

		$DB->insert_record("pluginboletas_boletas", $record);
		$action = "view";
	}
}



// Delete the selected receipt
if ($action == "delete")
{
	if ($idboleta == null)
	{
		print_error("No se selecciono boleta");
		$action = "view";
	}
	else
	{
		if ($boleta = $DB->get_record("pluginboletas_boletas", array("id"=>$idboleta)))
		{
			$DB->delete_records("pluginboletas_boletas", array("id"=>$boleta->id));
			$action = "view";
		}
		else
		{
			print_error("La boleta no existe");
			$action = "view";
		}
	}
}



if ($action == "view")
{
	$boletas = $DB->get_records("pluginboletas_boletas");
	$boletastable = new html_table();
	if (count($boletas) > 0)
	{
		$boletastable->head = array (
				"ID",
				"ID de usuario",
				"ID de sede",
				"Fecha de emisi&oacute;n",
				"Monto",
				"Ajustes"
		);
		
		foreach ($boletas as $boleta)
		{
			$deleteurl_boleta = new moodle_url ("/local/pluginboletas/index.php", array (
					"action" => "delete",
					"idboleta" => $boleta->id,
			));
			$deleteicon_boleta = new pix_icon ("t/delete", "Borrar");
			$deleteaction_boleta = $OUTPUT->action_icon (
					$deleteurl_boleta,
					$deleteicon_boleta,
					new confirm_action ("Desea borrar la boleta?")
			);
			
			$editurl_boleta = new moodle_url ("/local/pluginboletas/index.php", array (
					"action" => "edit",
					"idboleta" => $boleta->id
			));
			$editicon_boleta = new pix_icon ("i/edit", "Editar");
			$editaction_boleta = $OUTPUT->action_icon (
					$editurl_boleta,
					$editicon_boleta,
					new confirm_action ("Desea editar la boleta?")
			);
			
			$boletastable->data[] = array (
					$boleta->id,
					$boleta->usuarios_id,
					$boleta->sedes_id,
					date("d-m-Y", $boleta->fecha),
					"$".$boleta->monto,
					$deleteaction_boleta.$editaction_boleta
			);
		}
	}
	
	$buttonurl = new moodle_url("/local/pluginboletas/index.php", array("action" => "add"));

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

if ($action == "add")
{
	$addform->display();
}

if ($action == "view")
{
	echo $OUTPUT->tabtree($toprow, "Boletas");
	if (count($boletas) == 0)
	{
		echo html_writer::nonempty_tag("h4", "No existen boletas", array("align" => "center"));
	}
	else
	{
			echo html_writer::table($boletastable);
	}
	
	echo html_writer::nonempty_tag("div", $OUTPUT->single_button($buttonurl, "Anadir registro de boleta"), array("align" => "center"));
}

echo $OUTPUT->footer();