
<!doctype html>
<html lang="es" class="">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="">

    <title><?php echo $title; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo ASSETS; ?>css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo ASSETS; ?>css/offcanvas.css" rel="stylesheet">
    <link href="<?php echo ASSETS; ?>css/style.css" rel="stylesheet">

    <style type="text/css">
        .animated{
            opacity: 1;
            max-height: none;
            overflow: hidden;
            transition: all 3s;
        }
        .animated.ng-hide{
            opacity: 0;
            max-height: 0px;
            display: block !important;
        }
    </style>

    <!-- Angular Framework -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.7/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-animate.js"></script>
  </head>