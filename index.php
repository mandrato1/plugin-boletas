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
global $DB, $PAGE, $OUTPUT;

require_login();
if (isguestuser()){
	die();
}

$PAGE->set_title(get_string("title", "local_pluginboletas"));
$PAGE->set_heading(get_string("receipts_heading", "local_pluginboletas"));
echo $OUTPUT->header();

// Adds a record to the database
if ($action == "add"){
	$addform = new addboleta_form();
	
	if ($addform->is_cancelled()){
		$action = "view";
	}
	else if ($creationdata = $addform->get_data()){
		$record = new stdClass();
		$record->usuarios_id = $creationdata->usuarios_id;
		$record->sedes_id = $creationdata->sedes_id;
		$record->fecha = time();
		$record->monto = $creationdata->monto;

		$DB->insert_record("pluginboletas_boletas", $record);
		$action = "view";
	}
}

// Edits an existent record
if($action == "edit"){
	if($idboleta == null){
		print_error(get_string("receipt_notselected", "local_pluginboletas"));
		$action = "view";
	}else{
		if($boleta = $DB->get_record("pluginboletas_boletas", array("id" => $idboleta))){
			$editform = new editboleta_form(null, array(
					"idboleta" => $idboleta
			));
			
			$defaultdata = new stdClass();
			$defaultdata->usuarios_id = $boleta->usuarios_id;
			$defaultdata->sedes_id = $boleta->sedes_id;
			$defaultdata->monto = $boleta->monto;
			$editform->set_data($defaultdata);
			
			if($editform->is_cancelled()){
				$action = "view";
			}else if($editform->get_data()){
				$record = new stdClass();
				$record->id = $idboleta;
				$record->usuarios_id = $editform->get_data()->usuarios_id;
				$record->sedes_id = $editform->get_data()->sedes_id;
				$record->monto = $editform->get_data()->monto;
				
				$DB->update_record("pluginboletas_boletas", $record);
				$action = "view";
			}
		}else{
			print_error(get_string("receipt_doesntexist", "local_pluginboletas"));
			$action = "view";
		}
	}
}

// Delete the selected record
if ($action == "delete"){
	if ($idboleta == null){
		print_error(get_string("receipt_notselected", "local_pluginboletas"));
		$action = "view";
	}else{
		if ($boleta = $DB->get_record("pluginboletas_boletas", array("id" => $idboleta))){
			$DB->delete_records("pluginboletas_boletas", array("id" => $boleta->id));
			$action = "view";
		}else{
			print_error(get_string("receipt_doesntexist", "local_pluginboletas"));
			$action = "view";
		}
	}
}

// Lists all the records in the database
if ($action == "view"){
	$sql = "SELECT b.id, b.fecha, b.monto, CONCAT(u.firstname, ' ', u.lastname) AS nombre, s.direccion
			FROM {pluginboletas_boletas} AS b, {user} AS u, {pluginboletas_sedes} AS s 
			WHERE b.usuarios_id=u.id AND b.sedes_id=s.id
			GROUP BY b.id";
	
	$boletas = $DB->get_records_sql($sql, array(1));
	$boletastable = new html_table();
	
	if (count($boletas) > 0){
		$boletastable->head = array(
				"ID",
				get_string("date", "local_pluginboletas"),
				get_string("amount", "local_pluginboletas"),
				get_string("client", "local_pluginboletas"),
				get_string("address", "local_pluginboletas"),
				get_string("settings", "local_pluginboletas")
		);
		
		foreach ($boletas as $boleta){
			// Define deletion icon and url
			$deleteurl_boleta = new moodle_url("/local/pluginboletas/index.php", array(
					"action" => "delete",
					"idboleta" => $boleta->id,
			));
			$deleteicon_boleta = new pix_icon("t/delete", "Borrar");
			$deleteaction_boleta = $OUTPUT->action_icon(
					$deleteurl_boleta,
					$deleteicon_boleta,
					new confirm_action(get_string("receipt_deleteconfirm", "local_pluginboletas"))
			);
			
			// Define edition icon and url
			$editurl_boleta = new moodle_url("/local/pluginboletas/index.php", array(
					"action" => "edit",
					"idboleta" => $boleta->id
			));
			$editicon_boleta = new pix_icon("i/edit", "Editar");
			$editaction_boleta = $OUTPUT->action_icon(
					$editurl_boleta,
					$editicon_boleta,
					new confirm_action(get_string("receipt_editconfirm", "local_pluginboletas"))
			);
			
			$boletastable->data[] = array(
					$boleta->id,
					date("d-m-Y", $boleta->fecha),
					"$".$boleta->monto,
					$boleta->nombre,
					$boleta->direccion,
					$deleteaction_boleta.$editaction_boleta
			);
		}
	}
	
	$buttonurl = new moodle_url("/local/pluginboletas/index.php", array("action" => "add"));

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
}

// Displays the form to add a record
if ($action == "add"){
	$addform->display();
}

// Displays the form to edit a record
if( $action == "edit" ){
	$editform->display();
}

// Displays all the records, tabs, and options
if ($action == "view"){
	echo $OUTPUT->tabtree($toprow, get_string("receipts", "local_pluginboletas"));
	if (count($boletas) == 0){
		echo html_writer::nonempty_tag("h4", get_string("noreceipts", "local_pluginboletas"), array("align" => "center"));
	}else{
			echo html_writer::table($boletastable);
	}
	
	echo html_writer::nonempty_tag("div", $OUTPUT->single_button($buttonurl, get_string("addreceipt", "local_pluginboletas")), array("align" => "center"));
}

echo $OUTPUT->footer();