{include file='admin/main.tpl'}


<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading"> 编辑等级 #{$info->name}</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">

                            <input class="form-control maxwidth-edit" id="id" name="id" type="text" value="{$info->id}" hidden>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="name">等级名称</label>
                                <input class="form-control maxwidth-edit" id="name" name="name" type="text"
                                       value="{$info->name}">
                            </div>


                            <div class="form-group form-group-label">
                                <label class="floating-label" for="name">在线开通价格</label>
                                <input class="form-control maxwidth-edit" id="price" name="price" type="text"
                                       value="{$info->price}">
                            </div>


                            <div class="form-group form-group-label">
                                <label class="floating-label" for="sort">状态（隐藏后前台用户面板将无法购买此等级后台）</label>
                                <select id="status" class="form-control maxwidth-edit" name="status">
                                    <option value="0" {if $info->status==0}selected{/if}>隐藏</option>
                                    <option value="1" {if $info->status==1}selected{/if}>启用</option>                                        </select>
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
            url: "/admin/agent/type_edit_post",
            dataType: "json",
            data: {
                id: $$getValue("id"),
                price: $$getValue("price"),
                status: $$getValue("status"),
                name: $$getValue("name")
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

