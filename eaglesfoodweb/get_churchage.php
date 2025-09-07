<?php
include 'db.php';
$id = intval($_GET['id']);
$sql = "SELECT `id`, `language_id`, `age_number`, `title`, `date`, `content` FROM `church_ages` WHERE  id = $id";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

echo json_encode([
  'title' => $data['title'],
  'content' => $data['content']
]);
?>
