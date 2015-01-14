<?php
namespace Apigee\Util;

/**
 * Class ErrorHandling
 * @package Apigee\Util
 * @deprecated
 */
class ErrorHandling
{

    const SEVERITY_STATUS = 0;
    const SEVERITY_WARNING = 1;
    const SEVERITY_ERROR = 2;

    const DISPLAY_MESSAGE = 0;
    const DISPLAY_INLINE = 1;

    /* App-related codes: 0x00-0x0F */
    const CODE_APP_CREATED = 0x1;
    const CODE_APP_CANNOT_BE_DELETED = 0x2;
    const CODE_APP_CANNOT_BE_LOADED = 0x3;
    const CODE_APP_CANNOT_BE_SAVED = 0x4;

    /* User-related codes: 0x10-0x1F */
    const CODE_USER_CANNOT_BE_SAVED = 0x11;
    const CODE_USER_CANNOT_BE_LOADED = 0x12;

}
