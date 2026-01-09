<?php

declare(strict_types=1);

namespace App\Service;

readonly class Url
{
    /**
     * @param string $url
     */
    public function __construct(private string $url)
    {
    }

    /**
     * Generates frontend url
     *
     * @param string $path
     * @param array $parameters
     * @return string
     */
    public function getUrl(string $path, array $parameters = []): string
    {
        $url = $this->url . $path;
        if (count($parameters)) {
            $params = [];
            foreach ($parameters as $key => $value) {
                $params[] = sprintf('%s=%s', $key, $value);
            }

            $url .= '?' . implode('&', $params);
        }

        return $url;
    }

    /**
     * Retrieve service domain
     *
     * @return string
     */
    public function getDomain(): string
    {
        $parsedUrl = parse_url($this->url);
        return $parsedUrl['host'];
    }
}
