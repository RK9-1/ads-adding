<?php
// ==========================================
//  BEST IPTV AD-TO-STREAM SWITCHER (RENDER)
// ==========================================

session_start();

// --- CONFIGURATION ---
$JSON_URL = "https://sports-rk.vercel.app/channels.json";
$AD_M3U8  = "https://m3uplaylist-plum.vercel.app/output.m3u8";
$AD_DURATION = 15; // Ad kitne seconds baad expire maana jaye
// ---------------------

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    die("Error: Channel ID missing. Use ?id=your_channel_id");
}

// Browser/Player ko cache karne se roko (Buffering Fix)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Session Key Unique banao
$sessionKey = 'ad_seen_' . $id;

// --- LOGIC START ---

// Agar user ne abhi tak AD nahi dekha, ya time limit paar ho gayi hai
if (!isset($_SESSION[$sessionKey]) || (time() - $_SESSION[$sessionKey] > $AD_DURATION)) {
    
    // 1. Session set karo
    $_SESSION[$sessionKey] = time();
    
    // 2. Player ko AD par bhejo (307 Redirect = Temporary, taaki player wapas aaye)
    header("Location: " . $AD_M3U8, true, 307);
    exit;

} else {

    // --- REAL STREAM FETCHING ---
    
    // GitHub/Vercel se JSON uthao (User-Agent zaroori hai)
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $json = @file_get_contents($JSON_URL, false, $context);
    
    if ($json === false) {
        http_response_code(500);
        die("Error: Could not fetch channel list.");
    }

    $data = json_decode($json, true);

    if (isset($data[$id])) {
        // 3. Main Stream par bhejo
        $streamUrl = $data[$id];
        header("Location: " . $streamUrl, true, 307);
        exit;
    } else {
        http_response_code(404);
        die("Error: Channel ID not found in JSON.");
    }
}
?>
