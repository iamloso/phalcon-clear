<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    {{ get_title()}}
    <!-- Bootstrap -->
    {{ stylesheet_link(staticPath~'/assets/bootstrap/vendors/bootstrap/dist/css/bootstrap.min.css', false) }}
    {{ stylesheet_link(staticPath~'/assets/site.css', false) }}
    <!-- Font Awesome -->
    {{ stylesheet_link(staticPath~'/assets/bootstrap/vendors/font-awesome/css/font-awesome.min.css', false) }}
    <!-- jQuery -->
    {{ javascript_include(staticPath~'/assets/bootstrap/vendors/jquery/dist/jquery.min.js', false) }}
    <!-- Bootstrap -->
    {{ javascript_include(staticPath~'/assets/bootstrap/vendors/bootstrap/dist/js/bootstrap.min.js', false) }}
    {{ javascript_include(staticPath~'/assets/js/layer/layer.js', false) }}
    <!-- create special css @author sun wei-->
        <style type="text/css">
        .form-group  .required{color:red}
        </style>
    <!-- create special css @author sun wei-->



