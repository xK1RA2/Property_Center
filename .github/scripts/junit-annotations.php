<?php
$file = $argv[1] ?? 'junit.xml';
if (!is_file($file)) {
    echo "::error title=No test report::{$file} was not written, so PHPUnit crashed before finishing.\n";
    exit(0);
}

$xml = simplexml_load_file($file);
$shown = 0;

foreach ($xml->xpath('//testcase[failure or error]') as $case) {
    if ($shown++ >= 15) {
        break;
    }

    $problem = isset($case->failure) ? $case->failure : $case->error;
    $text = trim((string) $problem);
    $text = implode(' | ', array_slice(array_filter(array_map('trim', explode("\n", $text))), 0, 4));
    $text = str_replace(['%', "\r", "\n"], ['%25', '', '%0A'], mb_substr($text, 0, 700));
    $name = (string) $case['class'] . '::' . (string) $case['name'];

    echo "::error title=" . str_replace([',', ':'], ' ', $name) . "::{$text}\n";
}

$total = count($xml->xpath('//testcase[failure or error]'));
echo "::error title=Summary::{$total} failing test(s) in total\n";
