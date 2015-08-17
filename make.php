<?php 

// require settings
require_once('incs/settings.php'); 

// begin our session 
session_start();

// define the variables for response catching
$errors         = array();
$data           = array();

// validate the variables 
// if any of these variables don't exist, add an error to our $errors array

        // if there are no errors process our form, then return a message

        // if we are here the data is valid and we can insert it into database
        $logourl = $_POST["logourl"];
        $title = $_POST["title"];
        $intro = $_POST["main_text"];
        $dev_images = $_POST["dev_images"];
        $text = $_POST["text"];

        $dev_img_trim = rtrim($dev_images, ',');
        $page_images = explode(',', $dev_img_trim);

        $dir = "pages/";
        $dir2 = dirname(__FILE__) . "/pages/";

        // prepare the filename
        $pagename = strtolower(str_replace(" ", "-", $title)) . "-" . strtotime(date("d/m/Y h:i:s")) .".html"; 

        // create and open a the file so we can edit it
        $filename = fopen($dir.$pagename, "w") or die("Unable to open file!");

        // build the data
        
        $file_body = '<!DOCTYPE html>';
        $file_body .= '<html>';
        $file_body .= '<head>';
        $file_body .= '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">';
        $file_body .= '</head>';
        $file_body .= '<body>';
        $file_body .= '<div class="row" style="margin:0;">';
        $file_body .= '<div class="col-md-6">';
        $file_body .= '<img width="150px" height="auto" style="margin-bottom:0" src="'.$logourl.'">';
        $file_body .= $title;
        $file_body .= $intro;
        $file_body .= '</div><!-- end.6 col -->';
        $file_body .= '<div class="col-md-6 grid js-masonry"  data-masonry-options="{ columnWidth": 200, "itemSelector": ".grid-item" }">';
        foreach ($page_images as $page_image) {
            $file_body = '<div class="grid-item">';
            $file_body .= '<img width="100%" height="auto" style="display:block;margin-bottom:0" src="'.$page_image.'">';
            $file_body .= '</div>';
        }
        $file_body .= '</div><!-- end.6 col -->';
        $file_body .= '</div><!-- end .row -->';
        $file_body .= '<div class="row" style="margin:30px 0 0 0;">';
        $file_body .= '<div class="col-md-12">';
        $file_body .= $text;
        $file_body .= '</div><!-- end.12 col -->';
        $file_body .= '</div><!-- end .row -->';
        $file_body .= '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>';
        $file_body .= '<script src="http://cdnjs.cloudflare.com/ajax/libs/masonry/3.3.1/masonry.pkgd.min.js"></script>';
        $file_body .= '</body>';
        $file_body .= '</html>';

        // write it to the file
        fwrite($filename, $file_body);

        // close
        fclose($filename);        

        // connect to database
        $mysql_hostname = DB_HOST;
        $mysql_username = DB_USER;
        $mysql_password = DB_PASS;
        $mysql_dbname = DB_NAME;

        
        try
        {
            $dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname", $mysql_username, $mysql_password);
            // $message = a message saying we have connected

            // set the error mode to excptions
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // prepare the insert
            $stmt = $dbh->prepare("INSERT INTO pages (logo, title, text, main_text, images, downloadable ) VALUES (:logo, :title, :text, :main_text, :images, :downloadable )");

            // bind the parameters
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':logo', $logourl, PDO::PARAM_STR);
            $stmt->bindParam(':main_text', $intro, PDO::PARAM_STR);
            $stmt->bindParam(':text', $text, PDO::PARAM_STR);
            $stmt->bindParam(':images', $dev_images, PDO::PARAM_STR);
            $stmt->bindParam(':downloadable', $pagename, PDO::PARAM_STR);

            // execute the prepared statement
            $stmt->execute();

            // if all is done - that should of daved it to the DB - we say thank below
        }
        catch(Exception $e)
        {
            // if we are here, something has gone wrong with the database
            echo $e;
        }

?>