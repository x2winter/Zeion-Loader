
<?php
declare(strict_types=1);

header('Cache-Control: no-store');

$host = $_SERVER['HTTP_HOST'] ?? '';
$https = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
);

$scheme = $https ? 'https' : 'http';
$path = strtok($_SERVER['REQUEST_URI'] ?? '/load.php', '?');

if (!preg_match('/^[a-zA-Z0-9.-]+(?::[0-9]{1,5})?$/', $host)) {
    http_response_code(400);
    exit('Invalid host');
}

if ($path !== '/load.php' && $path !== '/api/load.php') {
    $path = '/load.php';
}

$uid = $_GET['uid'] ?? '';

if ($uid === '') {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    $makePart = static function (int $length) use ($chars): string {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $result;
    };

    $uid = 'ZEION-' . $makePart(5) . '&' . $makePart(4);

    $newUrl = $scheme . '://' . $host . $path
        . '?uid=' . rawurlencode($uid);

    header('Location: ' . $newUrl, true, 302);
    exit;
}

if (
    !is_string($uid) ||
    !preg_match('/^ZEION-[A-Z0-9]{5}&[A-Z0-9]{4}$/', $uid)
) {
    http_response_code(400);
    exit('Invalid UID');
}

$currentUrl = $scheme . '://' . $host . $path
    . '?uid=' . rawurlencode($uid);

$githubUrl =
    'https://raw.githubusercontent.com/x2winter/Zeion-Hub-Roblox/refs/heads/main/Loader.luau';

$ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
$isRoblox = str_contains($ua, 'roblox');

if ($isRoblox) {
    header('Content-Type: text/plain; charset=utf-8');

    echo <<<LUA
-- Zeion_Security_Service

local url = "{$githubUrl}"

local ok, data = pcall(function()
    return game:HttpGet(url, true)
end)

if not ok or type(data) ~= "string" or #data < 20 then
    warn("Zeion: Load failed")
    return
end

local fn = loadstring(data)

if type(fn) ~= "function" then
    warn("Zeion: Invalid script")
    return
end

local success, err = pcall(fn)

if not success then
    warn("Zeion: Execution failed")
end
LUA;
    exit;
}

header('Content-Type: text/html; charset=utf-8');

$urlJson = json_encode(
    $currentUrl,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
);

?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loadstring</title>
<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
body {
    min-height: 100vh;
    background: #191b30;
    color: white;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
}
.container {
    width: 82%;
    max-width: 350px;
}
.title {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    margin-bottom: 14px;
}
.title .icon { font-size: 19px; }
.title h1 {
    font-size: 21px;
    font-weight: 700;
}
.code-box {
    position: relative;
    width: 100%;
    height: 100px;
    background: #0d101f;
    border: 1px solid #22263c;
    border-radius: 13px;
    padding: 17px 13px;
    overflow: hidden;
}
.code-scroll {
    width: 100%;
    height: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: none;
}
.code-scroll::-webkit-scrollbar { display: none; }
pre {
    width: max-content;
    white-space: pre;
    font-family: Consolas, Monaco, monospace;
    font-size: 11px;
    line-height: 1.75;
    color: #d7d9e3;
}
.function { color: #55a5dc; }
.string { color: #c99a86; }
.copy-btn {
    position: absolute;
    top: 7px;
    right: 7px;
    padding: 6px 10px;
    border: none;
    border-radius: 9px;
    background: #282d52;
    color: #eee;
    font-size: 11px;
    cursor: pointer;
    z-index: 10;
}
.copy-btn:active { transform: scale(.95); }
.info {
    text-align: center;
    margin-top: 11px;
    color: #dedee5;
    font-size: 11px;
    line-height: 1.5;
}
.info-url { color: #dedee5; }
@media(max-width:600px) {
    .container {
        width: 80%;
        max-width: 340px;
    }
    .title h1 { font-size: 20px; }
    .title .icon { font-size: 18px; }
    .code-box { height: 98px; }
    pre { font-size: 11px; }
    .info { font-size: 11px; }
}
</style>
</head>
<body>
<div class="container">
    <div class="title">
        <span class="icon">📜</span>
        <h1>Loadstring</h1>
    </div>

    <div class="code-box">
        <button class="copy-btn" id="copyButton" onclick="copyCode()">
            Copy
        </button>
        <div class="code-scroll">
            <pre id="code"></pre>
        </div>
    </div>

    <div class="info">
        This code is protected by Zeion •<br>
        <span class="info-url">https://discord.gg/AcXbcV9hWx</span>
    </div>
</div>

<script>
const currentUrl = <?= $urlJson ?>;

const code = document.getElementById("code");
const line1 = document.createElement("span");
line1.textContent = "-- // Zeion_Security_Service";

const line2 = document.createElement("span");
line2.append(
    document.createTextNode("loadstring(game:HttpGet(")
);

const urlNode = document.createElement("span");
urlNode.className = "string";
urlNode.textContent = JSON.stringify(currentUrl);

line2.append(
    urlNode,
    document.createTextNode(", true))()")
);

code.append(line1, document.createTextNode("\n"), line2);

async function copyCode() {
    const text =
        '-- // Zeion_Security_Service\n' +
        'loadstring(game:HttpGet(' +
        JSON.stringify(currentUrl) +
        ', true))()';

    const button = document.getElementById("copyButton");

    try {
        await navigator.clipboard.writeText(text);
        button.innerText = "Copied!";
    } catch {
        const area = document.createElement("textarea");
        area.value = text;
        document.body.appendChild(area);
        area.select();

        const copied = document.execCommand("copy");
        area.remove();

        button.innerText = copied ? "Copied!" : "Copy failed";
    }

    setTimeout(() => {
        button.innerText = "Copy";
    }, 1200);
}
</script>
</body>
</html>
