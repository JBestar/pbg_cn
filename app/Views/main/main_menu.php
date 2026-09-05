
    <header class="admin-topbar">
        <button type="button" class="admin-menu-btn" id="btnAdminMenu" onclick="toggleAdminMenu();" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="admin-topbar-info">
            <span class="admin-topbar-item"><i class="far fa-circle"></i> <span id="spanUserName"></span></span>
    <?php if ($mb_level > LEVEL_AGENCY) {
        $isMaintainHdr = (new \App\Models\ConfSite_Model())->IsMaintain();
    ?>
            <span id="btnSiteMaintain"
                class="admin-maintain-btn"
                data-lock="<?= $isMaintainHdr ? '1' : '0' ?>"
                onclick="toggleSiteMaintain();">
                <?= $isMaintainHdr ? lang('Admin.opt_site_lock') : lang('Admin.label_site_maintain') ?>
            </span>
    <?php } ?>
            <span class="admin-topbar-item"><i class="far fa-circle"></i> <?= lang('Admin.money_hold') ?> : <span id="spanUserMoney" style="color:#ff0000;">0 원</span></span>
            <span class="admin-topbar-item"><i class="far fa-circle"></i> <?= lang('Admin.point_hold') ?> : <span id="spanUserPoint" style="color:#ff0000;">0 원</span></span>
    <?php if($mb_level == LEVEL_AGENCY) { ?>
            <span class="admin-topbar-item" style="cursor:pointer;" onclick="pointToMoney();">[<?= lang('Admin.point_convert') ?>]</span>
            <span class="admin-topbar-item"><i class="far fa-circle"></i> <?= lang('Admin.fee') ?> : <span id="spanGameRate" style="color:#ff0000;">0 %</span></span>
    <?php } ?>
            <span class="admin-topbar-item admin-topbar-ip"><i class="far fa-circle"></i> IP : <span id="spanUserIp"></span></span>
        </div>
        <div class="admin-topbar-actions">
    <?php if($mb_level == LEVEL_AGENCY) { ?>
            <button type="button" class="admin-action-btn admin-action-green" onclick="clickMenu('sub_charge');"><?= lang('Admin.btn_charge') ?></button>
            <button type="button" class="admin-action-btn admin-action-green" onclick="clickMenu('sub_exchange');"><?= lang('Admin.btn_exchange') ?></button>
    <?php } ?>
            <select id="adminLangSelect" class="admin-lang-select" onchange="changeAdminLang(this.value);">
                <option value="ko" <?= (isset($admin_locale) && $admin_locale==='ko') ? 'selected' : '' ?>><?= lang('Admin.lang_ko') ?></option>
                <option value="zh" <?= (isset($admin_locale) && $admin_locale==='zh') ? 'selected' : '' ?>><?= lang('Admin.lang_zh') ?></option>
                <option value="en" <?= (isset($admin_locale) && $admin_locale==='en') ? 'selected' : '' ?>><?= lang('Admin.lang_en') ?></option>
            </select>
            <button type="button" class="admin-action-btn admin-action-blue" onclick="clickMenu('setting');"><i class="fa fa-pencil-square-o"></i> <?= lang('Admin.setting') ?></button>
            <button type="button" class="admin-action-btn admin-action-red" onclick="logOut();"><i class="fas fa-sign-out-alt"></i> <?= lang('Admin.logout') ?></button>
        </div>
    </header>

    <div class="admin-nav-overlay" id="adminNavOverlay" onclick="closeAdminMenu();"></div>

    <div class="admin-main">
    <aside id="divLeftMenu" class="admin-sidebar">

        <div id="spanMainMenu0" class="spanLeftMenu <?=$menuitem_1?>" onclick="clickMenu('term_list');" ><i class="fas fa-calendar-alt"></i> <?= lang('Admin.menu_term') ?></div>

        <div class="spanLeftMenu"><i class="fas fa-gamepad"></i> <?= lang('Admin.menu_game') ?></div>
        <div id="spanMainMenu1" class="spanLeftSubMenu <?=$menuitem_2?>" onclick="clickMenu('game_result');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_game_result') ?></div>
        <div id="spanMainMenu2" class="spanLeftSubMenu <?=$menuitem_3?>" onclick="clickMenu('bet_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_bet_list') ?></div>

        <div class="spanLeftMenu"><i class="fas fa-sort-amount-down"></i> <?= lang('Admin.menu_sub') ?></div>
    <?php if($mb_level == LEVEL_AGENCY) { ?>
        <div id="spanMainMenu6" class="spanLeftSubMenu <?=$menuitem_5?>" onclick="clickMenu('member_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_agency') ?>&nbsp;&nbsp;</div>
    <?php } else if($mb_level > LEVEL_AGENCY) { ?>
        <div id="spanMainMenu6" class="spanLeftSubMenu <?=$menuitem_5?>" onclick="clickMenu('member_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_company') ?>&nbsp;&nbsp;</div>
    <?php }  ?>

        <div class="spanLeftMenu"><i class="fas fa-won-sign"></i> <?= lang('Admin.menu_money') ?></div>
        <div id="spanMainMenu8" class="spanLeftSubMenu <?=$menuitem_6?>" onclick="clickMenu('store_ce_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_store_ce') ?></div>
        <div id="spanMainMenu9" class="spanLeftSubMenu <?=$menuitem_7?>" onclick="clickMenu('agency_ce_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_agency_ce') ?></div>
        <div id="spanMainMenuPt" class="spanLeftSubMenu <?=$menuitem_11?>" onclick="clickMenu('point_convert_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_point_convert') ?></div>

        <div id="spanMainMenu10" class="spanLeftMenu <?=$menuitem_8?>" onclick="clickMenu('cancel_list');"><i class="fas fa-ban"></i> <?= lang('Admin.menu_cancel') ?></div>

        <?php if($mb_level == LEVEL_AGENCY) { ?>
        <div class="spanLeftMenu"><i class="fas fa-sticky-note"></i> <?= lang('Admin.menu_memo') ?></div>
        <div id="spanMainMenu11" class="spanLeftSubMenu <?=$menuitem_9?>" onclick="clickMenu('memo_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_memo_list') ?>&nbsp;&nbsp;</div>
        <div id="spanMainMenu12" class="spanLeftSubMenu <?=$menuitem_10?>" onclick="clickMenu('qna_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_qna') ?>&nbsp;&nbsp;
            <span id="spanQnaCnt" class="badge" style="background-color: rgb(153, 153, 153);">0</span>
        </div>
        <?php }  ?>

        <div class="spanLeftMenu" onclick="logOut();"><i class="fas fa-power-off"></i> <?= lang('Admin.logout') ?></div>
    </aside>

