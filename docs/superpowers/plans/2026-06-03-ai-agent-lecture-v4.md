# AI Agent Lecture Deck v4 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a revised Korean 2-hour AI Agent lecture package for WordPress website creators: an editable HTML slide deck first, then verified slide images embedded into a new blog-style Notion teaching page.

**Architecture:** Keep `deck-v3` untouched and create `deck-v4` as a standalone single-page HTML deck with local fonts, fixed 1920x1080 slides, URL slide navigation, and print CSS. Generate screenshots and a contact sheet as evidence, then use the verified images inside a separate Notion page that reads like a long-form lecture article with headings, prose, links, code, tables, and diagrams.

**Tech Stack:** HTML/CSS/vanilla JavaScript, local Paperlogy/Pretendard fonts, Chrome headless screenshots, Python HTML parser/PIL utilities, Notion API or Notion MCP/connector.

---

## File Structure

- Create: `outputs/ai-agent-lecture/deck-v4/index.html`
  - Single editable HTML slide deck.
  - Contains slide data, layout components, keyboard navigation, edit mode if copied from `deck-v3`, print CSS, and source notes in comments or hidden notes blocks.
- Create: `outputs/ai-agent-lecture/deck-v4/assets/fonts/Paperlogy-7Bold.woff2`
  - Copied from `outputs/ai-agent-lecture/deck-v3/assets/fonts/Paperlogy-7Bold.woff2`.
- Create: `outputs/ai-agent-lecture/deck-v4/assets/fonts/Paperlogy-9Black.woff2`
  - Copied from `outputs/ai-agent-lecture/deck-v3/assets/fonts/Paperlogy-9Black.woff2`.
- Create: `outputs/ai-agent-lecture/deck-v4/assets/fonts/PretendardVariable.woff2`
  - Copied from `outputs/ai-agent-lecture/deck-v3/assets/fonts/PretendardVariable.woff2`.
- Create: `outputs/ai-agent-lecture/deck-v4/slide-01.png` through `outputs/ai-agent-lecture/deck-v4/slide-46.png`
  - Browser-rendered slide screenshots used for QA and Notion embedding.
- Create: `outputs/ai-agent-lecture/deck-v4/contact-sheet.png`
  - Visual audit sheet for all slides.
- Create: `outputs/ai-agent-lecture/deck-v4/qa-evidence.json`
  - Machine-readable evidence of parsing, slide count, keyword coverage, screenshots, and Notion upload.
- Create: `outputs/ai-agent-lecture/notion/lecture-v4-payload.json`
  - Local record of the new Notion page payload and uploaded file references.
- Create: `outputs/ai-agent-lecture/notion/lecture-v4-page.json`
  - Local record of created Notion page ID, URL, title, and verification result.
- Modify: `docs/verification/2026-06-02-ai-agent-lecture-deck.qa-evidence.json`
  - Append a `deckV4` section after all deck-v4 verification succeeds.

## Source Inputs

- Existing deck reference: `outputs/ai-agent-lecture/deck-v3/index.html`
- Existing font files: `outputs/ai-agent-lecture/deck-v3/assets/fonts/`
- Handoff context: `docs/handoff/2026-06-02-ai-agent-lecture-handoff.md`
- Workflow contract: `docs/harness/workflows/lecture-to-html-pdf.md`
- User-edited Notion source page: `https://app.notion.com/p/374eb9be4dd3812a8dacfabfa266c084`
- Target Notion DB: Notes DB / data source known from prior work as `27bb1dd1-a1a3-4241-969b-2100db26d0ca`

## Slide Contract

Build 46 slides. Renumber every visible page number as `NN / 46`.

1. 좋은 머리에 손이 생겼다
2. 오늘의 지도: 두뇌에서 자동화까지
3. 우리가 알던 AI는 좋은 머리였다
4. 대답과 결과물은 다른 문제다
5. 마지막 1마일은 사람이 했다
6. 챗봇은 답을 주고 Agent는 일을 맡는다
7. 결과물이 채팅창 밖으로 나온다
8. AI의 손은 연결에서 나온다
9. 연결되면 업무가 이어진다
10. 이름보다 역할로 이해한다
11. 모델은 엔진이다
12. 도구는 실행 환경이다
13. 도구는 많지만 형태는 몇 가지다
14. CLI는 말로 컴퓨터를 조종하는 창이다
15. IDE는 개발자의 작업대다
16. 대화형 Agent 도구는 작업창을 감춘다
17. 오늘 설치할 도구: Codex 또는 Antigravity
18. 코딩은 컴퓨터에게 일을 시키는 언어다
19. 한 문장 요청은 빈칸이 너무 많다
20. Agent는 초엘리트 신입사원이다
21. 신입사원에게 필요한 세 가지
22. AGENTS.md는 회사 작업 매뉴얼이다
23. 좋은 AGENTS.md는 짧고 강하다
24. Skill은 특정 일을 위한 작업 레시피다
25. 검증된 Skill을 가져와도 된다
26. Superpowers는 작업 루프를 강제한다
27. Brainstorming: 요구사항을 같이 발견한다
28. Writing Plans: 실행 전 설계도를 만든다
29. Executing/Subagent: 계획을 작은 단위로 실행한다
30. Verification: 완료를 증거로 확인한다
31. Debugging: 실패 증거로 수정한다
32. 전체 루프: 계획, 실행, 검증, 수정
33. 외부 도구로 가는 출입문이 필요하다
34. MCP는 외부 서버에 손을 연결한다
35. WordPress MCP 자율 루프
36. GitHub는 저장소이자 멀티버스 작업 공간이다
37. 오늘의 실험: 네이버 블로그 RSS에서 WordPress로
38. 실습 1: Agent 도구 설치
39. 실습 2: Superpowers 설치와 첫 요청
40. 실습 3: 요구사항을 구체화한다
41. 실습 4: RSS 필드를 설계한다
42. 실습 5: Code Snippets용 PHP를 만든다
43. 실습 6: 관리자 화면과 카테고리 매핑
44. 실습 7: 검증 로그와 수정 루프
45. WordPress 제작자가 해볼 만한 자동화 아이디어
46. 경쟁력은 검증 가능한 위임 능력이다

