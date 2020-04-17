<!DOCTYPE html>
<html lang="en">

<head>
    {include file='user/head.tpl'}
    <title>开通代理 &mdash; {$config["appName"]}</title>
</head>
</head>

<body>
<div id="app">
    <div class="main-wrapper">
        {include file='user/navbar.tpl'}

        <style>
            .pricing .pricing-padding {
                padding: 20px;
                padding: 20px 0.25em 2ex 2%;
                text-align: center;
            }

            .pricing .pricing-details .pricing-item .pricing-item-icon {
                width: 20px;
                height: 20px;
                line-height: 20px;
                border-radius: 50%;
                text-align: center;
                background-color: #ffc107;
                color: #fff;
                margin-right: 10px;
            }

            .pricing.pricing-highlight .pricing-cta a {
                background-color: #dc3545;
                color: #fff;
            }

            a.tag-cyan, .tag-cyan {
                background: #e6fffb;
                color: #13c2c2;
                border-color: #87e8de;
            }
            a.tag-green, .tag-green {
                background: #f6ffed;
                color: #52c41a;
                border-color: #b7eb8f;
            }
            a.tag-red, .tag-red {
                background: #fff1f0;
                color: #f5222d;
                border-color: #ffa39e;
            }
            a.tag-blue, .tag-blue {
                background: #e6f7ff;
                color: #1890ff;
                border-color: #91d5ff;
            }

            a.card-tag, .card-tag {
                font-size: 12px;
                text-decoration: none;
                padding: 1px 6px 0;
                border-radius: 3px;
                border: 1px solid;
                height: 23px;
            }
        </style>

        <div id="main-page" class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>开通代理</h1>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-hero">
                                <div class="card-header" style="border-radius: 3px;box-shadow: 0 2px 6px #acb5f6;border: none;">
                                    <div class="card-icon">
                                        <i class="fas fa-check"></i>
                                    </div>

                                    <div class="card-inner">
                                        <p>1、可绑定自己的顶级域名，彰显个性。</p>
                                        <p>2、信息隔离，你的用户只会看到你的各项联系信息，后台可随时修改。</p>
                                        <p>3、可免费招收下级代理商，下级出售套餐你就可以赚钱</p>
                                        <p>4、前台套餐价格自定义，赠送在线支付接口，用户在线付款即可24H自助购买流量</p>
                                        <p>5、无需自己购置/维护服务器，坐享 <b><code style="color:#ffeb3b;">{{$config["appName"]}}</code></b> 各项资源和节点更新</p>
                                        <hr>
                                        <p>★ 本站同时接收运营商，支持将你的用户数据导入本站和提供最优惠的大客户价格，无缝接入本站系统！</p>
                                        <p><h5>当前余额：¥ {$user->money}</h5></p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>


            <div id="main-page" class="main-content" style="padding-top: 5px">
                <section class="section">

                    <div class="section-body">

                        <div class="row">
                        {foreach $levels as $level}
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="pricing pricing-highlight">
                                    <div class="pricing-title">
                                        <span>Lv.</span>{$level->level}  {$level->name}
                                    </div>
                                    <div class="pricing-padding">
                                        <div class="pricing-price">
                                            <div>¥{$level->price}</div>
                                            <div>{$level->tag}</div>
                                        </div>
                                        <div class="pricing-details">
                                            {$level->shop}
                                        </div>
                                    </div>

                                    <div class="pricing-details">
                                        <div class="pricing-item">
                                            <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                            <div class="pricing-item-label"> 用户管理</div>
                                        </div>

                                        <div class="pricing-item">
                                            <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                            <div class="pricing-item-label"> 套餐定价</div>
                                        </div>

                                        <div class="pricing-item">
                                            <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                            <div class="pricing-item-label"> 收益提现</div>
                                        </div>
                                    </div>

                                    <div class="pricing-cta">
                                        <a href="##" data-toggle="modal" data-target="#legacy-modal-1" onclick="buy({$level->id})">开通 <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </div>

                    </div>
                </section>
            </div>

        </div>
    </div>
</main>


{include file='user/footer.tpl'}

        {include file='user/scripts.tpl'}