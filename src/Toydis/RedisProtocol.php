<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/16/16
 * Time: 4:53 PM
 */

namespace Toydis;


class RedisProtocol
{

    protected $_socket;

    public function parse($client)
    {
        $buff = $client->buff;
        // telnet 下不停的按回车
        if (str_replace("\r\n", '', $buff) === '') {
            throw new \Exception('RECEIVING');
        }
        $type = $buff[0];
        if ($type !== '*') {
            if (substr($buff, -2) !== "\r\n") {
                throw new \Exception('RECEIVING');
            } else {
                return $this->parseInline($buff);
            }
        } else {
            $this->_socket = $fd = fopen('php://temp', 'r+');
            fwrite($fd, $buff);
            rewind($fd);
            return $this->parseRESP();
        }
    }

    protected function parseInline($content)
    {
        $content = trim($content);
        return array_map(function ($str){
            if ($str[0] == '"' || $str[0] == "'") {
                if ($str[0] != substr($str, -1)) {
                    throw new \Exception('ERR Protocol error: unbalanced quotes in request');
                }
                return substr($str, 1, -1);
            }
            return $str;
        }, array_values(array_filter(explode(' ', $content))));
    }

    protected function parseRESP()
    {
        $line = fgets($this->_socket);
        $type = $line[0];
        $line = mb_substr($line, 1, -2, '8bit');
        switch ($type) {
            case '$': // Bulk
                $length = $line + 2; // +2 for "\r\n"
                $data = '';
                while ($length > 0) {
                    $block = fread($this->_socket, $length);
                    if ($length !== strlen($block)) {
                        throw new \Exception('RECEIVING');
                    }
                    $data .= $block;
                    $length -= mb_strlen($block, '8bit');
                }

                return mb_substr($data, 0, -2, '8bit');
            case '*': // Multi-bulk
                $count = (int) $line;
                $data = [];
                for ($i = 0; $i < $count; $i++) {
                    $data[] = $this->parseRESP();
                }

                return $data;
        }
    }

}
