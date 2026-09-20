export default function handler(req, res) {
  try {
    const ua = (req.headers["user-agent"] || "").toLowerCase();
    const isRoblox = ua.includes("roblox");

    const currentUrl =
      `${req.headers["x-forwarded-proto"] || "https"}://${req.headers.host}${req.url}`;

    const githubUrl =
      "https://raw.githubusercontent.com/x2winter/Zeion-Hub-Roblox/refs/heads/main/Loader.luau";

    if (isRoblox) {
      res.setHeader("Content-Type", "text/plain; charset=utf-8");
      res.setHeader("Cache-Control", "no-store");

      return res.status(200).send(`-- Zeion_Security_Service

local url = "${githubUrl}"

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
end`);
    }

    res.setHeader("Content-Type", "text/html; charset=utf-8");
    res.setHeader("Cache-Control", "no-store");

    const safeUrl = currentUrl
      .replace(/\\/g, "\\\\")
      .replace(/"/g, '\\"')
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");

    return res.status(200).send(`<!DOCTYPE html>
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

.title .icon {
    font-size: 19px;
}

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

.code-scroll::-webkit-scrollbar {
    display: none;
}

pre {
    width: max-content;
    white-space: pre;
    font-family: Consolas, Monaco, monospace;
    font-size: 11px;
    line-height: 1.75;
    color: #d7d9e3;
}

.function {
    color: #55a5dc;
}

.string {
    color: #c99a86;
}

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

.copy-btn:active {
    transform: scale(.95);
}

.info {
    text-align: center;
    margin-top: 11px;
    color: #dedee5;
    font-size: 11px;
    line-height: 1.5;
}

.info-url {
    color: #dedee5;
}

@media(max-width:600px) {
    .container {
        width: 80%;
        max-width: 340px;
    }

    .title h1 {
        font-size: 20px;
    }

    .title .icon {
        font-size: 18px;
    }

    .code-box {
        height: 98px;
    }

    pre {
        font-size: 11px;
    }

    .info {
        font-size: 11px;
    }
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

const currentUrl = "${safeUrl}";

const codeHTML =
\`<span>-- // Zeion_Security_Service</span>
<span class="function">loadstring</span>(game:<span class="function">HttpGet</span>(<span class="string">"\${currentUrl}"</span>))()\`;

document.getElementById("code").innerHTML = codeHTML;

function copyCode() {

    const text =
\`-- // Zeion_Security_Service
loadstring(game:HttpGet("\${currentUrl}", true))()\`;

    navigator.clipboard.writeText(text);

    const button =
        document.getElementById("copyButton");

    button.innerText = "Copied!";

    setTimeout(() => {
        button.innerText = "Copy";
    }, 1200);
}

</script>

</body>
</html>`);

  } catch (err) {
    console.error(err);
    return res.status(500).send("Internal Server Error");
  }
}
