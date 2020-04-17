{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading"> 添加代理</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">
{*                <form id="main_form">*}
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">

                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="sort">代理等级</label>
                                    <select id="level" class="form-control maxwidth-edit" name="level">
                                        {$level}
                                    </select>
                                    </label>
                                </div>

                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="text">用户ID（用户列表中查看）</label>
                                    <input class="form-control maxwidth-edit" id="id" name="id" type="text">
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-10 col-md-push-1">
                                            <button id="submit" type="submit"
                                                    class="btn btn-block btn-brand waves-attach waves-light lists_add">添加
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
{*                </form>*}
                {include file='dialog.tpl'}

        </div>

    </div>
</main>

{include file='admin/footer.tpl'}


<script>

    $('.lists_add').click(function () {

        $.ajax({
            type: "POST",
            url: "/admin/agent/lists_add",
            dataType: "json",
            data: {
                level: $$getValue("level"),
                id: $$getValue("id")
            },
            success: data => {
                if (data.ret) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            },
            error: jqXHR => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${ldelim}data.msg{rdelim} 发生错误了。`;
            }
        });

    });





{*    {literal}*}
{*    $('#main_form').validate({*}
{*        rules: {*}
{*            // name: {required: true},*}
{*            // text: {required: true},*}
{*            id: {required: true}*}
{*        },*}
{*        {/literal}*}
{*        submitHandler: function () {*}
{*            {literal}*}
{*            $.ajax({*}
{*                type: "POST",*}
{*                url: "/admin/lists_add",*}
{*                dataType: "json",*}
{*                data: {*}
{*                    level: $$getValue("level"),*}
{*                    id: $$getValue("id")*}
{*                },*}
{*                {/literal}*}
{*                success: data => {*}
{*                    if (data.ret) {*}
{*                        $("#result").modal();*}
{*                        $$.getElementById('msg').innerHTML = data.msg;*}
{*                        window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});*}
{*                    } else {*}
{*                        $("#result").modal();*}
{*                        $$.getElementById('msg').innerHTML = data.msg;*}
{*                    }*}
{*                },*}
{*                error: jqXHR => {*}
{*                    $("#result").modal();*}
{*                    $$.getElementById('msg').innerHTML = `${ldelim}data.msg{rdelim} 发生错误了。`;*}
{*                }*}
{*            });*}
{*        }*}
{*    });*}

</script>

