<!-- sidebar menu -->
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <ul class="nav side-menu">

            {% for group,menu in menu_left %}

            <li><a>{{group}}<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    {% for item in menu %}
                    <li><a class="{% if menu_top_key==item['label'] %}active{% endif %}" href="{{ siteName}}{{item['url']}}">{{item['lable']}}</a></li>
                    {% endfor %}
                </ul>
            </li>
            {% endfor %}
        </ul>
    </div>
</div>
        <!-- /sidebar menu -->




