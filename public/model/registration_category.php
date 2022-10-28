<?php
    require "model/database.php";
    require "controller/error_category.php";

    function get_category($category_id) {
        global $database;

        $result = $database->query("SELECT * FROM `category` WHERE category_id = $category_id");
        if ($result) {
            $category = $result->fetch_assoc();
            
        }
        if ($category) {
            echo json_encode($category);
        }
        else if (is_string($category)) {
            error($category, 500);
        }
        else {
            error("The ID "  . $category_id . " was not found.", 404);
        }
            return $category;
    }

    function get_all_category() {
        global $database;

        $result = $database->query("SELECT * FROM `category`");

        if (!$result) {
            error("Fetching the Category faild.", 500);

        }

        else if ($result === true || $result->num_rows == 0) {
            return array();

        }

        $all_category = array();
        while ($category = $result->fetch_assoc()) {
            $all_category[] = $category;

        }

        return $all_category;
    }

    function create_new_category($active, $name) {
        global $database;

        $result = $database->query("INSERT INTO `category` (`active`, `name`) VALUES ('$active', '$name');");

        return true;   
        
    }

    function update_category($category_id, $name, $active) {
        global $database;

        $result = $database->query("UPDATE `category` SET name = '$name', active = $active WHERE category_id = $category_id");
        if (!$result) {
            return false;
        }
            return true;
    }

    function delete_category($category_id) {
        global $database;

		$category_id = intval($category_id);

		$result = $database->query("DELETE FROM `category` WHERE category_id = $category_id");

        if (!$result) {
            error("No category found for the ID " . $category_id . ".", 404);
        }

        else if($database->affected_rows == 0) {
            return null;
        }
        
        else {
            error("The data was deleted.", 200);
        }

        return true;
        
    }
?>