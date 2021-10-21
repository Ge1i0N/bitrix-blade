<?php

namespace Arrilot\BitrixBlade;

use Illuminate\Container\Container;

class BladeContainer extends Container
{
    /**
     * Get the application namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return BladeProvider::getNamespace();
    }
}
