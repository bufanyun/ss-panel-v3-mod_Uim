{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">生成卡密</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>系统中所有套餐卡密的列表。</p>
                            <p>显示表项:
                                {include file='table/checkbox.tpl'}
                            </p>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-main">
{*                        <div class="container">*}
{*                            <h4 class="content-heading">生成卡密</h4>*}
{*                        </div>*}
                        <div class="card-inner">
                            <p><i class="icon icon-lg">attach_money</i>
                                当前余额：<font color="#399AF2" size="5">{$user->money}</font> 元
                                &nbsp;&nbsp;&nbsp;<a class="btn fbtn-red " href="/admin/km/log"> 查看批量记录</a>
                            </p>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="sort">选择套餐</label>
                                <select id="type_id" class="form-control maxwidth-edit" name="type_id">
                                    {$type_list}
                                </select>
                                </label>
                            </div>
                        </div>

                        <div class="card-inner">
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="quick_create"> 输入要生成的卡密数量 </label>
                                <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control maxwidth-edit" id="quick_create">
                            </div>
                        </div>
                        <div class="card-action">
                            <div class="card-action-btn pull-left">
                                <a class="btn btn-flat waves-attach waves-light" id="quick_create_confirm"><span
                                            class="icon">check</span>&nbsp;生成</a>
                                &nbsp;&nbsp;<span id="price_count" style="color: red"></span>
                            </div>
                        </div>
                    </div>
                </div>






                <div class="table-responsive">
                    {include file='table/table.tpl'}
                </div>

                <div aria-hidden="true" class="modal modal-va-middle fade" id="delete_modal" role="dialog"
                     tabindex="-1">
                    <div class="modal-dialog modal-xs">
                        <div class="modal-content">
                            <div class="modal-heading">
                                <a class="modal-close" data-dismiss="modal">×</a>
                                <h2 class="modal-title">确认要删除？</h2>
                            </div>
                            <div class="modal-inner">
                                <p>请您确认。</p>
                            </div>
                            <div class="modal-footer">
                                <p class="text-right">
                                    <button class="btn btn-flat btn-brand-accent waves-attach waves-effect"
                                            data-dismiss="modal" type="button">取消
                                    </button>
                                    <button class="btn btn-flat btn-brand-accent waves-attach" data-dismiss="modal"
                                            id="delete_input" type="button">确定
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="modal modal-va-middle fade" id="changetouser_modal" role="dialog"
                     tabindex="-1">
                    <div class="modal-dialog modal-xs">
                        <div class="modal-content">
                            <div class="modal-heading">
                                <a class="modal-close" data-dismiss="modal">×</a>
                                <h2 class="modal-title">确认要切换为该用户？</h2>
                            </div>
                            <div class="modal-inner">
                                <p>请您确认。</p>
                            </div>
                            <div class="modal-footer">
                                <p class="text-right">
                                    <button class="btn btn-flat btn-brand-accent waves-attach waves-effect"
                                            data-dismiss="modal" type="button">取消
                                    </button>
                                    <button class="btn btn-flat btn-brand-accent waves-attach" data-dismiss="modal"
                                            id="changetouser_input" type="button">确定
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>



                {include file='dialog.tpl'}


        </div>


    </div>
</main>


{include file='admin/footer.tpl'}


<script>
    var money = '{$user->money}';

    function upstatus(id){

        $("#result").modal();
        $$.getElementById('msg').innerHTML = '暂不支持冻结。';
    }
    $('#quick_create').bind('input propertychange', function() {
        var count = $('#quick_create').val();
        var type_cost = $("#type_id option:selected").attr("data-cost");
        if(count == '') {
            $('#price_count').html('');
            return false;
        }else if(count > 200){
            $('#price_count').html('单次最多只能生成不超过200张。');
            return false;
        }
        var cost = (Number(count)*Number(type_cost)).toFixed(2);
        if(parseFloat(cost)>parseFloat(money)){
            $('#price_count').html('余额不足'+cost+'元，请先充值。');
            return false;
        }
        $('#price_count').html('需花费 '+cost+' 元。');
        console.log(money);
    });

    $('#type_id').bind('input propertychange', function() {
        var count = $('#quick_create').val();
        var type_cost = $("#type_id option:selected").attr("data-cost");
        if(count == '') {
            $('#price_count').html('');
            return false;
        }else if(count > 200){
            $('#price_count').html('单次最多只能生成不超过200张。');
            return false;
        }
        var cost = (Number(count)*Number(type_cost)).toFixed(2);
        if(parseFloat(cost)>parseFloat(money)){
            $('#price_count').html('余额不足'+cost+'元，请先充值。');
            return false;
        }
        $('#price_count').html('需花费 '+cost+' 元。');
        console.log(money);
    });


    function delete_modal_show(id) {
        deleteid = id;
        $("#delete_modal").modal();
    }

    function changetouser_modal_show(id) {
        changetouserid = id;
        $("#changetouser_modal").modal();
    }

    {include file='table/js_1.tpl'}

    window.addEventListener('load', () => {
        table_1 = $('#table_1').DataTable({
            order: [[1, 'desc']],
            stateSave: true,
            serverSide: true,
            ajax: {
                url: "/admin/km/ajax",
                type: "POST",
            },
            columns: [
                {literal}
                {"data": "op", "orderable": false},
                {"data": "id"},
                {"data": "km"},
                {"data": "type_id"},
                {"data": "batch"},
                {"data": "status"},
                {"data": "user_id"},
                {"data": "endtime"},
                {"data": "addtime"}
                {/literal}
            ],
            "columnDefs": [
                {
                    targets: ['_all'],
                    className: 'mdl-data-table__cell--non-numeric'
                }
            ],

            {include file='table/lang_chinese.tpl'}
        });

        var has_init = JSON.parse(localStorage.getItem(`${ldelim}window.location.href{rdelim}-hasinit`));

        if (has_init !== true) {
            localStorage.setItem(`${ldelim}window.location.href{rdelim}-hasinit`, true);
        } else {
            {foreach $table_config['total_column'] as $key => $value}
            var checked = JSON.parse(localStorage.getItem(window.location.href + '-haschecked-checkbox_{$key}'));
            if (checked) {
                $$.getElementById('checkbox_{$key}').checked = true;
            } else {
                $$.getElementById('checkbox_{$key}').checked = false;
            }
            {/foreach}
        }

        {foreach $table_config['total_column'] as $key => $value}
        modify_table_visible('checkbox_{$key}', '{$key}');
        {/foreach}

        function delete_id() {
            $.ajax({
                type: "DELETE",
                url: "/admin/user",
                dataType: "json",
                data: {
                    id: deleteid
                },
                success: data => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        {include file='table/js_delete.tpl'}
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: jqXHR => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `${ldelim}jqXHR{rdelim} 发生了错误。`;
                }
            });
        }


        $$.getElementById('delete_input').addEventListener('click', delete_id);

        // $$.getElementById('search_button').addEventListener('click', () => {
        //     if ($$.getElementById('search') !== '') search();
        // });


        function changetouser_id() {
            $.ajax({
                type: "POST",
                url: "/admin/user/changetouser",
                dataType: "json",
                data: {
                    userid: changetouserid,
                    adminid: {$user->id},
                    local: '/admin/user'
                },
                success: data => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href='/user'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: jqXHR => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `${ldelim}jqXHR{rdelim} 发生了错误。`;
                }
            });
        }

        $$.getElementById('changetouser_input').addEventListener('click', changetouser_id);

        function quickCreate() {

            // var count = $('#quick_create').val();
            // var type_cost = $("#type_id option:selected").attr("data-cost");
            $.ajax({
                type: 'POST',
                url: '/admin/km/create',
                dataType: 'json',
                data: {
                    count: $$getValue('quick_create'),
                    type_id: $$getValue('type_id')
                },
                success: data => {
                    if(data.ret == 1) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href='/admin/km'", 1200);
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
    })


</script>