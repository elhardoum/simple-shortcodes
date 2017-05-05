<?php

namespace App;

class Shortcodes
{
    private $shortcodes;

    public static function instance() {
        static $instance = null;
        
        if ( null === $instance ) {
            $instance = new Shortcodes;
            $instance->setup();
        }

        return $instance;
    }

    private function setup()
    {
        $this->shortcodes = (array) $this->shortcodes;
    }

    public function register($tag, $callback)
    {
        $this->shortcodes[$tag] = $callback;
    }

    public function removeAll($tag)
    {
        unset($this->shortcodes[$tag]);
    }

    public function doShortcode($content)
    {
        if ( !$this->shortcodes )
            return $content;

        foreach ( $this->shortcodes as $code=>$handle ) {
            $GLOBALS['ShortcodeHandle'] = $handle;
            $content = preg_replace_callback($this->regex($code), array($this, 'parse'), $content);
            unset($GLOBALS['ShortcodeHandle']);
        }

        return $content;
    }

    private function regex($code)
    {
        $regex = "/\[$code(.*?)?\](?:(.+?)?\[\/$code\])?/si";

        return $regex;
    }

    private function parse($matches)
    {
        global $ShortcodeHandle;

        $atts = $this->getAttributes(isset($matches[1]) ? $matches[1] : null);
        $content = $this->getContent(isset($matches[2]) ? $matches[2] : null);

        return call_user_func_array($ShortcodeHandle, array($atts, $content));
    }

    private function getAttributes($raw)
    {
        if ( !$raw || !trim($raw) )
            return array();

        $raw = preg_replace('/(\' |" )/si', ' ', $raw);
        $raw = preg_replace('/(="|=\')/si', '=', $raw);
        $raw = preg_replace('/("$|\'$)/si', '', $raw);
        $raw = trim($raw);
        $raw = preg_replace('/\s/si', '&', $raw);

        parse_str($raw, $atts);
        return $atts;
    }

    private function getContent($raw)
    {
        return trim($raw);
    }
}