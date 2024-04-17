# Pixmicat Administration Module
[![php](https://github.takahashi65.info/lib_badge/php-5.3.0.svg)](https://www.php.net/) 
[![no_maintenance](https://github.takahashi65.info/lib_badge/no_maintenance.svg)](https://github.com/potch/unmaintained.tech)
[![Size](https://img.shields.io/github/repo-size/Suzhou65/Pixmicat_mod_adminenhance)](https://shields.io/category/size)

針對 Pixmicat！ 圖咪貓貼圖版程式的增強型管理套件  

## Contents
- [Pixmicat Administration Module](#pixmicat-administration-module)
  * [Contents](#contents)
  * [Usage](#usage)
    + [WordFilter](#wordfilter)
    + [Image MD5 Hash ban](#image-md5-hash-ban)
    + [IP Adress ban](#ip-adress-ban)
    + [DNSBL server list](#dnsbl-server-list)
  * [Description](#description)
    + [DENY-INFO Folder](#deny-info-folder)
    + [PHP Program](#php-program)
      - [config.php](#configphp)
      - [dnsbl.php](#dnsblphp)
      - [mod_adminenhance.php](#mod-adminenhancephp)
      - [mod_pushpost.php](#mod-pushpostphp)
  * [Dependencies](#dependencies)
    + [Pixmicat](#pixmicat)
  * [Purpose of Development](#purpose-of-development)
  * [License](#license)
  * [Resources](#resources)

## Usage
### WordFilter
- 支援文字過濾功能觸發使用者封鎖  
- 支援推文模組的文字過濾功能  
- 支援推文模組的發言封鎖功能，藉由儲存在用戶端之資料 

### Image MD5 Hash ban
- 支援圖片封鎖功能觸發使用者封鎖  
- 支援圖片封鎖功能觸發使用者封鎖，藉由儲存在用戶端之資料可自定義使用者封鎖時間

### IP Adress ban
- 支援藉由符合網域與 IP 位址的使用者封鎖功能

### DNSBL server list
- 提供指定 DNSBL 伺服器來對付使用公開代理伺服器與洋蔥路由器的惡意使用者  

## Description
### DENY-INFO Folder
- 建議的封鎖資料

### PHP Program
#### config.php
- 針對 Pixmicat！ 預設的封鎖功能與 DNSBL 相關設定作更改  

#### dnsbl.php  
- Pixmicat！ 中關於 DNSBL 等封鎖功能的程式碼，刪除無效的 DNSBL 伺服器並更新  

#### mod_adminenhance.php  
- 增強型管理套件  

#### mod_pushpost.php  
- 增強型推文模組

## Dependencies
### Pixmicat
Pixmicat! Imageboard System, PIO 8th.Release.3 or Higher

## Purpose of Development 
跟智障還有鬧板廚講理是沒用的這點聰明人都知道，所以這種模組是必要的  
![ScreenShot](https://github.takahashi65.info/lib_img/github_meme_crazy_delphin.webp)

## License
General Public License -3.0

## Resources
- [Special Thanks & Developer support](https://github.com/ssk7833)
- [Pixmicat modules,mod_adminenhance](https://github.com/scribetw/pixmicat_modules/tree/develop/mod_adminenhance)  
- [Pixmicat! Imageboard System, on GitHub](https://github.com/scribetw/pixmicat/)  
- [Third party DNSBL server Lookup](http://www.dnsbl.info/dnsbl-database-check.php)
