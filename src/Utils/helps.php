<?php

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

if (! function_exists('dump_json')) {
    /**
     * local print pretty json
     * @param mixed $data
     * @return void
     */
    function dump_json(mixed $data): void
    {
        if (env('DUMP_JSON',true)) {
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $output = new ConsoleOutput;

            // 彩色样式定义
            $output->getFormatter()->setStyle('key', new OutputFormatterStyle('green'));
            $output->getFormatter()->setStyle('str', new OutputFormatterStyle('yellow'));
            $output->getFormatter()->setStyle('num', new OutputFormatterStyle('cyan'));
            $output->getFormatter()->setStyle('bool', new OutputFormatterStyle('magenta'));
            $output->getFormatter()->setStyle('null', new OutputFormatterStyle('red'));

            // 匹配 key: "value"
            $json = preg_replace('/"([^"]+)"\s*:/', '<key>"$1"</key>:', $json); // keys
            $json = preg_replace_callback('/: "(.*?)"/', function ($m) {
                return ': <str>"'.$m[1].'"</str>'; // string values
            }, $json);
            $json = preg_replace('/: (\d+\.\d+|\d+)/', ': <num>$1</num>', $json); // numbers
            $json = preg_replace('/: (true|false)/', ': <bool>$1</bool>', $json); // bools
            $json = preg_replace('/: null/', ': <null>null</null>', $json); // null

            $output->writeln($json);
        }
    }
}
