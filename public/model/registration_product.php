<?php
    require "model/database.php";
  //  require "controller/error.php";

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

    function get_all_product() {
        global $database;

        $result = $database->query("SELECT * FROM `product`");

        if (!$result) {
            error("Fetching the products faild.", 500);

        }

        else if ($result === true || $result->num_rows == 0) {
            return array();

        }

        $all_products = array();
        while ($product = $result->fetch_assoc()) {
            $all_products[] = $product;

        }

        return $all_products;
    }

    function create_new_product($sku, $active, $name, $image, $description, $price, $stock) {
        global $database;

        $result = $database->query("INSERT INTO `product` (`active`, `name`, `sku`, `image`, `description`, 'price', 'stock') VALUES ('$active', '$name', '$sku', '$image', '$description', '$price', '$stock');");

        return true;   
        
    }

    function update_product($product_id, $name, $active, $sku, $category_id, $image, $description, $price, $stock) {
		global $database;

		$result = $database->query("UPDATE `product` SET name = '$name', active = $active, sku = '$sku', category_id = $category_id, image = '$image', description = '$description', price = $price, stock = $stock WHERE product_id = $product_id");
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

        