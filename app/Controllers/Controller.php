<?php namespace App\Controllers;

//Telegram API
// curl --request GET --url 'https://api.telegram.org/APIKEY/setWebhook?url=https://URLWEB.COM/WEBHOOK
// webhook: https://api.telegram.org/botAPIKEY/setWebhook?url=https://URLWEB.COM/WEBHOOK

define('BOT_TOKEN', '0000000000:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
define('SERVER_HOOK', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaa');

class Controller extends BaseController
{
    public function index()
    {
        die();
    }

    // Hook for telegram
    public function hook($id_hook)
    {
        if ($id_hook == SERVER_HOOK)
        {
            $content = file_get_contents("php://input");
            $update = json_decode($content, true);

            if (!$update) { exit; }

            if (isset($update["message"]))
            {
                $this->processMessage($update["message"]);
            }

            else
            {
                exit;
            }

        }

    }

    //API methods

    private function apiRequestWebhook($method, $parameters)
    {
        if (!is_string($method))
        {
            error_log("Method name must be a string\n");
            return false;
        }

        if (!$parameters)
        {
            $parameters = array();
        }

        else if (!is_array($parameters))
        {
            error_log("Parameters must be an array\n");
            return false;
        }

        $parameters["method"] = $method;

        header("Content-Type: application/json");
        echo json_encode($parameters);
        return true;
    }

    private function exec_curl_request($handle)
    {
        $response = curl_exec($handle);

        if ($response === false)
        {
            $errno = curl_errno($handle);
            $error = curl_error($handle);
            error_log("Curl returned error $errno: $error\n");
            curl_close($handle);
            return false;
        }

        $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
        curl_close($handle);

        if ($http_code >= 500)
        {
            // do not wat to DDOS server if something goes wrong
            sleep(10);
            return false;
        }

        else if ($http_code != 200)
        {
            $response = json_decode($response, true);
            error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");

            if ($http_code == 401)
            {
                throw new Exception('Invalid access token provided');
            }

            return false;
        }

        else
        {
            $response = json_decode($response, true);

            if (isset($response['description']))
            {
                error_log("Request was successful: {$response['description']}\n");
            }

            $response = $response['result'];
        }

        return $response;
    }

    private function apiRequest($method, $parameters)
    {
        if (!is_string($method))
        {
            error_log("Method name must be a string\n");
            return false;
        }

        if (!$parameters)
        {
            $parameters = array();
        }

        else if (!is_array($parameters))
        {
            error_log("Parameters must be an array\n");
            return false;
        }

        foreach ($parameters as $key => &$val)
        {
            // encoding to JSON array parameters, for example reply_markup
            if (!is_numeric($val) && !is_string($val))
            {
                $val = json_encode($val);
            }
        }

        $url = API_URL.$method.'?'.http_build_query($parameters).'&parse_mode=html';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 10);

        return $this->exec_curl_request($handle);
    }

    private function apiRequestJson($method, $parameters)
    {
        if (!is_string($method))
        {
            error_log("Method name must be a string\n");
            return false;
        }

        if (!$parameters)
        {
            $parameters = array();
        }

        else if (!is_array($parameters))
        {
            error_log("Parameters must be an array\n");
            return false;
        }

        $parameters["method"] = $method;

        $handle = curl_init(API_URL);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 10);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
        curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

        return $this->exec_curl_request($handle);
    }

    //MSG Processor

    private function messageFilter($msg)
    {
        $msg = strtolower($msg);

        $msg = str_replace("á", "a", $msg);
        $msg = str_replace("é", "e", $msg);
        $msg = str_replace("í", "i", $msg);
        $msg = str_replace("ó", "o", $msg);
        $msg = str_replace("ú", "u", $msg);

        $msg = str_replace("Á", "a", $msg);
        $msg = str_replace("É", "e", $msg);
        $msg = str_replace("Í", "i", $msg);
        $msg = str_replace("Ó", "o", $msg);
        $msg = str_replace("Ú", "u", $msg);

        return $msg;
    }

    private function processMessage($message)
    {
        // process incoming message
        $message_id = $message['message_id'];
        $chat_id = $message['chat']['id'];

        if (isset($message['text']))
        {
            // incoming text message
            $text = $message['text'];
            $command = explode(" ", $text);

            //check chat id (you can add new chats in BaseController)
            if (in_array($chat_id, $this->AuthorizedChats))
            {
                // Main commands
                if (strpos($text, "/start") === 0)
                {
                    $this->apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Escribe "Stela ayuda" sin las comillas para aprender a usar el bot', 'reply_markup' => array(
                        'keyboard' => array(array('/Stela', '/Resumen', '/Datos', '/Ayuda')),
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true)));
                }

                else if (strpos($text, "/stop") === 0)
                {
                    exit;
                }

                else if (strpos($text, "/Ayuda") === 0)
                {
                    $help = $this->Maincommands->start($message, 'help');
                    $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $help));
                }

                else if (strpos($text, "/Resumen") === 0)
                {
                    $data = $this->Summarymodel->where('msg_group', $chat_id)->findAll();
                    $summary = $this->Maincommands->start($data, 'summary');

                    $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $summary));
                }

                else if (strpos($text, "/Datos") === 0)
                {
                    $data = $this->Maincommands->start($message, 'data');
                    $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $data));
                }

                else if (strpos($text, "/Stela") === 0)
                {
                    $stela = $this->Maincommands->start($message, 'stela');
                    $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $stela));
                }

                //Stela commands

                if ($this->messageFilter($command[0]) == "stela" || $this->messageFilter($command[0]) == "/stela")
                {
                    $this->stelaCommand($message, $command, $chat_id, $message_id);
                }
            }

            // Not authorized chats
            else
            {
                if ($this->messageFilter($command[0]) == "stela" || $this->messageFilter($command[0]) == "/stela")
                {
                    if ($this->messageFilter($command[1]) == "debug")
                    {
                        if ($this->messageFilter($command[2]) == "id")
                        {
                            $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $chat_id));
                        }
                    }

                    else
                    {
                        if ($this->Verbose == true)
                        {
                            $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Chat no autorizado"));
                        }
                    }
                }

                else
                {
                    if ($this->Verbose == true)
                    {
                        $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Chat no autorizado"));
                    }
                }
            }

        }

        /*
        else
        {
            $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Stela al aparato'));
        }
        */
    }

    private function stelaCommand($message, $command, $chat_id, $message_id)
    {
        $message_id = $message['message_id'];
        $chat_id = $message['chat']['id'];

        switch ($this->messageFilter($command[1]))
        {
            case "hello":
            case "hi":
            case "hola":
            case "ping":
                // Ping command
                $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Hola!'));
                break;

            case "start":
                // show buttons
                $this->apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Escribe "Stela ayuda" sin las comillas para aprender a usar el bot', 'reply_markup' => array(
                    'keyboard' => array(array('/Stela', '/Resumen', '/Datos', '/Ayuda')),
                    'one_time_keyboard' => true,
                    'resize_keyboard' => true)));
                break;

            case "ayuda":
            case "ayudame":
            case "help":
                $help = $this->Maincommands->start($message, 'help');
                $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $help));
                break;

            case "guapa":
            case "fea":
            case "guapo":
            case "feo":
                $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Tú <3'));
                break;

            case "resume":
            case "resumen":
                $data = $this->Summarymodel->where('msg_group', $chat_id)->findAll();
                $summary = $this->Maincommands->start($data, 'summary');

                $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $summary));                break;

            case "comando":
            case "comandos":
                $commands = $this->Commandlist->start();
                $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $commands));
                break;

            case "guardar":
            case "guarda":
                $emoji = "\xF0\x9F\x91\x8C \xF0\x9F\x91\x8C";
                $reply = $message['reply_to_message'];

                if ($reply['text'] != "")
                {
                    $data = [
                        'msg' => $reply['text'],
                        'msg_group' => $chat_id,
                        'msg_from' => $message['from']['first_name']
                    ];

                    $this->Summarymodel->insert($data);
                    $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $emoji));
                }

                else
                {
                    $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Cita el mensaje que quieres guardar y escribe Stela guarda ;)'));
                }
                break;

            case "tiempo":
                $estado = $this->Openweathermap->start($command[2]);
                if ($estado) // only for external apis
                {
                    $this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $estado));
                }
                break;

            default:
                //todo llamar libreria nlp
                //$this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Listado de comandos'));
                break;
        }
    }


}
