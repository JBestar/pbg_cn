

    <div class="divTitle"><?= lang('Admin.title_notice_mgmt') ?></div>
    <style>
    .notice-modal{position:fixed;left:50%;top:80px;transform:translateX(-50%);width:min(970px,calc(100% - 20px));border:1px solid #010101;background-color:#fefefe;display:none;z-index:2010;}
    .notice-modal .divList{height:auto;padding:10px;}
    .notice-form-table{width:100%;table-layout:fixed;}
    .notice-form-table th{width:110px;background:#f1f5f9;white-space:nowrap;}
    .notice-form-table td{text-align:left;padding:6px;}
    .notice-form-table input,.notice-form-table textarea{width:100%;box-sizing:border-box;}
    .notice-form-table input{height:30px;}
    .notice-form-table textarea{height:220px;resize:vertical;line-height:1.6;}
    .notice-view-table{width:100%;table-layout:fixed;}
    .notice-view-table th{background:#f1f5f9;white-space:nowrap;}
    .notice-view-table th.notice-col-title,.notice-view-table td.notice-col-title{width:220px;}
    .notice-view-table th.notice-col-date,.notice-view-table td.notice-col-date{width:160px;}
    .notice-view-table td{vertical-align:top;text-align:center;padding:8px 6px;word-break:break-all;}
    .notice-view-table td.notice-col-body{text-align:left;}
    .notice-view-body{white-space:pre-wrap;line-height:1.6;max-height:300px;overflow-y:auto;word-break:break-all;}
    .notice-modal-actions{text-align:center;padding:12px 0 4px;}
    .notice-modal-actions button{margin:0 4px;}
    .notice-title-cell{text-align:left;}
    </style>
    <div class="divSearch">
        <input type="text" id="inputDateS" name="inputDateS" value="" placeholder="" autocomplete="off" class="inputDate"
               onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">&nbsp;~&nbsp;
        <input type="text" id="inputDateE" name="inputDateE" value="" placeholder="" autocomplete="off" class="inputDate"
               onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
        &nbsp;&nbsp;&nbsp;<?= lang('Admin.th_title') ?> : <input type="text" id="inputNoticeTitle" name="inputNoticeTitle" class="inputDate" value="">
        &nbsp;&nbsp;<button type="button" class="btn_search btn_icon" onclick="reqSearch();" title="<?= lang('Admin.btn_search') ?>" aria-label="<?= lang('Admin.btn_search') ?>"><i class="fas fa-search"></i></button>
        <button type="button" class="btn_blue" style="float:right; margin-right:10px; padding:5px 14px; white-space:nowrap;" onclick="showWriteNotice();"><?= lang('Admin.btn_write_notice') ?></button>
    </div>
    <div class="divList">
        <table class="default_table">
            <thead>
                <tr>
                    <th style="width:70px;"><?= lang('Admin.th_no') ?></th>
                    <th><?= lang('Admin.th_title') ?></th>
                    <th style="width:170px;"><?= lang('Admin.th_reg_date') ?></th>
                    <th style="width:110px;"><?= lang('Admin.th_view_content') ?></th>
                    <th style="width:110px;"><?= lang('Admin.th_edit_delete') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList">
            </tbody>
        </table>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td id="tdPageNation" align="center" height="50">
                        <a class="light-theme" id="aPagePrev" href="javascript:prevPage();"><span class="light-theme"><<</span></a>
                        <span id="spanPaginationNum"></span>
                        <a class="light-theme" id="aPageNext" href="javascript:nextPage();"><span class="light-theme">>></span></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

<!-- <div class="divContent"> -->
</div>


<div id="divEditNotice" class="notice-modal">
    <div class="divTitle" id="divEditNoticeTitle"><?= lang('Admin.title_notice_write') ?></div>
    <div class="divList">
        <input type="hidden" id="noticeEditId" value="0">
        <table class="default_table notice-form-table">
            <tbody>
                <tr>
                    <th><?= lang('Admin.th_title') ?></th>
                    <td><input type="text" id="noticeEditTitle" maxlength="200" autocomplete="off"></td>
                </tr>
                <tr>
                    <th><?= lang('Admin.th_content') ?></th>
                    <td><textarea id="noticeEditContent"></textarea></td>
                </tr>
            </tbody>
        </table>
        <div class="notice-modal-actions">
            <button type="button" class="btn_blue" id="btnNoticeSave" onclick="reqSaveNotice();"><?= lang('Admin.btn_register') ?></button>
            <button type="button" class="btn_red" onclick="closeEditNotice();"><?= lang('Admin.btn_close') ?></button>
        </div>
    </div>
</div>


<div id="divViewNotice" class="notice-modal">
    <div class="divTitle"><?= lang('Admin.title_notice_view') ?></div>
    <div class="divList">
        <table class="default_table notice-view-table">
            <thead>
                <tr>
                    <th class="notice-col-title"><?= lang('Admin.th_title') ?></th>
                    <th class="notice-col-date"><?= lang('Admin.th_reg_date') ?></th>
                    <th><?= lang('Admin.th_content') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="tdNoticeViewTitle" class="notice-col-title"></td>
                    <td id="tdNoticeViewDate" class="notice-col-date"></td>
                    <td class="notice-col-body"><div id="noticeViewContent" class="notice-view-body"></div></td>
                </tr>
            </tbody>
        </table>
        <div class="notice-modal-actions">
            <button type="button" class="btn_red" onclick="closeViewNotice();"><?= lang('Admin.btn_close') ?></button>
        </div>
    </div>
</div>

<script>
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.btn_delete = <?= json_encode(lang('Admin.btn_delete'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_edit = <?= json_encode(lang('Admin.btn_edit'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_register = <?= json_encode(lang('Admin.btn_register'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.th_view_content = <?= json_encode(lang('Admin.th_view_content'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.title_notice_write = <?= json_encode(lang('Admin.title_notice_write'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.title_notice_edit = <?= json_encode(lang('Admin.title_notice_edit'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.msg_notice_title_req = <?= json_encode(lang('Admin.msg_notice_title_req'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.msg_notice_content_req = <?= json_encode(lang('Admin.msg_notice_content_req'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.msg_notice_saved = <?= json_encode(lang('Admin.msg_notice_saved'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.msg_notice_fail = <?= json_encode(lang('Admin.msg_notice_fail'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.msg_notice_delete_confirm = <?= json_encode(lang('Admin.msg_notice_delete_confirm'), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="/assets/js/notice_list.js?v=1"></script>
