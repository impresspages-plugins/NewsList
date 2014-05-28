<?php
namespace Plugin\NewsList;

class Slot
{

    public static function NewsList()
    {
        return Model::getNews();
    }

}