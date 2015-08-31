<?php

require_once(dirname(dirname(dirname(__FILE__)))."/config.php");

$context = context_system::instance();

$urlprinters = new moodle_url("/local/pluginboletas/index.php");
// Page navigation and URL settings
$PAGE->set_url($urlprinters);
$PAGE->set_context($context);
$PAGE->set_pagelayout("standard");

$PAGE->set_title("HOLA MUNDO");
$PAGE->set_heading("Este es un heading. HOLA!!");
echo $OUTPUT->header();

echo "Aqui veran las boletas... algun dia"; //PROGRAMAR AQUI!!!!

echo $OUTPUT->footer();