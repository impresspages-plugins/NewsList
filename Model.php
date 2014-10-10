<?php
/**
 * Created by PhpStorm.
 * User: Marijus
 * Date: 5/28/14
 * Time: 1:35 PM
 */

namespace Plugin\NewsList;

class Model
{
    public static function getNews($menu, $maxItems, $imageWidth)
    {

        if ($imageWidth) {
            $data['imageWidth'] = $imageWidth;
        }
        if ($maxItems == '') {
            $maxItems = null;
        }

        $menus = self::getMenuNames($menu); //support of menu names separated by comas

        $items = Array();

        foreach ($menus as $menu) {
            $items = array_merge($items, self::getNewsItems($menu, $maxItems, $imageWidth));
        }


        $data['items'] = $items;

        return ipView('view/news.php', $data)->render();

    }

    private static function getMenuNames($menu)
    {

        $cfgMenuString = $menu;
        if ($cfgMenuString){

            $cfgMenuString = str_replace(" ", "", $cfgMenuString);
            $menus = explode(',', $cfgMenuString);

        }else{

            $currLanguage = ipContent()->getCurrentLanguage()->getCode();
            $menusObj = \Ip\Internal\Pages\Service::getMenus($currLanguage );

            $menus = array();
            foreach ($menusObj as $menuObj){
                $menus[] = $menuObj['alias'];
            }

        }

        return $menus;
    }



    private static function getNewsItems($menu, $maxItems, $imageWidth)
    {

        $pages = self::getLatestMenuPages($menu, $maxItems);


        $items = array();

        foreach ($pages as $page) {


            $item['linkUrl'] = $page->getLink();



            $item['heading'] = $page->getTitle();

            $item['imgUrl'] = HelperPageContents::getPageImage($page, $imageWidth);
            $item['altText'] = $page->getTitle();

            $pageContent = HelperPageContents::getPageContent($page->getId());

            if (isset($pageContent['text'])){
                $item['text'] = Html2Text::convert($pageContent['text']);
            }else{
                $item['text'] = '';
            }

            $item['createdAt'] = ipFormatDate(strtotime($page->getCreatedAt()), 'NewsList');

            $items[] = $item;

        }

        return $items;
    }


    private static function getLatestMenuPages($menuName, $numberOfPages)
    {

        $currLanguage = ipContent()->getCurrentLanguage()->getCode();

        $menu = ipDb()->selectRow('page', array('id', 'alias'), array('alias' => $menuName, 'parentId' => 0, 'languageCode' => $currLanguage));
        if ($numberOfPages === null) {
            $children = ipContent()->getChildren($menu['id']);
        } else {
            $children = ipContent()->getChildren($menu['id'], 0, $numberOfPages);
        }


        return $children;
    }


}
