<?php
// Get random file from the images folder
$files = glob(realpath('images') . '/*.*');
$index = array_rand($files);
$image = $files[$index];

if (file_exists($image)) {
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Cache-Control: public");
    header("Content-Type: application/zip");
    header("Content-Transfer-Encoding: Binary");
    header("Content-Length:".filesize($image));
    header("Content-Disposition: attachment; filename=file.zip");
    readfile($image);
} else {
    die("Error: File not found.");
}

// Connect to the database
$host = "localhost";
$user = "root";
$pass = "";
$db = "php_task_db";

// Create connection
$connect = new mysqli($host, $user, $pass, $db);
// sql to create table visits
$sql = "CREATE TABLE IF NOT EXISTS visits (
    id INT NOT NULL AUTO_INCREMENT,
    ip_address VARCHAR(50) NOT NULL,
    user_agent VARCHAR(255) NOT NULL,
    view_date TIMESTAMP NOT NULL,
    page_url VARCHAR(255) NOT NULL,
    views_count INT NOT NULL,
    CONSTRAINT PK_visits PRIMARY KEY (id)
);";

if ($connect->query($sql) === TRUE) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $page_url = $_SERVER['HTTP_REFERER'];
    $date = date('Y-m-d h:i:s');

    // Insert Or Update visit info in table
    $sqlCheck = "SELECT * FROM visits WHERE ip_address = '".$ip_address."' AND user_agent = '".$user_agent."' AND page_url = '".$page_url."'";
    if ($result = $connect->query($sqlCheck)) {
        $row = $result->fetch_assoc();
        var_dump($row);
        if ($row) {
            $c = $row['views_count'] + 1;
            $sqlInsertUpdate = "UPDATE visits SET view_date = '".$date."', views_count = '".$c."' WHERE ip_address = '".$ip_address."' AND user_agent = '".$user_agent."' AND page_url = '".$page_url."'";
        } else {
            $sqlInsertUpdate = "INSERT INTO visits (ip_address, user_agent, view_date, page_url, views_count) VALUES ('".$ip_address."', '".$user_agent."', '".$date."', '".$page_url."', '1')";
        }
        if ($connect->query($sqlInsertUpdate) !== TRUE) {
            echo "Error: " . $sqlInsertUpdate . "<br>" . $connect->error;
        }
    }
} else {
    echo "Error creating table: " . $connect->error;
}

$connect->close();
?>
