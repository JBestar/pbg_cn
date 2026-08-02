


        <div class="divTitle">충전요청</div>


        <div class="divInfoBox" style="line-height:34px; font-size:14px;">
            <!-- b>계좌문의</b>
            <a class="btn_green" id="btn_bank_info" onclick="askBankInfo();">입금계좌 요청</a>
            <span id="str_bank_info" class="ui">(계좌요청을 하시면 입금계좌를 안내해 드립니다)</span> <br/ -->
            <b>요청금액</b> <input type="text" id="inputMoney" name="amount" value="0" size="12" readonly="" style="width:120px; text-align:right;">
            <button type="button" class="btn_blue" onclick="onMoneyClick(10000);">1만원</button>
            <button type="button" class="btn_blue" onclick="onMoneyClick(50000);">5만원</button>
            <button type="button" class="btn_blue" onclick="onMoneyClick(100000);">10만원</button>
            <button type="button" class="btn_blue" onclick="onMoneyClick(500000);">50만원</button>
            <button type="button" class="btn_blue" onclick="onMoneyClick(1000000);">100만원</button>
            <button type="button" class="btn_blue" onclick="onMoneyClick(0);">초기화</button><br>
            <b>입금자명</b> <input type="text" id="bank_member" name="bank_member" size="12" value="1" style="width:120px;" readonly="true">
            <button type="button" name="now_charge" id="charge_btn" class="btn_red" onclick="reqCharge();">충전신청</button>
        </div>

        <div style="position:relative; height:20px;"></div>
        <div class="divTitle">충전내역</div>
        <div class="divList">
            <table class="default_table">
                <thead>
                    <tr>
                        <th><?= lang('Admin.th_depositor') ?></th>
                        <th><?= lang('Admin.th_req_amount') ?></th>
                        <th><?= lang('Admin.th_req_date') ?></th>
                        <th><?= lang('Admin.th_proc_date') ?></th>
                        <th><?= lang('Admin.th_status') ?></th>
                        <th><?= lang('Admin.th_delete') ?></th>
                    </tr>
                </thead>
                <tbody id="tbodyList">
                    <!--
                    <tr>
                        <td class="tdDate">1</td>
                        <td class="tdDate">100,000</td>
                        <td class="tdDate">2021-10-11 15:48:41.450</td>
                        <td class="tdDate">2021-10-11 15:49:51.000</td>
                    
                        <td class="tdDate">완료</td>
                        <td class="tdDate">
                            <button type="button" class="btn_red" onclick="deleteCharge(90114);">삭제</button>
                        </td>
                    </tr>
                    -->
                </tbody>
            </table>
        </div>

        <div class="divPaging">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                        <td id="tdPageNation" align="center" height="50" style="display:none;">
                            <a class="light-theme" id="aPagePrev" href="javascript:prevPage();"><span class="light-theme"><<</span></a>
                            <span id="spanPaginationNum">
                                
                            </span>
                            <a class="light-theme" id="aPageNext" href="javascript:nextPage();"><span class="light-theme">>></span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>



    <!-- <div class="divContent"> -->    
    </div>


    <script src="/assets/js/sub_charge.js"></script>