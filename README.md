# pbg_cn — 中国语游戏机功率球投注端

同服务器部署的 **Electron + PHP** 投注终端。开奖数据直接读取本机 `powerball` 库的 `draw_results`，投注/结算在 `pbg_cn` 库完成（无需像 lion 那样跨服同步）。

## 目录

```
pbg_cn/
  api/           投注·状态·历史·结算 API
  config/        数据库与 powerball 地址
  public/        中文界面（无登录，?mid=机器码）
  electron/      键盘（A–V / W X Y Z / Esc）主进程
  worker/        结算守护进程
  sql/           建库脚本
```

## 1. 初始化数据库

```bat
D:\xampp\php\php.exe D:\xampp\htdocs\pbg_cn\scripts\setup_db.php
```

默认机器 `m01`，初始余额 $10,000。

## 2. 配置

编辑 `config/app.php`：

- `powerball_base_url` — mini 动画 iframe（默认 `http://powerball.com:82/`，hosts에 powerball.com 필요）
- `powerball_fetch_url` — 同机开奖拉取（默认 `http://127.0.0.1:82/lottery/getDrawResult`）
- `powerball_fetch_host` — `powerball.com`（82 포트 name-based vhost）
- `db` / `draw_db` — 投注库与开奖库

编辑 `electron/config.json`：

- `machineId` — 本机台编号（对应 `machines.code`）
- `appUrl` — 终端页地址（本地默认 `http://localhost:83/pbg_cn/public/index.html`）

## 3. 启动开奖拉取 + 结算（必开）

5 分钟边界时通过 **127.0.0.1** 调用 powerball `getDrawResult`，写入 `draw_results` 后结算：

```bat
scripts\start_settle.bat
```

或单独：

```bat
scripts\start_fetch_draw.bat
D:\xampp\php\php.exe D:\xampp\htdocs\pbg_cn\worker\fetch_draw.php --force
```

前端在倒计时 ≤5 秒 / 归零时也会调用 `?action=fetch_draw`。

## 4. 打开终端

浏览器（调试）：

```
http://localhost:83/pbg_cn/public/index.html?mid=m01
```
(本机 XAMPP `htdocs` 在端口 **83**；端口 80 是 IIS，不要用错。)

Electron：

```bat
cd D:\xampp\htdocs\pbg_cn\electron
npm install
npm start
```

## 键盘

| 键 | 作用 |
|----|------|
| A–H | 单式（功率球/普通球 单双大小） |
| O–V | 组合 |
| W | +$10 |
| X | +$100 |
| Y | +$500 |
| Z | 确认投注（金额为 0 时提示请选择金额） |
| Esc | 取消本期等待中的投注并退款；同时清除选定项目 |

仅最后一次选定的项目有效。成功投注后金额重置为 0。

## 赔率

与 lion `conf_game` 默认一致（单式 1.93，功率球组合 4.15/3.05，普通球组合 3.72），存于 `pbg_cn.conf_game`。

## API

| action | 方法 | 说明 |
|--------|------|------|
| status | GET | 余额、倒计时、赔率 |
| bet | POST | `{mid,mode,amount,round}` |
| cancel | POST | 取消本期等待单 |
| history | GET | 投注记录 |
| draws | GET | 开奖历史（방장픽용） |
| patterns | GET | 走势统计 |
| settle | GET | 触发结算 |
| fetch_draw | GET | `127.0.0.1` 로 powerball 추첨 수집（`force=1` 강제） |

## 说明

- 当前无登录；现金兑换暂未实现。后续管理页可改 `machines.balance`。
- 结算只在服务器侧进行（worker / settle API）。
- 币种显示为 `$`。
"# pbg_cn" 
