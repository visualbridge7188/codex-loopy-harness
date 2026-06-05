import { execSync } from 'child_process';
import test from 'node:test';
import assert from 'node:assert';

test('install-gptaku.mjs exit codes and validation', async (t) => {
  await t.test('fails when no arguments are provided', () => {
    assert.throws(() => {
      execSync('node scripts/install-gptaku.mjs', { stdio: 'pipe' });
    }, (err) => {
      return err.status === 1;
    });
  });

  await t.test('fails when invalid URL is provided', () => {
    assert.throws(() => {
      execSync('node scripts/install-gptaku.mjs "not-a-valid-url"', { stdio: 'pipe' });
    }, (err) => {
      return err.status === 1;
    });
  });

  await t.test('fails when cloning non-existent repo', () => {
    assert.throws(() => {
      execSync('node scripts/install-gptaku.mjs "https://github.com/fivetaku/non-existent-repo-12345.git"', { stdio: 'pipe' });
    }, (err) => {
      return err.status === 1;
    });
  });
});
