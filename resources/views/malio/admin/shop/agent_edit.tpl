{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">编辑商品</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">


                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="name">套餐名称</label>
                                <input class="form-control maxwidth-edit" id="name" type="text" value="{$shop->name}" disabled>
                                <p class="form-control-guide">
                                    流量：{$shop->bandwidth()}GB&nbsp; | &nbsp;等级：Lv.{$shop->user_class()}&nbsp; | &nbsp;有效天数：{$shop->class_expire()}天
                                </p>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <h4>设置价格</h4>成本价：{$shop->cost}
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="name">商店价格</label>
                                <input class="form-control maxwidth-edit" id="p1" type="text" value="{$shop->p1}">
                            </div>


                            <div class="form-group form-group-label">
                                <label class="floating-label" for="price">青铜代理价格</label>
                                <input class="form-control maxwidth-edit" id="p2" type="text" value="{$shop->p2}">
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="price">黄金代理价格</label>
                                <input class="form-control maxwidth-edit" id="p3" type="text" value="{$shop->p3}">
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="price">钻石代理价格</label>
                                <input class="form-control maxwidth-edit" id="p4" type="text" value="{$shop->p4}">
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
                                                class="btn btn-block btn-brand waves-attach waves-light">保存
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
    window.addEventListener('load', () => {
        function submit() {

            $.ajax({
                type: "PUT",
                url: "/admin/shop/{$shop->id}/edit_post",
                dataType: "json",
                data: {
                    p1: $$getValue('p1'),
                    p2: $$getValue('p2'),
                    p3: $$getValue('p3'),
                    p4: $$getValue('p4'),
                },
                success: data => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href='/admin/shop'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: jqXHR => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `发生错误：${
                            jqXHR.status
                            }`;
                }
            });
        }

        $("html").keydown(event => {
            if (event.keyCode === 13) {
                login();
            }
        });

        $$.getElementById('submit').addEventListener('click', submit);

    })
</script>
