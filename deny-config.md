# 提供給增強型管理套件（ mod_adminenhance ）的封鎖資料 
  
## 使用說明
在增強型管理套件（ mod_adminenhance ）所產生的資料表中，會以 **.ht_List_IP** 以及 **.ht_List_WORD** 兩個檔案分別存放封鎖網路位址與過濾關鍵字，圖片雜湊值則是 **.ht_List_IMG_MD5**，而使用這個檔名是為了隱藏而不被網路瀏覽器存取，如果想自行修改檔名或存放位置的話，也可以調整 mod_adminenhance 中關於資料表的設定  
  
[ mod_adminenhance 中的關於資料表的設定 ](https://github.com/Suzhou65/mod_adminenhance/blob/master/mod_adminenhance.php#L7)  

***  
### 資料格式  
如果使用 FTP 軟體連線到伺服器資料夾，並使用 Brackets、CotEditor 或是 Notepad++ 之類的編輯器打開資料表，會看到下列的資料格式  
  
#### 以 .ht_List_IP 來說會看到  
**192.168.1.1	IP	1497930252	0**  
192.168.1.1 就是封鎖的網路位址，IP 則是註解，1497930252 是寫入資料表的時間，0 則是設定成永遠封鎖  
  
#### 以 .ht_List_WORD 來說會看到  
**fromuid	推廣預防**  
fromuid 就是過濾的關鍵字，推廣預防則是註解  
  
***  
### 封鎖資料
以下提供可以參考的封鎖資料，基本上不建議使用 FTP 軟體連線到伺服器資料夾，並使用 Brackets、CotEditor 或是 Notepad++ 之類的編輯器打開資料表，因為會有段行、空格定義不同，以及 BOM 的問題，可能導致資料表毀損而無法正常使用，請登入 Pixmicat！的管理介面後在 **HOST | MD5 | WordFilter | BannedTime** 分頁填寫即可  
  
***
### 建議封鎖的網域，針對使用洋蔥路由器（ The Onion Router ）的惡意使用者  
tor  
.torservers.  
tor-exit  
.ori.  
.tor.  
.torworld.org  
  
請在上述封鎖網域的前後加上萬用字元，詳細請參閱[ mod_adminenhance 的書寫規則 ](https://github.com/pixmicat/pixmicat_modules/blob/develop/mod_adminenhance/mod_adminenhance.php#L259)  
***
### 建議封鎖的關鍵字，針對聲優廚與張貼論壇連結的使用者    
@hotmail.com  
fromuid  
fromu  
東山技安鼻  
糙機掰  
糙 機 掰  
糙.機.掰  
幹林娘  
幹 林 娘  
幹.林.娘.  
幹拎娘  
幹 拎 娘  
幹.拎.娘.  
幹你娘  
幹 你 娘  
幹.你.娘.  
臭鮑魚  
臭 鮑 魚  
臭.鮑.魚.  
幹鮑魚  
幹 鮑 魚  
幹.鮑.魚.  
腿開開幹鮑魚  
腿開開 幹 鮑 魚  
腿.開開.幹.鮑.魚  
肛交了  
肛.交.了  
肛.交.狗.  
上板大嬸  
上板.大.嬸  
早見大嬸  
早見.大.嬸.  
種田大嬸  
早見大嬸  
內田真禮  
小岩井大嬸  
松崗這肛交狗  