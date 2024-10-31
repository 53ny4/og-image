<?php

namespace s3ny4\OgImage\Template;

class TemplateManager
{
    protected static $templates = [];

    /**
     * Registers a template class with a key.
     *
     * @param string $key The key to identify the template.
     * @param string $className The fully qualified class name.
     * @throws \Exception
     */
    public static function registerTemplate(string $key, string $className)
    {

        self::$templates[$key] = $className;

        if (isset(self::$templates[$key])) {
            $className = self::$templates[$key];
            return new $className();
        } else {
            throw new \Exception("Template '$key' not found.");
        }
    }

}
