<?php

namespace Wehaa\RemoteView;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Compilers\CompilerInterface;

class RemoteViewCompiler extends BladeCompiler implements CompilerInterface
{
    /** @var Repository */
    protected $config;

    public function __construct($filesystem, $cache_path, Repository $config)
    {
        // Get Current Blade Instance
        $blade = app('view')->getEngineResolver()->resolve('blade')->getCompiler();

        if (!File::exists($cache_path)) {
            File::makeDirectory($cache_path, $mode = 0777, true, true);
        }

        parent::__construct($filesystem, $cache_path);
        $this->rawTags          = $blade->rawTags;
        $this->contentTags      = array_map('stripcslashes', $blade->contentTags);
        $this->escapedTags      = array_map('stripcslashes', $blade->escapedTags);
        $this->extensions       = $blade->getExtensions();
        $this->customDirectives = $blade->getCustomDirectives();
        $this->config           = $config;
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string $path
     * @return void
     */
    public function compile($path = null)
    {
        if (is_null($path)) {
            return;
        }

        $string = cache()->rememberForever(
            get_cache_key($path),
            function () use ($path) {
                return Storage::disk('s3')->get($path);
            }
        );

        // Compile to PHP
        $contents = $this->compileString($string);
        if (!is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($path), $contents);
        }
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @param  string $path
     * @return bool
     */
    public function isExpired($path)
    {
        $compiled = $this->getCompiledPath($path);
        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (!$this->cachePath || !$this->files->exists($compiled)) {
            return true;
        }

        $lastModified = cache()->tags('template_was_mod')->rememberForever(
            get_cache_key('wasmod_' . $path),
            function () use ($path) {
                return Storage::disk('s3')->lastModified($path);
            }
        );

        if ($lastModified >= $this->files->lastModified($compiled)) {
            cache()->forget(get_cache_key($path));
            return true;
        }
        return false;
    }
}
