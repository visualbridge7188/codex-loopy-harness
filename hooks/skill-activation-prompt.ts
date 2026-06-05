#!/usr/bin/env node
/**
 * skill-activation-prompt.ts
 * 
 * UserPromptSubmit hook for Codex Loopy Harness v2.
 * Reads skill-rules.json and matches user prompts against
 * keyword triggers and intent patterns to auto-suggest skills.
 * 
 * Adapted from: jung-wan-kim/claude-code-infrastructure-showcase
 * Enhanced with: Korean/English bilingual support, priority grouping
 */

import { readFileSync } from 'fs';
import { join } from 'path';

interface HookInput {
    session_id: string;
    transcript_path: string;
    cwd: string;
    permission_mode: string;
    prompt: string;
}

interface PromptTriggers {
    keywords?: string[];
    intentPatterns?: string[];
}

interface FileTriggers {
    pathPatterns?: string[];
    pathExclusions?: string[];
    contentPatterns?: string[];
}

interface SkillRule {
    type: 'guardrail' | 'domain';
    enforcement: 'block' | 'suggest' | 'warn';
    priority: 'critical' | 'high' | 'medium' | 'low';
    description?: string;
    promptTriggers?: PromptTriggers;
    fileTriggers?: FileTriggers;
}

interface SkillRules {
    version: string;
    skills: Record<string, SkillRule>;
}

interface MatchedSkill {
    name: string;
    matchType: 'keyword' | 'intent';
    matchedTerm?: string;
    config: SkillRule;
}

async function main() {
    try {
        // Read input from stdin
        const input = readFileSync(0, 'utf-8');
        const data: HookInput = JSON.parse(input);
        const prompt = data.prompt.toLowerCase();

        // Load skill rules - check multiple locations
        const projectDir = process.env.CLAUDE_PROJECT_DIR || data.cwd || process.cwd();
        const possiblePaths = [
            join(projectDir, 'skills', 'skill-rules.json'),
            join(projectDir, '.claude', 'skills', 'skill-rules.json'),
            join(projectDir, '.codex', 'skills', 'skill-rules.json'),
        ];

        let rules: SkillRules | null = null;
        for (const rulesPath of possiblePaths) {
            try {
                rules = JSON.parse(readFileSync(rulesPath, 'utf-8'));
                break;
            } catch {
                continue;
            }
        }

        if (!rules) {
            // No rules file found, exit silently
            process.exit(0);
        }

        const matchedSkills: MatchedSkill[] = [];

        // Check each skill for matches
        for (const [skillName, config] of Object.entries(rules.skills)) {
            const triggers = config.promptTriggers;
            if (!triggers) continue;

            // Keyword matching
            if (triggers.keywords) {
                const matchedKeyword = triggers.keywords.find(kw =>
                    prompt.includes(kw.toLowerCase())
                );
                if (matchedKeyword) {
                    matchedSkills.push({
                        name: skillName,
                        matchType: 'keyword',
                        matchedTerm: matchedKeyword,
                        config
                    });
                    continue; // Don't double-match
                }
            }

            // Intent pattern matching (regex)
            if (triggers.intentPatterns) {
                const matchedPattern = triggers.intentPatterns.find(pattern => {
                    try {
                        const regex = new RegExp(pattern, 'i');
                        return regex.test(prompt);
                    } catch {
                        return false; // Invalid regex, skip
                    }
                });
                if (matchedPattern) {
                    matchedSkills.push({
                        name: skillName,
                        matchType: 'intent',
                        matchedTerm: matchedPattern,
                        config
                    });
                }
            }
        }

        // Generate output if matches found
        if (matchedSkills.length > 0) {
            let output = '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n';
            output += '🎯 SKILL ACTIVATION CHECK\n';
            output += '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n';

            // Group by priority
            const critical = matchedSkills.filter(s => s.config.priority === 'critical');
            const high = matchedSkills.filter(s => s.config.priority === 'high');
            const medium = matchedSkills.filter(s => s.config.priority === 'medium');
            const low = matchedSkills.filter(s => s.config.priority === 'low');

            if (critical.length > 0) {
                output += '⚠️ CRITICAL SKILLS (REQUIRED):\n';
                critical.forEach(s => {
                    output += `  → ${s.name} [${s.matchType}: "${s.matchedTerm}"]\n`;
                    if (s.config.description) output += `    ${s.config.description}\n`;
                });
                output += '\n';
            }

            if (high.length > 0) {
                output += '📚 RECOMMENDED SKILLS:\n';
                high.forEach(s => {
                    output += `  → ${s.name} [${s.matchType}: "${s.matchedTerm}"]\n`;
                    if (s.config.description) output += `    ${s.config.description}\n`;
                });
                output += '\n';
            }

            if (medium.length > 0) {
                output += '💡 SUGGESTED SKILLS:\n';
                medium.forEach(s => {
                    output += `  → ${s.name} [${s.matchType}: "${s.matchedTerm}"]\n`;
                });
                output += '\n';
            }

            if (low.length > 0) {
                output += '📌 OPTIONAL SKILLS:\n';
                low.forEach(s => {
                    output += `  → ${s.name}\n`;
                });
                output += '\n';
            }

            output += 'ACTION: Use Skill tool BEFORE responding\n';
            output += '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n';

            console.log(output);
        }

        process.exit(0);
    } catch (err) {
        // Fail open - don't block the user's prompt
        console.error('skill-activation-prompt hook error:', err);
        process.exit(0);
    }
}

main().catch(() => process.exit(0));