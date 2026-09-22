

    
    <div class="divTitle"><?= lang('Admin.title_qna_mgmt') ?></div>
    
    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_sender') ?></th>
                    <th><?= lang('Admin.th_title') ?></th>                    
					<th><?= lang('Admin.th_status') ?></th>
					<th><?= lang('Admin.th_reg_date') ?></th>
					<th><?= lang('Admin.th_reply') ?></th>
					<th><?= lang('Admin.th_delete') ?></th>                    
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


<div id="divAnswerQna" style="position:absolute; left:calc(50% - 500px); top:100px; width:970px; border:1px solid #010101; background-color:#fefefe; display:none;">

    <div class="divTitle">문의답변</div>
    <div class="divList" style="height: 510px;">
        <input id="qnaId" type="hidden" disabled>

        <div style="position:absolute; left:5px; top:5px; width:90px; height:36px; background-color:#777777; line-height:36px; font-size:14px; text-align:center; color:#fefefe;">발송자</div>
		<div style="position:absolute; left:5px; top:43px; width:90px; height:36px; background-color:#777777; line-height:36px; font-size:14px; text-align:center; color:#fefefe;">제목</div>
		<div style="position:absolute; left:5px; top:80px; width:90px; height:190px; background-color:#777777; line-height:36px; font-size:14px; text-align:center; color:#fefefe;">문의내용</div>
        <div style="position:absolute; left:5px; top:272px; width:90px; height:190px; background-color:#777777; line-height:36px; font-size:14px; text-align:center; color:#fefefe;">답변내용</div>
        
        <input id="qnaSenderId" style="position:absolute; left:98px; top:8px; width:838px; height:24px;" disabled>
        <input id="qnaTitle" style="position:absolute; left:98px; top:45px; width:838px; height:24px;" disabled>
		<textarea id="qnaContent" style="position:absolute; left:98px; top:80px; width:838px; height:182px; resize:none; line-height:26px;" disabled></textarea>
        <textarea id="qnaAnswer" style="position:absolute; left:98px; top:272px; width:838px; height:182px; resize:none; line-height:26px;"></textarea>

		<div class="btn btn-primary" style="position:absolute; left:calc(50% - 100px); top:462px; width:100px; height:25px; line-height:25px;" onclick="reqAnswerQna();">답변하기</div>
        <div class="btn btn-secondary" style="position:absolute; left:calc(50% + 50px); top:462px; width:50px; height:25px; line-height:25px;" onclick="closeAnswerQna();">닫기</div>
	</div>

</div>



<script src="/assets/js/qna_list.js?v=2"></script>