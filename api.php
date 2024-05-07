<?php
session_start();
require 'functions.php';

$info = [];
$info['success'] = false;
$info['LOGGED_IN'] = false;
$info['data_type'] = $_POST['data_type'] ?? '';

if(!$info['LOGGED_IN'] && $info['data_type'] != 'user_signup' && $info['data_type'] != 'user_login')
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
            $file_size = filesize($destination);
            //sepcify the user
            $user_id = 0;

            $query = "INSERT INTO mydrive (file_name, file_size, file_path, user_id, file_type, date_created, date_updated) 
                        values ('$file_name', '$file_size', '$file_path', '$user_id', '$file_type', '$date_created', '$date_updated')";
            query($query);

            $info['success'] = true;
        }
    }else
    if($_POST['data_type'] == "get_files")
    {   
        $mode = $_POST['mode'];

        switch ($mode) {
            case 'MY DRIVE':
                $query = "select * from mydrive order by id desc limit 30";
                break;

            case 'FAVORITES':
                $query = "select * from mydrive where favorite = 1 order by id desc limit 30";
                break;

            case 'RECENT':
                $query = "select * from mydrive order by date_updated desc limit 30";
                break;

            case 'TRASH':
                $query = "select * from mydrive where trash = 1 order by id desc limit 30";
                break;
            
            default:
                $query = "select * from mydrive order by id desc limit 30";
                break;
        }

        $rows = query($query);
        
        if($rows)
        {
            foreach ($rows as $key => $row) {
                $rows[$key]['icon'] = $icons[$row['file_type']] ?? '<i class = "bi bi-question-square-fill class 39"></i>';
                $rows[$key]['date_created'] = get_date($row['date_created']);
                $rows[$key]['date_updated'] = get_date($row['date_updated']);
                $rows[$key]['file_size'] = round($row['file_size'] / (1024 * 1024)) . "Mb";

                if( $rows[$key]['file_size'] == ")Mb")
                {
                    $rows[$key]['file_size']  = round($row['file_size'] / ( 1024)) . "Kb";

                }
                
            }

            $info['rows'] = $rows;
            $info['success'] = true;
        }
    }else
    if($_POST['data_type'] == "user_signup")
    {
        //save to database
        //attributions:
        $email = addslashes($_POST['email']);
        $user_name = addslashes($_POST['username']);
        $password = addslashes($_POST['password']);
        $date_created = date("Y-m-d H:i:s");
        $date_updated = date("Y-m-d H:i:s");

        //validate data
        $errors = [];
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            console.log($errors);
            $errors['email'] = "Invalid email";
        }
        if(empty($errors))
        {
            $query = "INSERT INTO user (file_name, file_size, file_path, user_id, file_type, date_created, date_updated) 
                    values ('$file_name', '$file_size', '$file_path', '$user_id', '$file_type', '$date_created', '$date_updated')";
            query($query);

            $info['success'] = true;
        }
    }
}

echo json_encode($info);