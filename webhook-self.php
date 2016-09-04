<?php

$configs = require 'webhook-config.php';

$headers = getallheaders();

// 認証処理
// @see http://isometriks.com/verify-github-webhooks-with-php
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

$cmd = "git pull";
$output = '';
exec($cmd, $output);
var_dump($output);
