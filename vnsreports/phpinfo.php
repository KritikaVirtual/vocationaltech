<?php

echo $_SERVER['SERVER_NAME'];
echo "<br>";

echo isset($_GET["buildkey"]) ? '"' . $_GET["buildkey"] . '"' : "";
echo "<br>";
echo "<br>";

phpinfo();

?>