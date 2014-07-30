<?php
namespace Plugin\NewsList;

class Slot
{

    public static function NewsList($params)
    {
        if (!isset($params['maxItems']) || $params['maxItems'] == '') {
            $params['maxItems'] = null;
        }
        if (!isset($params['imageWidth'])) {
            $params['imageWidth'] = null;
        }
        return Model::getNews($params['menu'], $params['maxItems'], $params['imageWidth']);
    }

}
