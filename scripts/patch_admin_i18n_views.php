<?php
/**
 * One-shot: replace hardcoded Korean UI strings in admin views with lang().
 *   D:\xampp\php\php.exe D:\xampp\htdocs\pbg_cn\scripts\patch_admin_i18n_views.php
 */
$dir = 'D:/xampp/htdocs/pbg_cn_adm/app/Views/main';

$replacements = [
    'value="검색"' => 'value="<?= lang(\'Admin.btn_search\') ?>"',
    'value="검색" >' => 'value="<?= lang(\'Admin.btn_search\') ?>" >',
    'value="새로고침"' => 'value="<?= lang(\'Admin.btn_refresh\') ?>"',
    '>새로고침</button>' => '><?= lang(\'Admin.btn_refresh\') ?></button>',
    '>쪽지발송</button>' => '><?= lang(\'Admin.btn_send_memo\') ?></button>',

    '<th>일자</th>' => '<th><?= lang(\'Admin.th_date\') ?></th>',
    '<th>배팅금액</th>' => '<th><?= lang(\'Admin.th_bet_amount\') ?></th>',
    '<th>당첨금액</th>' => '<th><?= lang(\'Admin.th_win_amount\') ?></th>',
    '<th>포인트</th>' => '<th><?= lang(\'Admin.th_point\') ?></th>',
    '<th>배팅손익</th>' => '<th><?= lang(\'Admin.th_bet_pl\') ?></th>',
    '<th>알충전</th>' => '<th><?= lang(\'Admin.th_egg_in\') ?></th>',
    '<th>알회수</th>' => '<th><?= lang(\'Admin.th_egg_out\') ?></th>',
    '<th>충전</th>' => '<th><?= lang(\'Admin.th_charge\') ?></th>',
    '<th>환전</th>' => '<th><?= lang(\'Admin.th_exchange\') ?></th>',
    '<th>충환손익</th>' => '<th><?= lang(\'Admin.th_ce_pl\') ?></th>',
    '<th>신청금액</th>' => '<th><?= lang(\'Admin.th_req_amount\') ?></th>',
    '<th>신청일</th>' => '<th><?= lang(\'Admin.th_req_date\') ?></th>',
    '<th>처리일</th>' => '<th><?= lang(\'Admin.th_proc_date\') ?></th>',
    '<th>상태</th>' => '<th><?= lang(\'Admin.th_status\') ?></th>',
    '<th>삭제</th>' => '<th><?= lang(\'Admin.th_delete\') ?></th>',
    '<th>입금자명</th>' => '<th><?= lang(\'Admin.th_depositor\') ?></th>',
    '<th>발송자</th>' => '<th><?= lang(\'Admin.th_sender\') ?></th>',
    '<th>제목</th>' => '<th><?= lang(\'Admin.th_title\') ?></th>',
    '<th>등록일</th>' => '<th><?= lang(\'Admin.th_reg_date\') ?></th>',
    '<th>답변</th>' => '<th><?= lang(\'Admin.th_reply\') ?></th>',
    '<th>수신자</th>' => '<th><?= lang(\'Admin.th_receiver\') ?></th>',
    '<th>내용보기</th>' => '<th><?= lang(\'Admin.th_view_content\') ?></th>',
    '<th>수정/삭제</th>' => '<th><?= lang(\'Admin.th_edit_delete\') ?></th>',
    '<th>No.</th>' => '<th><?= lang(\'Admin.th_no\') ?>.</th>',
    '<th>No</th>' => '<th><?= lang(\'Admin.th_no\') ?></th>',
    '<th>요청자아이디</th>' => '<th><?= lang(\'Admin.th_req_uid\') ?></th>',
    '<th>요청자이름</th>' => '<th><?= lang(\'Admin.th_req_name\') ?></th>',
    '<th>내역</th>' => '<th><?= lang(\'Admin.th_detail\') ?></th>',
    '<th>이전보유금</th>' => '<th><?= lang(\'Admin.th_money_before\') ?></th>',
    '<th>변동금액</th>' => '<th><?= lang(\'Admin.th_money_change\') ?></th>',
    '<th>이후보유금</th>' => '<th><?= lang(\'Admin.th_money_after\') ?></th>',
    '<th>거래아이디</th>' => '<th><?= lang(\'Admin.th_trade_uid\') ?></th>',
    '<th>변동일시</th>' => '<th><?= lang(\'Admin.th_change_time\') ?></th>',
    '<th>등급</th>' => '<th><?= lang(\'Admin.th_level\') ?></th>',
    '<th>아이디</th>' => '<th><?= lang(\'Admin.th_uid\') ?></th>',
    '<th>이름</th>' => '<th><?= lang(\'Admin.th_name\') ?></th>',
    '<th>보유머니</th>' => '<th><?= lang(\'Admin.money_hold\') ?></th>',
    '<th>보유포인트</th>' => '<th><?= lang(\'Admin.point_hold\') ?></th>',
    '<th>하위머니합</th>' => '<th><?= lang(\'Admin.th_sub_money_sum\') ?></th>',
    '<th>배팅</th>' => '<th><?= lang(\'Admin.th_bet\') ?></th>',
    '<th>당첨</th>' => '<th><?= lang(\'Admin.th_win\') ?></th>',
    '<th>수수료(%)</th>' => '<th><?= lang(\'Admin.th_fee_pct\') ?></th>',
    '<th>매장수</th>' => '<th><?= lang(\'Admin.th_store_cnt\') ?></th>',
    '<th>충전/회수</th>' => '<th><?= lang(\'Admin.th_charge_recall\') ?></th>',
    '<th>수정</th>' => '<th><?= lang(\'Admin.th_edit\') ?></th>',
    '<th>소속</th>' => '<th><?= lang(\'Admin.th_belong\') ?></th>',
    '<th>회차한도</th>' => '<th><?= lang(\'Admin.th_round_limit\') ?></th>',
    '<th>단품/숫자한도</th>' => '<th><?= lang(\'Admin.th_single_limit\') ?></th>',
    '<th>조합한도</th>' => '<th><?= lang(\'Admin.th_mix_limit\') ?></th>',
    '<th>충전유형</th>' => '<th><?= lang(\'Admin.th_charge_type\') ?></th>',
    '<th>충전금액</th>' => '<th><?= lang(\'Admin.th_charge_amount\') ?></th>',
    '<th>입금자</th>' => '<th><?= lang(\'Admin.th_depositor_short\') ?></th>',
    '<th>요청일자</th>' => '<th><?= lang(\'Admin.th_req_datetime\') ?></th>',
    '<th>처리일자</th>' => '<th><?= lang(\'Admin.th_proc_datetime\') ?></th>',
    '<th>처리</th>' => '<th><?= lang(\'Admin.th_process\') ?></th>',
    '<th>환전유형</th>' => '<th><?= lang(\'Admin.th_exchange_type\') ?></th>',
    '<th>환전금액</th>' => '<th><?= lang(\'Admin.th_exchange_amount\') ?></th>',
    '<th>은행명</th>' => '<th><?= lang(\'Admin.th_bank_name\') ?></th>',
    '<th>계좌번호</th>' => '<th><?= lang(\'Admin.th_bank_num\') ?></th>',
    '<th>예금주</th>' => '<th><?= lang(\'Admin.th_bank_owner\') ?></th>',
    '<th>게임회차</th>' => '<th><?= lang(\'Admin.th_game_round\') ?></th>',
    '<th>일회차</th>' => '<th><?= lang(\'Admin.th_day_round\') ?></th>',
    '<th>추첨번호</th>' => '<th><?= lang(\'Admin.th_draw_nums\') ?></th>',
    '<th>숫자합계</th>' => '<th><?= lang(\'Admin.th_num_sum\') ?></th>',
    '<th>결과</th>' => '<th><?= lang(\'Admin.th_result\') ?></th>',
    '<th>마감시간</th>' => '<th><?= lang(\'Admin.th_close_time\') ?></th>',
    '<th>배팅수</th>' => '<th><?= lang(\'Admin.th_bet_count\') ?></th>',
    '<th>정산</th>' => '<th><?= lang(\'Admin.th_settle\') ?></th>',
    '<th>게임일자</th>' => '<th><?= lang(\'Admin.th_game_date\') ?></th>',
    '<th>일간회차</th>' => '<th><?= lang(\'Admin.th_day_round_full\') ?></th>',
    '<th>배팅건수</th>' => '<th><?= lang(\'Admin.th_bet_cnt\') ?></th>',
    '<th>배팅금합계</th>' => '<th><?= lang(\'Admin.th_bet_sum\') ?></th>',
    '<th>당첨금합계</th>' => '<th><?= lang(\'Admin.th_win_sum\') ?></th>',
    '<th>포인트합계</th>' => '<th><?= lang(\'Admin.th_point_sum\') ?></th>',
    '<th>손익</th>' => '<th><?= lang(\'Admin.th_profit\') ?></th>',
    '<th colspan="3">검색일합계</th>' => '<th colspan="3"><?= lang(\'Admin.th_search_day_sum\') ?></th>',
    '<th>게임</th>' => '<th><?= lang(\'Admin.th_game\') ?></th>',
    '<th>아이피</th>' => '<th><?= lang(\'Admin.th_ip\') ?></th>',
    '<th>유저배팅</th>' => '<th><?= lang(\'Admin.th_user_bet\') ?></th>',
    '<th>당첨금</th>' => '<th><?= lang(\'Admin.th_win_money\') ?></th>',
    '<th>배팅시간</th>' => '<th><?= lang(\'Admin.th_bet_time\') ?></th>',
    '<th>유저배팅변경</th>' => '<th><?= lang(\'Admin.th_user_bet_chg\') ?></th>',

    '<div class="divTitle">기간별내역</div>' => '<div class="divTitle"><?= lang(\'Admin.menu_term\') ?></div>',
    '<div class="divTitle">배팅내역</div>' => '<div class="divTitle"><?= lang(\'Admin.menu_bet_list\') ?></div>',
    '<div class="divTitle">배팅합계</div>' => '<div class="divTitle"><?= lang(\'Admin.menu_bet_sum\') ?></div>',
    '<div class="divTitle">게임결과</div>' => '<div class="divTitle"><?= lang(\'Admin.menu_game_result\') ?></div>',
];

$files = glob($dir . '/*.php');
foreach ($files as $file) {
    if (basename($file) === 'main_header.php' || basename($file) === 'main_footer.php') {
        continue;
    }
    $src = file_get_contents($file);
    $out = $src;
    foreach ($replacements as $from => $to) {
        // Don't double-replace
        if (strpos($to, 'lang(') !== false && strpos($out, $from) === false) {
            continue;
        }
        $out = str_replace($from, $to, $out);
    }
    if ($out !== $src) {
        file_put_contents($file, $out);
        echo 'patched ' . basename($file) . "\n";
    } else {
        echo 'skip ' . basename($file) . "\n";
    }
}
echo "done\n";
