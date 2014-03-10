<?php

namespace Apigee\Mint\Types;

final class MeteringType extends Type
{
    const UNIT = 'UNIT'; //Flat rate
    const VOLUME = 'VOLUME';
    const STAIR_STEP = 'STAIR_STEP'; //Bundled
}
