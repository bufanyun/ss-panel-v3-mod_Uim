

/**
 * 异步通用方法
 * @param url
 * @param data
 * @param type
 * @param callback
 * @constructor
 */
function AjaxMethod(url,data,type,callback) {
    $.ajax({
        type: type,
        url: url,
        data:data,
        dataType: "json",
        success: (data) => {
            if (data.ret) {
                swal({
                    title:'成功',
                    text:data.msg,
                    type: "success"
                }).then(function () {
                    window.location.href = callback;
                });
                return false;
            } else {
                swal("提示", data.msg, "error");
            }
        },
        error: (jqXHR) => {
            swal("失败", "发生错误：" + jqXHR.status, "error");
        }
    });
}


/**
 * randomWord 产生任意长度随机字母数字组合
 * randomFlag-是否任意长度 min-任意长度最小位[固定位数] max-任意长度最大位
 * @param randomFlag
 * @param min
 * @param max
 * @returns {string|string}
 */
function randomWord(randomFlag, min, max){
    var str = "",
        range = min,
        arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    // 随机产生
    if(randomFlag){
        range = Math.round(Math.random() * (max-min)) + min;
    }
    for(var i=0; i<range; i++){
        pos = Math.round(Math.random() * (arr.length-1));
        str += arr[pos];
    }
    return str;
}


/**
 * 加法
 * @param arg1
 * @param arg2
 * @returns
 */
function accAdd(arg1,arg2){
    var r1,r2,m;
    try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0};
    try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0};
    m=Math.pow(10,Math.max(r1,r2));
    return (arg1*m+arg2*m)/m;
}

/**
 * 减法
 * @param arg1
 * @param arg2
 * @returns
 */
function accSubtr(arg1,arg2){
    var r1,r2,m,n;
    try{r1=arg1.toString().split(".")[1].length;}catch(e){r1=0;}
    try{r2=arg2.toString().split(".")[1].length;}catch(e){r2=0;}
    m=Math.pow(10,Math.max(r1,r2));
    //动态控制精度长度
    n=(r1>=r2)?r1:r2;
    return ((arg1*m-arg2*m)/m).toFixed(n);
}

/***
 * 乘法，获取精确乘法的结果值
 * @param arg1
 * @param arg2
 * @returns
 */
function accMul(arg1,arg2)
{
    var m=0,s1=arg1.toString(),s2=arg2.toString();
    try{m+=s1.split(".")[1].length}catch(e){};
    try{m+=s2.split(".")[1].length}catch(e){};
    return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
}

/***
 * 除法，获取精确乘法的结果值
 * @param arg1
 * @param arg2
 * @returns
 */
function accDivCoupon(arg1,arg2){
    var t1=0,t2=0,r1,r2;
    try{t1=arg1.toString().split(".")[1].length;}catch(e){}
    try{t2=arg2.toString().split(".")[1].length;}catch(e){}
    with(Math){
        r1=Number(arg1.toString().replace(".",""));
        r2=Number(arg2.toString().replace(".",""));
        return (r1/r2)*pow(10,t2-t1);
    }
}

//左侧菜单
var nav_link_status=0;
$('.nav-link-lg2').click(function () {
    if(nav_link_status==0) {
        IsPC() ? $("body").attr("class","sidebar-mini") : $("body").attr("class","sidebar-show");
        nav_link_status=1;
    }else{
        $("body").attr("class","");
        nav_link_status=0;
    }
});

//修复移动端无法关闭遮罩
window.onclick = function(event) {;
    // console.log(event.target.nodeName);
    // console.log(event.target.className);
    if (event.target.className == 'sidebar-show') {
        $("body").attr("class","sidebar-gone");
        nav_link_status=0;
    }
}

//下拉工单
var has_dropdown=0;
$('.has-dropdown-ticket').click(function () {
    if(has_dropdown==0) {
        $(".dropdown").addClass("active");
        $(".main-sidebar .sidebar-menu li ul.dropdown-menu").css("display","block");
        has_dropdown=1;
    }else{
        $(".dropdown").removeClass("active");
        $(".main-sidebar .sidebar-menu li ul.dropdown-menu").css("display","none");
        has_dropdown=0;
    }
});

