import os
from redis import Redis
redis = Redis(host='redis', port=6379)
BASE_URL = 'https://www.youtube.com'
QUERY_URL = BASE_URL + '/results?search_query='

CWD = os.getcwd()
ROOT_DIR = os.path.dirname(os.path.abspath(__file__))
MEDIA_FOLDER = os.path.join(ROOT_DIR, 'music')

ALBUM_CONTAINER_ID = 'shelf-container'
ALBUM_CONTAINER_CLASS = 'ytd-search-refinement-card-renderer'
ALBUM_CONTAINER_ITEMS_XPATH = '/html/body/ytd-app/div[1]/ytd-page-manager/ytd-search/div[1]/ytd-two-column-search-results-renderer/ytd-secondary-search-container-renderer/div/ytd-universal-watch-card-renderer/div[4]/ytd-watch-card-section-sequence-renderer[2]/div/ytd-horizontal-card-list-renderer/div[2]/div[2]'
# ALBUM_CONTAINER_FULL_XPATH = '/html/body/ytd-app/div[1]/ytd-page-manager/ytd-search/div[1]/ytd-two-column-search-results-renderer/ytd-secondary-search-container-renderer/div/ytd-universal-watch-card-renderer/div[4]/ytd-watch-card-section-sequence-renderer[2]/div'
ALBUM_CONTAINER_FULL_XPATH = '/html/body/ytd-app/div[1]/ytd-page-manager/ytd-search/div[1]/ytd-two-column-search-results-renderer/ytd-secondary-search-container-renderer/div/ytd-universal-watch-card-renderer/div[4]/ytd-watch-card-section-sequence-renderer[2]/div/ytd-horizontal-card-list-renderer/div[2]'

BTN_RIGHT_FULL_XPATH = '/html/body/ytd-app/div[1]/ytd-page-manager/ytd-search/div[1]/ytd-two-column-search-results-renderer/ytd-secondary-search-container-renderer/div/ytd-universal-watch-card-renderer/div[4]/ytd-watch-card-section-sequence-renderer[2]/div/ytd-horizontal-card-list-renderer/div[2]/div[3]/div[2]/ytd-button-renderer/yt-button-shape/button'
click_script = """
document.evaluate('%s', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.click();
""" % BTN_RIGHT_FULL_XPATH
