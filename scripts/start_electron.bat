@echo off
cd /d D:\xampp\htdocs\pbg_cn\electron
if not exist node_modules (
  call npm install
)
call npm start
