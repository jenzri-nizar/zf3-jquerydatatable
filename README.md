# zf3-jquerydatatable
zend framework 3 jquery datatable

#Exemple
Controller
```php
$radio = new Element\Radio('rd');
$radio->setLabelAttributes(array('class' => 'col-md-4'));
$radio->setValueOptions(array(
            'Item 1' => "Item 1",
            'Item 2' => "Item 2",
            'Item 3' => "Item 3",
 ));

$range = new Element\Range('range');
$range ->setAttributes(array(
                'min'  => '0',   // default minimum is 0
                'max'  => '100', // default maximum is 100
                'step' => '5',   // default interval is 1
            ));
$range->setAttribute("class","form-control");

$Text=new \Zend\Form\Element\Text("test",[]);
$Text->setAttribute("class","form-control");

$this->DataTable()->setConfig('Album_1',array(
            "columns"=>array(
                "id"=>[
                    "label"=>"Id",
                    "search"=>
                        [
                            "element"=>$Text
                        ]

                ],
                "artist" =>[
                    "label"=>"Artist",
                    "search"=>
                        [
                            "element"=>$Text
                        ]

                ],
                "title"=>[
                    "label"=>"Title",
                    "search"=>
                        [
                            "type"=>"between",
                            "from"=>$Text,
                            "to"=>$Text
                        ]
                ],
            ),
            "lang"=>"fr",
            "limit"=>10,
            "ajax"=>true,
            "model"=>$this->getEvent()->getApplication()->getServiceManager()->get('AlbumTable'),
));

$this->DataTable()->setConfig('Album_2',array(
            "columns"=>array(
                "id"=>[
                    "label"=>"Id",
                    "search"=>
                        [
                            "element"=>$range
                        ]

                ],
                "artist" =>[
                    "label"=>"Artist",
                    "search"=>
                        [
                            "element"=>$radio
                        ]

                ],
                "title"=>[
                    "label"=>"Title",
                    "search"=>
                        [
                            "element"=>$Text

                        ]
                ],
            ),
            "search_label"=>"Recherche",
            "lang"=>"fr",
            "limit"=>10,
            "ajax"=>true,
            "model"=>$this->getEvent()->getApplication()->getServiceManager()->get('AlbumTable'),
        ));
```
View 
```php
<?php echo $this->datatable("Album_1");?>
<?php echo $this->datatable("Album_2");?>
```


#Resulta
![alt tag](https://raw.githubusercontent.com/jenzri-nizar/zf3-jquerydatatable/master/assets/screenshot_1.PNG)
![alt tag](https://raw.githubusercontent.com/jenzri-nizar/zf3-jquerydatatable/master/assets/screenshot_2.PNG)
