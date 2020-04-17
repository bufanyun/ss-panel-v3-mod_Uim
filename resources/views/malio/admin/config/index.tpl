{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">站点配置</h1>
        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">



            <div class="col-xx-12 col-sm-6">


                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-inner">
                                <div class="cardbtn-edit">
                                    <div class="card-heading">站点状态：<span style="color: #00C703">{if $agent->status==1}运行正常{else}锁定{/if}</span></div>
                                    <button class="btn btn-flat" onclick="location.href='/user/agent'"><span class="icon">verified_user</span>&nbsp;
                                    </button>
                                </div>
                                <p>当前等级：
                                    <button class="btn btn-subscription" type="button" id="filter-btn-universal">Lv.{$agent->level->level}&nbsp;{$agent->level->name} </button>
                                </p>
{*                                    <code id="ajax-block">[{$agent->level->name}]</code></p>*}
                                <p>开通时间：{$agent->addtime}</p>


                            </div>
                        </div>
                    </div>
                </div>


                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-inner">
                                <div class="cardbtn-edit">
                                    <div class="card-heading">邮箱绑定与修改</div>
                                    <button class="btn btn-flat" id="ss-pwd-update"><span class="icon">check</span>&nbsp;</button>
                                </div>

                                <p>绑定邮箱：<code id="ajax-user-passwd">{$user->email}</code>
                                    <button class="kaobei copy-text btn btn-subscription post_email_code" type="button">
                                        发送验证码
                                    </button>
                                </p>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="sspwd">验证码</label>
                                    <input class="form-control maxwidth-edit" id="email_code" type="text">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="sspwd">新的邮箱</label>
                                    <input class="form-control maxwidth-edit" id="new_email" type="text">
                                </div>
                                <br>
                                <p>为了确保您的代理账户安全，请不要随意修改绑定邮箱</p>
                                <p>请确认绑定邮箱为常用，用于接收站点通知</p>
{*                                <p>修改邮箱后，系统会发送一封确认邮件到新的邮箱，如有异常请及时做出调整</p>*}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-inner">
                                <div class="cardbtn-edit">
                                    <div class="card-heading">在线客服（https://chatra.io）</div>
                                    <button class="btn btn-flat" id="wechat-update"><span class="icon">check</span>&nbsp;
                                    </button>
                                </div>
{*                                <p>当前联络方式：*}
{*                                    <code id="ajax-im" data-default="imtype">*}
{*                                        微信                                                                                                                                                                                                    </code>*}
{*                                </p>*}
{*                                <p>当前联络方式账号：*}
{*                                    <code></code>*}
{*                                </p>*}
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="imtype">开启状态</label>
                                    <button class="form-control maxwidth-edit" id="imtype" data-toggle="dropdown"
                                            value="{$agent->panel['enable_chatra']}">{if $agent->panel['enable_chatra'] == 1}开启{else}关闭{/if}</button>
                                    <ul class="dropdown-menu" aria-labelledby="imtype">
                                        <li><a href="#" class="dropdown-option" onclick="return false;" val="1"
                                               data="imtype">开启</a></li>
                                        <li><a href="#" class="dropdown-option" onclick="return false;" val="0"
                                               data="imtype">关闭</a></li>
                                    </ul>
                                </div>

                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="wechat">Chatra 的 ChatraID，可以在 Chatra 提供的网站代码里找到</label>
                                    <input class="form-control maxwidth-edit" id="wechat" type="text" value="{$agent->panel['chatra_id']}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>


            <div class="col-xx-12 col-sm-6">

                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-inner">
                                <div class="cardbtn-edit">
                                    <div class="card-heading">绑定域名</div>
                                    <button class="btn btn-flat" id="domain-update"><span class="icon">check</span>&nbsp;
                                    </button>
                                </div>

                                <p>绑定域名请按下方的解析地址解析以后即可生效</p>
                                <p>修改网站名称和域名，价格：<code>{$agent->update_domain_price}</code>元/次</p>
                                <p>请将绑定域名按记录类型<code>CNAME</code>解析到：<code id="ajax-user-port">{$Host_Analysis}</code></p>


                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="pwd">站点名称</label>
                                    <input class="form-control maxwidth-edit" id="name" type="text" value="{$agent->panel['name']}">
                                </div>

                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="repwd">绑定域名 （绑定多个请以英文,隔开）</label>
                                    <input class="form-control maxwidth-edit" id="domain" type="text"  value="{$agent->domain}">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>




                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-inner">
                                <p class="card-heading">设置提现账户</p>
                                <p>请绑定一个支付宝收款账户，以便于系统为你的余额进行结算。</p>
                                <p>每次提现至少要达成<code data-default="ga-enable"> {$config['cash_min_money']} </code>元。</p>
                                <p>提现手续费为<code data-default="ga-enable"> {$config['cash_fee']} </code>元/次。</p>

                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="code">支付宝账户</label>
                                    <input type="text" id="account" placeholder=""
                                           class="form-control maxwidth-edit" value="{$agent->cash['account']}">
                                </div>

                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="code">真实姓名</label>
                                    <input type="text" id="account_name" placeholder=""
                                           class="form-control maxwidth-edit" value="{$agent->cash['name']}">
                                </div>

                                <br>
                                <input id="upload_account" style="display:none;" type='file' name='file' value="">
                                <input type="text" id="account_code" style="display:none;" value="{$agent->cash['img']}">
                                <button class="btn btn-red up_account_img">
                                    <span class="icon">cloud_upload</span>&nbsp;上传支付宝收款码
                                </button>&nbsp;<span id="upload_loadings" style="color: red;"></span>

                                <div class="form-group form-group-label">
                                <img id="account_img"
                                     style='width: 30%;font-family: "Helvetica","Arial",sans-serif;font-size: 14px;font-weight: 400;line-height: 20px;'
                                     src="{$agent->cash['img']}" />
                                </div>

                            </div>
                            <div class="card-action">
                                <div class="card-action-btn pull-left">

                                    <button class="btn btn-flat waves-attach" onClick="save_account()"><span
                                                class="icon">check</span>&nbsp;保存修改
                                    </button>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div aria-hidden="true" class="modal modal-va-middle fade" id="result" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-xs">
                        <div class="modal-content">
                            <div class="modal-inner">
                                <p class="h5 margin-top-sm text-black-hint" id="msg"></p>
                            </div>
                            <div class="modal-footer">
                                <p class="text-right">
                                    <button class="btn btn-flat btn-brand-accent waves-attach" data-dismiss="modal" type="button"
                                            id="result_ok">知道了
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
</main>


<div aria-hidden="true" class="modal modal-va-middle fade" id="coupon_modal" role="dialog"
     tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-heading">
                <a class="modal-close" data-dismiss="modal">×</a>
                <h2 class="modal-title">邮箱安全验证</h2>
            </div>
            <div class="modal-inner">
                <div class="form-group form-group-label">
                    <label class="floating-label" for="coupon">请输入验证码</label>
                    <input class="form-control maxwidth-edit" id="cash_email_code" type="text">
                </div>
            </div>
            <div class="modal-footer">
                <p class="text-right">
                    <button class="btn btn-flat btn-brand waves-attach" data-dismiss="modal"
                            id="coupon_input" type="button">确定
                    </button>
                </p>
            </div>
        </div>
    </div>
</div>


{include file='admin/footer.tpl'}
<script src="//cdn.bootcss.com/layer/3.0.1/layer.min.js"></script>

<script>

    var account = "{$agent->cash['account']}";
    var account_name = "{$agent->cash['name']}";
    var account_img = "{$agent->cash['img']}";
    function save_account() {
    if($$getValue('account') == account && $$getValue('account_name') == account_name && $("#account_img")[0].src == account_img ){
        $("#result").modal();
        $$.getElementById('msg').innerHTML = `没有任何修改`;
        return !1;
    }


        //邮箱验证
        $.ajax({
            type: "GET",
            url: "config/edit_email",
            dataType: "json",
            data: {
                act: 'cash'
            },
            success: (data) => {
                if (data.ret ==1) {
                    layer.msg(data.msg);
                    //
                } else {
                  layer.msg(data.msg);
                }
            },
            error: (jqXHR) => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${
                    data.msg
                } 出现了一些错误`;
            }
        })


        $("#coupon_modal").modal();
    }


    $("#coupon_input").click(function () {

        if($$getValue('cash_email_code') == ""){
            layer.msg('没有输入验证码');
            return !1;
        }
        $.ajax({
            type: "POST",
            url: "config/update_cash",
            dataType: "json",
            data: {
                cash_email_code: $$getValue('cash_email_code'),
                account_name: $$getValue('account_name'),
                account_code: $$getValue('account_code'),
                account: $$getValue('account'),
            },
            success: (data) => {
                if (data.ret) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    //
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            },
            error: (jqXHR) => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${
                    data.msg
                } 发生了错误`;
            }
        })
    });

    $('.up_account_img').click(function () {
        $('#upload_account').click();
    });


    $('#upload_account').change(function () {
        var fileM=document.querySelector("#upload_account");
            //获取文件对象，files是文件选取控件的属性，存储的是文件选取控件选取的文件对象，类型是一个数组
            var fileObj = fileM.files[0];
            //创建formdata对象，formData用来存储表单的数据，表单数据时以键值对形式存储的。
            var formData = new FormData();
            formData.append('file', fileObj);
            $.ajax({
                url: "config/cupdate",
                type: "post",
                dataType: "json",
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if(data.ret==1) {
                        $("#account_img").attr('src',data.url);
                        $("#account_code").val(data.url);
                        layer.msg(data.msg);
                    }else {
                        layer.msg(data.msg);
                    }
                },
            });

    });

    //发送验证码
    $(".post_email_code").click(function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = `发送中..`;
            $.ajax({
                type: "GET",
                url: "config/edit_email",
                dataType: "json",
                data: {
                    act: 'send'
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('ajax-user-port').innerHTML = data.msg;
                        $$.getElementById('msg').innerHTML = `${
                            data.msg
                        }`;
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `${
                        data.msg
                    } 出现了一些错误`;
                }
            })
    });

    //提交邮箱修改
    $("#ss-pwd-update").click(function () {
        var email_code = $("#email_code").val();
        var new_email = $("#new_email").val();

        $("#result").modal();
        $$.getElementById('msg').innerHTML = `提交中..`;
        $.ajax({
            type: "GET",
            url: "config/edit_email",
            dataType: "json",
            data: {
                email_code: email_code,
                new_email: new_email
            },
            success: (data) => {
                if (data.ret == 1) {
                    $("#result").modal();
                    $$.getElementById('ajax-user-passwd').innerHTML = new_email;
                    $$.getElementById('msg').innerHTML = '修改成功';
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            },
            error: (jqXHR) => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = ` 出现了一些错误`;
            }
        })
    })