## Required Keywords

The final HTML and Notion page must contain these exact strings:

- `네이버 블로그 RSS`
- `MCP`
- `AGENTS.md`
- `Skill`
- `GitHub`
- `Superpowers`
- `검증 가능한 위임`
- `Code Snippets`
- `Codex`
- `Antigravity`

## Design Rules

- Title font: Paperlogy.
- Body font: Pretendard.
- Main emphasis: Tailwind orange shades:
  - `#f97316`
  - `#ea580c`
  - `#ffedd5`
- Cobalt stays as grid, structural line, slide chrome, and diagram support.
- Avoid dumping storyboard prose directly onto slides.
- Each slide has one primary claim and one visual proof object.
- Avoid text overlap with page numbers, right-side decorations, and diagram labels.
- Keep cards at 8px border radius or less.
- No nested cards.
- No decorative gradient orbs.
- Do not use visible text to explain keyboard shortcuts or UI mechanics.

---

### Task 1: Verify External Links And Current Tool Names

**Files:**
- Create: `outputs/ai-agent-lecture/deck-v4/link-sources.json`

- [ ] **Step 1: Search official/current sources for install and reference links**

Run these searches in a browser or web search tool, preferring official or primary sources:

```text
OpenAI Codex official documentation
Google Antigravity official website
Anthropic Claude Skills official documentation GitHub
Superpowers Codex skills GitHub
Andrej Karpathy AGENTS.md GitHub
Vercel find skills GitHub
WordPress MCP Adapter plugin Automattic
@automattic/mcp-wordpress-remote npm
```

Expected: Each source has a current URL and a short note explaining why it is credible.

- [ ] **Step 2: Save the verified source list**

Create `outputs/ai-agent-lecture/deck-v4/link-sources.json` with this shape:

```json
{
  "verifiedAt": "2026-06-03",
  "sources": [
    {
      "label": "OpenAI Codex",
      "url": "https://help.openai.com/",
      "type": "official",
      "usedFor": "Codex installation/reference slide"
    },
    {
      "label": "Google Antigravity",
      "url": "https://antigravity.google/",
      "type": "official",
      "usedFor": "Antigravity installation/reference slide"
    }
  ]
}
```

Use the actual verified URLs discovered in Step 1. Do not keep the sample URLs if a better official URL exists.

- [ ] **Step 3: Verify JSON syntax**

Run:

```bash
python3 -m json.tool outputs/ai-agent-lecture/deck-v4/link-sources.json >/dev/null
```

Expected: exit code 0.

- [ ] **Step 4: Commit**

```bash
git add outputs/ai-agent-lecture/deck-v4/link-sources.json
git commit -m "docs: record ai agent lecture source links"
```

Expected: commit succeeds. If the repo intentionally does not track `outputs/`, skip this commit and record the reason in `outputs/ai-agent-lecture/deck-v4/qa-evidence.json` during Task 7.

### Task 2: Create Deck v4 Shell And Copy Fonts

**Files:**
- Create: `outputs/ai-agent-lecture/deck-v4/index.html`
- Create: `outputs/ai-agent-lecture/deck-v4/assets/fonts/Paperlogy-7Bold.woff2`
- Create: `outputs/ai-agent-lecture/deck-v4/assets/fonts/Paperlogy-9Black.woff2`
- Create: `outputs/ai-agent-lecture/deck-v4/assets/fonts/PretendardVariable.woff2`

- [ ] **Step 1: Create directories and copy fonts**

Run:

