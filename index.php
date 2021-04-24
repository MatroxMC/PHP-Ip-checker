<?php

$file_patch = "ips.txt";
$link = "http://ip-api.com/php/{ip_custom}?fields=status,message,continent,continentCode,country,countryCode,region,regionName,city,zip,lat,lon,timezone,offset,currency,isp,org,as,asname,reverse,mobile,proxy,hosting,query";
$skip_ip = ["0.0.0.0", "127.0.0.1"];

ipChecker::startCheck($skip_ip, $file_patch, $link);

class ipChecker
{
    public static function startCheck($skip_ip, $link)
    {
        $checked = [];
        foreach (explode("\n", file_get_contents($file_patch)) as $id => $ip)
        {
            if (!in_array($ip, $skip_ip) and !in_array($ip, $checked))
            {
                $ddd = @file_get_contents(str_replace("{ip_custom}", $ip, $link));

                if (($data = $ddd) == false) {
                    echo PHP_EOL. "\e[31mHTTP request failed wait 40s.\e[39m". PHP_EOL;
                    sleep(40);
                } else {
                    $ip_info = unserialize($data);
                    $format = self::formatData($ip_info, false);
                    FileManager::saveIp("output.txt", $format);
                    if ($ip_info["hosting"] or $ip_info["proxy"]) FileManager::saveIp("hosting-proxy.txt", $format);
                    $checked[] = $ip;

                    echo "----------------". self::formatData($ip_info, true) ."----------------";
                }

            }
        }
    }

    public static function formatData($datas, $color = false)
    {
        if ($color)
        {
            $text = "\n";
            foreach ($datas as $name => $data)
            {
                $data = (is_bool($data)) ? ($data == true) ? "\e[31mtrue\e[39m" : "\e[32mfalse\e[39m" : "\e[37m".$data."\e[39m";
                $text .= "\e[36m$name\e[39m : $data\e[39m\n";
            }
            return $text;
        }else{
            $text = "\n";
            foreach ($datas as $name => $data)
            {
                $data = (is_bool($data)) ? ($data == true) ? "true" : "false" : $data;
                $text .= "$name : $data\n";
            }
            return $text;
        }

    }
}

class FileManager
{
    public static function saveIp($file, $string)
    {
        $current = file_get_contents($file);
        $current .= "---------------\n".$string."\n---------------\n";
        file_put_contents($file, $current);
    }
}


