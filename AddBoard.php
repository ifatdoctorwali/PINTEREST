<?php
session_start();
// Add logic to add items to a board here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Board</title>
    <link rel="stylesheet" href="styles/addboard.css">
</head>
<body>
    <h1>Add to Board</h1>
    <form action="add_to_board_handler.php" method="POST">
        <label for="item-name">Item Name:</label>
        <input type="text" id="item-name" name="item_name" required>
        <label for="board-id">Select Board:</label>
        <select id="board-id" name="board_id">
            <option value="1">Board 1</option>
            <option value="2">Board 2</option>
            <!-- Populate dynamically -->
        </select>
        <button type="submit">Add</button>
    </form>
</body>
</html>
