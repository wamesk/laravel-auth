<?php

namespace Wame\LaravelAuth\Http\Actions;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use hisorange\BrowserDetect\Parser as Browser;

class RegisterDeviceAction
{
    public function handle(
        Model $user,
        string $deviceToken,
    ): string
    {
        $browserInfo = $this->getBrowserInfo();

        /** @var Model $deviceClass */
        $deviceClass = resolve(config('wame-auth.device_model', 'App\\Models\\Device'));

        $deviceName = $this->getDeviceName($browserInfo);

        $device = $this->createOrUpdateDeviceModel($deviceClass, $deviceToken, $user, $deviceName, $browserInfo);

        return $device->createToken($deviceName)->plainTextToken;
    }

    private function getDeviceName($browserInfo)
    {
        if ($browserInfo['isDesktop']) {
            $name =  $browserInfo['deviceType'] . ' ' . $browserInfo['platformName'] . ' ' . $browserInfo['browserName'];
        } elseif ($browserInfo['isMobile'] || $browserInfo['isTablet']) {
            $name =  $browserInfo['deviceType'] . ' ' . $browserInfo['deviceFamily'] . ' ' . $browserInfo['deviceModel'] . ', ' . $browserInfo['mobileGrade'] . ', ' . $browserInfo['platformVersion'];
        } else {
            $name =  $browserInfo['userAgent'];
        }

        return trim($name);
    }

    protected function getBrowserInfo()
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
            //'mobileGrade' => Browser::mobileGrade(),
            'isChrome' => Browser::isChrome(),
            'isFirefox' => Browser::isFirefox(),
            'isOpera' => Browser::isOpera(),
            'isSafari' => Browser::isSafari(),
            'isIE' => Browser::isIE(),
            //isIEVersion' => Browser::isIEVersion(),
            'isEdge' => Browser::isEdge(),
            'isInApp' => Browser::isInApp(),
        ];
    }

    /**
     * @param Model $deviceClass
     * @param string $deviceToken
     * @param Model $user
     * @param mixed $deviceName
     * @param array $browserInfo
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    private function createOrUpdateDeviceModel(Model $deviceClass, string $deviceToken, Model $user, mixed $deviceName, array $browserInfo): \Illuminate\Database\Eloquent\Builder|Model
    {
        $device = $deviceClass::query()->updateOrCreate([
            'device_token' => $deviceToken,
        ], [
            'user_id' => $user->id,
            'name' => $deviceName,
            'data' => $browserInfo,
            'device_token' => $deviceToken,
            'last_login' => Carbon::now()
        ]);
        return $device;
    }

}
