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


    public function __invoke($ref=null)
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
                   $.fn.serializeControls=function(){
	var keyindex=0;

	var data={};function buildInputObject(arr,val){if(arr.length<1)
return val;var objkey=arr[0];if(objkey.slice(-1)=="]"){objkey=objkey.slice(0,-1);}
var result={};if(arr.length==1){
	if(!objkey){
		objkey=keyindex;
	}
	result[objkey]=val;
	}else{
	arr.shift();
	var nestedVal=buildInputObject(arr,val);
	result[objkey]=nestedVal;
	}
return result;
}
$.each(this.serializeArray(),function(){
	var val=this.value;
	var c=this.name.split("[");
	var a=buildInputObject(c,val);
	$.extend(true,data,a);
	keyindex++;
});return data;}


                      $(function(){
                        $("#datatable_'.$ref.'").dataTable({
                            "processing": true,
                            "serverSide": true,
                            "ajax": { // define ajax settings
                                "url": \''.$_SERVER["REQUEST_URI"].'?ajax=true\',
                                "data": function(data) {
                                   var datafilter= $("form#JqueryDataTableFormFilter").serializeControls();
                                   $.each(datafilter, function(key, value) {
                                        data[key] = value;
                                    });
                                   console.log(datafilter);
                                }
                            },
                            //"ajax": \''.$_SERVER["REQUEST_URI"].'?ajax=true\',
                            "orderCellsTop": true

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