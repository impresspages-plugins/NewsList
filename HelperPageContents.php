<?php
/**
 * Created by PhpStorm.
 * User: Marijus
 * Date: 5/28/14
 * Time: 3:22 PM
 */

namespace Plugin\NewsList;

class HelperPageContents
{

    public static function getPageContent($pageId)
    {

        $revisionId = self::getRevisionId($pageId);
        if ($revisionId) {
            $pageContent = self::getWidgetsForNews($revisionId);
            return $pageContent;
        } else {
            return false;
        }

    }

    public static function getPageImage($page, $imageWidth)
    {
        $pageId = $page->getId();

        $revisionId = self::getRevisionId($pageId);
        $widgets = self::getWidgets($revisionId);

        $imageUrl = false;

        foreach ($widgets as $widget) {

            if ($widget['type'] == 'Image') {
                $imageUrl = self::scaleImage($widget['data']['imageOriginal'], $imageWidth);
                break;

            } elseif ($widget['type'] == 'Gallery') {

                if (isset($widget['data']['images'][0]['imageOriginal'])) {
                    $imageUrl = self::scaleImage($widget['data']['images'][0]['imageOriginal'], $imageWidth);
                    break;
                }
            }


        }

        return $imageUrl;

    }

    private static function scaleImage($file, $imageWidth)
    {

        if ($imageWidth){

            $options = array(
                'type' => 'fit',
                'width' => $imageWidth,
                'height' => 1000,
                'forced' => false
            );

            $thumbnail = ipReflection($file, $options);

            if (!$thumbnail) {
                return false;
            } else {
                return ipFileUrl($thumbnail);
            }

        }else{
            return ipFileUrl('file/repository/' . $file);
        }

    }

    private static function getRevisionId($pageId)
    {

        $revisionTable = ipTable('revision');
        $sql = "
                SELECT * FROM $revisionTable
                WHERE
                    `pageId` = ? AND
                    `isPublished` = 1
                ORDER BY `createdAt` DESC, `revisionId` DESC
            ";
        $revision = ipDb()->fetchRow($sql, array($pageId));
        if ($revision) {
            return $revision['revisionId'];
        } else {
            return false;
        }


    }

    private static function hasLeadBreakWidget($allWidgets)
    {

        $hasLeadBreak = false;

        foreach ($allWidgets as $widget) {
            if ($widget['type'] == 'LeadBreak') {
                $hasLeadBreak = true;
                break;
            }
        }

        return $hasLeadBreak;

    }

    private static function html2text($html)
    {

        $html2text = new \Ip\Internal\Text\Html2Text($html, false);
        $text = esc($html2text->get_text());
        $text = str_replace("\n", '<br/>', $text);

        return $text;
    }

    private static function getWidgetHeading($widget)
    {

        if (($widget['type'] == 'Heading') && isset($widget['data']['title'])) {
            $title = $widget['data']['title'];
        } else {
            $title = false;
        }

        $title = self::html2text($title);

        return $title;
    }


    private static function getWidgetText($widget)
    {
        if (($widget['type'] == 'Text') && isset($widget['data']['text'])) {
            $text = $widget['data']['text'];
        } else {
            $text = false;
        }

        return $text;
    }

    /**
     * Returns widget elements
     * @param $pageId
     */
    private static function getWidgets($publishedRevisionId, $block = 'main')
    {

        /** @var \Ip\Page $revisionId */
        $widgetRecords = ipDb()->selectAll(
            'widget', '*',
            array(
                'revisionId' => $publishedRevisionId,
                'isVisible' => 1,
                'isDeleted' => 0,
                'blockName' => $block
            ),
            'ORDER BY position ASC'
        );

        $widgetData = array();
        if (!empty($widgetRecords)) {

            foreach ($widgetRecords as $widgetRecord) {
                $widgetFiltered = self::getWidget($widgetRecord);



                if ($widgetRecord['name'] == 'Columns') {
                    if (empty($widgetFiltered['data'])) {
                        $widgetFiltered['data'] = array();
                    }
                    if (empty($widgetFiltered['data']['cols'])) {
                        $widgetFiltered['data']['cols'] = array('column' . $widgetRecord['id'] . '_1', 'column' . $widgetRecord['id'] . '_2');
                    }
                    foreach($widgetFiltered['data']['cols'] as $col) {
                        $widgetData = array_merge($widgetData, self::getWidgets($publishedRevisionId, $col));
                    }
                } else {
                    if ($widgetFiltered) {
                        $widgetData[] = $widgetFiltered;
                    }

                }
            }
        }

        return $widgetData;
    }

    public static function getWidget($widgetRecord)
    {

        if (isset($widgetRecord['name'])) {

            $widget['type'] = $widgetRecord['name'];

            if (isset($widgetRecord['skin'])) {
                $widget['layout'] = $widgetRecord['skin'];
            }

            if (isset($widgetRecord['blockName'])) {
                $widget['blockName'] = $widgetRecord['blockName'];
            }

            if (isset($widgetRecord['data'])) {
                $widget['data'] = json_decode($widgetRecord['data'], true);
                switch ($widget['type']) {
                    case 'Image':
//                        self::copyWidgetFile($widget['data']['imageOriginal']);
                        break;
                    case 'Gallery':
//                        self::copyWidgetGalleryFiles($widget['data']);
                        break;
                }
            } else {
                $widget = false;
            }

        } else {
            $widget = false;
        }

        return $widget;
    }

    private static function getWidgetsForNews($revisionId)
    {

        $allWidgets = self::getWidgets($revisionId);

        $widgets = array();

        if (self::hasLeadBreakWidget($allWidgets)) {

            $widgets = self::getContentBeforeLeadBreak($allWidgets);

        } else {

            foreach ($allWidgets as $widget) {

                $widgetText = self::getWidgetHeading($widget);
                if ($widgetText) {
                    $widgets['heading'] = $widgetText;
                    break;
                }
            }

            foreach ($allWidgets as $widget) {

                $widgetText = self::getWidgetText($widget);
                if ($widgetText) {
                    $widgets['text'] = $widgetText;
                    break;
                }
            }
        }

        return $widgets;
    }

    /**
     * Gets all text till first lead break
     * @param $allWidgets
     * @return mixed|string
     */
    private static function getContentBeforeLeadBreak($allWidgets)
    {

        $text = '';
        $heading = false;

        $cnt = 0;

        foreach ($allWidgets as $widget) {

            if (!$heading) {
                $heading = self::getWidgetHeading($widget);
            }

            $text .= self::getWidgetText($widget);
            if ($widget['type'] == 'LeadBreak') {
                break;
            }
            $cnt++;
        }

        $content['heading'] = $heading;
        $content['text'] = $text;

        return $content;
    }


}
