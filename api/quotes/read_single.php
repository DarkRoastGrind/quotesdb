<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Quote.php';

$database = new Database();
$db = $database->connect();
$quote = new Quote($db);
$quote->id = isset($_GET['id']) ? $_GET['id'] : die();
$quote->read_single();

$quote_arr = array(
    'id' => $quote->id,
    'quote' => $quote->quote,
    'author' => $quote->author_id,
    'category' => $quote->category_id
);

print_r(json_encode($quote_arr));