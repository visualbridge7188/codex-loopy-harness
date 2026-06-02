# Lecture Conversation To HTML Deck And PDF Workflow

This workflow turns a lecture conversation or transcript into a structured HTML presentation and exported PDF.

## Goal

Create a repeatable pipeline:

```text
lecture conversation -> cleaned transcript -> teaching outline -> slide storyboard -> HTML deck -> browser QA -> PDF export
```

The first version should prioritize clarity, verifiability, and editability over heavy automation.

## Inputs

Accepted inputs:

- Raw lecture conversation text.
- Audio/video transcript.
- Notes from a lecture exchange.
- Reference links or files.
- Desired audience and duration.

Useful metadata:

- Lecture title.
- Audience level.
- Target slide count.
- Tone.
- Language.
- Required examples.
- Export size, such as 16:9 slides or A4 handout.

## Outputs

Primary outputs:

- Cleaned transcript or lecture notes.
- Slide outline.
- HTML presentation.
- PDF export.

Optional outputs:

- Speaker notes.
- Handout version.
- Source bibliography.
- QA evidence record.

## Recommended Capability Routing

| Phase | Capability |
| --- | --- |
| Transcript cleanup | `humanizer`, general writing, source verification |
| Structure and storyline | Superpowers brainstorming/planning |
| Visual slide system | Browser plugin, frontend/design guidance |
| HTML deck rendering | HTML/CSS, Browser verification |
| PDF export | PDF skill, browser print/export, Playwright candidate |
| Evidence | `docs/verification/evidence-template.qa-evidence.json` |

## Phase 1: Contract

Define before writing:

- Audience.
- Lecture goal.
- Target slide count.
- Required language.
- Whether the output is for live speaking, self-study, or both.
- PDF export format.
- Required verification checks.

Example acceptance criteria:

- Deck has a clear title, agenda, body, recap, and next-action slide.
- Each slide has one main claim.
- HTML renders without overflow at desktop and print/PDF sizes.
- PDF export preserves slide boundaries.
- Source claims are traceable to transcript or references.

## Phase 2: Transcript Processing

Transform raw conversation into:

1. Clean transcript.
2. Key claims.
3. Definitions.
4. Examples.
5. Open questions.
6. Source notes.

Do not remove nuance too early. First preserve meaning, then compress.

## Phase 3: Teaching Outline

Create a teaching outline:

```text
1. Why this matters
2. Core concept
3. Step-by-step method
4. Example
5. Common failure modes
6. Recap
7. Practice or next action
```

For technical lectures, add:

- Architecture diagram.
- Decision table.
- Failure/recovery loop.
- Verification checklist.

## Phase 4: Slide Storyboard

For each slide, define:

- Slide title.
- One-sentence claim.
- Proof object: quote, diagram, example, table, or checklist.
- Speaker note.
- Visual layout.

Avoid turning transcript paragraphs directly into slides.

## Phase 5: HTML Deck

Build an HTML deck when editability and PDF export matter.

Recommended structure:

```text
outputs/<task-slug>/
  index.html
  styles.css
  deck-notes.md
  export.pdf
  qa-evidence.json
```

HTML requirements:

- Fixed slide aspect ratio.
- Print CSS with one slide per page.
- No text overflow.
- Responsive preview for editing.
- Semantic sections for slides.
- Speaker notes kept outside visible slides or in a separate notes file.

## Phase 6: Browser QA

Verify:

- First slide, middle slide, and final slide render correctly.
- Text does not overflow.
- Slide boundaries survive print preview/export.
- Images and diagrams load.
- Korean text renders cleanly if Korean is used.

Preferred checks:

- Browser screenshot.
- Print stylesheet check.
- PDF page count equals slide count.
- Spot-check at least 3 pages after export.

## Phase 7: PDF Export

Export options:

1. Browser print to PDF.
2. Playwright PDF export when the Playwright skill is installed.
3. A dedicated HTML-to-PDF tool if project constraints require it.

PDF QA:

- Page count equals slide count.
- No clipped text.
- No missing images.
- Margins and backgrounds are preserved.
- Links and source notes are acceptable for the use case.

## Phase 8: Evidence And Memory

Create or update an evidence record with:

- Source files.
- Capabilities used.
- Browser checks.
- PDF export check.
- Findings and fixes.
- Durable preferences, such as deck style or audience assumptions.

Capture memory only when it changes future work.

## Practical First Build

Start with one lecture conversation and produce a small 8-12 slide HTML deck.

Do not build a full deck generator first. Build one good deck, verify the loop, then extract reusable templates.

Recommended first template:

- Title
- Audience promise
- Agenda
- Concept map
- 3-5 teaching slides
- Failure modes
- Checklist
- Recap
- Next action

