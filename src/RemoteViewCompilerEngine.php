<?php

namespace Wehaa\RemoteView;

use Wehaa\RemoteView\RemoteViewCompiler;
use Illuminate\View\Engines\CompilerEngine;

class RemoteViewCompilerEngine extends CompilerEngine
{
    /**
     * RemoteViewCompilerEngine constructor.
     *
     * @param RemoteViewCompiler $bladeCompiler
     */
    public function __construct(RemoteViewCompiler $bladeCompiler)
    {
        parent::__construct($bladeCompiler);
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array   $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        $this->lastCompiled[] = $path;

        // If this given view has expired, which means it has simply been edited since
        // it was last compiled, we will re-compile the views so we can evaluate a
        // fresh copy of the view. We'll pass the compiler the path of the view.
        if ($this->compiler->isExpired($path)) {
            $this->compiler->compile($path);
        }

        $compiled = $this->compiler->getCompiledPath($path);

        // Once we have the path to the compiled file, we will evaluate the paths with
        // typical PHP just like any other templates. We also keep a stack of views
        // which have been rendered for right exception messages to be generated.
        $results = $this->evaluatePath($compiled, $data);

        array_pop($this->lastCompiled);

        return $results;
    }
}