```bash
mkdir -p outputs/ai-agent-lecture/deck-v4/assets/fonts
cp outputs/ai-agent-lecture/deck-v3/assets/fonts/Paperlogy-7Bold.woff2 outputs/ai-agent-lecture/deck-v4/assets/fonts/Paperlogy-7Bold.woff2
cp outputs/ai-agent-lecture/deck-v3/assets/fonts/Paperlogy-9Black.woff2 outputs/ai-agent-lecture/deck-v4/assets/fonts/Paperlogy-9Black.woff2
cp outputs/ai-agent-lecture/deck-v3/assets/fonts/PretendardVariable.woff2 outputs/ai-agent-lecture/deck-v4/assets/fonts/PretendardVariable.woff2
```

Expected: all three font files exist under `deck-v4/assets/fonts/`.

- [ ] **Step 2: Create the HTML shell**

Create `outputs/ai-agent-lecture/deck-v4/index.html` with:

```html
<!doctype html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AI Agent lecture deck v4</title>
  <style>
    @font-face {
      font-family: "Paperlogy";
      src: url("./assets/fonts/Paperlogy-9Black.woff2") format("woff2");
      font-weight: 900;
    }
    @font-face {
      font-family: "Paperlogy";
      src: url("./assets/fonts/Paperlogy-7Bold.woff2") format("woff2");
      font-weight: 700;
    }
    @font-face {
      font-family: "Pretendard";
      src: url("./assets/fonts/PretendardVariable.woff2") format("woff2");
      font-weight: 100 900;
    }
    :root {
      --orange-500: #f97316;
      --orange-600: #ea580c;
      --orange-100: #ffedd5;
      --cobalt: #1d4ed8;
      --cobalt-soft: #dbeafe;
      --ink: #171717;
      --muted: #525252;
      --line: #d4d4d4;
      --paper: #fafafa;
      --white: #ffffff;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      background: #e5e5e5;
      color: var(--ink);
      font-family: "Pretendard", system-ui, sans-serif;
    }
    .viewport {
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 24px;
    }
    .deck {
      width: min(100vw - 48px, 1920px);
      aspect-ratio: 16 / 9;
      position: relative;
    }
    .slide {
      display: none;
      position: absolute;
      inset: 0;
      width: 1920px;
      height: 1080px;
      transform-origin: top left;
      background: var(--paper);
      overflow: hidden;
      padding: 86px 104px;
    }
    .slide.active { display: block; }
    .kicker {
      color: var(--orange-600);
      font-size: 28px;
      font-weight: 800;
      letter-spacing: 0;
      margin-bottom: 22px;
    }
    h1, h2, h3 {
      font-family: "Paperlogy", "Pretendard", system-ui, sans-serif;
      letter-spacing: 0;
      margin: 0;
    }
    h1 {
      font-size: 96px;
      line-height: 1.02;
      max-width: 1240px;
    }
    .claim {
      margin-top: 34px;
      font-size: 42px;
      line-height: 1.32;
      color: var(--muted);
      max-width: 1180px;
      font-weight: 650;
    }
    .accent { color: var(--orange-600); }
    .page {
      position: absolute;
      right: 76px;
      bottom: 54px;
      color: var(--muted);
      font-size: 24px;
      font-weight: 700;
    }
    .chrome-line {
      position: absolute;
      left: 0;
      right: 0;
      bottom: 116px;
      border-top: 2px solid var(--cobalt-soft);
    }
    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 36px;
      margin-top: 54px;
    }
    .panel {
      border: 2px solid var(--line);
      border-radius: 8px;
      background: var(--white);
      padding: 34px;
    }
    .panel h3 {
      font-size: 38px;
      margin-bottom: 18px;
    }
    .panel p, .panel li {
      font-size: 31px;
      line-height: 1.35;
      color: var(--muted);
      margin: 0;
    }
    .flow {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 22px;
      margin-top: 58px;
    }
    .step {
      min-height: 180px;
      border: 2px solid var(--cobalt-soft);
      border-radius: 8px;
      background: #fff;
      padding: 26px;
    }
    .step strong {
      display: block;
      color: var(--orange-600);
      font-size: 30px;
      margin-bottom: 12px;
    }
    .step span {
      display: block;
      font-size: 27px;
      line-height: 1.35;
      color: var(--muted);
    }
    @media print {
      body { background: white; }
      .viewport { display: block; padding: 0; }
      .deck { width: 1920px; }
      .slide {
        display: block;
        position: relative;
        page-break-after: always;
        transform: none !important;
      }
    }
  </style>
</head>
<body>
  <main class="viewport">
    <div class="deck" id="deck"></div>
  </main>
  <script>
    const slides = [];
    const deck = document.getElementById("deck");
    let current = Math.max(1, Math.min(Number(new URLSearchParams(location.search).get("slide")) || 1, slides.length || 1));

    function render() {
      deck.innerHTML = slides.map((slide, index) => `
        <section class="slide ${index + 1 === current ? "active" : ""}" data-slide="${index + 1}">
          ${slide.html}
          <div class="chrome-line"></div>
          <div class="page">${String(index + 1).padStart(2, "0")} / ${String(slides.length).padStart(2, "0")}</div>
        </section>
      `).join("");
      scaleDeck();
    }

    function scaleDeck() {
      const host = document.querySelector(".deck");
      const scale = host.clientWidth / 1920;
      document.querySelectorAll(".slide").forEach((slide) => {
        slide.style.transform = `scale(${scale})`;
      });
    }

    addEventListener("resize", scaleDeck);
    addEventListener("keydown", (event) => {
      if (event.key === "ArrowRight") current = Math.min(current + 1, slides.length);
      if (event.key === "ArrowLeft") current = Math.max(current - 1, 1);
      history.replaceState(null, "", `?slide=${current}`);
      render();
    });
    render();
  </script>
</body>
</html>
```

