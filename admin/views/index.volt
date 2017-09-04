{% include "layouts/head.volt" %}

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="{{ siteName}}/index/info" class="site_title" style="display: block"><img src="{{staticPath}}/assets/image/{{menu_logo}}" style="width: 32px;height: 32px;">&nbsp;<span>{{menu_title}}</span></a>
                </div>
                <div class="clearfix"></div>
                <br/>
                <!-- sidebar menu -->
                {% include "layouts/sidebar.volt" %}
                <!-- /sidebar menu -->
            </div>
        </div>
        <!-- top navigation -->
        {% include "layouts/nav.volt" %}
        <!-- top navigation -->
        <!-- page content -->
        <div class="right_col" role="main">
            <div class="">
                {{ flash.output() }}
                {{ content() }}
            </div>
        </div>
        <!-- /page content -->
        <!-- footer content -->
        <footer>
            <div class="pull-right">

            </div>
            <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
    </div>
</div>

{% include "layouts/footer.volt" %}
