{include file='admin/main.tpl'}


<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">提现管理</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-md-12">
            <section class="content-inner margin-top-no">

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>系统中所有提现记录。</p>
                            <p>显示表项:
                                {include file='table/checkbox.tpl'}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card">

                    <div class="card-main">

                        <div class="card-inner">
                            <p><i class="icon icon-lg">attach_money</i>
                                当前余额：<font color="#399AF2" size="5">{$user->money}</font> 元
                            </p>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="quick_create"> 请输入提现金额 </label>
                                <input class="form-control maxwidth-edit" id="quick_create" type="text">
                            </div>
                        </div>
                        <div class="card-action">
                            <div class="card-action-btn pull-left">
                                <a class="btn btn-flat waves-attach waves-light" id="quick_create_confirm"><span
                                            class="icon">check</span>&nbsp;确认</a>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="table-responsive">
                    {include file='table/table.tpl'}
                </div>

                {include file='dialog.tpl'}
        </div>


    </div>
</main>


{include file='admin/footer.tpl'}

<script src="//cdn.bootcss.com/layer/3.0.1/layer.min.js"></script>

<script>
    {include file='table/js_1.tpl'}

    $(document).ready(function () {
        {include file='table/js_2.tpl'}
    });

    
    function look_money_code(id,uid,code,make_money) {

        console.log('uid:'+uid);
        var content = '<center><img width=\"400px\" height=\"560px\" src=\"'+code+'\"><p></center>';
        layer.open({
            title: '代理(#'+uid+ ')的收款码， 打款金额：'+make_money+'元。',
            area: ['460px', '680px'],
            shade: 0.3,
            anim: 1,
            shadeClose: true, //开启遮罩关闭
            content: content,
            btn: ['已打款','关闭'],
            yes: function(){
                look_callback(id,1);
            },
            btn2: function() {
                layer.closeAll();
            }
        })
    }
    
    function look_callback(id,status) {
        layer.closeAll();
        update_status(id,status);
    }

    function update_status(id,status) {
        $.ajax({
            type: 'GET',
            url: '/admin/agent/cash_log/'+id+'/'+status,
            dataType: 'json',
            data: {
                // userEmail: $$getValue('quick_create')
            },
            success: data => {

                if(data.ret == 1) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("window.location.reload()", 1200);
                }else{
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }

            },
            error: jqXHR => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${ldelim}jqXHR{rdelim} 发生了错误。`;
            }
        })
    }



    function quickCreate() {
        $.ajax({
            type: 'POST',
            url: '/admin/agent/cash_post',
            dataType: 'json',
            data: {
                userEmail: $$getValue('quick_create')
            },
            success: data => {

                if(data.ret == 1) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href='/admin/agent/cash'", 2500);
                }else{
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }

            },
            error: jqXHR => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${ldelim}jqXHR{rdelim} 发生了错误。`;
            }
        })
    }

    $$.getElementById('quick_create_confirm').addEventListener('click', quickCreate)
</script>