</script>



{literal}
    <script>
        $(document).ready(function () {
            $("#portreset").click(function () {
                $.ajax({
                    type: "POST",
                    url: "resetport",
                    dataType: "json",
                    data: {},
                    success: (data) => {
                        if (data.ret) {
                            $("#result").modal();
                            $$.getElementById('ajax-user-port').innerHTML = data.msg;
                            $$.getElementById('msg').innerHTML = `设置成功，新端口是 ${
                                data.msg
                            }`;
                        } else {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = data.msg;
                        }
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${
                            data.msg
                        } 出现了一些错误`;
                    }
                })
            })
        })
    </script>
    <script>
        $(document).ready(function () {
            $("#portspecify").click(function () {
                $.ajax({
                    type: "POST",
                    url: "specifyport",
                    dataType: "json",
                    data: {
                        port: $$getValue('port-specify')
                    },
                    success: (data) => {
                        if (data.ret) {
                            $("#result").modal();
                            $$.getElementById('ajax-user-port').innerHTML = $$getValue('port-specify');
                            $$.getElementById('msg').innerHTML = data.msg;
                        } else {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = data.msg;
                        }
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${
                            data.msg
                        } 出现了一些错误`;
                    }
                })
            })
        })
    </script>

    <script>
        $(document).ready(function () {
            $("#setpac").click(function () {
                $.ajax({
                    type: "POST",
                    url: "pacset",
                    dataType: "json",
                    data: {
                        pac: $("#pac").text()
                    },
                    success: (data) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${
                            data.msg
                        } 出现了一些错误`;
                    }
                })
            })
        })
    </script>

    <script>
        //修改域名
        $(document).ready(function () {
            $("#domain-update").click(function () {
                $.ajax({
                    type: "GET",
                    url: "config/dupdate",
                    dataType: "json",
                    data: {
                        name: $$getValue('name'),
                        domain: $$getValue('domain')
                    },
                    success: (data) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${
                            data.msg
                        } 出现了一些错误`;
                    }
                })
            })
        })
    </script>
{/literal}
<script>

