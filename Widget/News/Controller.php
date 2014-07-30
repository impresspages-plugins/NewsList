<?php
/**
 * @package   ImpressPages
 */


namespace Plugin\NewsList\Widget\News;

class Controller extends \Ip\WidgetController{
    public function getTitle() {
        return __('News', 'NewsList');
    }

    /**
     * Renders widget's HTML output
     *
     * You can extend this method when generating widget's HTML.
     *
     * @param int $revisionId Widget revision ID
     * @param int $widgetId Widget ID
     * @param int $widgetId Widget instance ID
     * @param array $data Widget data array
     * @param string $skin Skin name
     * @return string Widget's HTML code
     */

    /**
     * Renders widget's HTML output
     *
     * You can extend this method when generating widget's HTML.
     *
     * @param int $revisionId Widget revision ID
     * @param int $widgetId Widget ID
     * @param int $widgetId Widget instance ID
     * @param array $data Widget data array
     * @param string $skin Skin name
     * @return string Widget's HTML code
     */

    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {
        if (!isset($data['menu'])) {
            $data['menu'] = '';
        }
        if (!isset($data['maxItems'])) {
            $data['maxItems'] = null;
        }
        if (!isset($data['imageWidth'])) {
            $data['imageWidth'] = null;
        }
        return parent::generateHtml($revisionId, $widgetId, $data, $skin);
    }
}
