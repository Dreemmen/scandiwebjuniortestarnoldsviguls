<?php
//ob_start() so we can use redirect after this file inclusion
ob_start();

Global $_db;
Global $_shop;

$warnings = array('sku'=>false,'name'=>false,'price'=>false,'dublicate'=>false,
    'data_size'=>false,'item_height'=>false,'item_width'=>false,'item_length'=>false,'item_weight'=>false);
$params = array();
//requaried fields
$fields = array('sku'=>'','name'=>'','price'=>'','productType'=>'DVD','data_size'=>'','item_height'=>'','item_width'=>'','item_length'=>'','item_weight'=>'');

/* Validation - each unique case */
if(!empty($_POST['submit_condition'])){
            unset($_POST['submit_condition']);
    //sku
    if(!empty($_POST['sku']) && is_numeric($_POST['sku']) && $_POST['sku'] > 0){
        $fields['sku'] = $_POST['sku'];
        
        //test for dublicate
        $querry = $_db->handle->prepare("SELECT count(*) FROM `preces` WHERE `sku` = ? ;");
        $querry->bind_param("d", $_POST['sku']);
        $querry->execute();
        $querry->bind_result($count);
        $querry->fetch(); 
        $querry->close();
        
        if(!empty($count)){
            $warnings['dublicate'] = true;
        }
        
    }else{
        $warnings['sku'] = true;
        unset($_POST['submit_condition']);        
    }
    //name
    if(!empty($_POST['name'])){
        $fields['name'] = $_POST['name'];
    }else{
        $warnings['name'] = true;
        unset($_POST['submit_condition']);        
    }
    //price
    if(!empty($_POST['price']) && is_numeric($_POST['price']) && $_POST['price'] >= 0){
        $fields['price'] = $_POST['price'];
    }else{
        $warnings['price'] = true;
        unset($_POST['submit_condition']);        
    }
    //productType
    if(!empty($_POST['productType']) && in_array($_POST['productType'], array('DVD','Furniture','Book'))  ){
        $fields['productType'] = $_POST['productType'];
    }else{
        unset($_POST['submit_condition']);
    }
    
    // šitos vajag atsevišķi definēt, jo citā tabulā
    switch($fields['productType']):
        case 'DVD':
            $params[] = 'data_size';
        break;
        case 'Furniture':
            $params[] = 'item_height';
            $params[] = 'item_width';
            $params[] = 'item_length';
        break;
        case 'Book':
            $params[] = 'item_weight';
        break;
    endswitch;

    //numeric parametres validation
    foreach ($params as $par){
        if(!isset($_POST[$par]) || $_POST[$par] <= 0 || !is_numeric($_POST[$par])){
            $warnings[$par] = true;
        }else{
            $fields[$par] = $_POST[$par];
        }
    }
        $querry = $_db->handle->prepare("INSERT INTO `preces`(`sku`,`name`,`price`,`productType`) VALUES (?, ?, ?, ?);");
            $querry->bind_param("dsds", $fields['sku'], $fields['name'], $fields['price'], $fields['productType']);
            $querry->execute();
        $querry->close();

        $querry = $_db->handle->prepare("INSERT INTO `preces_parametri`(`p_id`,`name`,`value`) VALUES(?, ?, ?);");
        for ($i = 0; $i < count($params); ++$i){
            $querry->bind_param("dss", $fields['sku'], $params[$i], $fields[$params[$i]]);
            $querry->execute();
        }
        $querry->close();

        header("Location: http://" . $_SERVER['HTTP_HOST'] . '/' . WEBISTE_FOLDER_NAME);
        unset($_POST['submit_condition']);
        die();
}
//var_dump($warnings);
?>
<div>
    <div class="h0-title">Product Add</div>
    <nav class="top-nav">
        <a id="save-product-btn" href="#">Save</a>
        <a id="cancel-product-btn" href="<?php echo "http://" . $_SERVER['HTTP_HOST'] . '/' . WEBISTE_FOLDER_NAME; ?>">Cancel</a>
    </nav>
    <div style="clear:both"></div>
