<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>



    <div align="center">
     <div class="panel panel-primary" style="max-width: 400px; ">
       <div class="panel-heading" align="left">Select your location</div>
       <div class="panel-body" align="left">
          <form action="search_yelp.php" method="post" form class="form-horizontal" role="form">
            <div  class="form-group">
                <p>Select the type of location
              <select name="type_of_location" size="1">
                <option>Choose...</option>
                <option value="pubs">Pub</option>
                <option value="danceclubs">Clubs</option>
                <option value="karaoke">Karaoke</option>
              </select>
                </p>
                <p>Fill in your postal code
                <input name="location_input" type="text" maxlength="7" />
                </p>
                <div align="center" id="submitButton"><input type="submit" value="Submit" class="btn btn-primary btn-lg" role="button"></div>
              </div>
          </form>
       </div>
     </div>
   </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>