- [ ] **Step 3: Verify shell parses**

Run:

```bash
python3 -m html.parser outputs/ai-agent-lecture/deck-v4/index.html
```

Expected: exit code 0 with no parser errors.

- [ ] **Step 4: Commit**

```bash
git add outputs/ai-agent-lecture/deck-v4/index.html outputs/ai-agent-lecture/deck-v4/assets/fonts
git commit -m "feat: scaffold ai agent lecture deck v4"
```

Expected: commit succeeds. If `outputs/` is intentionally untracked, skip commit and record the reason in QA evidence.

### Task 3: Add All 46 Slides With Revised Storyline

**Files:**
- Modify: `outputs/ai-agent-lecture/deck-v4/index.html`

- [ ] **Step 1: Replace `const slides = [];` with slide data**

Use this structure for all slides:

```js
const slides = [
  {
    title: "좋은 머리에 손이 생겼다",
    html: `
      <div class="kicker">Opening</div>
      <h1>좋은 머리에<br><span class="accent">손이 생겼다</span></h1>
      <p class="claim">ChatGPT는 좋은 머리처럼 답을 알려줬습니다. Agent는 그 머리에 손이 붙어서 파일을 만들고, 도구를 연결하고, 결과를 확인합니다.</p>
      <div class="grid-2">
        <div class="panel"><h3>이전의 AI</h3><p>대답, 초안, 아이디어를 채팅창 안에서 제공</p></div>
        <div class="panel"><h3>이제의 AI Agent</h3><p>문서, 코드, 사이트, 자동화 결과물을 실제 작업 공간에 생성</p></div>
      </div>
    `
  }
];
```

Then complete the remaining 45 slide objects using the slide contract above. Each object must include one visible `h1`, one `.claim`, and at least one visual component such as `.grid-2`, `.flow`, `.panel`, diagram-like boxes, terminal window, IDE frame, or table.

- [ ] **Step 2: Add specialized layout CSS used by new slides**

Add these classes inside the existing `<style>` block:

```css
.quote {
  margin-top: 58px;
  border-left: 10px solid var(--orange-500);
  background: #fff7ed;
  padding: 34px 42px;
  font-size: 42px;
  line-height: 1.35;
  font-weight: 760;
}
.terminal-window {
  margin-top: 50px;
  background: #101010;
  color: #f5f5f5;
  border-radius: 8px;
  padding: 34px;
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  font-size: 30px;
  line-height: 1.45;
}
.terminal-window .prompt { color: #fb923c; }
.ide-frame {
  margin-top: 48px;
  display: grid;
  grid-template-columns: 360px 1fr;
  min-height: 470px;
  border: 2px solid var(--line);
  border-radius: 8px;
  overflow: hidden;
  background: white;
}
.ide-frame aside {
  background: #f5f5f5;
  border-right: 2px solid var(--line);
  padding: 30px;
  font-size: 28px;
  line-height: 1.6;
}
.ide-frame section {
  padding: 30px;
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  font-size: 28px;
  line-height: 1.45;
}
.manual-doc {
  margin-top: 50px;
  background: white;
  border: 2px solid var(--line);
  border-radius: 8px;
  padding: 34px;
  font-size: 30px;
  line-height: 1.45;
}
.manual-doc code {
  color: var(--orange-600);
  font-weight: 800;
}
.branch-map {
  margin-top: 52px;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 18px;
  align-items: center;
}
.branch-map div {
  background: white;
  border: 2px solid var(--cobalt-soft);
  border-radius: 8px;
  padding: 24px;
  min-height: 140px;
  font-size: 28px;
  line-height: 1.3;
  font-weight: 750;
}
.table {
  margin-top: 46px;
  display: grid;
  border: 2px solid var(--line);
  border-radius: 8px;
  overflow: hidden;
  background: white;
}
.table .row {
  display: grid;
  grid-template-columns: 330px 1fr 1fr;
}
.table .row > div {
  padding: 22px 26px;
  border-bottom: 1px solid var(--line);
  font-size: 27px;
  line-height: 1.35;
}
.table .head > div {
  background: #f5f5f5;
  font-weight: 850;
  color: var(--ink);
}
```

- [ ] **Step 3: Ensure the Superpowers slide group is explicit**

Slides 26-32 must include the named skills and these exact speaking anchors:

