<?php

# X^31 + X^3 + 1
# BINx31 - 2 147 483 647‬
function produceSequence(&$numbers, &$register, &$str, &$text, &$bits)
{
    $mask_obr = 2147483647; //31 bit
    $str = '';
    $bits = array();
    $text = array();
    foreach ($numbers as $b) {
        $bit_key = '';
        for ($i = 0; $i < 8; $i++) {
            $bin = decbin($register);
            $bin = str_pad($bin, 31, 0, STR_PAD_LEFT);
            $new_bit = intval($bin[0]) ^ intval($bin[28]);
            $bit_key .= $bin[0];
            $register = ($register << 1) | $new_bit;
            $register = $register & $mask_obr;
        }
        $bits[] = $bit_key;
        $clet = $b ^ bindec($bit_key);
        $str .= pack('C*', $clet);
        $text[] = $clet;
    }
}


function Cipher($filename, $key)
{
    $start_time = microtime(true);
    $register = $key;
    $enc_file = fopen($filename, 'rb');
    $chunk = fread($enc_file, filesize($filename));
    fclose($enc_file);
    $numbers = unpack('C*', $chunk);
    // echo "<pre>";
    // print_r($numbers);
    // echo "</pre>";

    echo "Биты исходного текста(первых 10 байт или, если менее, все байты файла):<br>";
    $i = 1;
    while (($i < 11) and ($i <= count($numbers))) {
        $bin = decbin($numbers[$i]);
        $bin = str_pad($bin, 8, 0, STR_PAD_LEFT);
        echo $bin;
        $i++;
    }

    echo "<br><br>";

    produceSequence($numbers, $register, $str, $text, $bits);

    $end_time = microtime(true);

    echo "Биты ключа:<br>";
    $stringBits = "";
    $i = 0;
    while (($i < 100) and ($i < count($bits))) {
        $stringBits .= $bits[$i];
        $i++;
    }
    //сделать вывод отчёт в таблице

    echo "<br><br>";

    echo "Биты зашифрованного текста:<br>";
    $i = 0;
    while (($i < 10) and ($i < count($text))) {
        $bin = decbin($text[$i]);
        $bin = str_pad($bin, 8, 0, STR_PAD_LEFT);
        echo $bin;
        $i++;
    }
    echo "<br><br>";


    $file_out = fopen("files/" . $filename . '.cph', 'wb');
    fwrite($file_out, $str);
    fclose($file_out);

    return $end_time - $start_time;
}

function DeCipher($filename, $key)
{
    $start_time = microtime(true);
    $register = $key;
    $mask_obr = 67108863;

    $enc_file = fopen($filename, 'rb');
    $chunk = fread($enc_file, filesize($filename));
    fclose($enc_file);
    $numbers = unpack('C*', $chunk);

    echo "Биты зашифрованного текста(первых 10 байт или, если менее, все байты файла):<br>";
    $i = 1;
    while (($i < 11) and ($i <= count($numbers))) {
        $bin = decbin($numbers[$i]);
        $bin = str_pad($bin, 8, 0, STR_PAD_LEFT);
        echo $bin;
        $i++;
    }
    echo "<br><br>";

    produceSequence($numbers, $register, $str, $text, $bits);
    $end_time = microtime(true);

    echo "Биты ключа:<br>";
    $i = 0;
    while (($i < 10) and ($i < count($bits))) {
        echo $bits[$i];
        $i++;
    }
    echo "<br><br>";

    echo "Биты расшифрованного текста:<br>";
    $i = 0;
    while (($i < 10) and ($i < count($text))) {
        $bin = decbin($text[$i]);
        $bin = str_pad($bin, 8, 0, STR_PAD_LEFT);
        echo $bin;
        $i++;
    }
    echo "<br><br>";

    $end_file = time() . substr($filename, 0, strlen($filename) - 4);
    $file_out = fopen("files/" . $end_file, 'wb');
    fwrite($file_out, $str);
    fclose($file_out);
    return $end_time - $start_time;
}
