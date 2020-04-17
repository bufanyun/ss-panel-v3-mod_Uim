{include file='admin/main.tpl'}


<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">提现与记录</h1>
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

<script>
    {include file='table/js_1.tpl'}

    $(document).ready(function () {
        {include file='table/js_2.tpl'}
    });



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



