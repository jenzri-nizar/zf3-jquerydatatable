# zf3-jquerydatatable
zend framework 3 jquery datatable

##Installation

1) Ajouter l'exigence suivante à votre fichier composer.json.
Dans la section:"require"

```php
"jenzri-nizar/zf3-jquerydatatable": "^1.0"
```
2) Ouvrez votre ligne de commande et exécutez

```php
composer update
```

Le module doit être enregistré dans **config/modules.config.php**
```php
'modules' => array(
    '...',
    'Zend\Paginator',
    'Zend\Db',
    'Zf3\Jquerydatatable'
),
```

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
                'min'  => '0',
                'max'  => '100',
                'step' => '5',   
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


#Resultat
![alt tag](https://raw.githubusercontent.com/jenzri-nizar/zf3-jquerydatatable/master/assets/screenshot_1.PNG)
![alt tag](https://raw.githubusercontent.com/jenzri-nizar/zf3-jquerydatatable/master/assets/screenshot_2.PNG)

#Exemple 2
Controller
```php
$this->DataTable()->setConfig('Album_2',array(
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
            "limit"=>5,
            "ajax"=>true,
            "model"=>$this->getEvent()->getApplication()->getServiceManager()->get('AlbumTable'),
            "buttons"=>[
                "class"=>"btn-buttons",
                "template" => "{update} {delete}",
                "buttons" => [
                    "delete"=>[
                        "url"=>"#/id={id}",
                        "click"=>"$(document).on('click','.ClassDeleteAjax',function(){alert('test');return false;});",
                        "html"=>"<span class='btn btn-danger glyphicon glyphicon-trash'></span>",
                        "class"=>"ClassDeleteAjax",
                        "attr"=>[
                            "data-id"=>"{id}",
                            "data-text"=>"Text",
                            "data-artist"=>"{artist}",
                        ]
                    ],
                    "update" => [
                        "url"=>"#/id={id}&artist={artist}",
                        "click"=>"",
                        "html"=>"<span class='btn btn-success glyphicon glyphicon-edit'></span>",
                        "class"=>"ClassUpdate",
                        "attr"=>[
                            "data-id"=>"{id}",
                            "data-text"=>"Test",
                        ]
                    ],
                ],
            ]
        ));
```

#Resultat
![alt tag](https://raw.githubusercontent.com/jenzri-nizar/zf3-jquerydatatable/master/assets/screenshot_3.PNG)
