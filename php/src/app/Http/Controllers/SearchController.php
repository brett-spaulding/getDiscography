<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class SearchController extends Controller
{

    protected function setUp() {
        $host = 'http://selenium-hub:4444';
        $capabilities = DesiredCapabilities::chrome();
        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments(['--no-sandbox', '--disable-dev-shm-usage']);
        $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);
        $driver = RemoteWebDriver::create($host, $capabilities);
        $driver->manage()->window()->maximize();
        return $driver;
    }

    public function search_artist(Request $request, string $artist)
    {
        \Log::info($artist);
        \Log::info($request);
        \Log::info('Getting Artist: ' . $artist);
//        $url = 'https://example.com';
        $url = 'https://music.youtube.com/search?q=' . str_replace(' ', '+', $artist);
        \Log::info('Search URL: ' . $url);
        \Log::info('=======================================');

        // the URL to the local Selenium Server
        $driver = $this->setUp();
        $driver->get($url);
        $html = $driver->getPageSource();

        \Log::info($html);
        \Log::info('=========================================');

        $driver->quit();

    }
}