<script>
function changeAdminLang(lang) {
    var jsonData = JSON.stringify({ lang: lang });
    $.ajax({
        url: '/api/set_lang',
        data: { json_: jsonData },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                location.reload();
            } else if (jResult.status === 'logout') {
                location.href = '/pages/login';
            }
        }
    });
}
</script>


    <div class="divContent">

        <div class="admin-account-wrap">
        <table class="default_table">
            <tbody><tr>
                <th><?= lang('Admin.th_today_bet') ?></th>
                <th><?= lang('Admin.th_today_win') ?></th>
                <th><?= lang('Admin.th_today_point') ?></th>
                <th><?= lang('Admin.th_today_bet_pl') ?></th>
                <th><?= lang('Admin.th_today_charge') ?></th>
                <th><?= lang('Admin.th_today_exchange') ?></th>
                <th><?= lang('Admin.th_today_ce_pl') ?></th>
                <th><?= lang('Admin.th_month_egg_in') ?></th>
                <th><?= lang('Admin.th_month_egg_out') ?></th>
                <th><?= lang('Admin.th_month_charge') ?></th>
                <th><?= lang('Admin.th_month_exchange') ?></th>
                <th><?= lang('Admin.th_month_ce_pl') ?></th>
            </tr>
            <tbody id="tbAccount">
                <tr>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney"><font color="#0000fe">0</font></td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney"><font color="#0000fe">0</font></td>
                </tr>
            </tbody>
        </table>
        </div>
