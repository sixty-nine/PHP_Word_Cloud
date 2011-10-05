<?php

function autoloader($className) {
    include(__DIR__ . "/" . str_replace('\\', '/', $className) . ".php");
}
spl_autoload_register("autoloader");