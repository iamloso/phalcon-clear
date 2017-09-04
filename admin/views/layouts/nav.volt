<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="col-xs-9">
                <div class="nav toggle">
                    <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                </div>
                <ul class="nav navbar-nav navbar-left" role="menu">
                    {% for menu in menu_top %}
                        <li><a {% if menu_top_key == menu['lable'] %}class="active"{% endif %} href="{{ siteName}}{{ menu['url'] }}">{{
                            menu['lable'] }}</a>
                        </li>
                    {% endfor %}
                </ul>
            </div>

            <div class="col-xs-3">
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown pull-right">
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                {% for menu in menu_top_right %}
                                <a href="{{ siteName}}{{menu['url']}}"> <i class="icon"></i><span> {{menu['lable']}}</span> </a>
                                {% endfor %}
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>

            <ul class="nav navbar-nav navbar-right" style="padding-right:23px;width:auto">
            <!-- 管理员信息 -->
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-user"></i>
                    admin
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href=<?php echo $this->centerConfig->ibankAdminHost;?> ><i class="fa fa-sign-out pull-right"></i>返回直销银行后台</a></li>
                  </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>