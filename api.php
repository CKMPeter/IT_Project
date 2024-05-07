<?php

session_start();
require 'functions.php';

$info = [];
$info['success'] = false;
$info['LOGGED_IN'] = is_logged_in();

if(!$info['LOGGED_IN'])
{
    echo json_encode($info);
    die;
}

function is_logged_in(){

        if(!empty($_SESSION['MY_DRIVE_USER']) && is_array($_SESSION['MY_DRIVE_USER']))
        return true;

    return false;
}

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['data_type']))
{
    $info['data_type'] = $_POST['data_type'];

    if($_POST['data_type'] == "upload_files")
    {
        $folder = 'uploads/';
        if(!file_exists($folder)){
            mkdir($folder, 0777, true);
            file_put_contents($folder.".HTACCESS", "Options -Indexes");
        }

        foreach($_FILES as $key => $file){
            $destination = $folder.time().$file['name'];

            //check if the file exist if do then add a random number.
            if(file_exists($destination)){
                $destination = $folder.time().rand(0, 9999).$file['name'];
            }

            move_uploaded_file($file['tmp_name'], $destination);

            //save to database
            //attributions:
            $file_type = $file['type'];
            $date_created = date("Y-m-d H:i:s");
            $date_updated = date("Y-m-d H:i:s");
            $file_name = $file['name'];
            $file_path = $destination;
            $file_size = filezise($destination);
            //sepcify the user
            $user_id = 0;

            $query = "INSERT INTO mydrive (file_name, file_size, file_path, user_id, file_type, date_created, date_updated) 
                        values ('$file_name', '$file_size', '$file_path', '$user_id', '$file_type', '$date_created', '$date_updated')";
            query($query);

            $info['success'] = true;
        }
    }else{
        if($_POST['data_type'] == "get_files")
        {    
            $query = "select * from mydrive order by id desc limit 30";
            $rows = query($query);
            
            if($rows)
            {
                $info['rows'] = $rows;
                $info['success'] = true;
            }
        }
    }
}

echo json_encode($info);