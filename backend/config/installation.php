<?php
include_once("../api/users.php");
include_once("../api/matchs.php");
include_once("../api/entrainements.php");
include_once("../api/selections.php");
include_once("../api/presences.php");
include_once("../api/disponibilites.php");

/*initUsers();
upgradeUsersFromfiles();

initMatchs();
upgradeMatchsFromfiles();

initEntrainements();
upgradeEntrainementsFromfiles();

initDisponibilites();
initPresences();
initSelections();*/

upgradeDisponibilitesFromFile();
upgradePresencesFromFile();
upgradeSelectionsFromFile();


?>