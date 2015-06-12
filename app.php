<?php

// require settings
require_once('incs/settings.php'); 

// begin our session 
session_start();

// check if the users is already logged in
if(!isset( $_SESSION['user_id'] ))
{
    header("Location: index.php");
}

// define the variables for response catching
$errors         = array();
$data           = array();

// check if form has been submitted 
// if it hasnt then don't to anything!
if (isset($_POST['submit'])) {

// validate the variables 
// if any of these variables don't exist, add an error to our $errors array
    if (empty($_POST['title']))
       $errors['title'] = '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Oops, seems like you forgot to enter a title</div>';

    if (empty($_POST['text']))
        $errors['text'] = '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Opps, seems you forgot to enter some text.</div>';

    if (empty($_FILES["file"]))
        $errors['file'] = '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>Opps, seems like you forgot to choose any images.</div>';

    // return a response
    // if there are any errors in our errors array, return a success boolean of false
    if ( ! empty($errors)) {

        // if there are items in our errors array, return those errors
        $data['success'] = false;
        $data['errors']  = $errors;

    } else {

        // if there are no errors process our form, then return a message

        // if we are here the data is valid and we can insert it into database
        $title = $_POST["title"];
        $text = $_POST["text"];

        function reArrayFiles(&$file_post) {
            $file_ary = array();
            $file_count = count($file_post['name']);
            $file_keys = array_keys($file_post);

            for ($i=0; $i<$file_count; $i++) {
                foreach ($file_keys as $key) {
                    $file_ary[$i][$key] = $file_post[$key][$i];
                }
            }

            return $file_ary;

        }

        if ($_FILES["file"]) {

            $location = '/www/sites/hug-pages/uploads/';
            $file_ary = reArrayFiles($_FILES['file']);
            $yourfiles = "";

            foreach ($file_ary as $file) {
                $filename = $file['name'];
                $temp_name = $file["tmp_name"];

                $yourfiles .= $filename."|";

                if(isset($filename)){
                    if(!empty($filename)){
                        if(move_uploaded_file($temp_name, $location.$filename)){
                            // it was uploaded - nothing to say here. - but you could add a message to say so? 
                        }
                    }
                } else {
                    $errors['upload'] = '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Sorry, we could not upload that file.</div>';
                }
            }

            // return a response
            // if there are any errors in our errors array, return a success boolean of false
            if ( ! empty($errors)) {

                // if there are items in our errors array, return those errors
                $data['success'] = false;
                $data['errors']  = $errors;

            }
        }

        $dir = "pages/";
        $dir2 = "/www/sites/hug-pages/pages/";

        // prepare the filename
        $pagename = strtolower(str_replace(" ", "-", $title)) . "-" . strtotime(date("d/m/Y h:i:s")) .".html"; 

        // create and open a the file so we can edit it
        $filename = fopen($dir.$pagename, "w") or die("Unable to open file!");

        // built the data
        
        $file_body = '<!DOCTYPE html>';
        $file_body .= '<html>';
        $file_body .= '<head>';
        $file_body .= '<title>Title of the document</title>';
        $file_body .= '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">';
        $file_body .= '</head>';
        $file_body .= '<body>';
        $file_body .= '<div  class="container">';
        $file_body .= '<div class="row">';
        $file_body .= '<div class="col-md-6">';
        $file_body .= $text;
        $file_body .= '</div><!-- end.6 col -->';
        $file_body .= '<div class="col-md-5 col-md-offset-1">';
            foreach ($file_ary as $file) {
                $file_body .= '<img class="img-thumbnail img-responsive" style="margin-bottom:20px;" src="'.base.'uploads/'.$file['name'].'">';
            }
        $file_body .= '</div><!-- end.4 col -->';
        $file_body .= '</div><!-- end .row -->';
        $file_body .= '</div><!-- end .comtainer ->';
        $file_body .= '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>';
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
            $stmt = $dbh->prepare("INSERT INTO pages (title, text, images, downloadable ) VALUES (:title, :text, :file, :downloadable )");

            // bind the parameters
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':text', $text, PDO::PARAM_STR);
            $stmt->bindParam(':file', $yourfiles, PDO::PARAM_LOB);
            $stmt->bindParam(':downloadable', $pagename, PDO::PARAM_STR);

            // execute the prepared statement
            $stmt->execute();

            // if all is done - that should of daved it to the DB - we say thank below
        }
        catch(Exception $e)
        {
            // if we are here, something has gone wrong with the database
            $errors['save'] = "We are sorry but that didn't work. See the error below: <br><br>" .$e;


            // return a response
            // if there are any errors in our errors array, return a success boolean of false
            if ( ! empty($errors)) {

                // if there are items in our errors array, return those errors
                $data['success'] = false;
                $data['errors']  = $errors;

            }
        }

        // show a message of success and provide a true success variable
        $data['success'] = true;
        $data['message'] = '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Page created sucessfully...</div>';
    
    }

}

