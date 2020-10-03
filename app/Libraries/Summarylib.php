<?php
namespace App\Libraries;

//use App\Models\Summary;
//TODO hay que implementar esta libraria

class Summarylib
{
    public function start($message, $chat_id)
    {

        $emoji = "\xF0\x9F\x91\x8C \xF0\x9F\x91\x8C";
        $reply = $message['reply_to_message'];

        if ($reply['text'] != "")
        {
            $this->Summarymodel = new Summary();

            $data = [
                'msg' => $reply['text'],
                'msg_group' => $chat_id,
                'msg_from' => $message['from']['first_name']
            ];

            $this->Summarymodel->insert($data);
            //$this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $emoji));
            return $emoji;
        }

        else
        {
            //$this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Cita el mensaje que quieres guardar y escribe Stela guarda ;)'));
            return 'Cita el mensaje que quieres guardar y escribe Stela guarda ;)';
        }
    }
}