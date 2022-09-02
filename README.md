# 實作短網址

實作的念頭來自 [chivincent's blog - 以 PHP FFI 使用 libcurl 構建 URL Parser](https://chivincent.net/p/url-parser-using-libcurl-with-php-ffi/)

常見實作有二種： `自增序列`、 `摘要`



## 自增序列

每筆網址進來時便產生一組 id, 再將 id 從 10 進位轉成 62 進位

常見取 id 方式： `mysql`、 `redis`

### 優點

簡單、快速、好用、不會重複


### 缺點

id 產生有順序。連續產生三筆時，會發現網址從 xD1Ada 變 xD1Adb 再變 xD1Adc

id 數字小時，轉成 62 進位後，得到的字串不足六碼。

```
 12345 => 3d7 // 僅有三個字

 1654552345 => 1NYkE9  // 數字夠大，字數才足夠六位
```

### 改進

將 id 加入隨機資料及可產生足夠字數的資料進去

例如：
```
(rand(1,9) * pow(10, 9) + id)
```



## 摘要

使用雜湊(hash)函式對長網址做處理


### 優點

不會有自增序時的缺點

### 缺點

各家函式有一定機率產生重複的值

執行較秏時


### 改進


選用重複機率較低且非加密型的雜湊函式，例如：[murmurHash](https://zh.wikipedia.org/wiki/Murmur%E5%93%88%E5%B8%8C)

```
hash('murmur3a', 'abc132'); // 452f94c7
```

但仍可能發生重複，目前常見做法是將長網址加入特定訊息後再重新處理

如果還是重複就再重做一次上述的流程直到不重複



## 相關


短網址存 redis 套上 bloom filter 用來檢查是否有重複及短網址的存活時間(例如：1小時)，有請求時再更新存活時間





## 參考資料：

[短网址服务的原理是什么？](https://www.zhihu.com/question/19852154)

[短网址(short URL)系统的原理及其实现](https://hufangyun.com/2017/short-url/)

[短 URL 系统是怎么设计的？ - iammutex的回答 - 知乎](https://www.zhihu.com/question/29270034/answer/46446911)

[短 URL 系统是怎么设计的？ - 码海的回答 - 知乎](https://www.zhihu.com/question/29270034/answer/1679116463)
