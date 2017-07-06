<?php
/**
 * Created by PhpStorm.
 * User: nhatminh
 * Date: 30/3/17
 * Time: 9:12 AM
 */
    global $conn;

    // Connect to database, return connection 
    function connectDB() {
        global $conn;
        try {
            $conn = mysqli_connect('mysql','dev','dev','test');
            return $conn;
        }
        catch(Exception $e)
        {
            die('Error : ' . $e->getMessage());
            return false;
        }
        mysqli_set_charset($conn,'utf8');
    }

    function disconnectDB() {
        global $conn;
        if ($conn) {
            mysqli_close($conn);
        }
    }

    // Get HTTP method
    function get_method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    // export id from url
    function handle_url() {
        $url = explode('/', $_GET['id']);
        return $_GET['id'];
    }

    function get_all() {
        global $conn;
        $sql = "SELECT * FROM staffs";
        $query = mysqli_query($conn,$sql);
        $result = array();
        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $result[]=$row;
            }
        }
        echo json_encode($result);
        return $result;
    }

    function get_staff($id) {
        global $conn;  
        if (empty($id)) {   // call get_all function if there is no id 
            get_all(); 
            return;
        }
        $sql = "SELECT * FROM staffs WHERE id = $id";
        $query = mysqli_query($conn,$sql);
        $result = array();
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            $result = $row;
        }
        if ($result) {
            echo json_encode($result);
            return $result;
        } else {
            echo "There is no information of id = $id";
        }
    }

    function add_staff($name, $age, $phone) {
        global $conn;
        // to avoid SQL injection
        $body = file_get_contents("php://input");
        parse_str($body);

        $sql = "INSERT INTO staffs(name,age,phone)
                VALUES('$name','$age','$phone')";
        echo $sql;
        echo PHP_EOL;
        $query = mysqli_query($conn,$sql);
        echo $query;
        if ($query) {
            echo "Successfully added.";
            return $query;
        } else {
            echo "failed to add.";
            return;
        }
    }

    function edit_staff($name, $age, $phone) {
        global $conn;
        $body = file_get_contents("php://input");
        parse_str($body);
        // to avoid SQL injection 

        $id = handle_url(); 
        if (!$id) {
            echo "Failed to identify id = $id";
            return;
        }
        $sql = "UPDATE staffs SET
                name = '$name',
                age = '$age',
                phone = '$phone'
                WHERE id = $id";
        echo $sql;
        echo PHP_EOL;
        $query = mysqli_query($conn,$sql);
        if ($query) {
            echo "Successfully edited profile (id = $id)";
            return $query; 
        } else { 
            echo "Failed to edit profile (id = $id)"; 
            return;
        }
    }

    function delete_staff($id) {
        global $conn;
        $sql = "DELETE FROM staffs
                WHERE id = $id";
        $query = mysqli_query($conn,$sql);
        if ($query) {
            echo "Successfully deleted profile (id = $id)";
            return $query;
        } else {
            echo "Failed to delete profile (id = $id)";
            return;
        }
    }
    // use method 'GET' to view all elements or only one
    if (get_method() == 'GET') {
        connectDB();
        get_staff(handle_url());
        disconnectDB();
    } 
    // use method 'PUT' to edit one element 
    elseif (get_method() == 'PUT') {
        connectDB();
        edit_staff($_PUT['name'],$_PUT['age'],$_PUT['phone']);
        disconnectDB();
    }
    // use method 'POST' to add one new element with +1 id
    elseif (get_method() == 'POST') {
        connectDB();
        add_staff($_POST['name'],$_POST['age'],$_POST['phone']);
        disconnectDB();
    }
    // use method 'DELETE' to delete one element
    elseif (get_method() == 'DELETE') {
        connectDB();
        delete_staff(handle_url());
        disconnectDB();
    }
?>