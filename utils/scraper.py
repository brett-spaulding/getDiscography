import bs4
import time

from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from .browser import new_browser
from const import *


def process_scraped_data(artist, res_value):
    # Process the gathered HTML data for list of data
    html = bs4.BeautifulSoup(res_value, features="html.parser")
    albums = html.find_all('a')
    albums_data_list = []
    for album in albums:
        album_data = {
            'artist': artist.title(),
            'downloaded': False,
            'downloading': False,
        }
        if album.has_key('href'):
            album_data.update({'link': album['href']})

        album_image = album.find('img')
        if album_image and album_image.has_key('src'):
            album_data.update({'cover': album_image['src']})

        album_title = album.find('div', {'id': 'card-title'})
        if album_title and hasattr(album_title, 'text'):
            album_data.update({'album': album_title.text.replace('\n', '').replace('/', '-')})

        albums_data_list.append(album_data)

    return albums_data_list


def scrape(artist):
    browser = new_browser(headless=True)
    url = QUERY_URL + artist
    browser.maximize_window()
    browser.implicitly_wait(1)
    response = browser.get(url)
    last_height = browser.execute_script("return document.body.scrollHeight")
    browser.execute_script("window.scrollTo(0, 500);")
    time.sleep(1)
    scrape_data = ''

    try:
        # get the financial value when it's populated to the page
        value_element = WebDriverWait(browser, 5).until(
            EC.presence_of_element_located(locator=(By.XPATH, '//div[@id="shelf-container"]'))
        )
        element = browser.find_element(By.XPATH, ALBUM_CONTAINER_ITEMS_XPATH)
        if element:
            time.sleep(1)
            scrape_data += element.get_attribute('outerHTML')
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
                scrape_data += element.get_attribute('outerHTML')
                time.sleep(1)
                btn_right_displayed = btn_right.is_displayed()
                if safety_index > 5:
                    btn_right_displayed = False
                time.sleep(1)

    finally:
        # after 5 seconds, give up
        browser.quit()

    return scrape_data
