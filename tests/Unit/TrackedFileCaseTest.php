<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Git on Windows/macOS (core.ignorecase=true) silently keeps the OLD spelling when a file is renamed
 * only by letter case. The working copy then looks right locally while the repository, and therefore
 * every Linux clone, still has the old name (e.g. app/Models/checkout.php for class Checkout).
 *
 * This compares what git tracks with what is on disk and fails on any difference.
 */
class TrackedFileCaseTest extends TestCase
{
    public function test_every_tracked_file_is_spelled_on_disk_exactly_as_git_tracks_it(): void
    {
        $root = dirname(__DIR__, 2);

        if (!is_dir($root . '/.git') || !$this->gitAvailable($root)) {
            $this->markTestSkipped('Not a git checkout, nothing to compare.');
        }

        $tracked = array_filter(explode("\0", $this->git($root, 'ls-files -z')));
        $this->assertNotEmpty($tracked);

        $listing = [];
        $mismatches = [];

        foreach ($tracked as $path) {
            $current = $root;
            foreach (explode('/', $path) as $segment) {
                $entries = $listing[$current] ??= (@scandir($current) ?: []);

                if (!in_array($segment, $entries, true)) {
                    $onDisk = null;
                    foreach ($entries as $entry) {
                        if (strcasecmp($entry, $segment) === 0) {
                            $onDisk = $entry;
                        }
                    }
                    if ($onDisk !== null) {
                        $mismatches[] = "git tracks '{$path}' but the file on disk is '{$onDisk}'";
                    }
                    break;
                }
                $current .= '/' . $segment;
            }
        }

        $this->assertSame(
            [],
            $mismatches,
            "Case-only renames git did not record (fix with: git mv -f a a.tmp && git mv -f a.tmp A):\n" . implode("\n", $mismatches)
        );
    }

    public function test_class_names_match_their_file_names_exactly(): void
    {
        $root = dirname(__DIR__, 2);
        $problems = [];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("$root/app", \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            if (preg_match('/^\s*(?:final\s+|abstract\s+)?(?:class|trait|interface|enum)\s+([A-Za-z_][A-Za-z0-9_]*)/m', file_get_contents($file->getPathname()), $m)) {
                if ($m[1] . '.php' !== $file->getFilename()) {
                    $problems[] = $file->getFilename() . ' declares ' . $m[1];
                }
            }
        }

        $this->assertSame([], $problems, "PSR-4 requires file name == class name:\n" . implode("\n", $problems));
    }

    private function gitAvailable(string $root): bool
    {
        return trim($this->git($root, '--version')) !== '';
    }

    private function git(string $root, string $args): string
    {
        return (string) @shell_exec('git -C ' . escapeshellarg($root) . ' ' . $args . ' 2>' . (PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null'));
    }
}
