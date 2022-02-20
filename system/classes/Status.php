<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

class Status
{
    public const OK = 1;
    public const BAD = 0;
    public const LOGGED = 2;
    public const BAD_LOGGED = 3;
    public const BAD_MAIL = 4;
    public const BAD_PASSWORD = 5;
    public const PASSWORD_DOESNT_MATCH = 6;
    public const BAD_USER = 7;
    public const NOT_USER = 8;
    public const NOT_VALID = 9;
    public const BAD_CODE = 10;
    public const BAD_MOVE = 11;
    public const FILE_NOT_EXIST = 12;
    public const FILE_EXIST = 13;
    public const BIG_SIZE = 14;
    public const BAD_FORMAT = 15;
    public const NOT_FOUND = 16;
    public const FOUND = 17;
    public const OWNER_FOUND = 18;
    public const OWNER = 19;
    public const NOT_DATA = 20;
    public const LIMIT = 21;
    public const MAX = 22;
    public const BAD_RIGHTS = 23;
    public const PERMISSION = 24;
    public const PRIVACY = 25;
    public const BAD_FRIEND = 26;
    public const FRIEND = 27;
    public const BAD_DEMAND = 28;
    public const DEMAND = 29;
    public const BAD_DEMAND_OWNER = 30;
    public const DEMAND_OWNER = 31;
    public const NOT_MONEY = 32;
    public const BLACKLIST = 33;
    public const ANTISPAM = 34;
    public const SUBSCRIPTION = 35;
}