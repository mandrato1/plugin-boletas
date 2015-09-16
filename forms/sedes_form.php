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

class addlocal_form extends moodleform {
	function definition (){
		global $DB, $CFG;
		$mform = $this->_form;

		// Address input
		$mform->addElement("text", "direccion", "Direccion (calle y numeracion)");
		$mform->setType("direccion", PARAM_TEXT);

		// Set action to "add"
		$mform->addElement ("hidden", "action", "add");
		$mform->setType ("action", PARAM_TEXT);

		$this->add_action_buttons(true);
	}
	function validation ($data, $files){
		global $DB;
		$errors = array();

		$address = $data["direccion"];

		if (isset($data["direccion"]) && !empty($data["direccion"]) && $data["direccion"] != "" && $data["direccion"] != null ){
		}else{
			$errors["direccion"] = "Este campo es requerido";
		}

		return $errors;
	}
}

class editlocal_form extends moodleform {
	function definition (){
		global $DB, $CFG;
		$mform = $this->_form;
		$instance = $this->_customdata;
		$idlocal = $instance["idlocal"];

		// Retrieves the previous information registered
		$local = $DB->get_record("pluginboletas_sedes", array("id" => $idlocal));
		
		// Address input
		$mform->addElement("text", "direccion", "Direccion (calle y numeracion)");
		$mform->setType("direccion", PARAM_TEXT);
		$mform->setDefault("direccion", $local->direccion);

		// Set action to "edit"
		$mform->addElement ("hidden", "action", "edit");
		$mform->setType ("action", PARAM_TEXT);
		$mform->addElement("hidden", "idlocal", $idlocal);
		$mform->setType("idlocal", PARAM_INT);

		$this->add_action_buttons (true);
	}

	function validation($data, $files){
		global $DB;
		
		$errors = array ();

		if (isset($data["direccion"]) && !empty($data["direccion"]) && $data["direccion"] != "" && $data["direccion"] != null ){
		}else{
			$errors["direccion"] = "Este campo es requerido";
		}

		return $errors;
	}
}