```text
Superpowers는 AI Agent에게 일을 잘 시키기 위한 작업 루프입니다.
brainstorming은 바로 만들지 않고 요구사항을 먼저 발견하게 합니다.
writing-plans는 실행 전에 설계도를 문서로 고정합니다.
subagent-driven-development는 큰 일을 작은 실행 단위로 나눕니다.
verification-before-completion은 완료 주장을 증거로 바꿉니다.
systematic-debugging은 실패 증거에서 원인을 좁힙니다.
```

- [ ] **Step 4: Ensure the Naver RSS demo slide group is practical**

Slides 37-44 must include this demo path:

```text
Naver Blog RSS -> Agent 계획 -> Code Snippets/PHP -> WordPress 관리자 화면 -> 카테고리 매핑 -> 프론트 표시 -> 검증 로그 -> 수정 루프
```

Include fields:

```text
title, link, description, pubDate, category, image
```

Include category behavior:

```text
네이버 카테고리를 새 WordPress 카테고리로 만들거나, 사용자가 지정한 기존 카테고리에 매핑한다.
```

- [ ] **Step 5: Verify slide count, numbering, and keywords**

Run:

```bash
python3 - <<'PY'
from html.parser import HTMLParser
from pathlib import Path
import re
html = Path("outputs/ai-agent-lecture/deck-v4/index.html").read_text()
class P(HTMLParser):
    def error(self, message): raise AssertionError(message)
P().feed(html)
slide_objects = len(re.findall(r"title:\\s*[\"'`]", html))
required = ["네이버 블로그 RSS", "MCP", "AGENTS.md", "Skill", "GitHub", "Superpowers", "검증 가능한 위임", "Code Snippets", "Codex", "Antigravity"]
missing = [word for word in required if word not in html]
print({"slide_objects": slide_objects, "missing": missing})
assert slide_objects == 46
assert not missing
PY
```

Expected: prints `slide_objects: 46` and `missing: []`.

- [ ] **Step 6: Commit**

```bash
git add outputs/ai-agent-lecture/deck-v4/index.html
git commit -m "feat: add ai agent lecture deck v4 slides"
```

Expected: commit succeeds or skip is recorded if `outputs/` is intentionally untracked.

### Task 4: Local Browser Render And Slide Screenshot QA

**Files:**
- Create: `outputs/ai-agent-lecture/deck-v4/slide-01.png` through `outputs/ai-agent-lecture/deck-v4/slide-46.png`
- Create: `outputs/ai-agent-lecture/deck-v4/contact-sheet.png`

- [ ] **Step 1: Start a local static server**

Run:

```bash
python3 -m http.server 4184 --directory outputs/ai-agent-lecture/deck-v4
```

Expected: server listens at `http://127.0.0.1:4184/`. Keep this session running until screenshots are done.

- [ ] **Step 2: Capture all slides with Chrome headless**

In a separate terminal, run:

```bash
for i in $(seq 1 46); do
  n=$(printf "%02d" "$i")
  "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" \
    --headless=new \
    --disable-gpu \
    --hide-scrollbars \
    --window-size=1920,1080 \
    --virtual-time-budget=1800 \
    --screenshot="outputs/ai-agent-lecture/deck-v4/slide-${n}.png" \
    "http://127.0.0.1:4184/?slide=${i}"
done
```

Expected: 46 PNG files are created and none are zero bytes.

- [ ] **Step 3: Generate contact sheet**

Run:

```bash
python3 - <<'PY'
from pathlib import Path
from PIL import Image, ImageDraw
base = Path("outputs/ai-agent-lecture/deck-v4")
files = [base / f"slide-{i:02d}.png" for i in range(1, 47)]
thumbs = []
for file in files:
    img = Image.open(file).convert("RGB")
    img.thumbnail((384, 216))
    canvas = Image.new("RGB", (384, 246), "white")
    canvas.paste(img, (0, 0))
    d = ImageDraw.Draw(canvas)
    d.text((12, 222), file.stem, fill=(0, 0, 0))
    thumbs.append(canvas)
cols = 4
rows = (len(thumbs) + cols - 1) // cols
sheet = Image.new("RGB", (cols * 384, rows * 246), "#e5e5e5")
for idx, thumb in enumerate(thumbs):
    sheet.paste(thumb, ((idx % cols) * 384, (idx // cols) * 246))
sheet.save(base / "contact-sheet.png")
print(base / "contact-sheet.png")
PY
```

Expected: `outputs/ai-agent-lecture/deck-v4/contact-sheet.png` exists.

- [ ] **Step 4: Inspect contact sheet**

Open `outputs/ai-agent-lecture/deck-v4/contact-sheet.png` and check:

```text
No title overlaps.
No body text crosses slide boundaries.
No text collides with the page number.
No right-side decoration covers prose.
Slides 26-32 visibly teach Superpowers as a workflow.
Slides 37-44 visibly teach the Naver RSS to WordPress demo.
```