//默认选中
var location_pathname=window.location.pathname;
$(function(){
    if(IsPC() != true){
        $(".sidebar-style-2").css("overflow","auto");
    }
    $(".nav-link").each(function(index, element) {
        if(location_pathname == $(this).attr('href')){
            $(this).parent().addClass("active");

            //工单子菜单
            if($(this).attr('href').indexOf("ticket") >= 0 ) {
                $('.has-dropdown-ticket').click();
            }
        }
    });
})

function checkins() {
    $.ajax({
        type: "POST",
        url: "/user/checkin",
        dataType: "json",
        success: (data) => {
            if (data.ret) {
                swal("签到成功",data.msg,"success");
                $("#checkin-div").html('<a href="#" class="btn btn-icon disabled icon-left btn-primary"><i class="far fa-calendar-check"></i> 明日再来</a>');
            } else {
                swal("签到失败", data.msg, "error");
            }
        },
        error: (jqXHR) => {
            swal("签到失败", "发生错误：" + jqXHR.status, "error");
        }
    });
}

function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
        "SymbianOS", "Windows Phone",
        "iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}


function oneclickImport(type) {
    if(type == 'clash') {
        // if(IsPC() != true){
        //     swal("提示","导入配置到此客户端需要在电脑端操作，无法在移动端导入。","error");
        //     return false;
        // }
        window.location.href="clash://install-config?url="+clash;
    }else if(type == 'quantumult') {
        if(IsPC() == true){
            swal("提示","导入配置到此客户端需要在手机浏览器操作，无法在电脑端导入。","error");
            return false;
        }
        window.location.href = quantumult;
    }else if(type == 'shadowrocket'){
        if(IsPC() == true){
            swal("提示","导入配置到此客户端需要在手机浏览器操作，无法在电脑端导入。","error");
            return false;
        }
        window.location.href = shadowrocket;
    }
}

//监听屏幕宽度自适应
var windowWidth = $(window).width();
if(windowWidth < 1010){
    $("body").attr("class","sidebar-gone");
}
if(windowWidth >= 1010){
    $("body").attr("class","");
}
window.addEventListener('load', function() {


    window.addEventListener('resize', function() {
        // console.log('qw'+window.innerWidth);
        if(window.innerWidth < 1010){
            $(".sidebar-style-2").css("overflow","auto");
            $("body").attr("class","sidebar-gone");
        }
        if(window.innerWidth >= 1010){
            $(".sidebar-style-2").css("overflow","hidden"); //禁止滚动--隐藏滚动条
            $("body").attr("class","");
        }
    })

})



//订阅套餐
function subscribePlan(id) {
    $("#main-page").css("display","none");
    $("#buy-page").css("display","block");

    return getplaninfo(id);
    // getplantime(id);
}

//获取套餐可用时长
function getplantime(id) {
    $.ajax({
        type: "GET",
        url: "/user/shop/getplantime?num="+id,
        dataType: "json",
        success: (data) => {
            if (data.ret) {
                getplaninfo(id);
            } else {
                swal("套餐获取失败", data.msg, "error");
            }
        },
        error: (jqXHR) => {
            swal("获取失败", "发生错误：" + jqXHR.status, "error");
        }
    });
}

