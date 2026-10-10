<?php

namespace Tests\Unit;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Windows and macOS file systems ignore letter case, Linux (the production server and CI) does not.
 * A view called 'property.create' that lives in Create.blade.php works on a developer PC and is a
 * "View not found" 500 on the server. This test compares every reference with the exact on-disk name,
 * so the mistake is caught on any machine.
 */
class ViewCaseSensitivityTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 2);
    }

    public function test_every_view_reference_matches_a_file_with_exactly_the_same_case(): void
    {
        $problems = [];

        foreach (['app', 'resources/views', 'routes'] as $dir) {
            foreach ($this->phpFiles($dir) as $file) {
                $source = file_get_contents($file);
                $display = substr($file, strlen($this->root) + 1);

                preg_match_all('/(?:\bview|@include|@extends|@includeIf|@each|View::make)\(\s*[\'"]([A-Za-z0-9_.\-]+)[\'"]/', $source, $m);
                foreach (array_unique($m[1]) as $name) {
                    $relative = 'resources/views/' . str_replace('.', '/', $name) . '.blade.php';
                    if (!$this->existsExactly($relative)) {
                        $problems[] = "$display: view '$name' has no file named exactly $relative";
                    }
                }
            }
        }

        $this->assertSame([], $problems, "View references that only work on case-insensitive file systems:\n" . implode("\n", $problems));
    }

    public function test_every_blade_component_tag_resolves_with_exactly_the_same_case(): void
    {
        $problems = [];

        foreach (['resources/views', 'app'] as $dir) {
            foreach ($this->phpFiles($dir) as $file) {
                preg_match_all('/<x-([A-Za-z0-9_.\-]+)/', file_get_contents($file), $m);
                $display = substr($file, strlen($this->root) + 1);

                foreach (array_unique($m[1]) as $name) {
                    $path = 'components/' . str_replace('.', '/', $name);
                    $blade = "resources/views/{$path}.blade.php";
                    $index = "resources/views/{$path}/index.blade.php";

                    // class based component: <x-dealers> -> App\View\Components\Dealers
                    $class = 'app/View/Components/' . str_replace(' ', '', ucwords(str_replace(['-', '.'], ' ', $name))) . '.php';

                    if ($this->existsExactly($blade) || $this->existsExactly($index) || $this->existsExactly($class)) {
                        continue;
                    }

                    $problems[] = "$display: <x-$name> has no component file named exactly $blade";
                }
            }
        }

        $this->assertSame([], $problems, "Component tags that only work on case-insensitive file systems:\n" . implode("\n", $problems));
    }

    public function test_the_checker_itself_detects_a_case_mismatch(): void
    {
        // a path with the wrong case must be reported as missing even where the OS would open it
        $this->assertFalse($this->existsExactly('RESOURCES/views/home/index.blade.php'));
        $this->assertTrue($this->existsExactly('resources/views/home/index.blade.php'));
    }

    /** @return iterable<string> */
    private function phpFiles(string $dir): iterable
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator("{$this->root}/{$dir}", FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                yield str_replace('\\', '/', $file->getPathname());
            }
        }
    }

    /** True only if every path segment exists with exactly this spelling. */
    private function existsExactly(string $relative): bool
    {
        $current = $this->root;

        foreach (explode('/', $relative) as $segment) {
            $entries = @scandir($current) ?: [];
            if (!in_array($segment, $entries, true)) {
                return false;
            }
            $current .= '/' . $segment;
        }

        return true;
    }
}