Expected: no blocking visual issues. If a blocking issue is found, edit `index.html`, rerun Steps 2-4, and keep only the corrected screenshots.

- [ ] **Step 5: Commit screenshot evidence**

```bash
git add outputs/ai-agent-lecture/deck-v4/slide-*.png outputs/ai-agent-lecture/deck-v4/contact-sheet.png
git commit -m "test: capture ai agent lecture deck v4 screenshots"
```

Expected: commit succeeds or skip is recorded if generated outputs are intentionally untracked.

### Task 5: Build The Blog-Style Notion Teaching Page Payload

**Files:**
- Create: `outputs/ai-agent-lecture/notion/lecture-v4-payload.json`

- [ ] **Step 1: Create Notion content outline locally**

Create `outputs/ai-agent-lecture/notion/lecture-v4-payload.json` with a page title and ordered sections:

```json
{
  "title": "AI Agent 강의 교안 v4 - WordPress 제작자를 위한 검증 가능한 위임",
  "topic": "노코드/AI/자동화",
  "style": "blog-article-with-slide-images",
  "sections": [
    {
      "heading": "좋은 머리에 손이 생겼다",
      "slides": [1, 2, 3],
      "prose": "ChatGPT를 처음 쓸 때 우리는 좋은 머리를 빌린다고 느꼈습니다. 질문하면 답을 주고, 글을 써주고, 아이디어를 정리해줍니다. 하지만 그 결과를 실제 문서, 웹사이트, 고객 안내문, 자동화 도구로 옮기는 마지막 일은 여전히 사람이 했습니다.",
      "blocks": ["quote", "image", "paragraph"]
    },
    {
      "heading": "ChatGPT와 AI Agent의 차이",
      "slides": [4, 5, 6, 7, 8, 9],
      "prose": "챗봇은 대답을 줍니다. Agent는 목표를 받고 작업 공간으로 들어갑니다. 그래서 결과물이 채팅창 안의 텍스트가 아니라 한글 문서, 구글 문서, 워드 파일, PDF, PPT, 코드, 웹사이트 수정 결과로 나옵니다.",
      "blocks": ["image", "paragraph", "table"]
    },
    {
      "heading": "이름보다 역할로 이해하는 도구 지도",
      "slides": [10, 11, 12, 13, 14, 15, 16, 17, 18],
      "prose": "모델은 엔진입니다. Codex, Antigravity, Cursor, Claude Code 같은 도구는 그 엔진을 작업 공간에 연결하는 실행 환경입니다.",
      "blocks": ["image", "paragraph", "table", "code"]
    },
    {
      "heading": "에이전트에게 필요한 작업 매뉴얼",
      "slides": [19, 20, 21, 22, 23, 24, 25],
      "prose": "AI Agent는 초엘리트 신입사원처럼 똑똑하지만, 우리 회사의 작업 방식과 금지사항과 검증 기준은 모릅니다. 그래서 AGENTS.md, Skill, 작업 루프가 필요합니다.",
      "blocks": ["image", "paragraph", "quote"]
    },
    {
      "heading": "Superpowers로 작업 루프 만들기",
      "slides": [26, 27, 28, 29, 30, 31, 32],
      "prose": "Superpowers는 AI Agent에게 바로 만들라고 시키는 대신, 먼저 묻고, 계획하고, 실행하고, 검증하고, 실패하면 디버깅하게 만드는 작업 루프입니다.",
      "blocks": ["image", "paragraph", "code", "mermaid"]
    },
    {
      "heading": "MCP와 WordPress 자율 개발 루프",
      "slides": [33, 34, 35],
      "prose": "WordPress는 내 노트북 안이 아니라 외부 서버에 있는 경우가 많습니다. Agent가 그 사이트를 직접 확인하고 고치려면 MCP나 API 같은 연결 통로가 필요합니다.",
      "blocks": ["image", "paragraph", "code"]
    },
    {
      "heading": "실습: 네이버 블로그 RSS를 WordPress로 가져오기",
      "slides": [37, 38, 39, 40, 41, 42, 43, 44],
      "prose": "오늘의 실험은 네이버 블로그 RSS에서 새 글 정보를 읽고 WordPress에 표시하거나 가져오는 Code Snippets용 PHP를 만드는 과정입니다.",
      "blocks": ["image", "paragraph", "table", "code"]
    },
    {
      "heading": "GitHub와 다음 자동화 아이디어",
      "slides": [36, 45, 46],
      "prose": "결과물은 저장소에 남겨야 다시 쓸 수 있습니다. GitHub는 코드 저장소이자 여러 버전을 동시에 실험할 수 있는 작업 공간입니다.",
      "blocks": ["image", "paragraph", "table"]
    }
  ]
}
```

- [ ] **Step 2: Add Mermaid and code examples to payload**

Append these exact content objects to the relevant sections in `lecture-v4-payload.json`:

