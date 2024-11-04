<?php

namespace Arrilot\BitrixBlade;

use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler as BaseCompiler;

class BladeCompiler extends BaseCompiler
{
    protected $classComponentAliases = [
        'dynamic-component' => \Arrilot\BitrixBlade\BladeDynamicComponent::class,
    ];

    /**
     * Compile the given Blade template contents.
     *
     * @param string $value
     *
     * @return string
     */
    public function compileString($value)
    {
        $result = '<?php if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) die();?>';
        $result .= '<?php if(!empty($arResult)) extract($arResult, EXTR_SKIP);?>';

        return $result . parent::compileString($value);
    }

    public function component($class, $alias = null, $prefix = '')
    {
        if (!is_null($alias) && Str::contains($alias, '\\')) {
            [$class, $alias] = [$alias, $class];
        }

        $namespace = BladeProvider::getNamespace();

        if (is_null($alias)) {
            $alias = Str::contains($class, $namespace)
                ? collect(explode('\\', Str::after($class, $namespace)))->map(function ($segment) {
                    return Str::kebab($segment);
                })->implode(':')
                : Str::kebab(class_basename($class));
        }

        if (!empty($prefix)) {
            $alias = $prefix . '-' . $alias;
        }

        $this->classComponentAliases[$alias] = $class;
    }

    /**
     * Compile the component tags.
     *
     * @param   string $value
     * @return  string
     */
    protected function compileComponentTags($value)
    {
        if (!$this->compilesComponentTags) {
            return $value;
        }

        return (new BladeComponentTagCompiler(
            $this->classComponentAliases,
            $this->classComponentNamespaces,
            $this
        ))->compile($value);
    }
}
