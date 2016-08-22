<?php
/**
 * User: Jenzri
 * Date: 21/08/2016
 * Time: 15:22
 */

namespace Datatable\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\InlineScript;
class Datatable extends AbstractHelper
{

    private $inlineScript;

    private $datatable;

    private $sm;
    public function __construct($sm)
    {
        $this->setSm($sm);
        $this->setDatatable($sm->get('ControllerPluginManager')->get('DataTable'));
        $this->setInlineScript($sm->get('ViewHelperManager')->get("inlinescript"));
    }

    /**
     * @return InlineScript
     */
    public function getInlineScript()
    {
        return $this->inlineScript;
    }

    /**
     * @param InlineScript $inlineScript
     */
    public function setInlineScript($inlineScript)
    {
        $this->inlineScript = $inlineScript;
    }

    /**
     * @return mixed
     */
    public function getDatatable()
    {
        return $this->datatable;
    }

    /**
     * @param mixed $datatable
     */
    public function setDatatable($datatable)
    {
        $this->datatable = $datatable;
    }

    /**
     * @return mixed
     */
    public function getSm()
    {
        return $this->sm;
    }

    /**
     * @param mixed $sm
     */
    public function setSm($sm)
    {
        $this->sm = $sm;
    }


    public function __invoke($ref)
    {
       $config= $this->getDatatable()->getConfig($ref);
        if($config){
            $columns=$config['columns'];
            $paginator=$config['paginator'];
            $ajax=$config['ajax'];
            $View=new \Zend\View\Model\ViewModel();
            $View->setVariables(array(
                "columns"=>$columns,
                "paginator"=>$paginator,
                "ref"=>$ref,
                "ajax"=>$ajax
            ));

            $html='<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
                <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
 ';
            if(!$ajax){
                $html.='
                   <script>
                      $(function(){
                        $("#datatable_'.$ref.'").dataTable();
                      })
                  </script>';

            }
            else{
                $html.='
                   <script>
                      $(function(){
                        $("#datatable_'.$ref.'").dataTable({
                            "processing": true,
                            "serverSide": true,
                            "ajax": \''.$_SERVER["REQUEST_URI"].'?ajax=true\'
                        });
                      })
                  </script>';
            }
            $html.=$this->Render($View);
            return $html;
        }
        return "";
    }

    private function Render(\Zend\View\Model\ViewModel $View){
        $renderer=new \Zend\View\Renderer\PhpRenderer();
        $renderer->setHelperPluginManager($this->getSm()->get('ViewRenderer')->getHelperPluginManager());
        $resolver = new \Zend\View\Resolver\TemplateMapResolver([
            "DataTable"=>__dir__ ."/../../../view/datatable/datatable.phtml"
        ]);
        $View->setTemplate("DataTable");
        $renderer->setResolver($resolver);
        return $renderer->render($View);
    }
}