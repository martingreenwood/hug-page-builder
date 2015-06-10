<?php

// require settings
require_once('incs/settings.php'); 

// begin our session 
session_start();

// connect to database
$mysql_hostname = DB_HOST;
$mysql_username = DB_USER;
$mysql_password = DB_PASS;
$mysql_dbname = DB_NAME;


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

            <nav class="navbar-inverse">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Menu</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
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
                        <h1>NHBC Hug Page Builder</h1>
                    </div>

                </div>

                <div class="col-lg-3">

                    <div class="well">

                        <h4> Recently Created Pages </h4>
                        <hr>

                        <?php

                        try
                        {
                            $dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname", $mysql_username, $mysql_password);
                            // $message = a message saying we have connected

                            // set the error mode to excptions
                            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            // prepare the insert
                            $stmt = $dbh->prepare("SELECT * FROM pages");

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

                        <?php foreach($pages as $row): ?>

                        <div class="feedbody">
                            <div class="title"><a href="edit.php?id=<?php echo $row["id"]; ?>"> <?php echo $row["title"]; ?> </a></div>
                        </div>
                        
                        <?php endforeach; ?>
                       
                    </div>

                </div>
                
                <div class="col-lg-9">

                    <div class="well">
                        
                        <h4> Make a New Page </h4>
                        <hr>

                        <?php

                        $id = "";
                        if (isset($_GET["id"]){
                            $id = $_GET["id"];
                        }

                        try
                        {
                            $dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname", $mysql_username, $mysql_password);
                            // $message = a message saying we have connected

                            // set the error mode to excptions
                            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            // prepare the insert
                            $stmt = $dbh->prepare("SELECT * FROM pages WHERE id=$id");

                            // fetch assoc info for statement
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);

                            // that's t we're done here - use $row['title']; etc to fetch ifo.

                        }
                        catch(Exception $e)
                        {
                            echo $e; 
                        }

                        ?>


                        <form class="form-horizontal" id="savepage" enctype="multipart/form-data" method="post" action="save-page.php">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="text" maxlength="40" name="title" class="form-control" id="title" value="<?php echo $row['title']; ?>" placeholder="Development Title">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <textarea class="text form-control" id="summernote" name="text" placeholer="Development Text" rows="18"><?php echo $row['text']; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input id="file" type="file" class="file" name="file[]" multiple="true">
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

