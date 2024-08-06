<?php

namespace App\Http\Controllers;

use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Http\Request;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class SearchController extends Controller
{

    protected function setUp()
    {
        $host = 'http://selenium-hub:4444';
        $capabilities = DesiredCapabilities::chrome();
        $chromeOptions = new ChromeOptions();
        // TODO: Add '--headless' back in to arguments
        $chromeOptions->addArguments(['--no-sandbox', '--disable-dev-shm-usage']);
        $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);
        $driver = RemoteWebDriver::create($host, $capabilities);
        $driver->manage()->window()->maximize();
        return $driver;
    }

    public function search_artist(Request $request, string $artist)
    {
        \Log::info('Getting Artist: ' . $artist);
//        $url = 'https://example.com';
        $url = 'https://music.youtube.com/search?q=' . str_replace(' ', '+', $artist);
        \Log::info('Search URL: ' . $url);
        \Log::info('=======================================');

        // the URL to the local Selenium Server
        $driver = $this->setUp();
        $driver->get($url);

        // Click the artist button to force a "structure" of results
        $artistBtnXpath = '//a[@title="Show artist results"]';
        $driver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath($artistBtnXpath))
        );
        $driver->findElement(WebDriverBy::xpath($artistBtnXpath))->click();
        // Youtube has multiple elements with the same ID (Naughty!).  We will give a reasonable analog time to render.
        sleep(5);

        $contentDivs = $driver->findElements(WebDriverBy::cssSelector('#contents'));
        $divCount = 0;
        foreach ($contentDivs as $content) {
            $divCount += 1;

            $artists = $content->findElements(WebDriverBy::xpath('//ytmusic-responsive-list-item-renderer'));

            if ($artists) {
                $resultCap = 6;
                $resultIndex = 0;
                foreach ($artists as $artist) {
                    // There are a bunch of elements with no text in them; just a quick and dirty filter
                    $hasText = $artist->getText();
                    if ($hasText) {
                        $resultIndex += 1;
                        \Log::info('===================================================================================================================================');
                        \Log::info('===================================================================================================================================');
//                        \Log::info($artist->getDomProperty('innerHTML'));

                        // Artist Data Targeting
                        $artistThumbnail = $artist->findElement(WebDriverBy::cssSelector('img'))->getAttribute('src');
                        $artistLink = $artist->findElements(WebDriverBy::cssSelector('a'));
                        $artistHref = $artistLink[0]->getAttribute('href');
                        $artistName = $artistLink[0]->getAttribute('aria-label');

                        \Log::info($artistName . ': ' . $artistHref);
                        \Log::info($artistThumbnail);

                        if($resultCap <= $resultIndex) {
                            break;
                        }

                    }
                }

                if ($divCount === 1) {
                    break;
                }

            }
        }

        $driver->quit();
    }
}
