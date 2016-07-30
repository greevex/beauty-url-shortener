<?php
/**
 * @author greevex
 * @date   : 7/29/16 11:49 AM
 */

namespace mpcmf\apps\defaultApp\libraries\shurl;

use mpcmf\system\pattern\singleton;
use Symfony\Component\Console\Helper\DebugFormatterHelper;

class shurlLib
{
    use singleton;

    const DEBUG = false;

    public function __construct()
    {

    }

    public function generate($url)
    {
        $prm = substr(sha1($url, true), 0, 8);



        return trim(base64_encode($prm), '=');
    }

    public function hash($url, $outputHashLen = 12)
    {
        $h0 = sprintf('%032s', decbin(0x67452301));
        $h1 = sprintf('%032s', decbin(0xEFCDAB89));
        $h2 = sprintf('%032s', decbin(0x98BADCFE));

        $k0 = sprintf('%032s', decbin(0x5A827999));
        $k1 = sprintf('%032s', decbin(0x6ED9EBA1));
        $k2 = sprintf('%032s', decbin(0x8F1BBCDC));
        $k3 = sprintf('%032s', decbin(0xCA62C1D6));

        $data = $this->str2bin($url) . '1';

        $baseLen = 448;

        $binLen = strlen($data);
        while($binLen % 512 !== $baseLen) {
            $binLen++;
            $data .= '0';
        }
        $binLenBin = str_pad(decbin($binLen), 64, '0', STR_PAD_LEFT);
        $data .= $binLenBin;


        $c0 = $h0;
        $c1 = $h1;
        $c2 = $h2;
        $tmp = null;

        foreach(str_split($data, 512) as $chunk) {
            $subChunks = str_split($chunk, 32);

            for($i = 16; $i < 80; $i++) {
                $subChunks[$i] = $this->binXor($this->binXor($this->binXor($subChunks[$i - 3], $subChunks[$i - 4]), $subChunks[$i - 6]), $subChunks[$i - 8]);
            }

            foreach($subChunks as $i => $subC) {
                if ($i <= 19) {
                    $f = $this->binOr($this->binAnd($c1, $c2), $this->binAnd($this->binNot($c1), $c0));
                    $k = $k0;
                } elseif ($i <= 39) {
                    $f = $this->binXor($this->binXor($c1, $c2), $c0);
                    $k = $k1;
                } elseif ($i <= 59) {
                    $f = $this->binOr($this->binOr($this->binAnd($c1, $c2), $this->binAnd($c1, $c0)), $this->binAnd($c2, $c0));
                    $k = $k2;
                } else {
                    $f = $this->binXor($this->binXor($c1, $c2), $c0);
                    $k = $k3;
                }

                $tmpInt = bindec($this->binLeftRotate($c0, 5)) + bindec($f) + bindec($c2) + bindec($k) + bindec($subC);
                $tmp = substr(str_pad(decbin($tmpInt), 32, '0', STR_PAD_LEFT), -32);

                $c2 = $this->binLeftRotate($c1, 30);
                $c1 = $c0;
                $c0 = $tmp;
            }

            self::DEBUG && var_dump('=== 3 ===', $c0, $c1, $c2);

            $h0 = $this->binXor($h0, $c0);
            $h1 = $this->binXor($h1, $c1);
            $h2 = $this->binXor($h2, $c2);
        }

        $hashStr = hex2bin(sprintf('%08s', dechex(bindec($h0))) . sprintf('%08s', dechex(bindec($h1))) . sprintf('%08s', dechex(bindec($h2))));
//        return bin2hex($hashStr);

        $result = '';
        for($i = 0; $i < $outputHashLen; $i++) {
            $result .= self::$dict[ord($hashStr[$i]) % 64];
        }

        return $result;
    }


    public function hash2($url, $outputHashLen = 12, $options = [
        'h0' => 3612248634,
        'h1' => 2844536214,
        'h2' => 1697334478,
        'k0' => 288723168,
        'k1' => 1514888828,
        'k2' => 4047859396,
        'k3' => 1087655364,
    ])
    {
        $h0 = $options['h0'];
        $h1 = $options['h1'];
        $h2 = $options['h2'];

        $k0 = $options['k0'];
        $k1 = $options['k1'];
        $k2 = $options['k2'];
        $k3 = $options['k3'];

        $data = $this->str2bin($url) . '1';

        $baseLen = 448;

        $binLen = strlen($data);
        while($binLen % 512 !== $baseLen) {
            $binLen++;
            $data .= '0';
        }
        $binLenBin = str_pad(decbin($binLen), 64, '0', STR_PAD_LEFT);
        $data .= $binLenBin;
        $realLen = strlen($data);

        $c0 = $h0;
        $c1 = $h1;
        $c2 = $h2;

        for($dataOffset = 0; $dataOffset < $realLen; $dataOffset += 512) {
            $chunk = substr($data, $dataOffset, 512);

            $subChunks = [];
            for($chunkOffset = 0; $chunkOffset < 512; $chunkOffset += 32) {
                $subChunks[] = bindec(substr($chunk, $chunkOffset, 32));
            }

            for($i = 16; $i < 80; $i++) {
                $subChunks[$i] = $this->int32Overhead($subChunks[$i - 3] ^ $subChunks[$i - 8] ^ $subChunks[$i - 14] ^ $subChunks[$i - 16]);
            }

            foreach($subChunks as $i => $subC) {
                if ($i <= 19) {
                    $f = ($c1 & $c2) | ((~$c1) & $c0);
                    $k = $k0;
                } elseif ($i <= 39) {
                    $f = $c1 ^ $c2 ^ $c0;
                    $k = $k1;
                } elseif ($i <= 59) {
                    $f = ($c1 & $c2) | ($c1 & $c0) | ($c2 & $c0);
                    $k = $k2;
                } else {
                    $f = $c1 ^ $c2 ^ $c0;
                    $k = $k3;
                }

                $tmp = $this->intLeftRotate($c0, 5) + $f + $c2 + $k + $subC;

                $c2 = $this->int32Overhead($this->intLeftRotate($c1, 30));
                $c1 = $c0;
                $c0 = $tmp;
            }

            self::DEBUG && var_dump('=== 3 ===', $c0, $c1, $c2);

            $h0 = $this->int32Overhead($h0 ^ $c0);
            $h1 = $this->int32Overhead($h1 ^ $c1);
            $h2 ^= $c2;
        }

        $hashStr = hex2bin(sprintf('%08s', dechex($h0)) . sprintf('%08s', dechex($h1)) . sprintf('%08s', dechex($h2)));

        $result = '';
        for($i = 0; $i < $outputHashLen; $i++) {
            $result .= self::$dict[ord($hashStr[$i]) % 64];
        }

        return $result;
    }

