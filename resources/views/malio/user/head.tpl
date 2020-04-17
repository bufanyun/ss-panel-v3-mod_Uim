<meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

<!-- General CSS Files -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.8.2/css/all.min.css">

<!-- Template CSS -->
<link rel="stylesheet" href="/theme/malio/css/style-{$malio_config['theme_color']}.css">
<link rel="stylesheet" href="/theme/malio/assets/css/components.css">

<!-- Custom CSS -->
<link rel="stylesheet" href="/theme/malio/css/malio.css">

{if $malio_config['google_analytics_code'] != ''}
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$malio_config['google_analytics_code']}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  {literal}function gtag() {dataLayer.push(arguments);}{/literal}
  gtag('js', new Date());
  gtag('config', '{$malio_config['google_analytics_code']}');
</script>
{/if}

{if $config["sspanelAnalysis"] == 'true'}
<!-- Google Analytics -->
{*<script>*}
{*    window.ga = window.ga || function () {*}
{*        (ga.q = ga.q || []).push(arguments)*}
{*    };*}
{*    ga.l = +new Date;*}
{*    ga('create', 'UA-143778338-1', 'auto');*}
{*    var hostDomain = window.location.host || document.location.host || document.domain;*}
{*    ga('set', 'dimension1', hostDomain);*}
{*    ga('send', 'pageview');*}
{*    (function () {*}
{*        function perfops() {*}
{*            var js = document.createElement('script');*}
{*            js.src = 'https://cdn.jsdelivr.net/npm/perfops-rom';*}
{*            document.body.appendChild(js);*}
{*        }*}
{*        if (document.readyState === 'complete') {*}
{*            perfops();*}
{*        } else {*}
{*            window.addEventListener('load', perfops);*}
{*        }*}
{*    })();*}
{*</script>*}
<script async src="https://www.google-analytics.com/analytics.js"></script>
<!-- End Google Analytics -->
{/if}

{if $malio_config['enable_crisp'] == true}{include file='crisp.tpl'}{/if}
{if $malio_config['enable_chatra'] == true}{include file='chatra.tpl'}{/if}


<div id="ascrail2000" class="nicescroll-rails nicescroll-rails-vr" style="width: 8px; z-index: 892; cursor: grab; position: fixed; top: 0px; left: 242px; height: 731px; touch-action: none; display: block; opacity: 0;">

  <div class="nicescroll-cursors" style="position: relative; top: 0px; float: right; width: 6px; height: 631px; background-color: rgb(66, 66, 66); border: 1px solid rgb(255, 255, 255); background-clip: padding-box; border-radius: 5px; touch-action: none;">

  </div>
</div>

<div id="ascrail2000-hr" class="nicescroll-rails nicescroll-rails-hr" style="height: 8px; z-index: 892; top: 723px; left: 0px; position: fixed; display: none; width: 242px; opacity: 0;">
  <div class="nicescroll-cursors" style="position: absolute; top: 0px; height: 6px; width: 250px; background-color: rgb(66, 66, 66); border: 1px solid rgb(255, 255, 255); background-clip: padding-box; border-radius: 5px; left: 0px;">

  </div>
</div>


<script>
  var clash = '{$subInfo["clash"]}';
  var quantumult = '{if $malio_config["quantumult_sub_type"]=="v2ray"}{$subInfo["quantumult_v2"]}{elseif $malio_config["quantumult_sub_type"]=="ss"}{$subInfo["ss"]}{elseif $malio_config["quantumult_sub_type"]=="ssr"}{$subInfo["ssr"]}{/if}';
  var shadowrocket = '{$subInfo["shadowrocket"]}';
</script>