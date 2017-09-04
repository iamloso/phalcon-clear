$(function(){
    //页面响应处理顶部菜单
    if ($(window).width() > 768){
        $('.navbar').removeClass('min-down');
    }
    if ($(window).width() <= 767){
        $('.navbar').addClass('min-down');
    }

    $(window).on('resize', function () {
        if ($(window).width() <= 767){
            $('.navbar').addClass('min-down');
        }
        if ($(window).width() > 768){
            $('.navbar').removeClass('min-down');
        }
        //$('#sidebar-collapse').collapse('hide')
    });

    //左侧菜单
    $("[data-pull-down]").find('a').click(function(){
        if($(this).parent().find("ul").is(":hidden")){
            $(this).siblings("ul").slideDown("fast");
            $(this).find('em').removeClass('glyphicon-plus').addClass('glyphicon-minus');
        }else{
            $(this).siblings("ul").slideUp("fast");
            $(this).find('em').removeClass('glyphicon-minus').addClass('glyphicon-plus');
        }
    });

    //表单验证
    var selectHandlerCheck = function(eles){
        var selectflg = false;
        $.each(eles,function(k,item){
            var parTxt = $(this).parents('.form-group').find('label').text();
            if($(this).val() =='@'){
                layer.alert('请选择'+parTxt, 0);
                selectflg = false;
                $(this).next();
                return false;
            }else{
                selectflg = true;
                return true;
            }
        })
        return selectflg;
    }

    var inputHandlerCheck = function(eles){
        var flg = false;
        $.each(eles,function(k,item){
            var parTxt = $(this).parents('.form-group').find('label').text();
            if($(this).val() == ''){
                layer.alert('请输入'+parTxt, 0);
                flg = false;
                $(this).next();
                return false;
            }else if(!/^(?!_)(?!.*?_$)(?!-)(?!.*?-$)[a-zA-Z0-9_\-\u4e00-\u9fa5]+$/.test($(this).val())){
                layer.alert('请输入正确的'+parTxt, 0);
                flg = false;
                $(this).next();
                return false;
            }else if($(this).val().length > 30){
                layer.alert('不能输入大于30位字符'+parTxt, 0);
                flg = false;
                $(this).next();
                return false;
            }else if($(this).val() == '@'){
                layer.alert('请选择'+parTxt, 0);
                flg = false;
                item.next();
                return false;
            }else{
                flg = true;
                return true;
            }
        })
        return flg;
    }
    /*
    function checkForm(){
        $("select.form-control").bind('change',function(){
            var item = $(this);
            selectHandlerCheck(item);
        });
        $("input.form-control").bind('blur',function(){
            var item = $(this);
            inputHandlerCheck(item);
        });
    }
    */
//    if($('#no-verify').length == 0){
//        //checkForm();
//        $('.form-group .btn').bind('click',function(){
//            var selectItem = $("select.form-control"),
//                inputItem = $("input.form-control");
//            if(inputHandlerCheck($('form .form-control'))){
//                $('form').submit()
//            }else{
//                return false;
//            }
//        })
//    }
});

/** 调用方法
*  defaultMsg以对象形式传入参数
*  <a href="###" onclick="defaultMsg({'msg':'是吗','width':'300'})">点击</a>
*  <a href="###" onclick="defaultMsg({'msg':'是吗','jumpurl':'xxx','ok':'确定消息','cannel':'取消消息','width':'300'})">点击</a>
*/
function defaultMsg(option){
    if(option == null){
        return false;
    }
    option = option || {};
    layer.confirm(option.msg, {
        btn: ['确定','取消'] //按钮
    }, function(){
    	var tipmsg = option.ok ? option.ok: '删除中...';
        layer.msg(tipmsg, {icon: 1});
        //TODO 异常检测
        if(option.jumpurl){
            location.href = option.jumpurl;
        }
    }, function(){
    	var tipmsg = option.cannel ? option.cannel: '放弃删除';
        layer.msg(tipmsg, {shift: 6});
    });

}


//所有标的删除操作
$(function(){
    /*
	$('.bootstrap-table .table').find('a.delete').bind('click',function(e){
        e.preventDefault();
        defaultMsg({'msg':'确定删除吗？','jumpurl': $(this).attr("href")});
    })
    */
    $('.delete').bind('click',function(e){
        e.preventDefault();
        defaultMsg({'msg':'确定删除吗？','jumpurl': $(this).attr("href")});
    })    
    
})
/*
    onclick="showContent({'title': '提现申请','content': 'html'})

*/
function showContent(options){
    options = options || {};
    layer.open({
        type: 1
        ,content: options.content
        //,area: ['100px', '300px']
        ,shade: 0.6 //遮罩透明度
        ,title: options.title
        ,closeBtn: true
        ,btn: false
        ,padding:'10px'
    });
};