//获取套餐规格
function getplaninfo(id) {
    $.ajax({
        type: "GET",
        url: "/user/shop/getplaninfo?num="+id+"&time="+sessionStorage.getItem('shoptime'),
        dataType: "json",
        success: (data) => {
            if (data.ret) {
                $("#shop-name").text(data.name);
                $("#total").text(data.price);

                //获取优惠价
                var coupon_money = 0.00;
                if(!isset(sessionStorage.getItem('credit'))){
                    $("#coupon-row").css("display","none");
                    sessionStorage.setItem('coupon', '');
                    sessionStorage.setItem('credit', 100); //默认无优惠
                }
                if(sessionStorage.getItem('coupon') != '' && sessionStorage.getItem('credit') != 100) {
                    var per = str_replace("%","",sessionStorage.getItem('credit')); //过滤老版本%
                    var per = accDivCoupon(per,100);
                    coupon_money = accMul(data.price,per);
                    $("#coupon-money").text( number_format(coupon_money, 2, '.', ','));
                    $("#coupon-row").css("display", "block");
                    $("#coupon-btn").html('<i class="fas fa-tag"></i> 当前优惠码：'+sessionStorage.getItem('coupon'));
                }

                //余额支付
                if(userMoney > (data.price-coupon_money)) {
                    var account_money = data.price-coupon_money;
                }else{
                    var account_money = userMoney;
                }
                sessionStorage.setItem('account_money', account_money);
                $('#account-money').text( number_format(account_money, 2, '.', ','));

                //还需支付
                var pay_amount = data.price-coupon_money-account_money;
                sessionStorage.setItem('pay_amount', pay_amount);
                $('#pay-amount').text(number_format(pay_amount, 2, '.', ','));

                //截取商品id
                let index = id.lastIndexOf("_")
                shopid =id.substring(index+1,id.length);
                sessionStorage.setItem('shopid', shopid);

                //更新套餐名称
                $('.setplan').attr("class", "color col-12 col-md-2 col-lg-2 setplan");
                $("#plan_" + shopid).attr("class", "color col-12 col-md-2 col-lg-2  active setplan");

                //更新月
                $("#1month").attr("class", "color col-12 col-md-2 col-lg-2");
                $("#3month").attr("class", "color col-12 col-md-2 col-lg-2");
                $("#6month").attr("class", "color col-12 col-md-2 col-lg-2");
                $("#12month").attr("class", "color col-12 col-md-2 col-lg-2");
                if(!isset(sessionStorage.getItem('shoptime'))) {
                    sessionStorage.setItem('shoptime', '1month');  //默认1个月
                    $("#1month").attr("class", "color col-12 col-md-2 col-lg-2 active");
                }else{
                    $("#" + sessionStorage.getItem('shoptime')).attr("class", "color col-12 col-md-2 col-lg-2 active");
                }

                if(!isset(sessionStorage.getItem('autorenew'))) {
                    sessionStorage.setItem('autorenew', 0);  //默认自动续费
                }
                if(!isset(sessionStorage.getItem('payment'))) {
                    sessionStorage.setItem('payment', 'alipay'); //默认支付方式
                }

            } else {
                swal("套餐获取失败", data.msg, "error");
            }
        },
        error: (jqXHR) => {
            swal("获取失败", "发生错误：" + jqXHR.status, "error");
        }
    });
}

//input选项卡
function selectItem(type,id) {
    if(type == 'plan') {
        let index = id.lastIndexOf("_")
        shopid =id.substring(index+1,id.length);
        // console.log('shopid:'+shopid);
        subscribePlan(id);
        sessionStorage.setItem('shopid', shopid);
        $('.setplan').attr("class", "color col-12 col-md-2 col-lg-2 setplan");
        $("#" + id).attr("class", "color col-12 col-md-2 col-lg-2  active setplan");
    }else if(type == 'autorenew'){
        autorenew = 1;
        if(id == 'autorenew-on'){
            autorenew = 0;
        }
        sessionStorage.setItem('autorenew', autorenew);
        $("#autorenew-on").attr("class", "color col-12 col-md-2 col-lg-2");
        $("#autorenew-off").attr("class", "color col-12 col-md-2 col-lg-2");
        $("#" + id).attr("class", "color col-12 col-md-2 col-lg-2 active");
    }else if(type == 'payment'){
        payment = 'alipay';
        if(id == 'wechat'){
            payment = 'wechat';
        }
        sessionStorage.setItem('payment', payment);
        $("#alipay").attr("class", "color col-12 col-md-2 col-lg-2");
        $("#wechat").attr("class", "color col-12 col-md-2 col-lg-2");
        $("#" + id).attr("class", "color col-12 col-md-2 col-lg-2 active");
    }else if(type == 'time'){
        sessionStorage.setItem('shoptime', id);
        //获取套餐价格
        subscribePlan('plan_'+sessionStorage.getItem('shopid'));
        $("#1month").attr("class", "color col-12 col-md-2 col-lg-2");
        $("#3month").attr("class", "color col-12 col-md-2 col-lg-2");
        $("#6month").attr("class", "color col-12 col-md-2 col-lg-2");
        $("#12month").attr("class", "color col-12 col-md-2 col-lg-2");
        $("#" + id).attr("class", "color col-12 col-md-2 col-lg-2 active");
    }

}
//返回商店
function backToShop() {
    $("#main-page").css("display","block");
    $("#buy-page").css("display","none");
}

