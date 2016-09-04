<?php

$configs = require 'webhook-config.php';

$headers = getallheaders();

// pull requestイベント時のみ実行
if ('pull_request' !== $headers['x-github-event']) {
    die('Not Pull Request Event');
}

// 認証処理
// @see http://isometriks.com/verqify-github-webhooks-with-php
$hubSignature = $headers['x-hub-signature'];

// Split signature into algorithm and hash
list($algo, $hash) = explode('=', $hubSignature, 2);

// Get payload
$payload = file_get_contents('php://input');
 
// Calculate hash based on payload and the secret
$payloadHash = hash_hmac($algo, $payload, $configs['secret']);

// Check if hashes are equivalent
if ($hash !== $payloadHash) {
    // Kill the script or do something else here.
    die('Bad secret');
}

// Your code here.
$data = json_decode($payload);

if (empty($configs['repo'][$data->repository->name])) {
    die('Repositoy Not Found');
}

$config = $configs['repo'][$data->repository->name];

// pull requestがマージされた場合に実行する
// マージの判定は、action: closed かつ merged: true
// @see https://developer.github.com/v3/activity/events/types/#pullrequestevent
if ($data->action !== 'closed') {
    die('Not closed');
}

if (!$data->pull_request->merged) {
    die('Not merged');
}

// base: pull request先ブランチ
$to = $data->pull_request->base->ref;

// head: pull request元ブランチ
$from = $data->pull_request->head->ref;

// develop へのマージ
if ($to === 'develop') {
    $path = $config['dev'];
    $cmd = "cd $path; git pull;";
    $output = [];
    exec($cmd, $output);
    var_dump($output);

// master へのマージ(hotfixのみ)
} elseif ($to === 'master' && $from === 'hotfix') {
    $path = $config['prd'];
    $cmd = "cd $path; git pull;";
    $output = [];
    exec($cmd, $output);
    var_dump($output);

} else {
    die('Not target branch');
}
