<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Controllers\Helpers;

trait SocialiteProviderHelper {

    /**
     * @return array
     */
    public function getAvailableSocialiteProviders(): array
    {
        $providersDirectory = glob(base_path('vendor/socialiteproviders/*'));

        $providers = [];
        foreach ($providersDirectory as $providerDirectory) {
            $providerFiles = glob($providerDirectory . '/*.php');

            foreach ($providerFiles as $providerFile) {
                if (basename($providerFile) == 'Provider.php') {
                    $nameSpace = $this->extractNamespace($providerFile);
                    if ($nameSpace) {
                        $explodedNameSpace = explode('\\', $nameSpace);
                        $providerClass = $nameSpace . '\\Provider';
                        $providerName = end($explodedNameSpace);
                        $providers[$providerClass] = $providerName;
                    }
                }
            }
        }

        return $providers;
    }

    /**
     * @param string $file
     * @return string|false
     */
    private function extractNamespace(string $file) : string|false
    {
        $contents = file_exists($file) ? file_get_contents($file) : $file;
        foreach (token_get_all($contents) as $token) {
            if ($token[0] == T_NAME_QUALIFIED) {
                return $token[1];
            }
        }
        return false;
    }
}
