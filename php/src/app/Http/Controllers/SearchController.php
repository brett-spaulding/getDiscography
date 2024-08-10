<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\WebDriver;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Http\Request;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class SearchController extends Controller
{

    /**
     * The default Artist data to be returned from this controller.
     *
     * @return array<string, string>
     */
    public $defaultArtistData = ['id', 'name', 'thumbnail', 'url_remote'];

    /**
     * Fallback scrape option for the youtube music search page; in some cases there are no additional artists available
     *
     * @return RemoteWebDriver
     */
    protected function scrapeArtist($driver)
    {
        $response = [];
        $artistContainer = $driver->findElement(WebDriverBy::cssSelector('.main-card-content-container'));
        $artistThumbnail = $artistContainer->findElement(WebDriverBy::cssSelector('img'))->getAttribute('src');
        $artistLink = $artistContainer->findElements(WebDriverBy::cssSelector('a'));
        $artistHref = $artistLink[0]->getAttribute('href');
        $artistName = $artistLink[0]->getAttribute('title');

        $data = [
            'name' => $artistName,
            'thumbnail' => $artistThumbnail,
            'url_remote' => $artistHref,
        ];
        $artist_id = Artist::findOrCreateByName($artistName, $data);
        return $artist_id->read();
    }

    /**
     * The first scrape that is attempted; this will return the artists and similar artists per youtube so we can return
     * the users search with additional suggestions, or a list of suggestions if their exact search isn't found.
     *
     * @return RemoteWebDriver
     */
    protected function scrapeArtists($driver)
    {
        $response = [];
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
                        // Artist Details
                        $artistThumbnail = $artist->findElement(WebDriverBy::cssSelector('img'))->getAttribute('src');
                        $artistLink = $artist->findElements(WebDriverBy::cssSelector('a'));
                        $artistHref = $artistLink[0]->getAttribute('href');
                        $artistName = $artistLink[0]->getAttribute('aria-label');
                        // Create if we don't have it yet
                        $data = [
                            'name' => $artistName,
                            'thumbnail' => $artistThumbnail,
                            'url_remote' => $artistHref,
                        ];
                        $artist_id = Artist::findOrCreateByName($artistName, $data);
                        $response[] = $artist_id->read();
                        // Limit the results, there are alot of them
                        if ($resultCap <= $resultIndex) {
                            break;
                        }
                    }
                }
                // There are 4 div#contents returned, one empty and 3 with duplicated info
                break;
            }
        }

        return $response;
    }

    public function search_artist(string $artist)
    {
        $url = 'https://music.youtube.com/search?q=' . str_replace(' ', '+', $artist);
        $driver = WebDriver::setUp();
        $driver->get($url);

        // Add handling for no artist button; Some artists searches don't have this option (Ex The Black Dahlia Murder)
        try {
            $response = $this->scrapeArtists($driver);
        } catch (\Exception) {
            \Log::warning('Could not get list of artists, attempting to get single artist card..');
            $response = $this->scrapeArtist($driver);
        } finally {
            $driver->quit();
        }
        return response()->json($response);
    }

}
