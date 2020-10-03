<?php
namespace App\Libraries;

class Maincommands
{
    public function start($data, $params)
    {
        switch ($params)
        {
            case 'help':
                return $this->help();
                break;

            case 'summary':
                return $this->summary($data);
                break;

            case 'data':
                return $this->data();
                break;

            case 'stela':
                return $this->stela();
                break;
        }
    }

    private function help()
    {
        $helptext = "<b>¿Necesitas ayuda?</b>".PHP_EOL.PHP_EOL
            ."Para poder ayudarte, escribe en el chat <i>-Stela comando parametro- (sin guiones)</i>. Por ejemplo:"
            .PHP_EOL."<code>Stela tiempo Madrid</code>"
            .PHP_EOL.PHP_EOL."Para ver un listado de los comandos disponibles, escribe:"
            .PHP_EOL."<code>Stela comandos</code>";
        return $helptext;
    }

    private function summary($data)
    {
        //TODO hay que eliminar cada 10 mensajes

        $summarytext = "<b>Últimos mensajes guardados:</b>".PHP_EOL.PHP_EOL;
        $count = 1;

        foreach ($data as $msg)
        {
            $summarytext = $summarytext.$msg['msg_from'].': '.$msg['msg'].PHP_EOL.PHP_EOL;
            $count=$count+1;
        }

        return $summarytext;
    }

    private function data()
    {
        $datatext = "<b>Nuestros datos</b>".PHP_EOL.PHP_EOL
            ."Nuestra web: <a href='https://quedadasestelaresmadrid.com'><i>quedadasestelaresmadrid.com</i></a>"
            .PHP_EOL."Nuestro Twitter: <a href='https://twitter.com/estelaresmadrid'><i>EstelaresMadrid</i></a>";
        return $datatext;
    }

    private function stela()
    {
        $text = "stela";
        return $text;
    }
}