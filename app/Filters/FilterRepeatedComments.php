<?php

namespace App\Filters;

class FilterRepeatedComments extends BloomFilterRedis
{
    protected string $bucket = 'rptc';

    protected array $hashFunction = ['BKDRHash', 'SDBMHash', 'JSHash'];
}