?>

<!DOCTYPE html>

<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home / Login</title>

    <?php include("incs/head.php"); ?>
        
    </head>
    
    <body id="app">

        <nav class="navbar-inverse" style="padding: 10px 0;">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Menu</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a style="padding: 0 15px; display: block;" href="#"><img style="padding: 11px 0; width: 150px;" width="150" src="https://cldup.com/-YgCTzP60U.png" alt=""></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>

        
        <div class="container">

            <div class="row">

                <div class="col-lg-12">

                    <div class="page-header">
                        <h2>NHBC HUG Builder</h2>
                    </div>

                    <div class="text">

                        <p>Welsome to the NHBC HUG page builder. This tool has been designed to help you generate a page to be uploaded to your NHBC Portal. </p>
                        <p>Below you will see a list of previously built pages which you can download and a form to build a new page.</p>
                        <p>If you experience any issues using this app, please <a href="mailto:martin@cactuscreative.com">contact the developer</a></p>
                        <p><strong>Happy building.,</strong></p>
                        <br><br>

                    </div>

                </div>

                <div class="col-lg-4">

                        <h4> Recently Created Pages </h4>
                        <hr>

                        <?php

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
                            $stmt = $dbh->prepare("SELECT * FROM pages ORDER BY id DESC");

                            // execute the prepared statement
                            $stmt->execute();

                            //fetch the data and store it in a var
                            $pages = $stmt->fetchAll();

                            // that's t we're done here - use $row to make your loop etc.

                        }
                        catch(Exception $e)
                        {
                            echo $e; 
                        }

                        ?>

                        <ul class="list-group pageme">
                        <?php foreach($pages as $row): ?>

                            <li class="list-group-item"><a href="download.php?id=<?php echo $row["id"]; ?>">
                                <?php echo $row["title"]; ?> 
                                    <span class="glyphicon glyphicon-download" aria-hidden="true"></span> 
                            </a></li>
                        
                        <?php endforeach; ?>
                        </ul>

                        <?php if (!$pages): ?>
                            <div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> No HUG pages here.</div>
                        <?php endif; ?>
                       

                </div>
                
                <div class="col-lg-8">

                    <h4> Make a New Page </h4>
                    <hr>
                    <?php 
                    if (isset($data['errors'])):
                        echo $data['errors'];
                    endif;

                    if (isset($data['message'])):
                        echo $data['message']; 
                    endif;
                    ?>

                    <div class="well">

                        <form class="form-horizontal" id="savepage" enctype="multipart/form-data" method="post" action="app.php">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="text" maxlength="40" name="title" class="form-control" id="title" placeholder="Development Title">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <textarea class="text form-control" id="summernote" name="text" placeholer="Development Text" rows="18"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input id="file" type="file" class="file" name="file[]" multiple="true" data-show-upload="false">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="submit" id="submit" class="btn btn-default">Make Page</button>
                                </div>
                            </div>
                        </form>


                    </div>

                </div>

            </div>

        </div> <!-- /container -->

        <?php include("incs/footer.php"); ?>
    </body>
</html>