//验证优惠码
function updateCoupon() {
    var coupon = $("#coupon-code").val();
    if(empty(coupon)){
        swal("提示", '优惠码不得为空', "error");
        return false;
    }
    $.ajax({
        type: "POST",
        url: "/user/coupon_check",
        data:{
            coupon: coupon,
            shop: getActiveShopId()
        },
        dataType: "json",
        success: (data) => {
            if (data.ret) {
                sessionStorage.setItem('coupon', coupon);
                sessionStorage.setItem('credit', data.credit);

                $("#coupon-btn").html('<i class="fas fa-tag"></i> 当前优惠码：'+coupon);
                $("#coupon-modal").modal('hide');
                //刷新套餐
                subscribePlan('plan_'+getActiveShopId());
            } else {
                swal("提示", data.msg, "error");
            }
        },
        error: (jqXHR) => {
            swal("获取失败", "发生错误：" + jqXHR.status, "error");
        }
    });
}

//优惠码输入框过滤
function hideFeedback() {

}

//取消优惠码
function cancelCoupon() {
    sessionStorage.setItem('coupon', '');
    $("#coupon-btn").html('<i class="fas fa-tag"></i> 使用优惠码');

    //刷新套餐
    subscribePlan('plan_'+getActiveShopId());
}

//获取选中的商品id
function getActiveShopId() {
    return sessionStorage.getItem('shopid');
}


//发起购买
$("#pay-confirm").click(function () {

    var coupon = sessionStorage.getItem('coupon');
    var shop = getActiveShopId();
    var autorenew = sessionStorage.getItem('autorenew');
    var disableothers = 1;
    var shoptime = sessionStorage.getItem('shoptime');

    var pay_amount = sessionStorage.getItem('pay_amount');
    if(pay_amount > 0){
        /**
         * 跳转支付平台
         */
        $.ajax({
            type: "POST",
            url: "/user/payment/purchase",
            data:{
                price: pay_amount,
                type: sessionStorage.getItem('payment')
            },
            dataType: "json",
            success: (data) => {
                if (data.ret) {
                    // $("#maliopay-modal").modal('show');
                    $("#maliopay-modal").modal({backdrop: 'static', keyboard: false});
                    $("#to-maliopay").attr('href',data.url);
                    loadmsg();
                } else {
                    swal("提示", data.msg, "error");
                }
            },
            error: (jqXHR) => {
                swal("获取失败", "发生错误：" + jqXHR.status, "error");
            }
        });

        return false;
    }


    //使用余额支付
    $.ajax({
        type: "POST",
        url: "/user/buy",
        data:{
            shoptime: shoptime,
            coupon: coupon,
            shop: shop,
            autorenew: autorenew,
            disableothers: disableothers
        },
        dataType: "json",
        success: (data) => {
            if (data.ret) {
                swal({
                    title: data.msg,
                    type: "success"
                }).then(function () {
                    window.location.href = '/user';
                });
                return false;
            } else {
                swal("提示", data.msg, "error");
            }
        },
        error: (jqXHR) => {
            swal("获取失败", "发生错误：" + jqXHR.status, "error");
        }
    });

});
// swal("签到成功","你获得了 ++ MB流量","success");

