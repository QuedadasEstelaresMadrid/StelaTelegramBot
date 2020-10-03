<?php
namespace App\Libraries;

//API OpenWeatherMap
define('WEATHER_API', 'http://api.openweathermap.org/data/2.5/weather?q=');
define('WEATHER_API_TOKEN', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

class Openweathermap
{
    public function start($params)
    {
        if (is_numeric($params))
        {
            //TODO se asume codigo postal
        }

        else if (is_string($params))
        {
            //TODO se asume que es una ciudad en un futuro molaria tener en la DB ciudades y pueblos, y no solo espania
            //TODO filtrar espacios y tildes
            return $this->get_city($params);
        }

        else
        {
            return $this->get_city($params);
        }
    }

    private function get_city($city)
    {
        $url = WEATHER_API.$city."&appid=".WEATHER_API_TOKEN."&units=metric&lang=es";

        // always use try catch for external apis
        try
        {
            $request = file_get_contents($url);
            $request = json_decode($request);

            if (isset($request->main->temp))
            {
                $text = "<b>El tiempo actual en ".$request->name."</b>".PHP_EOL.PHP_EOL;
                //TODO evidentemente hay que hacer esto bien
                return $text.$request->weather[0]->description
                    .'. Temp '.$request->main->temp .'º Hum '
                    .$request->main->humidity.'%'.PHP_EOL.'Temp max '
                    .$request->main->temp_max.'º min '.$request->main->temp_min.'º'
                    .PHP_EOL.'Viento '.$request->wind->speed.' Km/h dirección '.$request->wind->deg.'º'
                    .PHP_EOL.'Nubes '.$request->clouds->all.'% cubierto';
            }

            else
            {
                return "Openweathermap API error";
            }
        }

        catch (\Exception $e)
        {
            //TODO logear error
            return "Openweathermap API error";
        }
        catch (Throwable $e)
        {
            //TODO logear error
            return "Openweathermap API error";
        }
    }
}