<?php

function autoloader($className) {
    // A little bit hacky...
    include(__DIR__ . "/../" . str_replace('Dreamcraft/WordCloud/', '', str_replace('\\', '/', $className)) . ".php");
}
spl_autoload_register("autoloader");