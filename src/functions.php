<?php

use Arrilot\BitrixBlade\BladeProvider;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory;

if (!function_exists('renderBladeTemplate')) {
    /**
     * Render blade template callback.
     *
     * @param $templateFile
     * @param $arResult
     * @param $arParams
     * @param $arLangMessages
     * @param $templateFolder
     * @param $parentTemplateFolder
     * @param $template
     *
     * @return void
     */
    function renderBladeTemplate($templateFile, $arResult, $arParams, $arLangMessages, $templateFolder, $parentTemplateFolder, $template)
    {
        Bitrix\Main\Localization\Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/template.php');

        $view = BladeProvider::getViewFactory();

        BladeProvider::addTemplateFolderToViewPaths($template->__folder);

        global $APPLICATION, $USER;

        echo $view->file($_SERVER['DOCUMENT_ROOT'] . $templateFile, compact(
            'arParams',
            'arResult',
            'arLangMessages',
            'template',
            'templateFolder',
            'parentTemplateFolder',
            'APPLICATION',
            'USER'
        ))->render();

        $epilogue = $templateFolder . '/component_epilog.php';
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $epilogue)) {
            $component = $template->__component;
            $component->SetTemplateEpilog([
                'epilogFile'     => $epilogue,
                'templateName'   => $template->__name,
                'templateFile'   => $template->__file,
                'templateFolder' => $template->__folder,
                'templateData'   => false,
            ]);
        }

        BladeProvider::removeTemplateFolderFromViewPaths($template->__folder);
    }
}


if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string|null  $abstract
     * @param  array  $parameters
     * @return mixed|\Illuminate\Contracts\Foundation\Application
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}
if (!function_exists('view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string|null  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    function view($view = null, $data = [], $mergeData = [])
    {
        $factory = app(Factory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }
}
