<?php

namespace App\Filters;

use Exception;
use Illuminate\Support\Facades\Redis;

abstract class BloomFilterRedis
{
    /**
     * 桶身名稱
     *
     * @var string
     */
    protected string $bucket;

    /**
     * 使用的函式
     *
     * @var array<int, string>
     */
    protected array $hashFunction = [];

    protected Bloom $hash;

    public function __construct()
    {
        if (!$this->bucket || count($this->hashFunction) === 0) {
            throw new Exception("需要定義 bucket 和 hashFunction", 1);
        }
        $this->hash = new Bloom;
    }

    /**
     * 添加到集合中
     */
    public function add(string $content)
    {
        Redis::pipeline(function ($pipe) use ($content) {
            foreach ($this->hashFunction as $function) {
                $hash = $this->hash->{$function}($content);
                // redis 支援向量資料結構
                $pipe->setBit($this->bucket, $hash, 1);
            }
        });
    }

    /**
     * 查詢是否存在
     * 回 false 代表一定不存在, 回 true 則代表可能存在
     */
    public function exists(string $string)
    {
        $len = strlen($string);
        $res = Redis::pipeline(function ($pipe) use ($string, $len) {
            foreach ($this->hashFunction as $function) {
                $hash = $this->hash->{$function}($string, $len);
                $pipe = $pipe->getBit($this->bucket, $hash);
            }
        });

        foreach ($res as $bit) {
            if ($bit === 0) {
                return false;
            }
        }
        return true;
    }
}