    public function sha1($url, $outputHashLen = 12)
    {
        $hashStr = sha1($url, true);

        $result = '';
        for($i = 0; $i < $outputHashLen; $i++) {
            $result .= self::$dict[ord($hashStr[$i]) % 64];
        }

        return $result;
    }

    public function md5($url, $outputHashLen = 12)
    {
        $hashStr = md5($url, true);

        $result = '';
        for($i = 0; $i < $outputHashLen; $i++) {
            $result .= self::$dict[ord($hashStr[$i]) % 64];
        }

        return $result;
    }

    private function int32Overhead($int)
    {
        return bindec(substr(str_pad(decbin($int), 32, '0', STR_PAD_LEFT), -32));
    }

    private function binXor($str1, $str2)
    {
        $len = strlen($str1);
        for($i = 0; $i < $len; $i++) {
            $str1[$i] = (string)((int)($str1[$i] xor $str2[$i]));
        }

        return $str1;
    }

    private function binAnd($str1, $str2)
    {
        $len = strlen($str1);
        for($i = 0; $i < $len; $i++) {
            $str1[$i] = (string)((int)($str1[$i] && $str2[$i]));
        }

        return $str1;
    }

    private function binOr($str1, $str2)
    {
        $len = strlen($str1);
        for($i = 0; $i < $len; $i++) {
            $str1[$i] = (string)((int)($str1[$i] || $str2[$i]));
        }

        return $str1;
    }

    private function binNot($str1)
    {
        $len = strlen($str1);
        for($i = 0; $i < $len; $i++) {
            $str1[$i] = (string)((int)!$str1[$i]);
        }

        return $str1;
    }

    private function binLeftRotate($str, $count)
    {
        return substr($str, $count) . substr($str, 0, $count);
    }

    private function intLeftRotate($int, $count)
    {
        $str = decbin($int);

        return bindec(substr($str, $count) . substr($str, 0, $count));
    }

    private function str2bin($mystring) {
        $mybitseq = '';
        $end = strlen($mystring);
        /** @noinspection ForeachInvariantsInspection */
        for($i = 0 ; $i < $end; $i++){
            $mybyte = decbin(ord($mystring[$i])); // convert char to bit string
            $mybitseq .= substr('00000000', 0, 8 - strlen($mybyte)) . $mybyte; // 8 bit packed
        }
        return $mybitseq;
    }

    private static $dict = array (
        0 => 'a',
        1 => 'b',
        2 => 'c',
        3 => 'd',
        4 => 'e',
        5 => 'f',
        6 => 'g',
        7 => 'h',
        8 => 'i',
        9 => 'j',
        10 => 'k',
        11 => 'l',
        12 => 'm',
        13 => 'n',
        14 => 'o',
        15 => 'p',
        16 => 'q',
        17 => 'r',
        18 => 's',
        19 => 't',
        20 => 'u',
        21 => 'v',
        22 => 'w',
        23 => 'x',
        24 => 'y',
        25 => 'z',
        26 => 'A',
        27 => 'B',
        28 => 'C',
        29 => 'D',
        30 => 'E',
        31 => 'F',
        32 => 'G',
        33 => 'H',
        34 => 'I',
        35 => 'J',
        36 => 'K',
        37 => 'L',
        38 => 'M',
        39 => 'N',
        40 => 'O',
        41 => 'P',
        42 => 'Q',
        43 => 'R',
        44 => 'S',
        45 => 'T',
        46 => 'U',
        47 => 'V',
        48 => 'W',
        49 => 'X',
        50 => 'Y',
        51 => 'Z',
        52 => '0',
        53 => '1',
        54 => '2',
        55 => '3',
        56 => '4',
        57 => '5',
        58 => '6',
        59 => '7',
        60 => '8',
        61 => '9',
        62 => '_',
        63 => '-',
    );
}