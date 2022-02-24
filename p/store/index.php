<?php
    if(!defined('WEBISTE_FOLDER_NAME')) die('d');
    // ###
    Global $_db;
    Global $_shop;
    
if(!empty($_POST['submit_condition']) && isset($_POST['nameOfChoice'])){
    unset($_POST['submit_condition']);
    foreach ($_POST['nameOfChoice'] as $key => $value){
        if(!is_numeric($value)) unset($_POST['nameOfChoice'][$key]);
    }
    
    If(count($_POST['nameOfChoice']) > 0) $_db->handle->query("DELETE FROM `preces` WHERE `sku` IN (". join(', ', $_POST['nameOfChoice']) .")");
}
?>
<div>
    <div class="h0-title">Product List</div>
    <nav class="top-nav">
        <a id="add-product-btn" href="add-product">ADD</a>
        <a id="delete-product-btn" onclick="mass_delete()">MASS DELETE</a>
    </nav>
    <div style="clear:both"></div>
</div>
<form id="mass_delete_form" action="" method="post">
<section class="flex-container">
<?php
    foreach($_shop->get_products() as $item){//$_filter->get_values());
?>
    <div class="flex-box">
        <input type="checkbox" name="nameOfChoice[]" class="delete-checkbox" value="<?php echo $item['sku']; ?>">
        <div><b><?php echo $item['sku']; ?></b></div>
        <div><?php echo $item['name']; ?></div>
        <div><?php echo $item['productType']; ?></div>
        <div><?php echo $item['price']; ?></div>
        <?php
            foreach($_shop->params[$item['sku']] as $param){//$_filter->get_values());
        ?>
            <div><?php echo $param['name'] . ": " . $param['value']; ?></div>
        <?php } ?>
    </div>
<?php
    }
?>
<input name="submit_condition" type="hidden" value="1" />
</section>
</form>
<script>
function mass_delete(){
    var n = 0;
    var form_inputs = document.getElementById('mass_delete_form').getElementsByTagName('input');
    for(var i = 0; i <= form_inputs.length - 1; i++){
        if(form_inputs[i].checked) n = n+1;
    }
    if(n>0) document.getElementById('mass_delete_form').submit();
}
</script>