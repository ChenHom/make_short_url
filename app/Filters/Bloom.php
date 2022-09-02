<?php

namespace App\Filters;

/**
 * @link https://xbingo.cn/htpqd
 */
class Bloom
{
    /**
     * 由 Justin Sobel 編寫的按位散列函式
     */
    public function JSHash($string, $len = null)
    {
        $hash = 1315423911;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash ^= (($hash << 5) + ord($string[$i]) + ($hash >> 2));
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * 該雜湊演算是依據 AT＆T 貝爾實驗室的 Peter J. Weinberger 的工作。
     * Aho Sethi 和 Ulman 編寫的"編譯器 原理，技術和工具）" 一書建議使用採用此特定演算法中的散列方式的散列函式。
     */
    public function PJWHash($string, $len = null)
    {
        $bitsInUnsignedInt = 4 * 8; //（unsigned int）（sizeof（unsigned int）* 8）;
        $threeQuarters = ($bitsInUnsignedInt * 3) / 4;
        $oneEighth = $bitsInUnsignedInt / 8;
        $highBits = 0xFFFFFFFF << (int) ($bitsInUnsignedInt - $oneEighth);
        $hash = 0;
        $test = 0;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = ($hash << (int) ($oneEighth)) + ord($string[$i]);
        }
        $test = $hash & $highBits;
        if ($test != 0) {
            $hash = (($hash ^ ($test >> (int)($threeQuarters))) & (~$highBits));
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * 類似 PJW Hash 功能，但針對 32 位元處理器進行調整。它的來源為 unix系統上的 widley 函式。
     */
    public function ELFHash($string, $len = null)
    {
        $hash = 0;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = ($hash << 4) + ord($string[$i]);
            $x = $hash & 0xF0000000;
            if ($x != 0) {
                $hash ^= ($x >> 24);
            }
            $hash &= ~$x;
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * 這個雜湊函式來自 Brian Kernighan 和 Dennis Ritchie 的書 “The C Programming Language”。
     * 它是一個簡單的雜湊函式，使用一組奇怪的可能種子，它們形成 31 .... 31 ... 31 等模式，它似乎與 DJB 雜湊函式非常相似。
     */
    public function BKDRHash($string, $len = null)
    {
        $seed = 131; # 31 131 1313 13131 131313 etc..

        $hash = 0;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = (int) (($hash * $seed) + ord($string[$i]));
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * 這是在開源 SDBM 專案中首選的演算法。
     * 雜湊函式似乎對許多不同的資料集有良好的整體分布。似乎適合用於資料集中元素裡有 MSB 高差異的情況。
     */
    public function SDBMHash($string, $len = null)
    {
        $hash = 0;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = (int) (ord($string[$i]) + ($hash << 6) + ($hash << 16) - $hash);
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * 由 Daniel J. Bernstein 教授製作的演算法，首先在 usenet 新聞組 comp.lang.c 上向世界發表。
     * 它是有史以來發佈最有效率的雜湊函式之一。
     */
    public function DJBHash($string, $len = null)
    {
        $hash = 5381;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = (int) (($hash << 5) + $hash) + ord($string[$i]);
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * Donald E. Knuth 在“計算機編程藝術 第 3 卷”中提出的演算法，主題是排序和搜索 第 6.4 章。
     */
    public function DEKHash($string, $len = null)
    {
        $len || $len = strlen($string);
        $hash = $len;
        for ($i = 0; $i < $len; $i++) {
            $hash = (($hash << 5) ^ ($hash >> 27)) ^ ord($string[$i]);
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * 参考 [http://www.isthe.com/chongo/tech/comp/fnv/](http://www.isthe.com/chongo/tech/comp/fnv/)
     */
    public function FNVHash($string, $len = null)
    {
        $prime = 16777619; //32位的prime 2^24 + 2^8 + 0x93 = 16777619
        $hash = 2166136261; //32位的offset
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = (int) ($hash * $prime) % 0xFFFFFFFF;
            $hash ^= ord($string[$i]);
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }
}
