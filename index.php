<?php

require_once 'src/Game.php';
require_once 'src/Player.php';
require_once 'src/Card.php';

use Heartbreak\Game;

$game = new Game();
$game->play();
