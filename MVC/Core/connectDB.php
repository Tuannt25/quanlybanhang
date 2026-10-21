<?php
class connectDB {
    public $con;

    function __construct() {
        $this->con = mysqli_connect('localhost', 'root', '', 'qlbanhang');
        if (!$this->con) {
            die("Kết nối CSDL thất bại: " . mysqli_connect_error());
        }
        mysqli_set_charset($this->con, "utf8mb4");
    }
}
?>
