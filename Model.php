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
    public static function getNews()
    {

        if (ipGetOption('NewsList.imageWidth')) {
            $data['imgWidth'] = ipGetOption('NewsList.imageWidth');
        }

        $menus = self::getMenuNames();

        $items = Array();

        foreach ($menus as $menu) {
            $items = array_merge($items, self::getNewsItems($menu));
        }

        $data['items'] = $items;

        return ipView('view/news.php', $data)->render();

    }

    private static function getMenuNames()
    {

        $cfgMenuString = ipGetOption('NewsList.menuList');
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

    private static function getDateFromStr($strTime){

        $timestamp = strtotime($strTime);
        return date('Y-m-d', $timestamp);

    }

    private static function getNewsItems($menu)
    { //TODOX

        $pages = self::getLatestMenuPages($menu, ipGetOption('NewsList.maxItems'));


        $items = array();

        foreach ($pages as $page) {


            $item['linkUrl'] = $page->getLink();



            $item['heading'] = $page->getTitle();

            $item['imgUrl'] = HelperPageContents::getPageImage($page);
            $item['altText'] = $page->getTitle();

            $pageContent = HelperPageContents::getPageContent($page->getId());

            if (isset($pageContent['text'])){
                $item['text'] = $pageContent['text'];
            }else{
                $item['text'] = '';
            }

            $item['createdAt'] =  self::getDateFromStr($page->getCreatedAt());

            $items[] = $item;

        }

        return $items;
    }


    private static function getLatestMenuPages($menuName, $numberOfPages)
    {

        $currLanguage = ipContent()->getCurrentLanguage()->getCode();

        $menu = ipDb()->selectRow('page', array('id', 'alias'), array('alias' => $menuName, 'parentId' => 0, 'languageCode' => $currLanguage));
        $children = ipContent()->getChildren($menu['id'], 0, $numberOfPages);

        return $children;
    }


}
