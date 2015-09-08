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

$url = new moodle_url("/local/pluginboletas/index.php");
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout("standard");

// Possible actions -> view, add, edit or delete. Standard is view mode
$action = optional_param("action", "view", PARAM_TEXT);
$idboleta = optional_param("idboleta", null, PARAM_INT);
$date = date("Ymd", time());
global $DB, $PAGE, $OUTPUT;

$PAGE->set_title("Boletas UAI");
$PAGE->set_heading("Boletas UAI");
echo $OUTPUT->header();

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
				"Fecha de emisi&oacute;n",
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
					date("d-m-Y", $boleta->fecha),
					$deleteaction_boleta.$editaction_boleta
			);
		}
	}
	
	
}


if ($action == "view")
{
	
	if (count($boletas) == 0)
	{
		echo html_writer::nonempty_tag("h4", "No existen boletas", array("align" => "center"));
	}
	else
	{
			echo html_writer::table($boletastable);
	}
}

echo $OUTPUT->footer();