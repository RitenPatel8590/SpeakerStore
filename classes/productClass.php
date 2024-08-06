<?php
class Product
{
    private $conn;
    private $table_name = "products";

    public $product_id;
    public $product_name;
    public $description;
    public $price;
    public $category_id;
    public $category_name;
    public $image_url;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get all products
    public function getAllProducts()
    {
        $query = "SELECT p.product_id, p.product_name, p.description, p.price, c.category_name, p.image_url
                  FROM " . $this->table_name . " p
                  LEFT JOIN categories c ON p.category_id = c.category_id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read products with optional filters
    public function read($filters = [])
    {
        $query = "SELECT c.category_name as category_name, p.product_id, p.product_name, p.description, p.price, p.category_id, p.image_url
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.category_id";

        if (!empty($filters)) {
            $query .= " WHERE ";
            $conditions = [];
            foreach ($filters as $key => $value) {
                $conditions[] = "p.$key = :$key";
            }
            $query .= implode(" AND ", $conditions);
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }

        $stmt->execute();
        return $stmt;
    }


    public function readOne()
    {
        $query = "SELECT c.category_name as category_name, p.product_id, p.product_name, p.description, p.price, p.category_id, p.image_url
              FROM " . $this->table_name . " p 
              LEFT JOIN categories c ON p.category_id = c.category_id 
              WHERE p.product_id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->product_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->product_name = $row['product_name'];
            $this->price = $row['price'];
            $this->description = $row['description'];
            $this->category_id = $row['category_id'];
            $this->category_name = $row['category_name'];
            $this->image_url = $row['image_url'];

        } else {
            error_log("Product with ID " . $this->product_id . " not found.");
        }
    }


    // Get categories
    public function getCategories()
    {
        $query = "SELECT category_id, category_name FROM categories";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create a new product
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET product_name=:product_name, description=:description, price=:price, category_id=:category_id, image_url=:image_url";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->product_name = htmlspecialchars(strip_tags($this->product_name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));

        // Bind parameters
        $stmt->bindParam(":product_name", $this->product_name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":image_url", $this->image_url);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update a product
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET product_name = :product_name, description = :description, price = :price, category_id = :category_id, image_url = :image_url WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':product_id', $this->product_id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete a product
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->product_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>