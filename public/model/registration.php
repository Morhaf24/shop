<?php
    require "model/database.php";
    require "controller/error.php";

    function get_category($category_id) {

        global $database;

        $result = $database->query("SELECT * FROM `category` WHERE category_id = $category_id");

        if ($result) {
            $category = $result->fetch_assoc();

            return $category;
        }
    }
    ?>