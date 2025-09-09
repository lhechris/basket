<?php
include_once("entrainement.php");
include_once("presences.php");
include_once("selections.php");

echo ("initialisation Entrainements\n");
initEntrainements("2025-09-01","2025-09-04",44);


echo("Initialisation table presences\n");
initPresences();
echo("Initialisation table selections\n");
initSelections();