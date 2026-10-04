

    
    
    <div class="divTitle"><?= lang('Admin.title_memo_mgmt') ?></div>
    <style>
    .memo-unread td{font-weight:700;background:#fff6e8;}
    .memo-view-table{width:100%;table-layout:fixed;}
    .memo-view-table th{background:#f1f5f9;white-space:nowrap;}
    .memo-view-table th.memo-col-uid,.memo-view-table td.memo-col-uid{width:120px;}
    .memo-view-table th.memo-col-title,.memo-view-table td.memo-col-title{width:200px;}
    .memo-view-table td{vertical-align:top;text-align:center;padding:8px 6px;word-break:break-all;}
    .memo-view-table td.memo-col-body{text-align:left;}
    .memo-view-body{white-space:pre-wrap;line-height:1.6;max-height:260px;overflow-y:auto;word-break:break-all;}
    .memo-view-actions{text-align:center;padding:12px 0 6px;}
    </style>
    <div class="divSearch">
        <!--
		<form id="formSearch" method="get" action="/Main/bet_list">
        -->
			<input type="text" id="inputDateS" name="inputDateS" value="" placeholder="" autocomplete="off" class="inputDate"
                   onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">&nbsp;~&nbsp;
            <input type="text" id="inputDateE" name="inputDateE" value="" placeholder="" autocomplete="off" class="inputDate"
                   onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
			&nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> : <input type="text" id="inputUserID" name="inputUserID" class="inputDate" value="">
			&nbsp;&nbsp;<button type="button" class="btn_search btn_icon" onclick="reqSearch();" title="<?= lang('Admin.btn_search') ?>" aria-label="<?= lang('Admin.btn_search') ?>"><i class="fas fa-search"></i></button>
            <button class="btn_blue" style="float:right; margin-right:10px; padding:5px 14px; white-space:nowrap;" onclick="showEditMemo();"><?= lang('Admin.btn_send_memo') ?></button>
	</div>
    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_sender') ?></th>
                    <th><?= lang('Admin.th_receiver') ?></th>
                    <th><?= lang('Admin.th_title') ?></th>
					<th><?= lang('Admin.th_reg_date') ?></th>
					<th><?= lang('Admin.th_view_content') ?></th>
                    <th><?= lang('Admin.th_edit_delete') ?></th>
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
                        <span id="spanPaginationNum">
                            <!--
                            <a class="light-theme" ><span class="current">1</span></a>
                            <a class="light-theme" ><span class="light-theme">2</span></a>
                            -->
                        </span>
                        <a class="light-theme" id="aPageNext" href="javascript:nextPage();"><span class="light-theme">>></span></a>
                    </td>
                </tr>
            </tbody>
        </table>

	</div>

    

<!-- <div class="divContent"> -->    
</div>


<div id="divEditMemo" style="position:absolute; left:calc(50% - 500px); top:100px; width:970px; border:1px solid #010101; background-color:#fefefe; display:none; z-index:2010;">

    <div class="divTitle">쪽지발송</div>
    <div class="divList" style="height: 320px;">
        <div style="position:absolute; left:5px; top:5px; width:90px; height:36px; background-color:#777777; line-height:36px; font-size:14px; text-align:center; color:#fefefe;">수신자</div>
		<div style="position:absolute; left:5px; top:43px; width:90px; height:36px; background-color:#777777; line-height:36px; font-size:14px; text-align:center; color:#fefefe;">제목</div>
		<div style="position:absolute; left:5px; top:80px; width:90px; height:190px; background-color:#777777; line-height:36px; font-size:14px; text-align:center; color:#fefefe;">내용</div>
        <select id="memoEditRecverId" name="memoSenderId" style="position:absolute; left:98px; top:8px; width:838px; height:24px;">
            <!--
            <option value="여수2">여수2 (여수2)</option>
            -->        
        </select>
        
        <input id="memoEditTitle" style="position:absolute; left:98px; top:45px; width:838px; height:24px;">
		<textarea id="memoEditContent" style="position:absolute; left:98px; top:80px; width:838px; height:182px; resize:none; line-height:26px;"></textarea>

		<div class="btn btn-primary" style="position:absolute; left:calc(50% - 100px); top:272px; width:100px; height:25px; line-height:25px;" onclick="reqEditMemo();">쪽지발송</div>
        <div class="btn btn-secondary" style="position:absolute; left:calc(50% + 50px); top:272px; width:50px; height:25px; line-height:25px;" onclick="closeEditMemo();">닫기</div>
	</div>

</div>

<div id="divModMemo" style="position:absolute; left:calc(50% - 500px); top:100px; width:970px; border:1px solid #010101; background-color:#fefefe; display:none; z-index:2010;">

    <div class="divTitle">쪽지수정</div>
    <div class="divList" style="height: 320px;">
        <div style="position:absolute; left:5px; top:5px; width:90px; height:36px; background-color:#777777; line-height:36px; font-size:14px; text-align:center; color:#fefefe;">수신자</div>
		<div style="position:absolute; left:5px; top:43px; width:90px; height:36px; background-color:#777777; line-height:36px; font-size:14px; text-align:center; color:#fefefe;">제목</div>
		<div style="position:absolute; left:5px; top:80px; width:90px; height:190px; background-color:#777777; line-height:36px; font-size:14px; text-align:center; color:#fefefe;">내용</div>
        <input id="memoModeId" hidden>
        <select id="memoModRecverId" name="memoModSenderId" style="position:absolute; left:98px; top:8px; width:838px; height:24px;" disabled>
            <!--
            <option value="여수2">여수2 (여수2)</option>
            -->        
        </select>
        
        <input id="memoModTitle" style="position:absolute; left:98px; top:45px; width:838px; height:24px;">
		<textarea id="memoModContent" style="position:absolute; left:98px; top:80px; width:838px; height:182px; resize:none; line-height:26px;"></textarea>

		<div class="btn btn-primary" style="position:absolute; left:calc(50% - 100px); top:272px; width:100px; height:25px; line-height:25px;" onclick="reqModeMemo();">쪽지수정</div>
        <div class="btn btn-secondary" style="position:absolute; left:calc(50% + 50px); top:272px; width:50px; height:25px; line-height:25px;" onclick="closeModeMemo();">닫기</div>
	</div>

</div>


<div id="divViewMemo" style="position:absolute; left:calc(50% - 500px); top:100px; width:1000px; border:1px solid #010101; background-color:#fefefe; display:none; z-index:2010;">

    <div class="divTitle">쪽지보기</div>

    <div class="divList" style="height:auto; padding:8px;">
        <table class="default_table memo-view-table">
            <thead>
                <tr>
                    <th class="memo-col-uid"><?= lang('Admin.th_sender') ?></th>
                    <th class="memo-col-uid"><?= lang('Admin.th_receiver') ?></th>
                    <th class="memo-col-title"><?= lang('Admin.th_title') ?></th>
                    <th><?= lang('Admin.th_content') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="tdViewSendUid" class="memo-col-uid"></td>
                    <td id="tdViewRecvUid" class="memo-col-uid"></td>
                    <td id="tdViewTitle" class="memo-col-title"></td>
                    <td class="memo-col-body"><div id="memoViewContent" class="memo-view-body"></div></td>
                </tr>
            </tbody>
        </table>
        <div class="memo-view-actions">
            <button type="button" class="btn_red" onclick="closeViewMemo();"><?= lang('Admin.btn_close') ?></button>
        </div>
    </div>
    
</div>

<script>
window.ADMIN_UID = <?= json_encode((string)(isset($memo_me_uid) ? $memo_me_uid : ''), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.btn_send_memo = <?= json_encode(lang('Admin.btn_send_memo'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_delete = <?= json_encode(lang('Admin.btn_delete'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.th_view_content = <?= json_encode(lang('Admin.th_view_content'), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="/assets/js/memo_list.js?v=5"></script>