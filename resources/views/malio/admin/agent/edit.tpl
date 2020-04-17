{include file='admin/main.tpl'}


<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading"> 编辑代理 #{$info->uid}</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">

                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">

                                <input class="form-control maxwidth-edit" id="uid" name="uid" type="text" value="{$info->uid}" hidden>

                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="name">备注（仅自己可见）</label>
                                    <input class="form-control maxwidth-edit" id="remark" name="remark" type="text"
                                           value="{$info->remark}">
                                </div>


                                <div class="form-group form-group-label">
                                        <label class="floating-label" for="sort">代理面板（关闭后代理收益也将取消）</label>
                                        <select id="status" class="form-control maxwidth-edit" name="status">
                                            <option value="0" {if $info->status==0}selected{/if}>已关闭</option>
                                            <option value="1" {if $info->status==1}selected{/if}>已开启</option>                                        </select>
                                    </label>
                                </div>


                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="sort">用户状态（禁用后代理面板也将无法登陆）</label>
                                    <select id="enable" class="form-control maxwidth-edit" name="enable">
                                        <option value="0" {if $info->enable==0}selected{/if}>禁用</option>
                                        <option value="1" {if $info->enable==1}selected{/if}>正常</option>                                        </select>
                                    </label>
                                </div>



                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="sort">代理等级</label>
                                        <select id="level" class="form-control maxwidth-edit" name="level">
                                            {$level}
                                        </select>
                                    </label>
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
                                                    class="btn btn-block btn-brand waves-attach waves-light lists_add">修改
                                            </button>
                                        </div>
                                    </div>
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
    $('.lists_add').click(function () {

        $.ajax({
            type: "POST",
            url: "/admin/agent/lists_edit_post",
            dataType: "json",
            data: {
                uid: $$getValue("uid"),
                remark: $$getValue("remark"),
                status: $$getValue("status"),
                enable: $$getValue("enable"),
                level: $$getValue("level")
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

</script>

