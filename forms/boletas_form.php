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

class addboleta_form extends moodleform
{
	function definition ()
	{
		global $CFG;
		$mform = $this->_form;
		
		$mform->addElement ("text", "id", "ID");
		$mform->setType ("id", PARAM_TEXT);
		
		$this->add_action_buttons(true);
	}
	function validation ($data, $files)
	{
		global $DB;
		$errors = array();
		
		$id = $data["id"];
		
		if (isset($data["id"]) && !empty($data["id"]) && $data["id"] != "" && $data["id"] != null )
		{
			if (!$DB->get_recordset_select("pluginboletas_boletas", "id = ?", array($id)))
			{
				$errors["id"] = "El ID ingresado ya existe";
			}
		}
		else
		{
			$errors["id"] = "Este campo es requerido";
		}
		
		return $errors;
	}
}



