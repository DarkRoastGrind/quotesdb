<?php
    // Include headers
    require_once '../../config/headers.php';

    include_once '../../config/Database.php';
    include_once '../../models/Quote.php';

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog category object
    $quote = new Quote($db);

    // Get ID
    $quote->id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get post
    $quote->read_single();

    // Create array
    $quote_arr = array(
        'id' =>   $quote->id,
        'quote' => $quote->quote,
        'author' => $quote->author_id,
        'category' => $quote->category_id
    );

    // Make JSON
    print_r(json_encode($quote_arr));