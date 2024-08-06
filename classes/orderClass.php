<?php
class Order
{
    private $conn;
    private $table_name = "orders";

    public $id;
    public $user_id;
    public $total_amount;
    public $order_date;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = "SELECT o.order_id, o.user_id, o.order_date, u.username, u.email, 
                         (SELECT SUM(oi.price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.order_id) AS total_amount
                  FROM " . $this->table_name . " o
                  JOIN users u ON o.user_id = u.user_id
                  ORDER BY o.order_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT o.order_id, o.user_id, o.order_date, u.username, u.email, 
                         (SELECT SUM(oi.price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.order_id) AS total_amount
                  FROM " . $this->table_name . " o
                  JOIN users u ON o.user_id = u.user_id
                  WHERE o.order_id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->user_id = $row['user_id'];
        $this->total_amount = $row['total_amount'];
        $this->order_date = $row['order_date'];
        $this->username = $row['username'];
        $this->email = $row['email'];
        $this->address = $row['address'];
    }
}
?>