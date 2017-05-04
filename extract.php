<?php
if (count($argv) < 3) {
    die("Please pass the HAR file and the directory to continue");
}
$file = $argv[1];
$out = $argv[2];

$json = json_decode(file_get_contents($file));

foreach ($json->log->entries as $file) {
    $ext = "";
    foreach ($file->response->headers as $response) {
        if ($response->name == "Content-Type") {
            $ext = $response->value;
            break;
        }
    }

    $mapping = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'video/mp4' => 'mp4',
    ];

    if (!isset($mapping[$ext])) {
        echo "Cant file mapping for $ext\n";
        continue;
    }

    $filename = md5($file->request->url) . "." . $mapping[$ext];

    if (!file_exists($out . $filename)) {
        file_put_contents($out . $filename, base64_decode($file->response->content->text));
    }
}
