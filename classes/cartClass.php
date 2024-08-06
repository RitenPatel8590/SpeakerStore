<?php
class Cart
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function incrementQuantity($product_id, $user_id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error incrementing quantity: " . $e->getMessage());
        }
    }

    // Add product to cart
    public function addToCart($user_id, $product_id, $quantity)
    {
        try {
            // Check if the product is already in the cart
            $stmt = $this->db->prepare("SELECT quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingItem) {
                // Update quantity if item already exists
                $new_quantity = $existingItem['quantity'] + $quantity;
                $stmt = $this->db->prepare("UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
                $stmt->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            } else {
                // Insert new item into the cart
                $stmt = $this->db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            }

            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error adding to cart: " . $e->getMessage());
        }
    }

    public function decrementQuantity($product_id, $user_id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = :user_id AND product_id = :product_id AND quantity > 1");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error decrementing quantity: " . $e->getMessage());
        }
    }

    public function removeItem($product_id, $user_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error removing item: " . $e->getMessage());
        }
    }

    public function getCartItems($user_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT c.*, p.product_name, p.price FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching cart items: " . $e->getMessage());
        }
    }
    public function clearCart($user_id) {
        $query = "DELETE FROM cart WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>