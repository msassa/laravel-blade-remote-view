<?php

namespace Wehaa\RemoteView;

use Illuminate\Support\Facades\Facade;

class RemoteViewFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'remote-view';
    }
}
