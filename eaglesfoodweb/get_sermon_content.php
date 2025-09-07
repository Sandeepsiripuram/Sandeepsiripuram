<?php
include 'db.php';
$id = intval($_GET['id']);
$sql = "SELECT sermon_title, content FROM sermons_content WHERE sermon_id = $id LIMIT 1";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

echo json_encode([
  'title' => $data['sermon_title'],
  'content' => $data['content']
]);
?>