</script>

{literal}
    <script>
        $(document).ready(function () {
            $("#wechat-update").click(function () {
                $.ajax({
                    type: "POST",
                    url: "config/update_chatra",
                    dataType: "json",
                    data: {
                        chatra_id: $$getValue('wechat'),
                        enable_chatra: $$getValue('imtype')
                    },
                    success: (data) => {
                        if (data.ret) {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = data.msg;
                        } else {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = data.msg;
                        }
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${data.msg} 出现了一些错误`;
                    }
                })
            })
        })
    </script>

    <script>
        $(document).ready(function () {
            $("#ssr-update").click(function () {
                $.ajax({
                    type: "POST",
                    url: "ssr",
                    dataType: "json",
                    data: {
                        protocol: $$getValue('protocol'),
                        obfs: $$getValue('obfs'),
                        obfs_param: $$getValue('obfs-param')
                    },
                    success: (data) => {
                        if (data.ret) {
                            $("#result").modal();
                            $$.getElementById('ajax-user-protocol').innerHTML = $$getValue('protocol');
                            $$.getElementById('ajax-user-obfs').innerHTML = $$getValue('obfs');
                            $$.getElementById('ajax-user-obfs-param').innerHTML = $$getValue('obfs-param');
                            $$.getElementById('msg').innerHTML = data.msg;
                        } else {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = data.msg;
                        }
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${data.msg} 出现了一些错误`;
                    }
                })
            })
        })
    </script>


    <script>
        $(document).ready(function () {
            $("#relay-update").click(function () {
                $.ajax({
                    type: "POST",
                    url: "relay",
                    dataType: "json",
                    data: {
                        relay_enable: $$getValue('relay_enable'),
                        relay_info: $$getValue('relay_info')
                    },
                    success: (data) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${data.msg} 出现了一些错误`;
                    }
                })
            })
        })
    </script>

    <script>
        $(document).ready(function () {
            $("#unblock").click(function () {
                $.ajax({
                    type: "POST",
                    url: "unblock",
                    dataType: "json",
                    data: {},
                    success: (data) => {
                        if (data.ret) {
                            $("#result").modal();
                            $$.getElementById('ajax-block').innerHTML = `IP：${
                                data.msg
                            } 没有被封`;
                            $$.getElementById('msg').innerHTML = `IP：${
                                data.msg
                            } 解封成功过`;
                        } else {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = data.msg;
                        }
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${data.msg} 出现了一些错误`;
                    }
                })
            })
        })
    </script>


    <script>
        $(document).ready(function () {
            $("#ga-test").click(function () {
                $.ajax({
                    type: "POST",
                    url: "gacheck",
                    dataType: "json",
                    data: {
                        code: $$getValue('code')
                    },
                    success: (data) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${data.msg} 出现了一些错误`;
                    }
                })
            })
        })
    </script>


    <script>
        $(document).ready(function () {
            $("#ga-set").click(function () {
                $.ajax({
                    type: "POST",
                    url: "gaset",
                    dataType: "json",
                    data: {
                        enable: $$getValue('ga-enable')
                    },
                    success: (data) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${data.msg} 出现了一些错误`;
                    }
                })
            })
        })
    </script>

    <script>
        $(document).ready(function () {
            let newsspwd = Math.random().toString(36).substr(2);

        })
    </script>


    <script>
        $(document).ready(function () {
            $("#mail-update").click(function () {
                $.ajax({
                    type: "POST",
                    url: "mail",
                    dataType: "json",
                    data: {
                        mail: $$getValue('mail')
                    },
                    success: (data) => {
                        if (data.ret) {
                            $("#result").modal();
                            $$.getElementById('ajax-mail').innerHTML = ($$getValue('mail') === '1') ? '发送' : '不发送'
                            $$.getElementById('msg').innerHTML = data.msg;
                        } else {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = data.msg;
                        }
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${data.msg} 出现了一些错误`;
                    }
                })
            })
        })
    </script>
{/literal}
<script>
    $(document).ready(function () {
        $("#theme-update").click(function () {
            $.ajax({
                type: "POST",
                url: "theme",
                dataType: "json",
                data: {
                    theme: $$getValue('theme')
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href='/user/edit'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                {literal}
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `${
                        data.msg
                    } 出现了一些错误`;
                }
            })
        })
    })
</script>


    <script>
        $(document).ready(function () {
            $("#method-update").click(function () {
                $.ajax({
                    type: "POST",
                    url: "method",
                    dataType: "json",
                    data: {
                        method: $$getValue('method')
                    },
                    success: (data) => {
                        $$.getElementById('ajax-user-method').innerHTML = $$getValue('method');
                        if (data.ret) {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = '修改成功';
                        } else {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = data.msg;
                        }
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = `${
                            data.msg
                        } 出现了一些错误`;
                    }
                })
            })
        })
        
        $("#wechat-update").click(function () {
            console.log('status:'+$('#imtype').val());
            console.log('key:'+$('#wechat').val());
        });
    </script>
{/literal}
