from seleniumrequests import Firefox
from selenium.webdriver.firefox.options import Options


def new_browser(headless=True):
    options = Options()
    options.add_argument("--window-size=1920,1080")
    if headless:
        options.headless = True
        # options.add_argument('--headless')
        browser = Firefox(options=options, executable_path='/usr/local/bin/geckodriver')
    else:
        browser = Firefox(options=options)
    return browser
