<?php

namespace OptimistDigital\NovaBlog\Nova;

use Laravel\Nova\Resource;
use OptimistDigital\NovaBlog\NovaBlog;
use Illuminate\Http\Request;
use OptimistDigital\NovaLocaleField\Filters\LocaleFilter;
use OptimistDigital\NovaLocaleField\Filters\LocaleChildrenFilter;

abstract class TemplateResource extends Resource
{
    protected $templateClass;

    protected function getTemplateClass()
    {
        if (isset($this->templateClass)) return $this->templateClass;

        // $templates = $this->type === 'post'
        //     ? NovaBlog::getPostTemplates()
        //     : NovaBlog::getRegionTemplates();

        if (isset($this->template)) {
            foreach ($templates as $template) {
                if ($template::$name == $this->template) $this->templateClass = new $template($this->resource);
            }
        }

        return $this->templateClass;
    }

    protected function getTemplateFields(): array
    {
        $templateClass = $this->getTemplateClass();
        $templateFields = [];

        if (isset($templateClass)) {
            $rawFields = $templateClass->fields(request());
            $templateFields = array_map(function ($field) {
                if (!empty($field->attribute)) {
                    $field->attribute = 'data->' . $field->attribute;
                }
                return $field->hideFromIndex();
            }, $rawFields);
        }

        return $templateFields;
    }
}