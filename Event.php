<?php
/**
 * Load JavaScript file.
 */
namespace Plugin\NewsList;

class Event
{
    public static function ipBeforeController()
    {
        ipAddCss('assets/newsList.css');
    }

}
