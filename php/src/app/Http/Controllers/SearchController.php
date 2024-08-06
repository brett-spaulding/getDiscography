<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Http\Request;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class SearchController extends Controller
{

    protected static function buildResponse($artist_id) {
        return [
            'name' => $artist_id->name,
            'url_remote' => $artist_id->url_remote,
            'thumbnail' => $artist_id->thumbnail,
        ];
    }

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
        $response = [];
        $url = 'https://music.youtube.com/search?q=' . str_replace(' ', '+', $artist);
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

                        $artistThumbnail = $artist->findElement(WebDriverBy::cssSelector('img'))->getAttribute('src');
                        $artistLink = $artist->findElements(WebDriverBy::cssSelector('a'));
                        $artistHref = $artistLink[0]->getAttribute('href');
                        $artistName = $artistLink[0]->getAttribute('aria-label');

                        $existingArtist = Artist::findByName($artistName)->first();
                        if (!$existingArtist) {
                            $artist_id = new Artist();
                            $artist_id->name = $artistName;
                            $artist_id->thumbnail = $artistThumbnail;
                            $artist_id->url_remote = $artistHref;
                            $artist_id->save();

                            $response += [$this->buildResponse($artist_id)];
                        } elseif (!$existingArtist->selected) {
                            // Send the unselected artists back to client as suggestions
                            $response += [$this->buildResponse($existingArtist)];
                        }

                        // Limit the results, there are alot of them
                        if($resultCap <= $resultIndex) {
                            break;
                        }
                    }
                }

                // There are 4 div#contents returned, one empty and 3 with duplicated info
                if ($divCount === 1) {
                    break;
                }

            }
        }
        $driver->quit();
        return response()->json($response);
    }
}
