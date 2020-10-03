<?php
namespace App\Libraries;

class Commandlist
{
    public function start()
    {
        return "<b>Listado de comandos:</b>".PHP_EOL.PHP_EOL
            ."stela tiempo ciudad".PHP_EOL
            ."stela guarda (respondiendo a un mensaje)";
    }
}