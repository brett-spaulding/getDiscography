from const import CWD
from seleniumrequests import Firefox
from selenium.webdriver.firefox.options import Options


def new_browser(headless=True):
    options = Options()
    options.add_argument("--window-size=1920,1080")
    if headless:
        options.headless = True
        # options.add_argument('--headless')
        try:
            # See if the driver is on path
            browser = Firefox(options=options, executable_path='/usr/local/bin/geckodriver')
        except:
            # Get the dist folder as a backup
            browser = Firefox(options=options, executable_path=CWD + '/drivers/geckodriver-0.33.0/dist/geckodriver.exe')

    else:
        browser = Firefox(options=options)
    return browser
