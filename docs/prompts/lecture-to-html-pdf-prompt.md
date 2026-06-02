# Enhanced Prompt: Lecture Conversation To HTML Deck And PDF

Use this prompt when asking Codex to run the lecture-to-deck workflow.

```text
I want to turn a lecture conversation or transcript into a polished HTML slide deck and exportable PDF.

Use the Codex Loopy Harness workflow in this repository.

Goal:
- Convert the source lecture material into a clear teaching deck.
- Produce an editable HTML presentation.
- Export or prepare it for PDF export.
- Verify the result with browser/PDF checks and record evidence.

Inputs I will provide:
- Lecture conversation or transcript: [paste or file path]
- Audience: [who this is for]
- Target slide count: [number or range]
- Language: [Korean / English / bilingual]
- Tone: [practical / academic / executive / workshop]
- Output purpose: [live lecture / self-study / sales / internal training]
- Required references or examples: [links/files/notes]

Operating rules:
1. First restate the task as a contract with acceptance criteria.
2. Check existing skills/plugins before creating new tools.
3. Use verified capabilities where relevant:
   - Superpowers for planning and verification discipline.
   - Browser for HTML rendering checks.
   - PDF skill or browser export flow for PDF handling.
   - Presentations skill only if a PPTX deliverable is explicitly needed.
4. Preserve the lecture's meaning before compressing it.
5. Do not convert raw transcript paragraphs directly into slides.
6. Build a slide storyboard before HTML.
7. Each slide must have one main claim and one proof object.
8. Create or update a QA evidence record.
9. Do not claim completion until fresh verification passes.

Expected outputs:
- Clean lecture summary.
- Slide outline.
- Slide storyboard with title, claim, proof object, and speaker note.
- HTML deck files.
- PDF export or export instructions, depending on available tools.
- QA evidence file.
- Brief note of reusable memory facts, if any.

Acceptance criteria:
- The deck has title, agenda, body, recap, and next-action slides.
- The slide count matches the requested range or explains why it changed.
- HTML renders without visible overflow.
- PDF export preserves slide boundaries.
- Important claims trace back to the source lecture or provided references.
- High/critical QA findings are fixed before completion.

Before implementation, ask me only if a missing input would materially change the deck.
Otherwise, make reasonable assumptions and record them in the evidence file.
```

## Short Version

```text
Use the Codex Loopy Harness to turn this lecture transcript into an HTML deck and PDF-ready export. First define the contract, then clean the transcript, create a teaching outline, storyboard slides, build the HTML deck, verify in browser/PDF, and record evidence. Prefer existing skills/plugins over new tools. Do not claim completion without fresh verification.
```

