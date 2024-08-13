<?php

namespace App\Models;

use App\Utils\ImageUrl;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverAction;
use Facebook\WebDriver\WebDriverExpectedCondition;

class WebScraper
{
    /**
     * Fallback scrape option for the youtube music search page; in some cases there are no additional artists available
     *
     * @return RemoteWebDriver
     */
    public static function scrapeArtist($driver)
    {
        $response = [];
        $artistContainer = $driver->findElement(WebDriverBy::cssSelector('.main-card-content-container'));
        $artistThumbnail = $artistContainer->findElement(WebDriverBy::cssSelector('img'))->getAttribute('src');
        $artistLink = $artistContainer->findElements(WebDriverBy::cssSelector('a'));
        $artistHref = $artistLink[0]->getAttribute('href');
        $artistName = $artistLink[0]->getAttribute('title');

        // Resize image and save to file, provide path to data
        $imageUrl = ImageUrl::modifyGoogleImageUrl($artistThumbnail);
        $imageFileUrl = ImageUrl::save_img_url($imageUrl, 'artist');

        $data = [
            'name' => $artistName,
            'thumbnail' => $artistThumbnail,
            'url_remote' => $artistHref,
            'image' => $imageFileUrl,
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
    public static function scrapeArtists($driver)
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

                        // Resize image and save to file, provide path to data
                        $imageUrl = ImageUrl::modifyGoogleImageUrl($artistThumbnail);
                        $imageFileUrl = ImageUrl::save_img_url($imageUrl, 'artist');

                        // Create if we don't have it yet
                        $data = [
                            'name' => $artistName,
                            'thumbnail' => $artistThumbnail,
                            'url_remote' => $artistHref,
                            'image' => $imageFileUrl,
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

    public static function processAlbums($albumContainer, $artist)
    {
        $albumLink = $albumContainer->findElement(WebDriverBy::cssSelector('a'));
        $albumHref = $albumLink->getAttribute('href');
        $albumTitle = $albumLink->getAttribute('title');
        $albumThumbnail = $albumLink->findElement(WebDriverBy::cssSelector('img'))->getAttribute('src');

        // Resize image and save to file, provide path to data
        $imageUrl = ImageUrl::modifyGoogleImageUrl($albumThumbnail);
        $imageFileUrl = ImageUrl::save_img_url($imageUrl, 'album');

        $data = [
            'name' => $albumTitle,
            'artist_id' => $artist->id,
            'thumbnail' => $albumThumbnail,
            'url_remote' => $albumHref,
            'image' => $imageFileUrl,
        ];
        $album_id = Album::findOrCreateByName($artist, $albumTitle, $data);
        AlbumQueue::addQueue($album_id);
    }

    /**
     * Scrape the album data from given artist page, create new album records and queue those records for download
     *
     * @return RemoteWebDriver
     */
    public static function scrapeAlbums($driver, $artist_id)
    {
        $url = 'https://music.youtube.com/' . $artist_id->url_remote;
        $driver->get($url);
        $response = 0;
        try {
            \Log::info('Looking for Albums button..');
            $albumBtn = $driver->findElements(WebDriverBy::xpath('//a[text()="Albums"]'));
            if ($albumBtn) {
                \Log::info('Clicking on located Albums button..');
                $albumBtn[0]->click();
                sleep(3);
                $itemsContainer = $driver->findElements(WebDriverBy::cssSelector('#items'));
                foreach ($itemsContainer as $item) {
                    $albumContainers = $item->findElements(WebDriverBy::cssSelector('.ytmusic-grid-renderer'));
                    if ($albumContainers) {
                        foreach ($albumContainers as $albumContainer) {
                            $response += 1;
                            WebScraper::processAlbums($albumContainer, $artist_id);
                        }
                    }
                }
            } else {
                \Log::info('Could not locate Albums button');
                $ytRows = $driver->findElements(WebDriverBy::cssSelector('ytmusic-carousel-shelf-renderer'));
                foreach ($ytRows as $ytRow) {
                    $contentGroup = $ytRow->findElements(WebDriverBy::cssSelector('#content-group'));
                    foreach ($contentGroup as $group) {
                        $groupName = $group->getText();
                        if ($groupName == 'Albums') {

                            // Sometimes we don't have the option to click the albums button to filter
                            // Yet, the albums are in a carousel and the images won't load unless they are in view
                            $caroselNextButton = $driver->findElements(WebDriverBy::cssSelector('#next-items-button'));
                            if ($caroselNextButton) {
                                // Youtube is smart enough to block this without an action
                                for ($i = 0; $i <= 3; $i++) {
                                    if ($caroselNextButton[0]->isEnabled()) {
                                        $action = $driver->action();
                                        $action->moveToElement($caroselNextButton[0])->click()->perform();
                                        sleep(1);
                                    }
                                }
                            }

                            $itemsContainer = $ytRow->findElements(WebDriverBy::cssSelector('#items'));
                            foreach ($itemsContainer as $item) {
                                $albumContainers = $item->findElements(WebDriverBy::cssSelector('ytmusic-two-row-item-renderer'));
                                if ($albumContainers) {
                                    foreach ($albumContainers as $albumContainer) {
                                        WebScraper::processAlbums($albumContainer, $artist_id);
                                    }
                                }
                            }
                        }
                    }

                }

            }
        } catch (\Exception $e) {
            \Log::warning('Failed to scrape albums: ---------');
            \Log::warning($e->getMessage());
        }

        return $response;
    }
}
