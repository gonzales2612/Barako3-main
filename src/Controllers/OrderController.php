<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\MenuItem;
use PDO;

class OrderController extends BaseController
{
    private $orderModel;
    private $menuItemModel;
    private $db;

    public function __construct($conn = null)
    {
        $this->db = $conn ?: new PDO('mysql:host=localhost;dbname=test', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->orderModel = new Order($this->db);
        $this->menuItemModel = new MenuItem($this->db);
    }

    // Show orders and menu items
    public function showOrders()
    {
        $orders = $this->orderModel->getAllOrdersWithMenuNames();
        $menuItems = $this->menuItemModel->getAllMenuItems();
        $ordersCountByMonth = $this->orderModel->getOrdersCountByMonth(); // New method
    
        $template = 'orders';
        $data = [
            'title' => 'OrdersTable',
            'orders' => $orders,
            'menuItems' => $menuItems,
            'ordersCountByMonth' => json_encode($ordersCountByMonth) // Ensure this is JSON encoded
        ];
    
        echo $this->render($template, $data);
    }

    // Handle the form submission to create a new order
    public function createOrder()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customerName = $_POST['customerName'];
        $menuItemIds = $_POST['menuItemIds'] ?? [];
        $quantities = $_POST['quantities'] ?? [];
        $orderDate = $_POST['orderDate']; // Capture the selected date

        if (empty($menuItemIds)) {
            echo "No menu items selected. Please select at least one item.";
            exit();
        }

        // Calculate total amount
        $totalAmount = 0;
        foreach ($menuItemIds as $menuItemId) {
            $menuItem = $this->menuItemModel->getMenuItemById($menuItemId);
            $quantity = $quantities[$menuItemId] ?? 1; // Default to 1 if not set
            $subtotal = $menuItem['Price'] * $quantity;
            $totalAmount += $subtotal;
        }

        // Create the order, including the order date
        $orderId = $this->orderModel->createOrder($customerName, $totalAmount, $orderDate);

        // Add menu items to the order with quantities
        foreach ($menuItemIds as $menuItemId) {
            $menuItem = $this->menuItemModel->getMenuItemById($menuItemId);
            $quantity = $quantities[$menuItemId];
            $subtotal = $menuItem['Price'] * $quantity;
            $this->orderModel->addMenuItemToOrder($orderId, $menuItemId, $quantity, $subtotal);
        }

        header('Location: /orders');
        exit();
    }
}

    // Remove order by ID
    public function removeOrder($id)
    {
        $this->orderModel->deleteOrderById($id);
        header('Location: /orders');
        exit();
    }
}