</div>
<form id="product_form" action="" method="post">
    <div class="form_line">
        <label for="sku">SKU</label>
        <input name="sku" id="sku" type="number" min="0.000001" value="<?=($warnings['sku'])?'':$fields['sku']?>" required />
        <div id="sku_wmsg" class="warning_msg <?=($warnings['sku'] || $warnings['dublicate'])?'':'hidden'?>">
            <?php if($warnings['dublicate']) echo "Provided SKU value already exists."; else echo "Required. Please, provide valid SKU value.";?>            
        </div>
    </div>
    <div class="form_line">
        <label for="name" >Name</label>
        <input name="name" id="name" type="text" value="<?=($warnings['name'])?'':$fields['name']?>" required />
        <div id="name_wmsg" class="warning_msg <?=($warnings['name'])?'':'hidden'?>"">Required. Please, provide valid name.</div>
    </div>
    <div class="form_line">
        <label for="price" >Price ($)</label>
        <input name="price" id="price" type="number" min="0.000001" value="<?=($warnings['price'])?'':$fields['price']?>" required/>
        <div id="price_wmsg" class="warning_msg <?=($warnings['price'])?'':'hidden'?>"">Required. Please, provide valid Price ($).</div>
    </div>
    <div class="form_line">
        <label for="productType">Type Switcher</label>
	<select name="productType" id='productType'>
		<option value="DVD" <?=($fields['productType'] == 'DVD')?'selected':''?>>DVD</option>
		<option value="Furniture" <?=($fields['productType'] == 'Furniture')?'selected':''?>>Furniture</option>
		<option value="Book" <?=($fields['productType'] == 'Book')?'selected':''?>>Book</option>
	</select>
    </div>
    <div id="type_fields">
        <div id="DVD" class="<?=($fields['productType'] == 'DVD')?'visible':'hidden'?>">
            <div class="form_line">
                <label for="data_size" >Size (Mb)</label>
                <input name="data_size" id="size" type="number" min="0.000001" value="<?=($warnings['data_size'])?'':$fields['data_size']?>" required />
                <div id="data_size_wmsg" class="warning_msg <?=($warnings['data_size'])?'':'hidden'?>">Required. Please, provide valid Size (MB).</div>
            </div>
            <div class="param_descr">Please, provide DVD's size in megabytes (MB). 1 GB = 1000 MB</div>
        </div>
        <div id="Furniture" class="<?=($fields['productType'] == 'Furniture')?'visible':'hidden'?>">
            <div class="form_line">
                <label for="item_height" >Height (CM)</label>
                <input name="item_height" id="height" type="number" min="0.000001" value="<?=($warnings['item_height'])?'':$fields['item_height']?>" />
                <div id="item_height_wmsg" class="warning_msg <?=($warnings['item_height'])?'':'hidden'?>">Required. Please, provide valid Height (CM).</div>
            </div>
            <div class="form_line">
                <label for="item_width" >Width (CM)</label>
                <input name="item_width" id="width" type="number" min="0.000001" value="<?=($warnings['item_width'])?'':$fields['item_width']?>" />
                <div id="item_width_wmsg" class="warning_msg <?=($warnings['item_width'])?'':'hidden'?>">Required. Please, provide valid Width (CM).</div>
            </div>
            <div class="form_line">
                <label for="item_length" >Length (CM)</label>
                <input name="item_length" id="length" type="number" min="0.000001" value="<?=($warnings['item_length'])?'':$fields['item_length']?>" />
                <div id="item_length_wmsg" class="warning_msg <?=($warnings['item_length'])?'':'hidden'?>">Required. Please, provide valid Length (CM).</div>
            </div>
            <div class="param_descr">Please, provide dimensions in HxWxL format in centimetres</div>
        </div>
        <div id="Book" class="<?=($fields['productType'] == 'Book')?'visible':'hidden'?>">
            <div class="form_line">
                <label for="item_weight" >Weight (KG)</label>
                <input name="item_weight" id="weight" type="number" min="0.000001" value="<?=($warnings['item_weight'])?'':$fields['item_weight']?>" />
                <div id="item_weight_wmsg" class="warning_msg <?=($warnings['item_weight'])?'':'hidden'?>">Required. Please, provide valid Weight (KG).</div>
            </div>
            <div class="param_descr">Please, provide book's weight in kilograms (KG)</div>
        </div>
    </div>
      <input name="submit_condition" type="hidden" value="1" />
</form>
<script>
    // function changes input field visbility depending on chosen product type
    document.getElementById('productType').onchange = function (){
        var elements = document.getElementById('type_fields').getElementsByClassName('visible');
        
        elements[0].classList.add('hidden');
        elements[0].classList.remove('visible');
        
        // this.value = id
        document.getElementById(this.value).classList.add('visible');
        document.getElementById(this.value).classList.remove('hidden');
        
        //make so only required inputs are of selected product type
        var type_field_inputs = document.getElementById('type_fields').getElementsByTagName('input');
        for(var i = 0; i <= type_field_inputs.length - 1; i++){
            if(type_field_inputs[i].hasAttribute('required')) type_field_inputs[i].removeAttribute('required'); //remove from all
        }
        var new_inputs = document.getElementById(this.value).getElementsByTagName('input');
        for(var i = 0; i <= new_inputs.length - 1; i++){
            new_inputs[i].setAttribute('required', 'required'); //add for specific (id = this.value)
        }
    }
    
    document.getElementById('save-product-btn').onclick = function (){
        var ok = true;
        
        //find inputs, check if required and value not null
        var form_inputs = document.getElementById('product_form').getElementsByTagName('input');
        for(var i = 0; i <= form_inputs.length - 1; i++){
            if(form_inputs[i].getAttribute('name') == 'submit_condition') continue; //exception
            document.getElementById(form_inputs[i].getAttribute('name') + '_wmsg').classList.add('hidden');
            document.getElementById(form_inputs[i].getAttribute('name') + '_wmsg').classList.remove('visible_ib');
            if(form_inputs[i].hasAttribute('required')){
                //numerical
                if(form_inputs[i].type == 'number' && form_inputs[i].value <= 0){
                    ok = false;
                    document.getElementById(form_inputs[i].getAttribute('name') + '_wmsg').classList.add('visible_ib');
                    document.getElementById(form_inputs[i].getAttribute('name') + '_wmsg').classList.remove('hidden');
                }
                //text
                if(form_inputs[i].type == 'text' && form_inputs[i].value == ''){
                    ok = false;
                    document.getElementById(form_inputs[i].getAttribute('name') + '_wmsg').classList.add('visible_ib');
                    document.getElementById(form_inputs[i].getAttribute('name') + '_wmsg').classList.remove('hidden');
                }
            }
        }
        
        if(true) document.getElementById('product_form').submit();
    }
</script>