<?php namespace interactivesolutions\honeycombcore\models;

use DB;
use Cog\Ownership\Contracts\HasOwner as HasOwnerContract;
use Cog\Ownership\Traits\HasOwner;

class HCOwnershipModel extends HCModel implements HasOwnerContract
{
    use HasOwner;
}