```json
{
  "mermaid": "flowchart LR\nA[Brainstorming\\n요구사항 발견] --> B[Writing Plans\\n설계도 작성]\nB --> C[Subagent / Executing\\n작은 단위 실행]\nC --> D[Verification\\n증거 확인]\nD --> E{문제 있음?}\nE -- 예 --> F[Systematic Debugging\\n원인 좁히기]\nF --> C\nE -- 아니오 --> G[완료 기록]"
}
```

```json
{
  "codeBlocks": [
    {
      "language": "text",
      "content": "이 기능을 바로 만들지 말고 brainstorming으로 요구사항을 먼저 정리해줘. 내가 놓친 필드, 화면, 검증 기준을 질문해줘."
    },
    {
      "language": "text",
      "content": "이제 writing-plans로 구현 계획을 작성해줘. 파일 범위, 작업 순서, 검증 방법을 포함해줘."
    },
    {
      "language": "text",
      "content": "계획대로 구현하고, 완료했다고 말하기 전에 verification-before-completion으로 증거를 남겨줘."
    }
  ]
}
```

- [ ] **Step 3: Verify JSON syntax**

Run:

```bash
python3 -m json.tool outputs/ai-agent-lecture/notion/lecture-v4-payload.json >/dev/null
```

Expected: exit code 0.

- [ ] **Step 4: Commit**

```bash
git add outputs/ai-agent-lecture/notion/lecture-v4-payload.json
git commit -m "docs: draft notion lecture page payload"
```

Expected: commit succeeds or skip is recorded if generated outputs are intentionally untracked.

### Task 6: Create New Notion Page With Images In The Correct Positions

**Files:**
- Create: `outputs/ai-agent-lecture/notion/lecture-v4-page.json`
- Modify: `outputs/ai-agent-lecture/deck-v4/qa-evidence.json`

- [ ] **Step 1: Confirm Notion capability**

Use one available path:

```text
Preferred: Notion MCP/connector if callable in this session.
Fallback: direct Notion API with the user-provided integration token stored only in environment variables.
```

Expected: Able to create a page in the Notes DB. Do not print the token in logs or final output.

- [ ] **Step 2: Upload slide images**

For each slide image from `outputs/ai-agent-lecture/deck-v4/slide-01.png` through `slide-46.png`, upload the image to Notion or attach it through the Notion file upload API.

Expected: Each uploaded file has a Notion-hosted file reference or external file block reference usable in a page block.

- [ ] **Step 3: Create a new page in Notes DB**

Create a separate page, not the existing source page, with:

```text
Title: AI Agent 강의 교안 v4 - WordPress 제작자를 위한 검증 가능한 위임
Topic: 노코드/AI/자동화
Status: Draft
Source: deck-v4
```

Expected: A new Notion page URL is returned.

- [ ] **Step 4: Insert content as blog-style article blocks**

Use this block order:

```text
H1 title
Intro paragraph
Quote block with core message
Table of contents / chapter list
H2 chapter heading
Paragraph prose
Slide image block at the exact point where that slide is discussed
H3 "발표 포인트"
Bulleted speaking cues
H3 "참고 링크"
Bookmark or paragraph links
H3 "실습 프롬프트"
Code block when relevant
Mermaid code block for the Superpowers loop
```

Expected: Images are not dumped at the top or bottom. Each slide image appears inside the matching chapter near the prose that explains it.

- [ ] **Step 5: Save local page metadata**

Create `outputs/ai-agent-lecture/notion/lecture-v4-page.json` using the values returned by the Notion create-page request:

```json
{
  "createdAt": "2026-06-03",
  "title": "AI Agent 강의 교안 v4 - WordPress 제작자를 위한 검증 가능한 위임",
  "notionPageId": "use the page id returned by the Notion create-page request",
  "notionUrl": "use the public or workspace URL returned by the Notion create-page request",
  "database": "Notes",
  "topic": "노코드/AI/자동화",
  "slideImageCount": 46,
  "status": "draft"
}
```

Use actual returned values.

- [ ] **Step 6: Verify Notion page content**

Read the created page back through Notion API/MCP and verify:

```text
The page is not the old source page.
The title matches the requested title.
There are 46 image blocks or file references.
The first slide image appears near the opening prose.
Superpowers section contains code examples and Mermaid code.
Naver RSS section contains fields title, link, description, pubDate, category, image.
GitHub section exists.
```

Expected: all checks pass.

- [ ] **Step 7: Commit local Notion metadata**

```bash
git add outputs/ai-agent-lecture/notion/lecture-v4-page.json outputs/ai-agent-lecture/deck-v4/qa-evidence.json
git commit -m "docs: record notion lecture page upload evidence"
```

Expected: commit succeeds or skip is recorded if generated outputs are intentionally untracked.

### Task 7: Final QA Evidence And Handoff

**Files:**
- Create or modify: `outputs/ai-agent-lecture/deck-v4/qa-evidence.json`
- Modify: `docs/verification/2026-06-02-ai-agent-lecture-deck.qa-evidence.json`

- [ ] **Step 1: Create deck-v4 evidence JSON**

