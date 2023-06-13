import os

import bs4
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from .browser import new_browser
from .yt_dlp_logger import *
from const import *
import time
import wget
import yt_dlp


def download_file(url, output):
    filename = wget.download(url, out=output)
    os.rename(filename, output + '/album.jpg')
    return filename


def process_download(artist):
    artist = artist.title()
    # Initialize a new browser object to go collect the data we need
    browser = new_browser(headless=False)
    url = QUERY_URL + artist
    browser.maximize_window()
    browser.implicitly_wait(1)
    response = browser.get(url)
    last_height = browser.execute_script("return document.body.scrollHeight")
    browser.execute_script("window.scrollTo(0, 500);")
    time.sleep(1)
    res_value = ''

    try:
        # get the financial value when it's populated to the page
        value_element = WebDriverWait(browser, 5).until(
            EC.presence_of_element_located(locator=(By.XPATH, '//div[@id="shelf-container"]'))
        )
        element = browser.find_element(By.XPATH, ALBUM_CONTAINER_ITEMS_XPATH)
        if element:
            time.sleep(1)
            res_value += element.get_attribute('outerHTML')
            btn_right = browser.find_element(By.XPATH, BTN_RIGHT_FULL_XPATH)
            btn_right_displayed = True
            safety_index = 0
            while btn_right_displayed:
                # actions = ActionChains(browser)
                # actions.move_to_element(btn_right).perform()
                safety_index += 1
                time.sleep(1)
                browser.execute_script(click_script)
                time.sleep(1)
                element = browser.find_element(By.XPATH, ALBUM_CONTAINER_ITEMS_XPATH)
                res_value += element.get_attribute('outerHTML')
                time.sleep(1)
                btn_right_displayed = btn_right.is_displayed()
                if safety_index > 5:
                    btn_right_displayed = False
                time.sleep(1)

    finally:
        # after 5 seconds, give up
        browser.quit()

    # Process the gathered HTML data for list of data
    html = bs4.BeautifulSoup(res_value, features="html.parser")
    albums = html.find_all('a')
    albums_data_list = []
    for album in albums:
        album_data = {'artist': artist.title()}
        if album.has_key('href'):
            album_data.update({'link': album['href']})

        album_image = album.find('img')
        if album_image and album_image.has_key('src'):
            album_data.update({'cover': album_image['src']})

        album_title = album.find('div', {'id': 'card-title'})
        if album_title and hasattr(album_title, 'text'):
            album_data.update({'album': album_title.text.replace('\n', '')})

        albums_data_list.append(album_data)

    processed_albums_data_list = []
    processed_album_names = []
    # Eliminate duplicate entries:
    for item in albums_data_list:
        if item.get('cover') and item.get('album') not in processed_album_names:
            processed_albums_data_list.append(item)
            processed_album_names.append(item.get('album'))

    #===:= Download the albums ===#
    # Create Artist folder/path
    artist_path = MEDIA_FOLDER + '/%s' % artist
    if not os.path.exists(artist_path):
        os.mkdir(artist_path)

    for item in processed_albums_data_list:
        print('---')
        # Create album folder
        album = item.get('album')
        album_path = artist_path + '/%s' % album
        if not os.path.exists(album_path):
            os.mkdir(album_path)

        # Save album cover
        if item.get('cover'):
            try:
                download_file(item.get('cover'), album_path)
            except Exception as e:
                print("Warning: %s" % e)

        # Download album
        print(item)
        ydl_opts = {
            'logger': MyLogger(),
            'progress_hooks': [my_hook],
            'format': 'mp3/bestaudio/best',
            'outtmpl': album_path + '/%(title)s.%(ext)s',
            # ℹ️ See help(yt_dlp.postprocessor) for a list of available Postprocessors and their arguments
            'postprocessors': [{  # Extract audio using ffmpeg
                'key': 'FFmpegExtractAudio',
                'preferredcodec': 'mp3',
            }]
        }

        with yt_dlp.YoutubeDL(ydl_opts) as ydl:
            try:
                error_code = ydl.download('https://youtube.com' + item.get('link'))
            except Exception as e:
                print('!!!!!!!!!')
                print(e)

    return processed_albums_data_list

