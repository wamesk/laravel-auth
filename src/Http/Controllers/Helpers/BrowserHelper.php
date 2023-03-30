<?php

namespace Wame\LaravelAuth\Http\Controllers\Helpers;

use hisorange\BrowserDetect\Parser as Browser;

class BrowserHelper
{
    public static function getDeviceName($browserInfo)
    {
        if ($browserInfo['isDesktop']) {
            $deviceName = $browserInfo['deviceType'] . ' ' . $browserInfo['platformName'] . ' ' . $browserInfo['browserName'];
        } elseif ($browserInfo['isMobile'] || $browserInfo['isTablet']) {
            $deviceName = $browserInfo['deviceType'] . ' ' . $browserInfo['deviceFamily'] . ' ' . $browserInfo['deviceModel'] . ', ' . $browserInfo['mobileGrade'] . ', ' . $browserInfo['platformVersion'];
        } else {
            $deviceName = $browserInfo['userAgent'];
        }

        return trim(preg_replace('/\s+/', ' ', $deviceName));
    }


    public static function getBrowserInfo(): array
    {
        return [
            'userAgent' => Browser::userAgent(),
            'isMobile' => Browser::isMobile(),
            'isTablet' => Browser::isTablet(),
            'isDesktop' => Browser::isDesktop(),
            'isBot' => Browser::isBot(),
            'deviceType' => Browser::deviceType(),
            'browserName' => Browser::browserName(),
            'browserFamily' => Browser::browserFamily(),
            'browserVersion' => Browser::browserVersion(),
            'browserVersionMajor' => Browser::browserVersionMajor(),
            'browserVersionMinor' => Browser::browserVersionMinor(),
            'browserVersionPatch' => Browser::browserVersionPatch(),
            'browserEngine' => Browser::browserEngine(),
            'platformName' => Browser::platformName(),
            'platformFamily' => Browser::platformFamily(),
            'platformVersion' => Browser::platformVersion(),
            'platformVersionMajor' => Browser::platformVersionMajor(),
            'platformVersionMinor' => Browser::platformVersionMinor(),
            'platformVersionPatch' => Browser::platformVersionPatch(),
            'isWindows' => Browser::isWindows(),
            'isLinux' => Browser::isLinux(),
            'isMac' => Browser::isMac(),
            'isAndroid' => Browser::isAndroid(),
            'deviceFamily' => Browser::deviceFamily(),
            'deviceModel' => Browser::deviceModel(),
            'mobileGrade' => Browser::mobileGrade(),
            'isChrome' => Browser::isChrome(),
            'isFirefox' => Browser::isFirefox(),
            'isOpera' => Browser::isOpera(),
            'isSafari' => Browser::isSafari(),
            'isIE' => Browser::isIE(),
//            'isIEVersion' => Browser::isIEVersion(),
            'isEdge' => Browser::isEdge(),
            'isInApp' => Browser::isInApp(),
        ];
    }






}