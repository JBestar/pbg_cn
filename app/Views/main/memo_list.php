

    
    
    <div class="divTitle"><?= lang('Admin.title_memo_mgmt') ?></div>
    <div class="divSearch">
        <!--
		<form id="formSearch" method="get" action="/Main/bet_list">
        -->
			<input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
            <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
			&nbsp;&nbsp;&nbsp;<?= lang('Admin.label_receiver') ?> : <input type="text" id="inputUserID" name="inputUserID" class="inputDate" value="">
			&nbsp;&nbsp;<input type="submit" onclick="reqSearch();" class="btn_search" value="<?= lang('Admin.btn_search') ?>">
            <button class="btn_blue" style="float:right; margin-right:10px; padding:5px 14px; white-space:nowrap;" onclick="showEditMemo();"><?= lang('Admin.btn_send_memo') ?></button>
	</div>
    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
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


<div id="divEditMemo" style="position:absolute; left:calc(50% - 500px); top:100px; width:970px; border:1px solid #010101; background-color:#fefefe; display:none;">

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

<div id="divModMemo" style="position:absolute; left:calc(50% - 500px); top:100px; width:970px; border:1px solid #010101; background-color:#fefefe; display:none;">

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


<div id="divViewMemo" style="position:absolute; left:calc(50% - 500px); top:100px; width:1000px; border:1px solid #010101; background-color:#fefefe; display:none;">

    <div class="divTitle">쪽지보기</div>

    <div class="divList" style="text-align:center; height: 320px;">
        <table class="default_table">
            <tbody>
                <tr>
                    <td style="width:60px; text-align:center;">수신자</td>
                    <td id="tdViewRecvUid" style="padding:0px 5px 0px 5px;"></td>
                </tr>
                <tr>
                    <td style="width:60px; text-align:center;">제목</td>
                    <td id="tdViewTitle" style="padding:0px 5px 0px 5px;"></td>
                </tr>
                <tr>
                    <td style="width:60px; text-align:center;">내용</td>
                    <td>
                        <textarea id="memoViewContent" style="width:900px; height:190px; resize:none; line-height:26px;"></textarea>
                    </td>
                </tr>
            </tbody>
    
        </table>
        <div class="btn btn-secondary" style="position:absolute; left:calc(50% - 40px); top:270px; width:80px; height:25px; line-height:25px;" onclick="closeViewMemo();">닫기</div>
    </div>
    
</div>

<script src="/assets/js/memo_list.js"></script>