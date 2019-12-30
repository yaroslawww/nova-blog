<?php

namespace OptimistDigital\NovaBlog\Nova;

use Laravel\Nova\Resource;

abstract class TemplateResource extends Resource
{
    protected $templateClass;

    protected function getTemplateClass()
    {
        if (isset($this->templateClass)) {
            return $this->templateClass;
        }

        if (isset($this->template)) {
            foreach ($templates as $template) {
                if ($template::$name == $this->template) {
                    $this->templateClass = new $template($this->resource);
                }
            }
        }
        return $this->templateClass;
    }

    /**
     * Gets the template fields and separates them into an
     * array of two keys: 'fields' and 'panels'.
     *
     * @return array
     **/
    protected function getTemplateFieldsAndPanels(): array
    {
        $templateClass = $this->getTemplateClass();
        $templateFields = [];
        $templatePanels = [];

        $handleField = function (&$field) {
            if (!empty($field->attribute)) {
                if (empty($field->panel)) {
                    $field->attribute = 'data->' . $field->attribute;
                } else {
                    $sanitizedPanel = preg_replace('/\s+/', '_', strtolower($field->panel));
                    $field->attribute = 'data->' . $sanitizedPanel . '->' . $field->attribute;
                }
            } else {
                if ($field instanceof \Laravel\Nova\Fields\Heading) {
                    return $field->hideFromDetail();
                }
            }
            if (method_exists($field, 'hideFromIndex')) {
                return $field->hideFromIndex();
            }
            return $field;
        };
        if (isset($templateClass)) {
            $rawFields = $templateClass->fields(request());
            foreach ($rawFields as $field) {
                // Handle Panel
                if ($field instanceof \Laravel\Nova\Panel) {
                    $field->data = array_map(function ($_field) use (&$handleField) {
                        return $handleField($_field);
                    }, $field->data);
                    $templatePanels[] = $field;
                    continue;
                }
                // Handle Field
                $templateFields[] = $handleField($field);
            }
        }

        return [
            'fields' => $templateFields,
            'panels' => $templatePanels,
        ];
    }
}