Create `outputs/ai-agent-lecture/deck-v4/qa-evidence.json`:

```json
{
  "task": "AI Agent lecture deck v4 and Notion teaching page",
  "date": "2026-06-03",
  "deck": {
    "path": "outputs/ai-agent-lecture/deck-v4/index.html",
    "slideCount": 46,
    "screenshots": "outputs/ai-agent-lecture/deck-v4/slide-01.png through slide-46.png",
    "contactSheet": "outputs/ai-agent-lecture/deck-v4/contact-sheet.png"
  },
  "notion": {
    "pageMetadata": "outputs/ai-agent-lecture/notion/lecture-v4-page.json",
    "imageBlocksExpected": 46,
    "style": "blog-style lecture article with images, prose, links, code, tables, and Mermaid"
  },
  "verification": {
    "htmlParser": "not_run_until_task7",
    "slideCount": "not_run_until_task7",
    "requiredKeywords": "not_run_until_task7",
    "screenshots": "not_run_until_task7",
    "contactSheetReview": "not_run_until_task7",
    "notionReadback": "not_run_until_task7"
  },
  "findings": []
}
```

Replace each `"not_run_until_task7"` with `"pass"` only after the relevant command or readback actually passes.

- [ ] **Step 2: Run final local verification**

Run:

```bash
python3 -m html.parser outputs/ai-agent-lecture/deck-v4/index.html
python3 - <<'PY'
from pathlib import Path
import json, re
html = Path("outputs/ai-agent-lecture/deck-v4/index.html").read_text()
slides = len(re.findall(r"title:\\s*[\"'`]", html))
pngs = sorted(Path("outputs/ai-agent-lecture/deck-v4").glob("slide-*.png"))
required = ["네이버 블로그 RSS", "MCP", "AGENTS.md", "Skill", "GitHub", "Superpowers", "검증 가능한 위임", "Code Snippets", "Codex", "Antigravity"]
missing = [word for word in required if word not in html]
print(json.dumps({"slides": slides, "pngs": len(pngs), "missing": missing}, ensure_ascii=False))
assert slides == 46
assert len(pngs) == 46
assert not missing
assert Path("outputs/ai-agent-lecture/deck-v4/contact-sheet.png").exists()
PY
```

Expected: parser passes and JSON output reports `slides: 46`, `pngs: 46`, `missing: []`.

- [ ] **Step 3: Append deck-v4 evidence to the project verification file**

Append this object under a new top-level key named `deckV4LecturePage` in `docs/verification/2026-06-02-ai-agent-lecture-deck.qa-evidence.json`:

```json
{
  "date": "2026-06-03",
  "scope": "Create revised 46-slide deck-v4 and separate blog-style Notion teaching page.",
  "filesChanged": [
    "outputs/ai-agent-lecture/deck-v4/index.html",
    "outputs/ai-agent-lecture/deck-v4/contact-sheet.png",
    "outputs/ai-agent-lecture/notion/lecture-v4-payload.json",
    "outputs/ai-agent-lecture/notion/lecture-v4-page.json"
  ],
  "verification": [
    {
      "command": "python3 -m html.parser outputs/ai-agent-lecture/deck-v4/index.html",
      "result": "pass"
    },
    {
      "command": "Chrome headless screenshots for slide=1..46",
      "result": "pass; 46 screenshots generated"
    },
    {
      "command": "Notion readback",
      "result": "pass; new page contains slide images and lecture sections"
    }
  ],
  "findings": []
}
```

Keep existing JSON valid.

- [ ] **Step 4: Verify evidence JSON syntax**

Run:

```bash
python3 -m json.tool outputs/ai-agent-lecture/deck-v4/qa-evidence.json >/dev/null
python3 -m json.tool docs/verification/2026-06-02-ai-agent-lecture-deck.qa-evidence.json >/dev/null
```

Expected: both commands exit 0.

- [ ] **Step 5: Commit final evidence**

```bash
git add outputs/ai-agent-lecture/deck-v4/qa-evidence.json docs/verification/2026-06-02-ai-agent-lecture-deck.qa-evidence.json
git commit -m "test: record ai agent lecture deck v4 evidence"
```

Expected: commit succeeds.

## Self-Review

**Spec coverage:** This plan covers the requested HTML-first workflow, expanded deck, Superpowers explanation, Naver RSS to WordPress demo, separate Notion page, blog-style Notion format, slide images placed inside prose, links, code blocks, tables, Mermaid, and verification evidence.

**Placeholder scan:** The plan avoids `TBD`, `TODO`, vague "handle later" tasks, and unbounded "write tests" instructions. The only sample values are explicitly marked as examples that must be replaced with actual returned values.

**Type consistency:** Paths are consistent across tasks: `deck-v4/index.html`, `deck-v4/slide-NN.png`, `deck-v4/contact-sheet.png`, `deck-v4/qa-evidence.json`, `notion/lecture-v4-payload.json`, and `notion/lecture-v4-page.json`.
