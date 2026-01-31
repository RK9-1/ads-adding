<?php
session_start();

// ========= CONFIG =========
$JSON_URL = "https://sports-rk.vercel.app/channels.json";
$AD_M3U8  = "https://m3uplaylist-plum.vercel.app/output.m3u8"; 
// ==========================

$id = $_GET['id'] ?? die("Missing channel id");

// Session Check: Kya is user ne abhi Ad dekha?
// Hum 20 seconds ka gap rakhenge taaki Ad baar-baar na chale
if (!isset($_SESSION['last_ad_'.$id]) || (time() - $_SESSION['last_ad_'.$id] > 20)) {
    
    // Pehli baar: Ad par bhej do
    $_SESSION['last_ad_'.$id] = time();
    header("Location: $AD_M3U8");
    exit;

} else {

    // Dusri baar (ya Ad ke turant baad): Real Stream par bhej do
    $json = @file_get_contents($JSON_URL);
    $data = json_decode($json, true);

    if (isset($data[$id])) {
        $streamUrl = $data[$id];
        header("Location: $streamUrl");
        exit;
    } else {
        http_response_code(404);
        exit("Channel not found");
    }
}
?>
