<!doctype html>
<html lang="<?= esc(service('request')->getLocale() ?: 'ko') ?>">
	<head>
        <meta content="IE=11.0000" http-equiv="X-UA-Compatible">
        <meta charset="utf-8">
	    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		
		<title><?=$site_name?> <?= lang('Admin.admin_suffix') ?></title>

		<link rel="shortcut icon" type="image/png" href="/favicon.ico"/>
        <link rel="stylesheet" href="/assets/css/lib/jquery-ui.css">
        <link rel="stylesheet" href="/assets/css/lib/all.css">

        <link rel="stylesheet" href="/assets/css/main.css?v=2">
        <link rel="stylesheet" href="/assets/css/button1.css">
        <link rel="stylesheet" href="/assets/css/button2.css">
        <link rel="stylesheet" href="/assets/css/simplePagination.css">        

        <link rel="stylesheet" href="/assets/css/layer.css">

        <!-- JQuery 1.12.4--> 
	    <script src="/assets/js/lib/jquery-1.12.4.min.js"></script>
        <script src="/assets/js/lib/jquery-ui-1.12.1.min.js"></script>	
        <script src="/assets/js/lib/fontawesome.js"></script>
        <!-- <link rel="stylesheet" href="/assets/js/lib/sweetalert.min.css">
        <script src="/assets/js/lib/sweetalert2.min.js"></script> -->
        <script src="/assets/js/lib/sweetalert2.all.min.js"></script>

        <script src="/assets/js/header.js?v=1"></script>
        <script src="/assets/js/util.js?v=4"></script>
        <script src="/assets/js/window.js"></script>
        <script>
        window.ADMIN_I18N = {
            game_powerball: <?= json_encode(lang('Admin.game_powerball'), JSON_UNESCAPED_UNICODE) ?>,
            label_game_type: <?= json_encode(lang('Admin.label_game_type'), JSON_UNESCAPED_UNICODE) ?>,
            label_period: <?= json_encode(lang('Admin.label_period'), JSON_UNESCAPED_UNICODE) ?>,
            label_uid: <?= json_encode(lang('Admin.label_uid'), JSON_UNESCAPED_UNICODE) ?>,
            label_game_round: <?= json_encode(lang('Admin.label_game_round'), JSON_UNESCAPED_UNICODE) ?>,
            mark_odd: <?= json_encode(lang('Admin.mark_odd'), JSON_UNESCAPED_UNICODE) ?>,
            mark_even: <?= json_encode(lang('Admin.mark_even'), JSON_UNESCAPED_UNICODE) ?>,
            mark_under: <?= json_encode(lang('Admin.mark_under'), JSON_UNESCAPED_UNICODE) ?>,
            mark_over: <?= json_encode(lang('Admin.mark_over'), JSON_UNESCAPED_UNICODE) ?>,
            mark_big: <?= json_encode(lang('Admin.mark_big'), JSON_UNESCAPED_UNICODE) ?>,
            mark_mid: <?= json_encode(lang('Admin.mark_mid'), JSON_UNESCAPED_UNICODE) ?>,
            mark_small: <?= json_encode(lang('Admin.mark_small'), JSON_UNESCAPED_UNICODE) ?>,
            mark_power: <?= json_encode(lang('Admin.mark_power'), JSON_UNESCAPED_UNICODE) ?>,
            mark_power_odd: <?= json_encode(lang('Admin.mark_power_odd'), JSON_UNESCAPED_UNICODE) ?>,
            mark_power_even: <?= json_encode(lang('Admin.mark_power_even'), JSON_UNESCAPED_UNICODE) ?>,
            mark_normal: <?= json_encode(lang('Admin.mark_normal'), JSON_UNESCAPED_UNICODE) ?>,
            mark_pb: <?= json_encode(lang('Admin.mark_pb'), JSON_UNESCAPED_UNICODE) ?>,
            bet_waiting: <?= json_encode(lang('Admin.bet_waiting'), JSON_UNESCAPED_UNICODE) ?>,
            bet_lose: <?= json_encode(lang('Admin.bet_lose'), JSON_UNESCAPED_UNICODE) ?>,
            bet_win: <?= json_encode(lang('Admin.bet_win'), JSON_UNESCAPED_UNICODE) ?>,
            bet_void: <?= json_encode(lang('Admin.bet_void'), JSON_UNESCAPED_UNICODE) ?>
        };
        </script>
    </head>

    <body class="lang-<?= esc(service('request')->getLocale() ?: 'ko') ?>" style="margin:0px; padding:0px; background-color:#f5f5f5;">