


        <div class="divTitle" style="margin-top:20px;">환전신청</div>

        <div class="divList" style="width:calc(100% - 50px); text-align:left; padding: 10px 10px 10px 10px;">
            <b>요청금액</b> <input type="text" id="inputMoney" name="amount" value="0" style="width:100px; text-align:right;" readonly="">
            <button type="button" class="btn_blue" onclick="onMoneyClick(10000);">1만원</button>
            <button type="button" class="btn_blue" onclick="onMoneyClick(50000);">5만원</button>
            <button type="button" class="btn_blue" onclick="onMoneyClick(100000);">10만원</button>
            <button type="button" class="btn_blue" onclick="onMoneyClick(500000);">50만원</button>
            <button type="button" class="btn_blue" onclick="onMoneyClick(1000000);">100만원</button>
            <button type="button" class="btn_blue" onclick="onMoneyClick(0);">초기화</button><br><br>

            <b>은&nbsp;행&nbsp;명</b> <input type="text" id="bank_name" name="bank_name" value="1" style="width:100px;" readonly="true">
            <b>계좌번호</b> <input type="text" id="bank_number" name="bank_number" value="1" style="width:160px;" readonly="true">
            <b>예금주명</b> <input type="text" id="bank_owner" name="bank_owner" value="1" style="width:100px;" readonly="true">
            <b>출금비번</b> <input type="password" id="bank_pwd" name="exchange_pass" size="10">
            <button type="button" id="btn_withdraw_req" class="btn_red" style="height:25px;" onclick="reqExchange()">환전신청</button>
        </div>

        <div style="position:relative; height:20px;"></div>
        <div class="divTitle">환전내역</div>
        <div class="divList">
            <table class="default_table">
                <thead>
                    <tr>
                        <th><?= lang('Admin.th_req_amount') ?></th>
                        <th><?= lang('Admin.th_req_date') ?></th>
                        <th><?= lang('Admin.th_proc_date') ?></th>
                        <th><?= lang('Admin.th_status') ?></th>
                        <th><?= lang('Admin.th_delete') ?></th>
                    </tr>
                </thead>
                <tbody id="tbodyList">
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


    <script src="/assets/js/sub_exchange.js"></script>