<?php
# TODO: cron, logディレクトリの作成、デイリー予定
# cronでは作業ディレクトリがホームディレクトリになるから、作業ディレクトリをcronディレクトリに変更
# chdir(__DIR__  . "/..");

$output = [];
$result = exec("php console del-pass-toks", $output);

// エラーが起きた時間を把握するため、エラーの場合だけerror_log.txtに記録
$messageIndex = 2;

if ($output[$messageIndex] === 'Error occurred.') {
    // 不要な1行目を削除
    array_shift($output);

    date_default_timezone_set('Asia/Tokyo');
    echo date("Y-m-d H:i:s" . "\n");

    if ($result) print_r($output);
}