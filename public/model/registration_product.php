<?php
    require "model/database.php";
    require "controller/error_product.php";

    function get_product($product_id) {
        global $database;

        $result = $database->query("SELECT * FROM `product` WHERE product_id = $product_id");
        if ($result) {
            $product = $result->fetch_assoc();
            
        }
        if ($product) {
            error($product, 200);
        }
        else if (is_string($product)) {
            error($product, 500);
        }
        else {
            error("The ID "  . $product_id . " was not found.", 404);
        }
            return $product;
    }

    function update_product($product_id, $sku, $active, $category_id, $name, $image, $description, $price, $stock) {
        global $database;

        $result = $database->query("UPDATE `Product` SET product_id = $product_id, sku = '$sku', active = $active, category_id = $category_id, name = '$name', image = '$image' description = '$description', price = $price, stock = $stock WHERE product_id = $product_id");
        if (!$result) {
            return false;
        }
            return true;
    }

    function delete_product($product_id) {
        global $database;

		$product_id = intval($product_id);

		$result = $database->query("DELETE FROM `product` WHERE product_id = $product_id");

        if (!$result) {
            error("No product found for the ID " . $product_id . ".", 404);
        }

        else if($database->affected_rows == 0) {
            return null;
        }
        
        else {
            error("The product was succsessfuly deleted.", 200);
        }

        return true;
        
    }
?>

        