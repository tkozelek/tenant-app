<?php

namespace App\Builders;

use App\Models\XmlFeed;

interface Builder
{
    public function build(XmlFeed $feed, iterable $variants): string;
}
