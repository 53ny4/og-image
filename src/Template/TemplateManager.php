<?php

namespace s3ny4\OgImage\Template;

class TemplateManager
{
    protected static $templates = [];

    /**
     * Registers a template class with a key.
     *
     * @param string $key       The key to identify the template.
     * @param string $className The fully qualified class name.
     */
    public static function registerTemplate($key, $className)
    {

        self::$templates[$key] = $className;
    }

    /**
     * Creates an instance of the registered template.
     *
     * @param string $key The key of the template to instantiate.
     * @return OgImageTemplateBase
     * @throws \Exception
     */
    public static function createTemplate($key)
    {

        if (isset(self::$templates[$key])) {
            $className = self::$templates[$key];
            return new $className();
        } else {
            throw new \Exception("Template '$key' not found.");
        }
    }
}
