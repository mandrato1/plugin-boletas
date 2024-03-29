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

require_once (dirname(dirname(dirname(dirname(__FILE__))))."/config.php");
require_once ($CFG->libdir."/formslib.php");

class addboleta_form extends moodleform {
	function definition(){
		global $DB, $CFG;
		$mform = $this->_form;
		
		// Query for retrieving users records in a certain way
		$sql = "SELECT id, CONCAT(firstname, ' ', lastname) AS name, email
				FROM {user}
				WHERE id>2";
		
		// Retrieves addresses and users records
		$sedes = $DB->get_records("pluginboletas_sedes");
		$users = $DB->get_records_sql($sql, array(1));
		
		// Select user input
		$usernames = array();
		foreach($users as $user){
			$id = $user->id;
			$name = $user->name;
			$email = $user->email;
			$usernames[$user->id] = $id." - ".$name." - ".$email;
		}
		$mform->addElement("select", "usuarios_id", get_string("user", "local_pluginboletas"), $usernames);
		
		// Select address input
		$address = array();
		foreach($sedes as $sede){
			$address[$sede->id] = $sede->direccion;
		}
		$mform->addElement ("select", "sedes_id", get_string("address", "local_pluginboletas"), $address);
		
		// Amount paid input
		$mform->addElement ("text", "monto", get_string("amount", "local_pluginboletas"));
		$mform->setType ("monto", PARAM_TEXT);
		
		// Set action to "add"
		$mform->addElement ("hidden", "action", "add");
		$mform->setType ("action", PARAM_TEXT);
		
		$this->add_action_buttons(true);
	}
	function validation ($data, $files){
		global $DB;
		$errors = array();
		
		$usuarios_id = $data["usuarios_id"];
		$sedes_id = $data["sedes_id"];
		$monto = $data["monto"];
		
		if (isset($data["usuarios_id"]) && !empty($data["usuarios_id"]) && $data["usuarios_id"] != "" && $data["usuarios_id"] != null ){
		}else{
			$errors["usuarios_id"] = get_string("field_required", "local_pluginboletas");
		}
		
		if (isset($data["sedes_id"]) && !empty($data["sedes_id"]) && $data["sedes_id"] != "" && $data["sedes_id"] != null ){
		}else{
			$errors["sedes_id"] = get_string("field_required", "local_pluginboletas");
		}
		
		if (isset($data["monto"]) && !empty($data["monto"]) && $data["monto"] != "" && $data["monto"] != null ){
		}else{
			$errors["monto"] = get_string("field_required", "local_pluginboletas");
		}
		
		return $errors;
	}
}

class editboleta_form extends moodleform {
	function definition (){
		global $DB, $CFG;
		$mform = $this->_form;
		$instance = $this->_customdata;
		$idboleta = $instance["idboleta"];
		
		// Query for retrieving users records in a certain way
		$sql = "SELECT id, CONCAT(firstname, ' ', lastname) AS name, email
				FROM {user} 
				WHERE id>2";
		
		// Retrieves addresses and users records
		$users = $DB->get_records_sql($sql, array(1));
		$sedes = $DB->get_records("pluginboletas_sedes");
		
		// Retrieves the previous information registered
		$boletadata = $DB->get_record("pluginboletas_boletas", array("id" => $idboleta));
		
		// Select user input
		foreach ($users as $user){
			$id = $user->id;
			$name = $user->name;
			$email = $user->email;
			$usernames[$user->id] = $id." - ".$name." - ".$email;
		}
		$mform->addElement("select", "usuarios_id", get_string("user", "local_pluginboletas"), $usernames);
		
		// Select address input
		foreach ($sedes as $sede){
			$address[$sede->id] = $sede->direccion;
		}
		$mform->addElement("select", "sedes_id", get_string("address", "local_pluginboletas"), $address);
		
		// Amount paid input
		$mform->addElement("text", "monto", get_string("amount", "local_pluginboletas"));
		$mform->setType("monto", PARAM_TEXT);
		$mform->setDefault("monto", $boletadata->monto);
		
		// Set action to "edit"
		$mform->addElement("hidden", "action", "edit");
		$mform->setType("action", PARAM_TEXT);
		$mform->addElement("hidden", "idboleta", $idboleta);
		$mform->setType("idboleta", PARAM_INT);
		
		$this->add_action_buttons(true);
	}
	
	function validation($data, $files){
		global $DB;
		$errors = array ();
		$monto = $data["monto"];
		
		if (isset($data["usuarios_id"]) && !empty($data["usuarios_id"]) && $data["usuarios_id"] != "" && $data["usuarios_id"] != null ){
		}else{
			$errors["usuarios_id"] = get_string("field_required", "local_pluginboletas");
		}
		
		if (isset($data["sedes_id"]) && !empty($data["sedes_id"]) && $data["sedes_id"] != "" && $data["sedes_id"] != null ){
		}else{
			$errors["sedes_id"] = get_string("field_required", "local_pluginboletas");
		}
		
		if (isset($data["monto"]) && !empty($data["monto"]) && $data["monto"] != "" && $data["monto"] != null ){
		}else{
			$errors["monto"] = get_string("field_required", "local_pluginboletas");
		}
		
		return $errors;
	}
}
