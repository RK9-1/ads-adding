<?php
// ========= CONFIG =========
$JSON_URL = "https://sports-rk.vercel.app/channels.json";
$AD_M3U8  = "https://m3uplaylist-plum.vercel.app/output.m3u8"; // 10–15 sec ad
// ==========================

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit("Missing channel id");
}

$id = $_GET['id'];

// Fetch channels.json
$json = @file_get_contents($JSON_URL);
if ($json === false) {
    http_response_code(500);
    exit("channels.json not reachable");
}

$data = json_decode($json, true);
if (!isset($data[$id])) {
    http_response_code(404);
    exit("Channel not found");
}

$streamUrl = $data[$id];

// Output M3U8 that plays AD first, then stream
header("Content-Type: application/vnd.apple.mpegurl");
header("Cache-Control: no-cache");

echo "#EXTM3U\n";
echo "#EXT-X-VERSION:3\n";
echo "#EXT-X-INDEPENDENT-SEGMENTS\n\n";

/* ---- AD FIRST ---- */
echo "#EXT-X-STREAM-INF:BANDWIDTH=800000\n";
echo $AD_M3U8 . "\n\n";

/* ---- REAL STREAM ---- */
echo "#EXT-X-STREAM-INF:BANDWIDTH=5000000\n";
echo $streamUrl . "\n";
