<?php

namespace App\Providers;

use Phalcon\Mvc\View\Engine\Volt as PhVolt;

class Volt extends Provider
{

    protected $serviceName = 'volt';

    public function register()
    {
        $this->di->setShared('volt', function ($view, $di) {

            $volt = new PhVolt($view, $di);

            $volt->setOptions([
                'compiledPath' => cache_path() . '/volt/',
                'compiledSeparator' => '_',
            ]);

            $compiler = $volt->getCompiler();

            $compiler->addFunction('config', function ($resolvedArgs) {
                return 'kg_config(' . $resolvedArgs . ')';
            });

            $compiler->addFunction('setting', function ($resolvedArgs) {
                return 'kg_setting(' . $resolvedArgs . ')';
            });

            $compiler->addFunction('full_url', function ($resolvedArgs) {
                return 'kg_full_url(' . $resolvedArgs . ')';
            });

            $compiler->addFunction('static_url', function ($resolvedArgs) {
                return 'kg_static_url(' . $resolvedArgs . ')';
            });

            $compiler->addFunction('icon_link', function ($resolvedArgs) {
                return 'kg_icon_link(' . $resolvedArgs . ')';
            });

            $compiler->addFunction('css_link', function ($resolvedArgs) {
                return 'kg_css_link(' . $resolvedArgs . ')';
            });

            $compiler->addFunction('js_include', function ($resolvedArgs) {
                return 'kg_js_include(' . $resolvedArgs . ')';
            });

            $compiler->addFunction('cos_img_url', function ($resolvedArgs) {
                return 'kg_cos_img_url(' . $resolvedArgs . ')';
            });

            $compiler->addFunction('substr', function ($resolvedArgs) {
                return 'kg_substr(' . $resolvedArgs . ')';
            });

            $compiler->addFunction('array_object', function ($resolvedArgs) {
                return 'kg_array_object(' . $resolvedArgs . ')';
            });

            $compiler->addFunction('object_array', function ($resolvedArgs) {
                return 'kg_object_array(' . $resolvedArgs . ')';
            });

            $compiler->addFilter('parse_markdown', function ($resolvedArgs) {
                return 'kg_parse_markdown(' . $resolvedArgs . ')';
            });

            $compiler->addFilter('duration', function ($resolvedArgs) {
                return 'kg_duration(' . $resolvedArgs . ')';
            });

            $compiler->addFilter('human_number', function ($resolvedArgs) {
                return 'kg_human_number(' . $resolvedArgs . ')';
            });

            $compiler->addFilter('human_size', function ($resolvedArgs) {
                return 'kg_human_size(' . $resolvedArgs . ')';
            });

            $compiler->addFilter('time_ago', function ($resolvedArgs) {
                return 'kg_time_ago(' . $resolvedArgs . ')';
            });

            $compiler->addFilter('anonymous', function ($resolvedArgs) {
                return 'kg_anonymous(' . $resolvedArgs . ')';
            });

            return $volt;
        });
    }

}