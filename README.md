# zf3-jquerydatatable
zend framework3 jquery datatable

#Exemple
Controller
```php
$this->DataTable()->setConfig('152541',array(
            "columns"=>array(
                "id"=>"Id",
                "name" =>"Name",
                "address"=>"Address",
                "city"=>"City",
                "postal_code"=>"Postal code",
                "phone"=>"Phone",
                "mobile"=>"Mobile",
                "email"=>"Email"
            ),
            "limit"=>10,
            "ajax"=>true,
            "model"=>"Obj Model"
        ));
```
View 
```php
<?php echo $this->datatable("152541");?>
```