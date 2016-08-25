<?php
/**
 * User: Jenzri
 * Date: 21/08/2016
 * Time: 15:22
 */

namespace Zf3\Jquerydatatable\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\InlineScript;
use Zend\View\Helper\HeadLink;
class Datatable extends AbstractHelper
{

    private $inlineScript;

    private $datatable;

    private $headLink;

    private $sm;

    public function __construct($sm)
    {
        $this->setSm($sm);
        $this->setDatatable($sm->get('ControllerPluginManager')->get('DataTable'));
        $this->setInlineScript($sm->get('ViewHelperManager')->get("inlinescript"));
        $this->setHeadLink($sm->get('ViewHelperManager')->get("headLink"));
    }

    /**
     * @return HeadLink
     */
    public function getHeadLink()
    {
        return $this->headLink;
    }

    /**
     * @param HeadLink $headLink
     */
    public function setHeadLink($headLink)
    {
        $this->headLink = $headLink;
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
        $this->getInlineScript()->appendFile("https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js");
        $this->getInlineScript()->appendFile("https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js");
        $this->getHeadLink()->prependStylesheet("https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css");

       $config= $this->getDatatable()->getConfig($ref);
        if($config){
            $columns=$config['columns'];
            $paginator=$config['paginator'];
            $ajax=$config['ajax'];
            $lang=$config['lang'];
            $search_label=$config['search_label'];
            $ActionButtons=$config['buttons'];
            $itemlimit=$config['limit'];
            $View=new \Zend\View\Model\ViewModel();
            $View->setVariables(array(
                "columns"=>$columns,
                "paginator"=>$paginator,
                "ref"=>$ref,
                "ajax"=>$ajax,
                "lang"=>$lang,
                "ActionButtons"=>$ActionButtons,
            ));

            $html='';

            if(!$ajax){


                $script='

                      $(function(){
                        $("#datatable_'.$ref.'").dataTable({
                        "language": {
                                "url": "http://cdn.datatables.net/plug-ins/1.10.12/i18n/'.$lang.'.json"
                            }
                        });
                      })
                  ';

            }
            else{
                $script='';
                if(!empty($ActionButtons['template'])){
                    $btnhtmlTmp=$ActionButtons['template'];
                    if(!empty($ActionButtons["buttons"])){
                        foreach($ActionButtons["buttons"] as $btnkey=> $BtnActVal){
                            $btnClick=array_key_exists("click",$BtnActVal)? $BtnActVal['click']:null;
                            if($btnClick){
                                $script.=$btnClick;
                            }
                        }
                    }
                }
                $script.='

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
                            "iDisplayLength": '.$itemlimit.',
                            "language": {
                                "url": "http://cdn.datatables.net/plug-ins/1.10.12/i18n/'.$lang.'.json"
                            },
                            "processing": true,
                            "serverSide": true,
                            "ajax": { // define ajax settings
                                "url": \''.$_SERVER["REQUEST_URI"].'?ajax=true\',
                                "data": function(data) {
                                   var datafilter= $("form#JqueryDataTableFormFilter_'.$ref.'").serializeControls();
                                   $.each(datafilter, function(key, value) {
                                        data[key] = value;
                                    });
                                   console.log(datafilter);
                                }
                            },
                            "orderCellsTop": true,
                            "dom": "Bfrtip",
                             buttons: [
                                {
                                    "text": "'.$search_label.'",
                                    "className":"btn btn-default BtnjQueryDataTableFilter",
                                    "action": function ( e, dt, node, config ) {
                                        dt.ajax.reload();
                                    }
                                }
                            ]
                        });
                      })
                  ';
            }
            $this->getInlineScript()->appendScript($script,
                'text/javascript',
                array('noescape' => true));
            $html=$this->Render($View);
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