//支付完成自动购买
function loadmsg() {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "/user/money",
        timeout: 10000, //ajax请求超时时间10s
        success: function (data, textStatus) {
            //从服务器得到数据，显示数据并继续查询
            if (data.ret == 1) {
                var account_money = sessionStorage.getItem('account_money');
                var pay_amount = sessionStorage.getItem('pay_amount');

                //如果余额满足购买则直接提交
                var need = accAdd(account_money,pay_amount);
                var need = number_format(need, 2, '.', ',')
                console.log(data.money,need);
                if(data.money >= need){
                    console.log('前往购买')
                    sessionStorage.setItem('pay_amount',0);  //清空还需支付金额
                    $("#pay-confirm").click();
                    $("#maliopay-modal").modal('hide');
                    return false;
                }
                setTimeout("loadmsg()", 4000);
                // layer.msg('支付成功，正在跳转中...', {icon: 16,shade: 0.01,time: 15000});
                // setTimeout(window.location.href=data.backurl, 1000);
            }else{
                setTimeout("loadmsg()", 4000);
            }
        },
        //Ajax请求超时，继续查询
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            if (textStatus == "timeout") {
                setTimeout("loadmsg()", 1000);
            } else { //异常
                setTimeout("loadmsg()", 4000);
            }
        }
    });
}

//修改密码
function passwordConfirm() {
    $.ajax({
        type: "POST",
        url: "/user/password",
        data:{
            oldpwd:$("#oldpwd").val(),
            pwd:$("#pwd").val(),
            repwd:$("#repwd").val()
        },
        dataType: "json",
        success: (data) => {
            if (data.ret) {
                swal({
                    title: data.msg,
                    type: "success"
                }).then(function () {
                    window.location.href = '/user/logout';
                });
                return false;
            } else {
                swal("提示", data.msg, "error");
            }
        },
        error: (jqXHR) => {
            swal("获取失败", "发生错误：" + jqXHR.status, "error");
        }
    });
}


//删除账号
function killConfirm() {
    return AjaxMethod('/user/kill',{passwd: $("#passwd").val()},'POST','/');
}
//充值码充值
function codeTopup() {
    return AjaxMethod('/user/code',{code: $("#topup-code").val()},'POST','/user');
}


//余额充值
function walletTopup(type) {
    $.ajax({
        type: "POST",
        url: "/user/payment/purchase",
        data:{
            price: $("#amount").val(),
            type: type
        },
        dataType: "json",
        success: (data) => {
            if (data.ret) {
                console.log(data.url);
                window.location.href = data.url;
            } else {
                swal("失败", data.errmsg, "error");
            }
        },
        error: (jqXHR) => {
            swal("获取失败", "发生错误：" + jqXHR.status, "error");
        }
    });
}

//提交工单
function createTicket() {
    return AjaxMethod('/user/ticket',{
        content: $('#ticket-content').val(),
        markdown: $('#ticket-content').val(),
        title: $("#title").val()
    },'POST','/user/ticket');
}

//关闭工单
function closeTicket(id) {
    return AjaxMethod('/user/ticket/'+id,{
        content: "此工单已关闭",
        status: 0,
        title: $("#title").val()
    },'PUT','/user/ticket');
}

//回复工单
function replyTicket(id) {
    return AjaxMethod('/user/ticket/'+id,{
        content: $('#ticket-content').val(),
        markdown: '',
        status: 1,
        title: ''
    },'PUT','/user/ticket/'+id+'/view');
}


//重置订阅
$('#reset-sub-link').click(function(){

    Swal.fire({
        title:'确定要重置订阅链接吗?',
        text:'此操作不可逆，请谨慎操作',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: '确定',
        cancelButtonText: '取消'
    }).then((result) => {
        if (result.value) {
            swal({
                title:'已重置订阅链接',
                text:'您需要在客户端内删除旧订阅链接，然后添加新订阅链接',
                type: "success"
            }).then(function () {
                window.location.href = '/user/url_reset';
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            /**
             * 取消
             */
            return false;
        }
    })

});

//生成随机连接密码
$("#ss-random-password").click(function () {
    $("#ss-password").val(randomWord(false, 6));
});

//修改连接密码
$("#ss-password-confirm").click(function () {
    return AjaxMethod('/user/sspwd',{sspwd: $("#ss-password").val()},'POST','/user/edit');
});


//购买代理
function buy(id) {
    return AjaxMethod('/user/agent_buy',{shop: id},'POST','/admin');
}


