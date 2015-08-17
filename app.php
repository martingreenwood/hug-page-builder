<?php

// require settings
require_once('incs/settings.php'); 

// begin our session 
session_start();

// check if the users is already logged in
if(!isset( $_SESSION['user_id'] ))
{
    header("Location: index.php?notloggedin");
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
                </div>

                <div class="col-lg-8">

                    <div class="text">
                        <p>Welsome to the NHBC HUG page builder. This tool has been designed to help you generate a page to be uploaded to your NHBC Portal. </p>
                        <p>Below you will see a list of previously built pages which you can download and a form to build a new page.</p>
                        <p>If you experience any issues using this app, please <a href="mailto:martin@cactuscreative.com">contact the developer</a></p>
                        <p><strong>Happy building.</strong></p>
                        <br><br>

                    </div>

                </div>

            </div>

            <?php if (isset($_GET['oops'])): ?>
            <div class="row done">
                <div class="col-lg-12 text-center">
                    <div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-triangle"></i> <b>Opps</b>, looks like something went wrong.</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
            <div class="row done">
                <div class="col-lg-12 text-center">
                    <div class="alert alert-success" role="alert"><i class="fa fa-check"></i> <b>Awesome</b>, that file was deleted..</div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">

                <div class="col-lg-4">

                        <h4>Recently Created Pages</h4>
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

                            <li class="list-group-item">
                                <a class="download" href="download.php?id=<?php echo $row["id"]; ?>"><i class="fa fa-download"></i> <?php echo $row["title"]; ?></a> 

                                <a class="func del" href="delete.php?id=<?php echo $row["id"]; ?>"><i class="fa fa-trash-o"></i></a>
                                <a class="func edit" href="edit.php?id=<?php echo $row["id"]; ?>"><i class="fa fa-pencil"></i></a>
                            </li>
                        
                        <?php endforeach; ?>
                        </ul>

                        <?php if (!$pages): ?>
                            <div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> No HUG pages here.</div>
                        <?php endif; ?>
                       

                </div>      

                <div class="col-lg-7 col-lg-offset-1">

                    <h4>Make a New Page </h4>
                    <hr>

                    <div class="">
                        <form class="form-horizontal" id="savepage" enctype="multipart/form-data" method="post" action="make.php">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label for="logo">Logo</label>
                                    <p>Add the location of the development logo.
                                    <span>e.g. http://www.domain.com/image.jpg</span></p>
                                    <input type="url" maxlength="" name="logourl" class="form-control" id="logourl">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label for="title">Development Title</label>
                                    <p>Add the title of your developemt.
                                    <span>e.g. Cragg Close, Kendal</span></p>
                                    <input type="text" maxlength="" name="title" class="form-control" id="title">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label for="title">Main Text</label>
                                    <p>This main text field is used for the intro to the development, it should consist of basic information which will sit adjacent to the images.
                                    <span>e.g. Cragg Close is on the northeast side of Kendal, just off Rydal Road and is in the ideal location being within one mile of two supermarkets...</span></p>
                                    <textarea class="text form-control" id="main_text" name="main_text" rows="18"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label for="title">Development Images</label>
                                    <p>Add the location of the images yopu wish to use for this development. Make sure each image is sperated by a comma. 
                                    <span>e.g. http://www.domain.com/image.jpg, http://www.domain.com/image2.jpg, http://www.domain.com/image3.jpg, http://www.domain.com/image4.jpg</span></p>
                                    <textarea class="form-control" id="dev_images" name="dev_images" rows=""></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label for="title">Contact info &amp; Misc</label>
                                    <p>This section is used to define the resrt of the development information.
                                    <span>e.g. Emergancy contact procedure, contact info etc....</span></p>
                                    <textarea class="text form-control" id="text" name="text" rows="18"></textarea>
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

