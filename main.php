<?php
include 'farm.php';

$farm = new Farm();

for ($i = 0; $i < 15; $i++){
    $farm->run();
}