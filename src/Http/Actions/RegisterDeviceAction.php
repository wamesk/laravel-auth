<?php

namespace Wame\LaravelAuth\Http\Actions;

use Carbon\Carbon;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RegisterDeviceAction
{
    public function handle(
        Model $user,
        string $deviceToken,
    ): string {
        $browserInfo = $this->getBrowserInfo();

        /** @var Model $deviceClass */
        $deviceClass = resolve(config('wame-auth.device_model', 'App\\Models\\Device'));

        $deviceName = $this->getDeviceName($browserInfo);

        $device = $this->createDevice($deviceClass, $deviceToken, $user, $deviceName, $browserInfo);

        return $device->createToken($deviceName)->plainTextToken;
    }

    private function getDeviceName($browserInfo): string
    {
        $deviceType = $browserInfo['deviceType'] ?? '';

        if ($browserInfo['isDesktop']) {
            $platformName = $browserInfo['platformName'] ?? '';
            $browserName = $browserInfo['browserName'] ?? '';

            $name = $deviceType.' '.$platformName.' '.$browserName;
        } elseif ($browserInfo['isMobile'] || $browserInfo['isTablet']) {
            $deviceFamily = $browserInfo['deviceFamily'] ?? '';
            $deviceModel = $browserInfo['deviceModel'] ?? '';
            $mobileGrade = $browserInfo['mobileGrade'] ?? '';
            $platformVersion = $browserInfo['platformVersion'] ?? '';

            $name = $deviceType.' '.$deviceFamily.' '.$deviceModel.', '.$mobileGrade.', '.$platformVersion;
        } else {
            $name = $browserInfo['userAgent'] ?? '';
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
            // 'mobileGrade' => Browser::mobileGrade(),
            'isChrome' => Browser::isChrome(),
            'isFirefox' => Browser::isFirefox(),
            'isOpera' => Browser::isOpera(),
            'isSafari' => Browser::isSafari(),
            'isIE' => Browser::isIE(),
            // isIEVersion' => Browser::isIEVersion(),
            'isEdge' => Browser::isEdge(),
            'isInApp' => Browser::isInApp(),
        ];
    }

    private function createDevice(Model $deviceClass, string $deviceToken, Model $user, mixed $deviceName, array $browserInfo): Builder|Model
    {
        return $deviceClass::query()->create([
            'user_id' => $user->id,
            'name' => $deviceName,
            'data' => $browserInfo,
            'device_token' => $deviceToken,
            'last_login' => Carbon::now(),
        ]);
    }
}
