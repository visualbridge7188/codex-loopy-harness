#!/usr/bin/env node

/**
 * search-skills.mjs — Programmatic skill search helper
 *
 * Searches the skills.sh API for skills matching a query.
 * Returns structured JSON for agent consumption.
 *
 * Usage:
 *   node skills/discover-skills/search-skills.mjs "<query>"
 *   node skills/discover-skills/search-skills.mjs "<query>" --limit 5
 *   node skills/discover-skills/search-skills.mjs "<query>" --json
 *
 * Output: JSON array of { name, slug, source, installs, install_url, quality }
 */

const SKILLS_API_BASE = process.env.SKILLS_API_URL || 'https://skills.sh';

// ── Helpers ──────────────────────────────────────────────────────────

function formatInstalls(count) {
  if (!count || count <= 0) return '0 installs';
  if (count >= 1_000_000) return `${(count / 1_000_000).toFixed(1).replace(/\.0$/, '')}M installs`;
  if (count >= 1_000) return `${(count / 1_000).toFixed(1).replace(/\.0$/, '')}K installs`;
  return `${count} install${count === 1 ? '' : 's'}`;
}

function assessQuality(skill) {
  const installs = skill.installs || 0;
  const source = skill.source || '';
  const trustedOrgs = ['vercel-labs', 'anthropics', 'microsoft', 'google', 'meta', 'aws'];
  const isTrustedSource = trustedOrgs.some(org => source.startsWith(org + '/'));

  let level = 'unknown';
  let score = 0;

  // Install-based scoring
  if (installs >= 100_000) { level = 'high'; score += 3; }
  else if (installs >= 10_000) { level = 'high'; score += 2; }
  else if (installs >= 1_000) { level = 'medium'; score += 1; }
  else if (installs >= 100) { level = 'low'; score += 0; }
  else { level = 'unverified'; score -= 1; }

  // Source-based scoring
  if (isTrustedSource) { score += 2; }
  else if (source) { score += 0; }
  else { score -= 1; }

  const recommendation = score >= 3 ? 'recommended'
    : score >= 1 ? 'acceptable'
    : score >= 0 ? 'caution'
    : 'skip';

  return { level, score, recommendation, isTrustedSource };
}

// ── API Search ───────────────────────────────────────────────────────

async function searchSkillsAPI(query, limit = 10) {
  const url = `${SKILLS_API_BASE}/api/search?q=${encodeURIComponent(query)}&limit=${limit}`;

  try {
    const res = await fetch(url, {
      headers: { 'Accept': 'application/json' },
      signal: AbortSignal.timeout(10000),
    });

    if (!res.ok) {
      return { ok: false, error: `API returned ${res.status}`, results: [] };
    }

    const data = await res.json();

    if (!data.skills || !Array.isArray(data.skills)) {
      return { ok: false, error: 'Invalid API response format', results: [] };
    }

    const results = data.skills
      .map(skill => {
        const quality = assessQuality(skill);
        return {
          name: skill.name || 'Unknown',
          slug: skill.id || '',
          source: skill.source || '',
          installs: skill.installs || 0,
          installs_human: formatInstalls(skill.installs),
          install_url: skill.source
            ? `npx skills add ${skill.source}${skill.id ? '@' + skill.id : ''} -y`
            : '',
          quality,
        };
      })
      .sort((a, b) => (b.installs || 0) - (a.installs || 0));

    return { ok: true, query, count: results.length, results };
  } catch (err) {
    return { ok: false, error: err.message, results: [] };
  }
}

// ── Local Skills Check ──────────────────────────────────────────────

async function checkLocalSkills() {
  const { execSync } = await import('child_process');
  try {
    const output = execSync('npx skills list 2>/dev/null', {
      encoding: 'utf-8',
      timeout: 15000,
      stdio: ['pipe', 'pipe', 'pipe'],
    });
    return { ok: true, installed: output.trim() };
  } catch {
    return { ok: false, installed: '' };
  }
}

// ── CLI Entry ────────────────────────────────────────────────────────

async function main() {
  const args = process.argv.slice(2);

  if (args.length === 0 || args.includes('--help') || args.includes('-h')) {
    console.log(`
Usage: node skills/discover-skills/search-skills.mjs "<query>" [options]

Options:
  --limit <n>    Max results (default: 10)
  --json         Output raw JSON (default: human-readable)
  --installed    List locally installed skills
  --help         Show this help

Examples:
  node skills/discover-skills/search-skills.mjs "react testing"
  node skills/discover-skills/search-skills.mjs "deploy" --limit 5 --json
`);
    process.exit(0);
  }

  // Handle --installed flag
  if (args.includes('--installed')) {
    const local = await checkLocalSkills();
    if (local.ok) {
      console.log(local.installed);
    } else {
      console.log('No locally installed skills found or skills CLI not available.');
    }
    process.exit(0);
  }

  // Parse query and options
  const limitIdx = args.indexOf('--limit');
  const limit = limitIdx !== -1 ? parseInt(args[limitIdx + 1], 10) || 10 : 10;
  const jsonOutput = args.includes('--json');
  const limitValueIdx = limitIdx !== -1 ? limitIdx + 1 : -1;
  const queryArgs = args.filter(a => !a.startsWith('--') && args.indexOf(a) !== limitValueIdx);
  const query = queryArgs.join(' ');

  if (!query) {
    console.error('Error: No search query provided.');
    process.exit(1);
  }

  const result = await searchSkillsAPI(query, limit);

  if (jsonOutput) {
    console.log(JSON.stringify(result, null, 2));
  } else {
    if (!result.ok) {
      console.error(`Search failed: ${result.error}`);
      process.exit(1);
    }

    if (result.count === 0) {
      console.log(`No skills found for: "${query}"`);
      console.log('Try alternative keywords or browse https://skills.sh/');
      process.exit(0);
    }

    console.log(`\nSkills found for "${query}" (${result.count} results):\n`);

    for (const skill of result.results) {
      const trust = skill.quality.isTrustedSource ? '✓ trusted' : '○ community';
      const rec = skill.quality.recommendation === 'recommended' ? '🟢'
        : skill.quality.recommendation === 'acceptable' ? '🟡'
        : skill.quality.recommendation === 'caution' ? '🟠'
        : '🔴';

      console.log(`  ${rec} ${skill.name}`);
      console.log(`     Source: ${skill.source} (${trust})`);
      console.log(`     Installs: ${skill.installs_human}`);
      if (skill.install_url) {
        console.log(`     Install: ${skill.install_url}`);
      }
      console.log();
    }

    // Summary
    const recommended = result.results.filter(s => s.quality.recommendation === 'recommended');
    if (recommended.length > 0) {
      console.log(`Top pick: ${recommended[0].name}`);
      console.log(`  ${recommended[0].install_url}`);
    }
  }
}

main().catch(err => {
  console.error('Fatal:', err.message);
  process.exit(1);
});