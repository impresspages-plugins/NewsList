<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: mangirdas
 * Date: 6/24/14
 * Time: 4:25 PM
 */

namespace Plugin\NewsList;

class AdminController
{

    /**
     * news.js ask to provide widget management popup HTML. This controller does this.
     * @return \Ip\Response\Json
     * @throws \Ip\Exception\View
     */
    public function widgetPopupHtml()
    {
        $widgetId = ipRequest()->getQuery('widgetId');
        $widgetRecord = \Ip\Internal\Content\Model::getWidgetRecord($widgetId);
        $widgetData = $widgetRecord['data'];

        //create form prepopulated with current widget data
        $form = $this->managementForm($widgetData);

        //Render form and popup HTML
        $viewData = array(
            'form' => $form
        );
        $popupHtml = ipView('view/editPopup.php', $viewData)->render();
        $data = array(
            'popup' => $popupHtml
        );
        //Return rendered widget management popup HTML in JSON format
        return new \Ip\Response\Json($data);
    }


    /**
     * Check widget's posted data and return data to be stored or errors to be displayed
     */
    public function checkForm()
    {
        $data = ipRequest()->getPost();
        $form = $this->managementForm();
        $data = $form->filterValues($data); //filter post data to remove any non form specific items
        $errors = $form->validate($data); //http://www.impresspages.org/docs/form-validation-in-php-3
        if ($errors) {
            //error
            $data = array (
                'status' => 'error',
                'errors' => $errors
            );
        } else {
            //success
            unset($data['aa']);
            unset($data['securityToken']);
            unset($data['antispam']);
            $data = array (
                'status' => 'ok',
                'data' => $data

            );
        }
        return new \Ip\Response\Json($data);
    }

    protected function managementForm($widgetData = array())
    {
        $form = new \Ip\Form();

        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);

        //setting hidden input field so that this form would be submitted to 'errorCheck' method of this controller. (http://www.impresspages.org/docs/controller)
        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'aa',
                'value' => 'NewsList.checkForm'
            )
        );
        $form->addField($field);

        //Input fields to adjust widget settings

        $field = new \Ip\Form\Field\Number(
            array(
                'name' => 'imageWidth',
                'label' => __('Image width', 'NewsList', false),
                'value' => empty($widgetData['imageWidth']) ? 200 : $widgetData['imageWidth'],
                'validators' => array('Required')
            )
        );
        $form->addField($field);

        $menus = \Ip\Internal\Pages\Service::getMenus(ipContent()->getCurrentLanguage()->getCode());
        $values = array();
        foreach ($menus as $menu) {
            $values[] = array($menu['title'], $menu['alias']);
        }

        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'menu',
                'label' => __('Menu', 'NewsList', false),
                'value' => empty($widgetData['menu']) ? null : $widgetData['menu'],
                'values' => $values
            )
        );
        $form->addField($field);

        $field = new \Ip\Form\Field\Number(
            array(
                'name' => 'maxItems',
                'label' => __('Max. number of items to be displayed', 'NewsList', false),
                'value' => empty($widgetData['maxItems']) ? null : $widgetData['maxItems']
            )
        );
        $form->addField($field);

        //ADD YOUR OWN FIELDS

        return $form;
    }



}
