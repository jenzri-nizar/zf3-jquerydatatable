<?php
/**
 * Created by PhpStorm.
 * User: Jenzri
 * Date: 21/08/2016
 * Time: 15:15
 */

namespace Datatable\Controller\Plugin;

use Zend\Db\Sql\Where;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class DataTable  extends AbstractPlugin
{

    private $Configs;

    private $Adapter;


    /**
     * @return \Zend\Db\Adapter\AdapterInterface;
     */
    public function getAdapter()
    {
        return $this->Adapter;
    }

    /**
     * @param \Zend\Db\Adapter\AdapterInterface; $Adapter
     */
    public function setAdapter(\Zend\Db\Adapter\AdapterInterface $Adapter)
    {
        $this->Adapter = $Adapter;
    }


    /**
     * @return mixed
     */
    public function getConfig($ref)
    {
        if(array_key_exists($ref,$this->Configs)){
           return $this->Configs[$ref];
        }
        return null;
    }

    /**
     * @param mixed $Config
     */

    public function setConfig($ref,$Config)
    {
        $defaults = [
                        'ajax'=>false,
                        'columns' => [],
                        'model' => null
        ];
        $Config = array_merge($defaults, $Config);
        if(empty($Config['columns'])){
            throw  new \Exception('Error columns');
        }
        if(! is_object($Config['model']) || !($Config['model'] instanceof  \Zend\Db\TableGateway\AbstractTableGateway)){
            throw  new \Exception('Error Model');
        }
        else{
            if(!method_exists ($Config['model'],'DatatableSearch')){
                throw  new \Exception('Error DatatableSearch not found');
            }
            else{
               $DataSelect= $Config['model']->DatatableSearch();
                if(!$DataSelect || !($DataSelect instanceof  \Zend\Db\Sql\Select)){
                    throw  new \Exception('Error DatatableSearch not \Zend\Db\Sql\Select');
                }

                $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                $paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect(
                // our configured select object
                    $DataSelect,
                    // the adapter to run it against
                    $this->getAdapter(),
                    // the result set to hydrate
                    $resultSetPrototype
                );

                $DataPaginator = new \Zend\Paginator\Paginator($paginatorAdapter);


                $DataPaginator->setCurrentPageNumber(1);
                $DataPaginator->setDefaultItemCountPerPage(array_key_exists("limit",$Config) ? $Config['limit']:10);
                $this->SetDataPaginator($ref,$DataPaginator);
                $Config['paginator']=$DataPaginator;
            }
        }

        $this->Configs[$ref] = $Config;
        //$paginator=new \Zend\Paginator\Paginator();
        //$paginator->getAdapter();
        if(isset($_GET['ajax']) && $_GET['ajax']=="true"){
            $this->AjaxCall($Config);
            exit;
        }
    }


    private function AjaxCall($Config){

            $columnsKey=array_keys($Config['columns']);
            $DataSelect= $Config['model']->DatatableSearch();
            $order=$_GET['order'];
            $SqlChanged=false;
            if(isset($order[0]['column']) && isset($order[0]['dir'])){
                $SqlChanged=true;
                $DataSelect->order($columnsKey[$order[0]['column']]." ".$order[0]['dir']);
            }
            $search=$_GET['search'];
            if($search['value']){
                $PredicateSet=[];
                $search=$search['value'];
                foreach($columnsKey as $key){
                    $PredicateSet[]= new \Zend\Db\Sql\Predicate\Like("$key",  "%$search%");
                }
                $DataSelect->where->addPredicate(new \Zend\Db\Sql\Predicate\PredicateSet(
                    $PredicateSet,
                    \Zend\Db\Sql\Predicate\PredicateSet::COMBINED_BY_OR
                ));
            }
            if($_GET['inputfilter']){

               $Where=$this->AjaxFilter($_GET['inputfilter'],$Config);
                if(!is_null($Where)){
                    $DataSelect->where->addPredicate($Where);
                    $SqlChanged=true;
                   //echo $DataSelect->getSqlString($this->getAdapter()->getPlatform());
                }
            }
            if($SqlChanged){
                // echo $DataSelect->getSqlString($this->getAdapter()->getPlatform());
                $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                $paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect(
                // our configured select object
                    $DataSelect,
                    // the adapter to run it against
                    $this->getAdapter(),
                    // the result set to hydrate
                    $resultSetPrototype
                );

                $DataPaginator = new \Zend\Paginator\Paginator($paginatorAdapter);
                $Config['paginator']=$DataPaginator;
            }

            $data=[];
            $data["data"]=[];
            $paginator=$Config['paginator'];
            $page=isset($_GET['start'])&& is_numeric($_GET['start']) ? $_GET['start'] :0;
            $length=isset($_GET['length'])&& is_numeric($_GET['length']) ? $_GET['length'] :10;
            $page=($page/$length)+1;
            $paginator->setCurrentPageNumber($page);
            $paginator->setDefaultItemCountPerPage($length);
            try{
                foreach($paginator as $item){
                    $_item=[];
                    foreach($columnsKey as $key){
                        if(is_object($item) &&  property_exists ($item,$key)){
                            $_item[]= $item->{$key};
                        }
                        else if(is_array($item) &&  array_key_exists ($key,$item)){
                            $_item[]= $item[$key];
                        }
                        else{
                            $_item[]="";
                        }

                    }
                    $data["data"][]=$_item;
                }
            }
            catch(\Exception $e){

            }


            $data["draw"]=isset($_GET['start']) ? $_GET['draw']:1;
            $data["recordsFiltered"]=$paginator->getTotalItemCount();
            $data["recordsTotal"]=$paginator->getTotalItemCount();
            echo json_encode($data);
            exit;

    }

    private function AjaxFilter($param,$Config){
        $Where=new Where();
        $ArrayInput=array();
        $IsWhere=false;
        foreach($Config['columns'] as $key =>$val)
        {
            $ArrayInput[$key]=[];
            if(array_key_exists("search",$val) &&( array_keys($val['search']) !== range(0, count($val['search']) - 1))){
                $ArrayInput[$key]=$val['search'];
            }
        }

        if(!empty($ArrayInput)){
            foreach($ArrayInput as $key => $settingSearch){
                if(array_key_exists($key,$param) && !empty($param[$key])){
                    $filtertype=strtolower (array_key_exists("type",$settingSearch) ? $settingSearch['type'] :"text");
                    if($filtertype=="between"){
                        $from=array_key_exists("from",$param[$key])? $param[$key]['from']:"";
                        $to=array_key_exists("to",$param[$key])? $param[$key]['to']:"";

                        if($from && $to){
                            $Where->between($key, $from, $to);
                            $IsWhere=true;
                        }
                    }
                    else{
                        $element =array_key_exists("element",$settingSearch) ? $settingSearch['element'] :null;
                        if($element instanceof \Zend\Form\Element) {
                            $filtertype = $element->getAttribute("type");
                            if($filtertype=="multi_checkbox"){
                                $in=$param[$key];
                                if(!is_array($in)){
                                    $in=[$in];
                                }
                                $Where->in($key,$in);
                                $IsWhere=true;
                            }
                            else{
                                $value=$param[$key];
                                $Where->like($key,"%".strtolower (trim($value))."%");
                                //$Where->equalTo($key,trim($value));
                                $IsWhere=true;
                            }
                        }
                    }
                }
            }
        }
        if($IsWhere)
            return $Where;

        return null;
    }
    private function SetDataPaginator($ref,$DataPaginator){
        $this->Configs[$ref]['paginator']=$DataPaginator;
    }

}